<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    public function __construct(private SmsService $smsService) {}

    public function index(Request $request): JsonResponse
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

        $logs = $query->latest()->paginate($request->per_page ?? 50);
        return response()->json($logs);
    }

    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'message' => 'required|string|max:160',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $sent = $this->smsService->send($customer->phone, $validated['message']);

        $log = SmsLog::create([
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'message' => $validated['message'],
            'status' => $sent ? 'sent' : 'failed',
            'sent_at' => $sent ? now() : null,
        ]);

        return response()->json($log, $sent ? 201 : 200);
    }
}
