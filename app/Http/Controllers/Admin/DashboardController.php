<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $overdueLoans = Loan::where('status', 'overdue')->count();
        $totalDebt = Loan::sum('remaining_amount');
        $totalPaid = Loan::sum('paid_amount');
        $totalLoanAmount = Loan::sum('loan_amount');

        $recentPayments = Payment::with('loan.customer', 'createdBy')
            ->latest()
            ->take(5)
            ->get();

        $recentLoans = Loan::with('customer', 'createdBy')
            ->latest()
            ->take(5)
            ->get();

        $unreadNotifications = auth()->user()->unreadNotifications()->latest()->take(5)->get();

        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartData[] = [
                'day' => $date->format('j'),
                'debt' => Loan::whereDate('loan_date', $date->toDateString())->sum('remaining_amount'),
            ];
        }

        return view('admin.dashboard', compact(
            'overdueLoans', 'totalDebt',
            'totalPaid', 'totalLoanAmount',
            'recentPayments', 'recentLoans', 'unreadNotifications', 'chartData'
        ));
    }
}
