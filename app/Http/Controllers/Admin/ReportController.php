<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('loan.customer', 'createdBy');

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('customer_id')) {
            $query->whereHas('loan', function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            });
        }

        $period = $request->period ?? 'daily';
        $payments = $query->latest()->get();
        $customers = Customer::orderBy('full_name')->get();

        $collections = [];
        if ($period === 'daily') {
            $collections = $payments->groupBy(function ($p) {
                return $p->payment_date->format('Y-m-d');
            })->map(function ($items) {
                return $items->sum('amount');
            });
        } elseif ($period === 'weekly') {
            $collections = $payments->groupBy(function ($p) {
                return $p->payment_date->format('Y-W');
            })->map(function ($items) {
                return $items->sum('amount');
            });
        } elseif ($period === 'monthly') {
            $collections = $payments->groupBy(function ($p) {
                return $p->payment_date->format('Y-m');
            })->map(function ($items) {
                return $items->sum('amount');
            });
        }

        $totalCollections = $payments->sum('amount');
        $loanStats = [
            'total' => Loan::count(),
            'active' => Loan::whereIn('status', ['pending', 'paying'])->count(),
            'overdue' => Loan::where('status', 'overdue')->count(),
        ];

        return view('admin.reports.index', compact(
            'payments', 'collections', 'totalCollections',
            'loanStats', 'period', 'customers'
        ));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ReportExport($request), 'report.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $payments = Payment::with('loan.customer', 'createdBy')->latest()->get();
        $pdf = Pdf::loadView('admin.reports.pdf', compact('payments'));
        return $pdf->download('report.pdf');
    }
}
