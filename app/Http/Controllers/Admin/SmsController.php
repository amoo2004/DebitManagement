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
        $this->smsService->send($customer->phone, $request->message);

        SmsLog::create([
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'message' => $request->message,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'SMS sent successfully.');
    }

    public function sendReminder(Loan $loan)
    {
        $customer = $loan->customer;
        $message = "Reminder: Your remaining debt is {$loan->remaining_amount}. Please pay today.";

        $this->smsService->send($customer->phone, $message);

        SmsLog::create([
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Reminder SMS sent successfully.');
    }
}
