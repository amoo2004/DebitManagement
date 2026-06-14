@extends('layouts.admin')

@section('title', 'SMS Logs')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Send Manual SMS</h2></div>
            <div class="card-body">
                <form action="{{ route('admin.sms.send-manual') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Customer</label>
                                <select name="customer_id" class="form-control" required>
                                    <option value="">Select Customer</option>
                                    @foreach(\App\Models\Customer::orderBy('full_name')->get() as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->full_name }} ({{ $customer->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Message</label>
                                <textarea name="message" class="form-control" rows="3" maxlength="160" required placeholder="Max 160 characters"></textarea>
                                <small class="text-muted">160 character limit</small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send SMS</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h2 class="card-title">SMS History</h2></div>
            <div class="card-body">
                @include('partials.filters', ['searchable' => true])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>#</th><th>Phone</th><th>Message</th><th>Status</th><th>Sent At</th></tr></thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $log->phone }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($log->message, 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->status == 'sent' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>{{ $log->sent_at ? $log->sent_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center">No SMS logs</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
