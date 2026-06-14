<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Loan #{{ $loan->id }}</title>
<style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    h2 { text-align: center; }
    .info-table { margin-bottom: 20px; }
    .info-table td:first-child { font-weight: bold; width: 150px; }
</style>
</head>
<body>
    <h2>{{ config('app.name') }} - Loan Details</h2>
    <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>

    <h3>Customer Information</h3>
    <table class="info-table">
        <tr><td>Name</td><td>{{ $loan->customer->full_name }}</td></tr>
        <tr><td>Phone</td><td>{{ $loan->customer->phone }}</td></tr>
        <tr><td>Address</td><td>{{ $loan->customer->address ?? 'N/A' }}</td></tr>
    </table>

    <h3>Loan Information</h3>
    <table class="info-table">
        <tr><td>Product</td><td>{{ $loan->product_name }}</td></tr>
        <tr><td>Loan Amount</td><td>{{ number_format($loan->loan_amount, 2) }}</td></tr>
        <tr><td>Paid Amount</td><td>{{ number_format($loan->paid_amount, 2) }}</td></tr>
        <tr><td>Remaining Amount</td><td>{{ number_format($loan->remaining_amount, 2) }}</td></tr>
        <tr><td>Loan Date</td><td>{{ $loan->loan_date->format('Y-m-d') }}</td></tr>
        <tr><td>Due Date</td><td>{{ $loan->due_date->format('Y-m-d') }}</td></tr>
        <tr><td>Status</td><td>{{ ucfirst($loan->status) }}</td></tr>
        <tr><td>Created By</td><td>{{ $loan->createdBy->name ?? 'N/A' }}</td></tr>
    </table>

    <h3>Payment History</h3>
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
            <tr><td colspan="5" style="text-align:center">No payments recorded</td></tr>
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
</body>
</html>