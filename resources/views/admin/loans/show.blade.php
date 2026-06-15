@extends('layouts.admin')

@section('title', 'Loan Details')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Loan #{{ $loan->id }}</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Customer</th><td><a href="{{ route('admin.customers.show', $loan->customer) }}">{{ $loan->customer->full_name }}</a></td></tr>
                    <tr><th>Phone</th><td>{{ $loan->customer->phone }}</td></tr>
                    <tr><th>Product</th><td>{{ $loan->product_name }}</td></tr>
                    <tr><th>Loan Amount</th><td>{{ number_format($loan->loan_amount, 2) }}</td></tr>
                    <tr><th>Paid Amount</th><td class="text-success">{{ number_format($loan->paid_amount, 2) }}</td></tr>
                    <tr><th>Remaining</th><td class="text-danger">{{ number_format($loan->remaining_amount, 2) }}</td></tr>
                    <tr><th>Loan Date</th><td>{{ $loan->loan_date->format('Y-m-d') }}</td></tr>
                    <tr><th>Due Date</th><td>{{ $loan->due_date->format('Y-m-d') }} @if($loan->due_time) {{ $loan->due_time }} @endif</td></tr>
                    <tr><th>Created By</th><td>{{ $loan->createdBy->name }}</td></tr>
                </table>
                <div class="btn-group w-100">
                    <a href="{{ route('admin.loans.edit', $loan) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                    <a href="{{ route('admin.payments.create') }}?loan_id={{ $loan->id }}" class="btn btn-success"><i class="fas fa-plus"></i> Record Payment</a>
                    <form action="{{ route('admin.loans.send-reminder', $loan) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info"><i class="fas fa-sms"></i> Send Reminder</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Payment History</h2></div>
            <div class="card-body p-0">
                <div style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped">
                    <thead><tr><th>#</th><th>Amount</th><th>Date</th><th>Method</th><th>Reference</th><th>By</th></tr></thead>
                    <tbody>
                        @forelse($loan->payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                            <td>{{ $payment->createdBy->name }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No payments recorded</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="1">Total</td>
                            <td>{{ number_format($loan->payments->sum('amount'), 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
