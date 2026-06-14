@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Customer Info</h2></div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Name</th><td>{{ $customer->full_name }}</td></tr>
                    <tr><th>Phone</th><td>{{ $customer->phone }}</td></tr>
                    <tr><th>Address</th><td>{{ $customer->address ?? 'N/A' }}</td></tr>
                    <tr><th>Total Debt</th><td class="text-danger">{{ number_format($customer->totalDebt(), 2) }}</td></tr>
                </table>
                <div class="d-flex gap-1 flex-wrap justify-content-center">
                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                    @if(request('tab') === 'payments')
                    <a href="{{ route('admin.payments.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Payment</a>
                    @else
                    <a href="{{ route('admin.loans.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Loan</a>
                    @endif
                    <a href="{{ route('admin.customers.pdf', $customer) }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        @if(request('tab') === 'payments')
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Payment History</h2>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>#</th><th>Date</th><th>Loan Product</th><th>Amount</th><th>Method</th><th>Reference</th></tr></thead>
<tbody>
    @forelse($payments as $payment)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $payment->payment_date->format('d/m/y') }}</td>
        <td>{{ $payment->loan->product_name }}</td>
        <td>{{ number_format($payment->amount, 2) }}</td>
        <td>{{ ucfirst($payment->payment_method) }}</td>
        <td>{{ $payment->reference_number ?? 'N/A' }}</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">No payments recorded</td></tr>
    @endforelse
</tbody>
<tfoot>
    <tr class="fw-bold">
        <td colspan="3">Total</td>
        <td>{{ number_format($payments->sum('amount'), 2) }}</td>
        <td colspan="2"></td>
    </tr>
</tfoot>
                </table>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Loan History</h2>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>#</th><th>Product</th><th>Amount</th><th>Paid</th><th>Remaining</th><th>Loan Date</th><th>Due Date</th></tr></thead>
<tbody>
    @forelse($customer->loans as $loan)
    <tr class="fw-bold">
        <td>{{ $loop->iteration }}</td>
        <td>{{ $loan->product_name }}</td>
        <td>{{ number_format($loan->loan_amount, 2) }}</td>
        <td>{{ number_format($loan->paid_amount, 2) }}</td>
        <td>{{ number_format($loan->remaining_amount, 2) }}</td>
        <td>{{ $loan->loan_date->format('d/m/y') }}</td>
        <td>{{ $loan->due_date->format('d/m/y') }} @if($loan->due_time) {{ $loan->due_time }} @endif</td>
    </tr>
    @foreach($loan->payments as $payment)
    <tr class="bg-light">
        <td></td>
        <td colspan="5" class="ps-4">
            <small><i class="fas fa-arrow-right text-success me-1"></i>Payment: {{ number_format($payment->amount, 2) }} on {{ $payment->payment_date->format('d/m/y') }}</small>
        </td>
        <td colspan="2"><small>{{ ucfirst($payment->payment_method) }} {{ $payment->reference_number ? '- Ref: '.$payment->reference_number : '' }}</small></td>
    </tr>
    @endforeach
    @if($loan->payments->isEmpty())
    <tr><td colspan="8" class="text-center text-muted py-1"><small>No payments</small></td></tr>
    @endif
    @empty
    <tr><td colspan="8" class="text-center">No loans</td></tr>
    @endforelse
</tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
