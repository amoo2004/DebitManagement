@extends('layouts.admin')

@section('title', 'Create Loan')

@section('content')
<div class="card">
    <div class="card-header"><h2 class="card-title">Create New Loan</h2></div>
    <div class="card-body">
        <form action="{{ route('admin.loans.store') }}" method="POST" id="loanForm">
            @csrf
            <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $selectedCustomer?->id ?? request('customer_id')) }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3" style="position:relative">
                        <label>Customer Name <span class="text-danger">*</span></label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control @error('customer_id') is-invalid @enderror" value="{{ old('customer_name', $selectedCustomer?->full_name) }}" autocomplete="off" required {{ $selectedCustomer ? 'readonly' : '' }}>
                        <div id="customerSuggestions" class="list-group" style="position:absolute;z-index:1000;max-height:200px;overflow-y:auto;display:none;width:100%"></div>
                        @if(!$selectedCustomer)
                        <small class="text-muted">Type to search existing customers, or enter a new name</small>
                        @endif
                        @error('customer_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Phone <span class="text-danger" id="phoneRequired" style="display:none">*</span></label>
                        <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="{{ old('customer_phone', $selectedCustomer?->phone) }}" placeholder="Required for new customer" {{ $selectedCustomer ? 'readonly' : '' }}>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name') }}" required>
                        @error('product_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Loan Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="loan_amount" class="form-control @error('loan_amount') is-invalid @enderror" value="{{ old('loan_amount') }}" required>
                        @error('loan_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Loan Date <span class="text-danger">*</span></label>
                        <input type="date" name="loan_date" class="form-control @error('loan_date') is-invalid @enderror" value="{{ old('loan_date', date('Y-m-d')) }}" required>
                        @error('loan_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label>Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                        @error('due_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mb-3">
                        <label>Time</label>
                        <input type="time" name="due_time" class="form-control @error('due_time') is-invalid @enderror" value="{{ old('due_time') }}">
                        @error('due_time') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Loan</button>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
var customers = @json($customers);
var suggestionBox = $('#customerSuggestions');
var customerField = $('#customer_name');

customerField.on('input', function() {
    var name = $(this).val().trim().toLowerCase();
    suggestionBox.empty();

    if (name.length === 0) {
        suggestionBox.hide();
        $('#customer_id').val('');
        return;
    }

    var matches = customers.filter(function(c) {
        return c.full_name.toLowerCase().indexOf(name) !== -1;
    });

    if (matches.length > 0) {
        $.each(matches, function(i, c) {
            var item = $('<a href="#" class="list-group-item list-group-item-action" style="padding:6px 12px;font-size:14px"></a>');
            item.text(c.full_name + ' - ' + c.phone);
            item.on('click', function(e) {
                e.preventDefault();
                customerField.val(c.full_name);
                $('#customer_id').val(c.id);
                $('#customer_phone').val(c.phone).prop('readonly', true);
                $('#phoneRequired').hide();
                suggestionBox.hide();
            });
            suggestionBox.append(item);
        });
        suggestionBox.show();
    } else {
        suggestionBox.hide();
    }
});

customerField.on('blur', function() {
    setTimeout(function() { suggestionBox.hide(); }, 200);
});

customerField.on('focus', function() {
    if ($(this).val().trim().length > 0 && suggestionBox.children().length > 0) {
        suggestionBox.show();
    }
});

$('#customer_name').on('change', function() {
    var name = $(this).val().trim();
    var match = customers.find(function(c) {
        return c.full_name.toLowerCase() === name.toLowerCase();
    });
    if (match) {
        $('#customer_id').val(match.id);
        $('#customer_phone').val(match.phone).prop('readonly', true);
        $('#phoneRequired').hide();
    } else {
        $('#customer_id').val('');
    }
});

$('#loanForm').on('submit', function(e) {
    if (!$('#customer_id').val() && !$('#customer_phone').val()) {
        e.preventDefault();
        alert('Please enter a phone number for the new customer.');
    }
});
</script>
@endpush