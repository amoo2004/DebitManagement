<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Loan Report</title>
<style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    h2 { text-align: center; }
</style>
</head>
<body>
    <h2>{{ config('app.name') }} - Loan Report</h2>
    <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    <table>
        <thead><tr><th>#</th><th>Customer</th><th>Phone</th><th>Total Loans</th><th>Total Amount</th><th>Total Paid</th><th>Total Remaining</th></tr></thead>
        <tbody>
            @foreach($loans as $loan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $loan->customer->full_name }}</td>
                <td>{{ $loan->customer->phone }}</td>
                <td>{{ $loan->total_loans }}</td>
                <td>{{ number_format($loan->total_amount, 2) }}</td>
                <td>{{ number_format($loan->total_paid, 2) }}</td>
                <td>{{ number_format($loan->total_remaining, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th>{{ $loans->sum('total_loans') }}</th>
                <th>{{ number_format($loans->sum('total_amount'), 2) }}</th>
                <th>{{ number_format($loans->sum('total_paid'), 2) }}</th>
                <th>{{ number_format($loans->sum('total_remaining'), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
