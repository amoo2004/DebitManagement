@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-info"><div class="inner"><h3>{{ $loanStats['total'] }}</h2><p>Total Loans</p></div><div class="icon"><i class="fas fa-file-invoice"></i></div></div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-warning"><div class="inner"><h3>{{ $loanStats['active'] }}</h2><p>Active Loans</p></div><div class="icon"><i class="fas fa-spinner"></i></div></div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-danger"><div class="inner"><h3>{{ $loanStats['overdue'] }}</h2><p>Overdue</p></div><div class="icon"><i class="fas fa-exclamation-circle"></i></div></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2 class="card-title">Collection Reports</h2></div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <select name="period" class="form-control">
                    <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="customer_id" class="form-control">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col-12">
                <h4>Total Collections: <span class="text-success">{{ number_format($totalCollections, 2) }}</span></h4>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Period</th><th>Amount</th></tr></thead>
                <tbody>
                    @forelse($collections as $periodKey => $amount)
                    <tr><td>{{ $periodKey }}</td><td>{{ number_format($amount, 2) }}</td></tr>
                    @empty
                    <tr><td colspan="2" class="text-center">No data for selected period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2 class="card-title">Payment Details</h2>
        <div class="card-tools">
            <a href="{{ route('admin.reports.export-excel', request()->query()) }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</a>
            <a href="{{ route('admin.reports.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>#</th><th>Customer</th><th>Product</th><th>Amount</th><th>Date</th><th>Method</th><th>By</th></tr></thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->loan->customer->full_name ?? 'N/A' }}</td>
                    <td>{{ $payment->loan->product_name ?? 'N/A' }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->payment_date->format('d/m/y') }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td>{{ $payment->createdBy->name ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">No payments found</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold"><td colspan="3">Total</td><td>{{ number_format($payments->sum('amount'), 2) }}</td><td colspan="3"></td></tr>
            </tfoot>
        </table>
        </div>
    </div>
</div>
@endsection
