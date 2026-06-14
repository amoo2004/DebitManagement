@extends('layouts.admin')

@section('title', 'Edit Payment')

@section('content')
<div class="card">
    <div class="card-header"><h2 class="card-title">Edit Payment #{{ $payment->id }}</h2></div>
    <div class="card-body">
        <form action="{{ route('admin.payments.update', $payment) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $payment->amount) }}" required>
                        @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                        @error('payment_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ $payment->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="mobile_money" {{ $payment->payment_method == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="cheque" {{ $payment->payment_method == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $payment->reference_number) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Loan</label>
                        <input type="text" class="form-control" value="#{{ $payment->loan->id }} - {{ $payment->loan->customer->full_name }}" disabled>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $payment->notes) }}</textarea>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
