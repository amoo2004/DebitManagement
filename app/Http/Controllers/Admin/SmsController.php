<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SmsLog;
use App\Models\Loan;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index(Request $request)
    {
        $query = SmsLog::with('customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('phone', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->latest()->take(10)->get();
        return view('admin.sms.index', compact('logs'));
    }

    public function sendManual(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'message' => 'required|string|max:160',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $sent = $this->smsService->send($customer->phone, $request->message);

        SmsLog::create([
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'message' => $request->message,
            'status' => $sent ? 'sent' : 'failed',
            'sent_at' => $sent ? now() : null,
        ]);

        if ($sent) {
            return back()->with('success', 'SMS sent successfully to ' . $customer->phone);
        }

        return back()->with('error', 'SMS failed to send. Check storage/logs/laravel.log for details.');
    }

    public function sendReminder(Loan $loan)
    {
        $customer = $loan->customer;
        $totalDue = $customer->loans()->whereIn('status', ['pending', 'paying', 'overdue'])->sum('remaining_amount');
        $dueDate = $loan->due_date->format('Y-m-d');
        $dueTime = $loan->due_time ?? '23:59';
        $message = "Dear {$customer->full_name}, your loan of TZS {$loan->loan_amount} is due on {$dueDate} at {$dueTime}. Total outstanding balance across all loans: TZS {$totalDue}. Please pay on time to avoid Discomfort. Thank you.";

        $sent = $this->smsService->send($customer->phone, $message);

        SmsLog::create([
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'message' => $message,
            'status' => $sent ? 'sent' : 'failed',
            'sent_at' => $sent ? now() : null,
        ]);

        if ($sent) {
            return back()->with('success', 'Reminder sent to ' . $customer->phone);
        }

        return back()->with('error', 'Reminder SMS failed. Check storage/logs/laravel.log for details.');
    }
}
