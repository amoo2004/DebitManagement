<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\SmsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::join('loans', 'payments.loan_id', '=', 'loans.id')
            ->join('customers', 'loans.customer_id', '=', 'customers.id')
            ->selectRaw('loans.customer_id, customers.full_name, customers.phone, COUNT(*) as total_payments, SUM(payments.amount) as total_amount, MAX(payments.payment_date) as latest_payment_date')
            ->groupBy('loans.customer_id', 'customers.full_name', 'customers.phone');

        if ($request->filled('customer_id')) {
            $query->where('loans.customer_id', $request->customer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customers.full_name', 'like', "%{$search}%")
                  ->orWhere('customers.phone', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('latest_payment_date', 'desc')
            ->paginate(15);

        $customers = Customer::whereHas('loans')->orderBy('full_name')->get();

        return view('admin.payments.index', compact('payments', 'customers'));
    }

    public function create()
    {
        $customers = Customer::whereHas('loans', function ($q) {
            $q->whereIn('status', ['pending', 'paying', 'overdue']);
        })->with(['loans' => function ($q) {
            $q->whereIn('status', ['pending', 'paying', 'overdue'])->oldest();
        }])->orderBy('full_name')->get();

        $selectedCustomerId = old('customer_id', request('customer_id'));
        $selectedCustomer = $selectedCustomerId ? Customer::find($selectedCustomerId) : null;

        return view('admin.payments.create', compact('customers', 'selectedCustomerId', 'selectedCustomer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $loans = $customer->loans()
            ->whereIn('status', ['pending', 'paying', 'overdue'])
            ->orderBy('loan_date')
            ->orderBy('id')
            ->get();

        if ($loans->isEmpty()) {
            return back()->with('error', 'No unpaid loans found for this customer.');
        }

        DB::transaction(function () use ($validated, $loans, $customer) {
            $remaining = $validated['amount'];
            $totalSmsMessage = "Payment received: {$validated['amount']}.";

            foreach ($loans as $loan) {
                if ($remaining <= 0) break;

                $applied = min($remaining, (float) $loan->remaining_amount);
                if ($applied <= 0) continue;

                Payment::create([
                    'loan_id' => $loan->id,
                    'amount' => $applied,
                    'payment_date' => $validated['payment_date'],
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                $loan->paid_amount += $applied;
                $loan->remaining_amount -= $applied;
                $loan->save();
                $loan->updateStatus();

                $remaining -= $applied;
            }

            SmsLog::create([
                'customer_id' => $customer->id,
                'phone' => $customer->phone,
                'message' => $totalSmsMessage,
                'status' => 'pending',
            ]);
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('loan.customer', 'createdBy');
        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load('loan.customer');
        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $loan = $payment->loan;
        $oldAmount = $payment->amount;

        $loan->paid_amount = $loan->paid_amount - $oldAmount + $validated['amount'];
        $loan->remaining_amount = $loan->loan_amount - $loan->paid_amount;

        if ($validated['amount'] > ($oldAmount + $loan->remaining_amount)) {
            return back()->with('error', 'Payment amount exceeds remaining balance.');
        }

        DB::transaction(function () use ($payment, $validated, $loan) {
            $payment->update($validated);
            $loan->save();
            $loan->updateStatus();
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $loan = $payment->loan;
        DB::transaction(function () use ($payment, $loan) {
            $loan->paid_amount -= $payment->amount;
            $loan->remaining_amount += $payment->amount;
            $loan->save();
            $loan->updateStatus();
            $payment->delete();
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    public function receipt(Payment $payment)
    {
        $payment->load('loan.customer', 'createdBy');
        return view('admin.payments.receipt', compact('payment'));
    }
}
