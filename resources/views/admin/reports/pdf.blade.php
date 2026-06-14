<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Payment Report</title>
<style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    h2 { text-align: center; }
</style>
</head>
<body>
    <h2>{{ config('app.name') }} - Payment Report</h2>
    <p>Generated: {{ now()->format('d/m/y H:i:s') }}</p>
    <table>
        <thead><tr><th>#</th><th>Customer</th><th>Product</th><th>Amount</th><th>Date</th><th>Method</th></tr></thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->id }}</td>
                <td>{{ $payment->loan->customer->full_name ?? 'N/A' }}</td>
                <td>{{ $payment->loan->product_name ?? 'N/A' }}</td>
                <td>{{ number_format($payment->amount, 2) }}</td>
                <td>{{ $payment->payment_date->format('d/m/y') }}</td>
                <td>{{ ucfirst($payment->payment_method) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><th colspan="3">Total</th><th>{{ number_format($payments->sum('amount'), 2) }}</th><th colspan="2"></th></tr>
        </tfoot>
    </table>
</body>
</html>
