@extends('layouts.admin')

@section('title', 'SMS Settings')

@section('content')
<div class="card">
    <div class="card-header"><h2 class="card-title">SMS Configuration (Meseji)</h2></div>
    <div class="card-body">
        <form action="{{ route('admin.settings.sms.update') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>API Key <span class="text-danger">*</span></label>
                        <input type="password" name="meseji_api_key" class="form-control" value="{{ setting('meseji_api_key') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Sender ID <span class="text-danger">*</span></label>
                        <input type="text" name="meseji_sender_id" class="form-control" value="{{ setting('meseji_sender_id') }}" placeholder="e.g. YOURBRAND">
                    </div>
                </div>
            </div>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Note:</strong> The sender ID must be registered and approved on your
                <a href="https://meseji.co.tz" target="_blank">meseji.co.tz</a> account first.
                Login to your meseji account and apply for a sender ID before sending messages.
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </form>

        @if (setting('meseji_api_key') && setting('meseji_sender_id'))
        <hr>
        <form action="{{ route('admin.settings.sms.test') }}" method="POST" class="mt-3">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Test Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="test_phone" class="form-control" placeholder="e.g. 0712345678 or 255712345678" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Send Test SMS</button>
        </form>
        @endif
    </div>
</div>
@endsection
