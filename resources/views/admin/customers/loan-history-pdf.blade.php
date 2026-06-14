<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Customer Loan History</title>
<style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    h2 { text-align: center; }
    .info-table td:first-child { font-weight: bold; width: 120px; }
    .section-title { margin-top: 20px; margin-bottom: 8px; font-size: 14px; font-weight: bold; }
</style>
</head>
<body>
    <h2>{{ config('app.name') }} - Customer Loan History</h2>
    <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>

    <h3>Customer Information</h3>
    <table class="info-table">
        <tr><td>Name</td><td>{{ $customer->full_name }}</td></tr>
        <tr><td>Phone</td><td>{{ $customer->phone }}</td></tr>
        <tr><td>Address</td><td>{{ $customer->address ?? 'N/A' }}</td></tr>
        <tr><td>Notes</td><td>{{ $customer->notes ?? 'N/A' }}</td></tr>
        <tr><td>Total Debt</td><td>{{ number_format($customer->totalDebt(), 2) }}</td></tr>
    </table>

    <h3>Loan History</h3>

    @forelse($customer->loans as $loan)
    <div class="section-title">Loan #{{ $loop->iteration }} - {{ $loan->product_name }}</div>
    <table>
        <tr><th>Amount</th><td>{{ number_format($loan->loan_amount, 2) }}</td></tr>
        <tr><th>Paid</th><td>{{ number_format($loan->paid_amount, 2) }}</td></tr>
        <tr><th>Remaining</th><td>{{ number_format($loan->remaining_amount, 2) }}</td></tr>
        <tr><th>Loan Date</th><td>{{ $loan->loan_date->format('Y-m-d') }}</td></tr>
        <tr><th>Due Date</th><td>{{ $loan->due_date->format('Y-m-d') }}</td></tr>
        <tr><th>Status</th><td>{{ ucfirst($loan->status) }}</td></tr>
    </table>

    <table>
        <thead><tr><th>#</th><th>Amount</th><th>Date</th><th>Method</th><th>Reference</th></tr></thead>
        <tbody>
            @forelse($loan->payments as $payment)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ number_format($payment->amount, 2) }}</td>
                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                <td>{{ ucfirst($payment->payment_method) }}</td>
                <td>{{ $payment->reference_number ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center">No payments</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="1">Total</th>
                <th>{{ number_format($loan->payments->sum('amount'), 2) }}</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
    @empty
    <p style="text-align:center">No loans found for this customer.</p>
    @endforelse
</body>
</html>