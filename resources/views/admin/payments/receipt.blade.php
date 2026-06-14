<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt #{{ $payment->id }}</title>
    <style>
        body { font-family: monospace; font-size: 14px; }
        .receipt { width: 300px; margin: 0 auto; padding: 20px; border: 1px dashed #333; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px; text-align: left; }
        hr { border-top: 1px dashed #333; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="text-center">
            <h3>{{ config('app.name') }}</h3>
            <p>Payment Receipt</p>
        </div>
        <hr>
        <table>
            <tr><td><strong>Receipt #:</strong></td><td class="text-right">{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
            <tr><td><strong>Date:</strong></td><td class="text-right">{{ $payment->payment_date->format('Y-m-d') }}</td></tr>
            <tr><td><strong>Customer:</strong></td><td class="text-right">{{ $payment->loan->customer->full_name }}</td></tr>
            <tr><td><strong>Product:</strong></td><td class="text-right">{{ $payment->loan->product_name }}</td></tr>
            <tr><td><strong>Method:</strong></td><td class="text-right">{{ ucfirst($payment->payment_method) }}</td></tr>
            @if($payment->reference_number)
            <tr><td><strong>Reference:</strong></td><td class="text-right">{{ $payment->reference_number }}</td></tr>
            @endif
        </table>
        <hr>
        <table>
            <tr><td><strong>Amount Paid:</strong></td><td class="text-right"><strong>{{ number_format($payment->amount, 2) }}</strong></td></tr>
            <tr><td><strong>Remaining:</strong></td><td class="text-right">{{ number_format($payment->loan->remaining_amount, 2) }}</td></tr>
        </table>
        <hr>
        <div class="text-center">
            <p>Thank you for your payment!</p>
            <button onclick="window.print()" style="padding:10px 20px;margin-top:10px;cursor:pointer;">Print Receipt</button>
        </div>
    </div>
</body>
</html>
