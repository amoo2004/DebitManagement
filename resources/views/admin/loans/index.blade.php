@extends('layouts.admin')

@section('title', 'Loans')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Loan List</h2>
        <div class="card-tools">
            <a href="{{ route('admin.loans.export-excel', request()->query()) }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</a>
            <a href="{{ route('admin.loans.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
            <a href="{{ route('admin.loans.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Loan</a>
        </div>
    </div>
    <div class="card-body">
        @include('partials.filters', ['searchable' => true])
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Total Loans</th>
                        <th>Total Amount</th>
                        <th>Total Paid</th>
                        <th>Total Remaining</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    <tr>
                        <td><a href="{{ route('admin.customers.show', $loan->customer) }}">{{ $loan->customer->full_name }}</a></td>
                        <td>{{ $loan->customer->phone }}</td>
                        <td>{{ $loan->total_loans }}</td>
                        <td>{{ number_format($loan->total_amount, 2) }}</td>
                        <td class="text-success">{{ number_format($loan->total_paid, 2) }}</td>
                        <td class="text-danger">{{ number_format($loan->total_remaining, 2) }}</td>
                        <td>
                            <a href="{{ route('admin.customers.show', $loan->customer) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.loans.create') }}?customer_id={{ $loan->customer_id }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">No loans found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $loans->links() }}
    </div>
</div>
@endsection