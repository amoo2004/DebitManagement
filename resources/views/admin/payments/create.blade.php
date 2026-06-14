@extends('layouts.admin')

@section('title', 'Record Payment')

@section('content')
<div class="card">
    <div class="card-header"><h2 class="card-title">Record Payment</h2></div>
    <div class="card-body">
        <form action="{{ route('admin.payments.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-total="{{ $customer->loans->sum('remaining_amount') }}" {{ old('customer_id', $selectedCustomerId) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->full_name }} - {{ $customer->phone }}
                            </option>
                            @endforeach
                        </select>
                        <small id="remainingInfo" class="text-muted"></small>
                        @error('customer_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                        @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                        @error('payment_method') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Record Payment</button>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
var allCustomers = @json($customers);

$('#customer_id').on('change', function() {
    var selected = $(this).find(':selected');
    var total = selected.data('total');
    var info = $('#remainingInfo');
    if (total !== undefined) {
        info.html('Total remaining: <strong>' + parseFloat(total).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong> &mdash; payment will be split across unpaid loans.');
        $('#amount').attr('max', total);

        var customerId = $(this).val();
        var customer = allCustomers.find(function(c) { return c.id == customerId; });
        if (customer && customer.loans.length > 1) {
            var breakdown = '<ul style="margin:4px 0 0 0;padding-left:16px;font-size:12px">';
            customer.loans.forEach(function(loan) {
                breakdown += '<li>' + loan.product_name + ': ' + parseFloat(loan.remaining_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' remaining</li>';
            });
            breakdown += '</ul>';
            info.append(breakdown);
        }
    } else {
        info.html('');
    }
});
$(document).ready(function() { $('#customer_id').trigger('change'); });
</script>
@endpush
@endsection