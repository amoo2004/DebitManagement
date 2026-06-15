<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\SmsLog;
use App\Services\SmsService;
use App\Exports\LoanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with('customer')
            ->selectRaw('customer_id, COUNT(*) as total_loans, SUM(loan_amount) as total_amount, SUM(paid_amount) as total_paid, SUM(remaining_amount) as total_remaining')
            ->groupBy('customer_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'unpaid') {
                $query->whereIn('status', ['pending', 'paying', 'overdue']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $loans = $query->oldest('customer_id')->paginate(15);
        return view('admin.loans.index', compact('loans'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('full_name')->get();
        $selectedCustomer = null;
        if ($request->filled('customer_id')) {
            $selectedCustomer = Customer::find($request->customer_id);
        }
        return view('admin.loans.create', compact('customers', 'selectedCustomer'));
    }

    public function store(Request $request, SmsService $smsService)
    {
        $rules = [
            'product_name' => 'required|string|max:255',
            'loan_amount' => 'required|numeric|min:0',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:loan_date',
            'due_time' => 'nullable|date_format:H:i',
        ];

        if ($request->filled('customer_id')) {
            $rules['customer_id'] = 'exists:customers,id';
        } else {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = 'required|string|max:20';
        }

        $validated = $request->validate($rules);

        $validated['paid_amount'] = 0;
        $validated['remaining_amount'] = $validated['loan_amount'];
        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        $sent = false;
        $message = '';

        DB::transaction(function () use ($request, &$validated, $smsService, &$sent, &$message) {
            if (!$request->filled('customer_id')) {
                $customer = Customer::create([
                    'full_name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                ]);
                $validated['customer_id'] = $customer->id;
            }
            $loan = Loan::create($validated);
            $customer = Customer::find($validated['customer_id']);
            $totalDue = $customer->loans()->whereIn('status', ['pending', 'paying', 'overdue'])->sum('remaining_amount');
            $dueTime = $validated['due_time'] ?? '23:59';
            $message = "Dear {$customer->full_name}, your loan of TZS {$validated['loan_amount']} has been approved. Outstanding balance: TZS {$totalDue}. Please repay before {$validated['due_date']} at {$dueTime}. Thank you.";

            $sent = $smsService->send($customer->phone, $message);

            SmsLog::create([
                'customer_id' => $customer->id,
                'phone' => $customer->phone,
                'message' => $message,
                'status' => $sent ? 'sent' : 'failed',
                'sent_at' => $sent ? now() : null,
            ]);
        });

        if ($sent) {
            return redirect()->route('admin.loans.index')
                ->with('success', 'Loan created and SMS sent successfully.');
        }

        return redirect()->route('admin.loans.index')
            ->with('error', 'Loan created but SMS failed to send.');
    }

    public function show(Loan $loan)
    {
        $loan->load('customer', 'createdBy', 'payments.createdBy');
        return view('admin.loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        $customers = Customer::orderBy('full_name')->get();
        return view('admin.loans.edit', compact('loan', 'customers'));
    }

    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_name' => 'required|string|max:255',
            'loan_amount' => 'required|numeric|min:0',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:loan_date',
            'due_time' => 'nullable|date_format:H:i',
        ]);

        $validated['remaining_amount'] = $validated['loan_amount'] - $loan->paid_amount;
        $loan->update($validated);
        $loan->updateStatus();

        return redirect()->route('admin.loans.index')
            ->with('success', 'Loan updated successfully.');
    }

    public function destroy(Loan $loan)
    {
        if ($loan->payments()->exists()) {
            return back()->with('error', 'Cannot delete loan with existing payments.');
        }
        $loan->delete();
        return redirect()->route('admin.loans.index')
            ->with('success', 'Loan deleted successfully.');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new LoanExport($request), 'loans.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Loan::with('customer')
            ->selectRaw('customer_id, COUNT(*) as total_loans, SUM(loan_amount) as total_amount, SUM(paid_amount) as total_paid, SUM(remaining_amount) as total_remaining')
            ->groupBy('customer_id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('customer', function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'unpaid') {
                $query->whereIn('status', ['pending', 'paying', 'overdue']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('loan_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('loan_date', '<=', $request->date_to);
        }

        $loans = $query->oldest('customer_id')->get();
        $pdf = Pdf::loadView('admin.loans.pdf', compact('loans'));
        return $pdf->download('loans.pdf');
    }

    public function singlePdf(Loan $loan)
    {
        $loan->load('customer', 'createdBy', 'payments');
        $pdf = Pdf::loadView('admin.loans.single-pdf', compact('loan'));
        return $pdf->download("loan-{$loan->id}.pdf");
    }
}
