{{-- FILE: resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')
@section('title','Dashboard')
@section('content')

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    @php
    $statCards = [
        ['label'=>"Today's Orders",  'value'=>$stats['orders_today'],       'icon'=>'bag-check',     'color'=>'#e94560'],
        ['label'=>"Today's Revenue", 'value'=>'€'.number_format($stats['revenue_today'],2), 'icon'=>'cash-coin','color'=>'#198754'],
        ['label'=>'Pending Approvals','value'=>$stats['pending_users'],     'icon'=>'person-check',  'color'=>'#ffc107'],
        ['label'=>'Pending Orders',  'value'=>$stats['pending_orders'],     'icon'=>'clock-history', 'color'=>'#0d6efd'],
    ];
    @endphp
    @foreach($statCards as $card)
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3" style="border-left-color:{{ $card['color'] }}">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-muted small mb-1">{{ $card['label'] }}</p>
                    <h4 class="fw-bold mb-0">{{ $card['value'] }}</h4>
                </div>
                <i class="bi bi-{{ $card['icon'] }} fs-2" style="color:{{ $card['color'] }};opacity:.5"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Sales chart --}}
    <div class="col-lg-8">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Revenue – Last 30 Days</h6>
            <canvas id="salesChart" height="90"></canvas>
        </div>
    </div>

    {{-- Quick stats --}}
    <div class="col-lg-4">
        <div class="card p-4 h-100">
            <h6 class="fw-bold mb-3">Quick Stats</h6>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">This month orders</span>
                <strong>{{ $stats['orders_this_month'] }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">This month revenue</span>
                <strong>€{{ number_format($stats['revenue_this_month'],2) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total customers</span>
                <strong>{{ $stats['total_customers'] }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">Active menu items</span>
                <strong>{{ $stats['active_items'] }}</strong>
            </div>
            @if($stats['pending_users'] > 0)
            <a href="{{ route('admin.users.index', ['status'=>'pending']) }}"
               class="btn btn-warning btn-sm mt-3 w-100">
                <i class="bi bi-person-check me-1"></i>{{ $stats['pending_users'] }} pending approval(s)
            </a>
            @endif
        </div>
    </div>

    {{-- Recent orders --}}
    <div class="col-12">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Recent Orders</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead><tr>
                        <th>Order #</th><th>Customer</th><th>Status</th>
                        <th>Total</th><th>Time</th><th></th>
                    </tr></thead>
                    <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td><code>{{ $order->order_number }}</code></td>
                        <td>{{ $order->user->name }}</td>
                        <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td class="fw-semibold">€{{ number_format($order->total,2) }}</td>
                        <td class="text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartData = @json($salesChart);
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: chartData.map(d => d.date),
        datasets: [{
            label: 'Revenue (€)',
            data:  chartData.map(d => parseFloat(d.total)),
            borderColor: '#e94560',
            backgroundColor: 'rgba(233,69,96,.1)',
            fill: true,
            tension: .4,
            pointBackgroundColor: '#e94560',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '€'+v } },
            x: { ticks: { maxTicksLimit: 10 } }
        }
    }
});
</script>
@endpush
@endsection
