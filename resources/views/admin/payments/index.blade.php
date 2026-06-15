@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Payment List</h2>
        <div class="card-tools">
            <a href="{{ route('admin.payments.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Record Payment</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <select name="customer_id" class="form-control">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->full_name }} - {{ $customer->phone }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Total Payments</th>
                        <th>Total Amount</th>
                        <th>Latest Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="{{ route('admin.customers.show', ['customer' => $payment->customer_id, 'tab' => 'payments']) }}">{{ $payment->full_name }}</a></td>
                        <td>{{ $payment->phone }}</td>
                        <td>{{ $payment->total_payments }}</td>
                        <td>{{ number_format($payment->total_amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->latest_payment_date)->format('d/m/y') }}</td>
                        <td style="white-space: nowrap;">
                            <a href="{{ route('admin.customers.show', ['customer' => $payment->customer_id, 'tab' => 'payments']) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.payments.create') }}?customer_id={{ $payment->customer_id }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">No payments found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $payments->links() }}
    </div>
</div>
@endsection