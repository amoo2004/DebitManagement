@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
.small-box { border-radius: 0.5rem; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-6 col-12">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($totalDebt, 2) }}</h3>
                <p>Total Debt</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            <a href="{{ route('admin.loans.index', ['status' => 'unpaid']) }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-6 col-12">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $overdueLoans }}</h3>
                <p>Overdue Loans</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <a href="{{ route('admin.loans.index', ['status' => 'overdue']) }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Monthly Debt</h2></div>
            <div class="card-body">
                <canvas id="debtChart" style="min-height:250px;height:250px;max-height:250px;max-width:100%"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Recent Loans</h2></div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>Customer</th><th>Amount</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($recentLoans as $loan)
                        <tr>
                            <td>{{ $loan->customer->full_name }}</td>
                            <td>{{ number_format($loan->loan_amount, 2) }}</td>
                            <td>{{ $loan->loan_date->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center">No loans yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Recent Payments</h2></div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>Customer</th><th>Amount</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->loan->customer->full_name ?? 'N/A' }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center">No payments yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('debtChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($chartData, 'day')) !!},
        datasets: [{
            label: 'Debt',
            data: {!! json_encode(array_column($chartData, 'debt')) !!},
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220,53,69,0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                title: { display: true, text: 'Day' }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        if (value >= 1000) { return (value / 1000).toFixed(value % 1000 === 0 ? 0 : 1) + 'k'; }
                        return value;
                    }
                }
            }
        }
    }
});
</script>
@endpush
