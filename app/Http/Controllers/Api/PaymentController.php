<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with('loan.customer', 'createdBy');

        if ($request->filled('loan_id')) {
            $query->where('loan_id', $request->loan_id);
        }

        $payments = $query->latest()->paginate(15);
        return response()->json($payments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $loan = Loan::findOrFail($validated['loan_id']);

        if ($validated['amount'] > $loan->remaining_amount) {
            return response()->json(['message' => 'Payment exceeds remaining balance.'], 422);
        }

        $validated['created_by'] = $request->user()->id;

        $payment = DB::transaction(function () use ($validated, $loan) {
            $payment = Payment::create($validated);

            $loan->paid_amount += $validated['amount'];
            $loan->remaining_amount -= $validated['amount'];
            $loan->save();
            $loan->updateStatus();

            return $payment;
        });

        $customer = $loan->customer;
        $remainingBalance = $customer->loans()->whereIn('status', ['pending', 'paying', 'overdue'])->sum('remaining_amount');
        $recordedAt = now()->format('Y-m-d H:i');
        $message = "Dear {$customer->full_name}, we have received your payment of TZS {$validated['amount']} on {$validated['payment_date']} at {$recordedAt}. Outstanding balance: TZS {$remainingBalance}. Thank you for your payment.";

        $smsService = app(SmsService::class);
        $sent = $smsService->send($customer->phone, $message);

        SmsLog::create([
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'message' => $message,
            'status' => $sent ? 'sent' : 'failed',
            'sent_at' => $sent ? now() : null,
        ]);

        return response()->json($payment->load('loan.customer'), 201);
    }

    public function show(Payment $payment): JsonResponse
    {
        $payment->load('loan.customer', 'createdBy');
        return response()->json($payment);
    }
}
