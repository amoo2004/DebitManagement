@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Notifications</h2>
        <div class="card-tools">
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check-double"></i> Mark All Read</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead><tr><th>Title</th><th>Message</th><th>Type</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($notifications as $notification)
                <tr class="{{ $notification->read_status ? '' : 'fw-bold' }}">
                    <td>{{ $notification->title }}</td>
                    <td>{{ $notification->message }}</td>
                    <td><span class="badge bg-info">{{ ucfirst($notification->type) }}</span></td>
                    <td>{{ $notification->created_at->diffForHumans() }}</td>
                    <td>
                        @if($notification->read_status)
                        <span class="badge bg-success">Read</span>
                        @else
                        <span class="badge bg-warning">Unread</span>
                        @endif
                    </td>
                    <td>
                        @if(!$notification->read_status)
                        <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No notifications</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $notifications->links() }}
@endsection
