{{-- FILE: resources/views/admin/reports/sales.blade.php --}}
@extends('layouts.admin')
@section('title','Sales Report')
@section('content')

{{-- Date filter --}}
<div class="card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3">
            <label class="form-label fw-medium small">From Date</label>
            <input type="date" name="from" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}">
        </div>
        <div class="col-sm-3">
            <label class="form-label fw-medium small">To Date</label>
            <input type="date" name="to" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}">
        </div>
        <div class="col-sm-2"><button class="btn btn-sm btn-primary w-100">Apply</button></div>
        <div class="col-sm-2">
            <a href="{{ route('admin.reports.export', ['from'=>\Carbon\Carbon::parse($from)->format('Y-m-d'),'to'=>\Carbon\Carbon::parse($to)->format('Y-m-d')]) }}"
               class="btn btn-sm btn-success w-100">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
        </div>
    </form>
</div>

{{-- Summary cards --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Total Orders',   'value'=> $summary->total_orders ?? 0,                              'icon'=>'bag-check',    'color'=>'#0d6efd'],
        ['label'=>'Total Revenue',  'value'=>'€'.number_format($summary->total_revenue ?? 0,2),         'icon'=>'cash-coin',    'color'=>'#198754'],
        ['label'=>'Total Discounts','value'=>'€'.number_format($summary->total_discounts ?? 0,2),       'icon'=>'ticket-perforated','color'=>'#ffc107'],
        ['label'=>'Avg Order Value','value'=>'€'.number_format($summary->avg_order ?? 0,2),             'icon'=>'graph-up',     'color'=>'#e94560'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="col-6 col-md-3">
        <div class="card p-3" style="border-left:4px solid {{ $card['color'] }}">
            <p class="text-muted small mb-1">{{ $card['label'] }}</p>
            <h4 class="fw-bold mb-0">{{ $card['value'] }}</h4>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Daily revenue chart --}}
    <div class="col-lg-8">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Daily Revenue</h6>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    {{-- Order status breakdown --}}
    <div class="col-lg-4">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Orders by Status</h6>
            <canvas id="statusChart"></canvas>
            <div class="mt-3">
                @foreach($byStatus as $row)
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="badge badge-{{ $row->status }}">{{ ucfirst($row->status) }}</span>
                    <strong>{{ $row->count }}</strong>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top items --}}
    <div class="col-12">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Top Selling Items</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light"><tr>
                        <th>#</th><th>Item</th><th>Qty Sold</th><th>Revenue</th>
                    </tr></thead>
                    <tbody>
                    @foreach($topItems as $i => $item)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td class="fw-medium">{{ $item->name }}</td>
                        <td>{{ $item->qty_sold }}</td>
                        <td class="fw-semibold">€{{ number_format($item->revenue, 2) }}</td>
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
const daily = @json($dailySales);
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: daily.map(d => d.date),
        datasets: [
            { label:'Revenue (€)', data: daily.map(d => parseFloat(d.revenue)),
              backgroundColor:'rgba(233,69,96,.7)', yAxisID:'y' },
            { label:'Orders', data: daily.map(d => d.orders),
              type:'line', borderColor:'#0d6efd', backgroundColor:'transparent',
              tension:.4, yAxisID:'y1' }
        ]
    },
    options: {
        responsive:true,
        scales: {
            y:  { beginAtZero:true, ticks:{ callback: v=>'€'+v }, position:'left' },
            y1: { beginAtZero:true, position:'right', grid:{ drawOnChartArea:false } }
        }
    }
});

const statusData = @json($byStatus);
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusData.map(d => d.status.charAt(0).toUpperCase()+d.status.slice(1)),
        datasets: [{ data: statusData.map(d => d.count),
            backgroundColor:['#ffc107','#0dcaf0','#0d6efd','#198754','#6c757d','#dc3545'] }]
    },
    options: { responsive:true, plugins:{ legend:{ position:'bottom' } } }
});
</script>
@endpush
@endsection
