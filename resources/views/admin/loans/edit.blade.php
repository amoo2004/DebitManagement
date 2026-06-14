@extends('layouts.admin')

@section('title', 'Edit Loan')

@section('content')
<div class="card">
    <div class="card-header"><h2 class="card-title">Edit Loan #{{ $loan->id }}</h2></div>
    <div class="card-body">
        <form action="{{ route('admin.loans.update', $loan) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $loan->customer_id) }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Customer <span class="text-danger">*</span></label>
                        <input type="text" id="customer_name" class="form-control" value="{{ $loan->customer->full_name }}" list="customerList" autocomplete="off" disabled>
                        <datalist id="customerList">
                            @foreach($customers as $customer)
                            <option value="{{ $customer->full_name }}" data-id="{{ $customer->id }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Product Name</label>
                        <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $loan->product_name) }}" required>
                        @error('product_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Loan Amount</label>
                        <input type="number" step="0.01" name="loan_amount" class="form-control @error('loan_amount') is-invalid @enderror" value="{{ old('loan_amount', $loan->loan_amount) }}" required>
                        @error('loan_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Loan Date</label>
                        <input type="date" name="loan_date" class="form-control @error('loan_date') is-invalid @enderror" value="{{ old('loan_date', $loan->loan_date->format('Y-m-d')) }}" required>
                        @error('loan_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $loan->due_date->format('Y-m-d')) }}" required>
                        @error('due_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mb-3">
                        <label>Time</label>
                        <input type="time" name="due_time" class="form-control @error('due_time') is-invalid @enderror" value="{{ old('due_time', $loan->due_time) }}">
                        @error('due_time') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="alert alert-info">
                <strong>Paid Amount:</strong> {{ number_format($loan->paid_amount, 2) }}<br>
                <strong>Remaining:</strong> {{ number_format($loan->remaining_amount, 2) }}
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection