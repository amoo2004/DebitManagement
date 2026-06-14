<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function summary(): JsonResponse
    {
        return response()->json([
            'total_customers' => Customer::count(),
            'total_loans' => Loan::count(),
            'active_loans' => Loan::whereIn('status', ['pending', 'paying'])->count(),
            'completed_loans' => Loan::where('status', 'completed')->count(),
            'overdue_loans' => Loan::where('status', 'overdue')->count(),
            'total_debt' => Loan::sum('remaining_amount'),
            'total_paid' => Loan::sum('paid_amount'),
            'today_collections' => Payment::whereDate('payment_date', today())->sum('amount'),
            'monthly_collections' => Payment::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)->sum('amount'),
        ]);
    }

    public function collections(Request $request): JsonResponse
    {
        $query = Payment::query();

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $total = $query->sum('amount');
        $count = $query->count();

        return response()->json([
            'total_collections' => $total,
            'total_transactions' => $count,
            'payments' => $query->with('loan.customer')->latest()->get(),
        ]);
    }
}
