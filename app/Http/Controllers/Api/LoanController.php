<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\SmsLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Loan::with('customer', 'createdBy');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->paginate(15);
        return response()->json($loans);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_name' => 'required|string|max:255',
            'loan_amount' => 'required|numeric|min:0',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:loan_date',
            'due_time' => 'nullable|date_format:H:i',
        ]);

        $validated['paid_amount'] = 0;
        $validated['remaining_amount'] = $validated['loan_amount'];
        $validated['status'] = 'pending';
        $validated['created_by'] = $request->user()->id;

        $loan = DB::transaction(function () use ($validated) {
            $loan = Loan::create($validated);
            $customer = $loan->customer;
            SmsLog::create([
                'customer_id' => $customer->id,
                'phone' => $customer->phone,
                'message' => "You have received a loan of {$validated['loan_amount']}. Please repay before {$validated['due_date']} at " . ($validated['due_time'] ?? '23:59') . ".",
                'status' => 'pending',
            ]);
            return $loan;
        });

        return response()->json($loan->load('customer'), 201);
    }

    public function show(Loan $loan): JsonResponse
    {
        $loan->load('customer', 'createdBy', 'payments');
        return response()->json($loan);
    }

    public function update(Request $request, Loan $loan): JsonResponse
    {
        $validated = $request->validate([
            'product_name' => 'string|max:255',
            'loan_amount' => 'numeric|min:0',
            'due_date' => 'date|after_or_equal:loan_date',
            'due_time' => 'nullable|date_format:H:i',
        ]);

        if (isset($validated['loan_amount'])) {
            $validated['remaining_amount'] = $validated['loan_amount'] - $loan->paid_amount;
        }

        $loan->update($validated);
        $loan->updateStatus();

        return response()->json($loan);
    }

    public function destroy(Loan $loan): JsonResponse
    {
        if ($loan->payments()->exists()) {
            return response()->json(['message' => 'Cannot delete loan with payments.'], 409);
        }
        $loan->delete();
        return response()->json(['message' => 'Loan deleted.']);
    }
}
