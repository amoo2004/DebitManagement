@extends('layouts.admin')

@section('title', 'SMS Settings')

@section('content')
<div class="card">
    <div class="card-header"><h2 class="card-title">SMS Configuration</h2></div>
    <div class="card-body">
        <form action="{{ route('admin.settings.sms.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>SMS Provider <span class="text-danger">*</span></label>
                <select name="sms_provider" class="form-control" required>
                    <option value="twilio" {{ setting('sms_provider') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                    <option value="africastalking" {{ setting('sms_provider') == 'africastalking' ? 'selected' : '' }}>Africa's Talking</option>
                </select>
            </div>
            <hr>
            <h5>Twilio Settings</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Account SID</label>
                        <input type="text" name="twilio_sid" class="form-control" value="{{ setting('twilio_sid') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Auth Token</label>
                        <input type="password" name="twilio_token" class="form-control" value="{{ setting('twilio_token') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>From Number</label>
                        <input type="text" name="twilio_from" class="form-control" value="{{ setting('twilio_from') }}">
                    </div>
                </div>
            </div>
            <hr>
            <h5>Africa's Talking Settings</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="africa_username" class="form-control" value="{{ setting('africa_username') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>API Key</label>
                        <input type="password" name="africa_api_key" class="form-control" value="{{ setting('africa_api_key') }}">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </form>
    </div>
</div>
@endsection
