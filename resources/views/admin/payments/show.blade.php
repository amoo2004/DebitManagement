@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="card">
    <div class="card-header"><h2 class="card-title">Payment #{{ $payment->id }}</h2></div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Customer</th><td>{{ $payment->loan->customer->full_name ?? 'N/A' }}</td></tr>
            <tr><th>Loan Product</th><td>{{ $payment->loan->product_name ?? 'N/A' }}</td></tr>
            <tr><th>Amount</th><td>{{ number_format($payment->amount, 2) }}</td></tr>
            <tr><th>Payment Date</th><td>{{ $payment->payment_date->format('Y-m-d') }}</td></tr>
            <tr><th>Method</th><td>{{ ucfirst($payment->payment_method) }}</td></tr>
            <tr><th>Reference</th><td>{{ $payment->reference_number ?? 'N/A' }}</td></tr>
            <tr><th>Notes</th><td>{{ $payment->notes ?? 'N/A' }}</td></tr>
            <tr><th>Recorded By</th><td>{{ $payment->createdBy->name }}</td></tr>
        </table>
    </div>
</div>
@endsection
