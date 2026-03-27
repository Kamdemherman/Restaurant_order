{{-- FILE: resources/views/admin/orders/index.blade.php --}}
@extends('layouts.admin')
@section('title','Orders')
@section('content')

<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3">
            <label class="form-label small fw-medium">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Order # or customer…" value="{{ request('search') }}">
        </div>
        <div class="col-sm-2">
            <label class="form-label small fw-medium">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach(['pending','confirmed','preparing','ready','delivered','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2">
            <label class="form-label small fw-medium">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-sm-2">
            <label class="form-label small fw-medium">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-sm-2">
            <button class="btn btn-sm btn-primary w-100">Filter</button>
        </div>
        <div class="col-sm-1">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Order #</th><th>Customer</th><th>Status</th>
                <th>Total</th><th>Payment</th><th>Printed</th><th>Date</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($orders as $order)
            <tr>
                <td><input type="checkbox" class="order-check" value="{{ $order->id }}"></td>
                <td><code>{{ $order->order_number }}</code></td>
                <td>
                    <p class="mb-0 fw-medium">{{ $order->user->name }}</p>
                    <small class="text-muted">{{ $order->user->phone }}</small>
                </td>
                <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                <td class="fw-semibold">€{{ number_format($order->total,2) }}</td>
                <td><span class="badge {{ $order->payment_status==='paid' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($order->payment_status) }}</span></td>
                <td>
                    @if($order->is_printed)
                        <i class="bi bi-check-circle-fill text-success" title="{{ $order->printed_at?->format('H:i') }}"></i>
                    @else
                        <i class="bi bi-circle text-muted"></i>
                    @endif
                </td>
                <td class="text-muted small">{{ $order->created_at->format('d/m H:i') }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                        <form action="{{ route('admin.orders.print', $order) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary" title="Print">
                                <i class="bi bi-printer"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-5">No orders found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 d-flex justify-content-between align-items-center">
        <div>
            <form action="{{ route('admin.orders.bulk-print') }}" method="POST" id="bulkPrintForm">
                @csrf
                <div id="bulkInputs"></div>
                <button type="submit" class="btn btn-sm btn-secondary" id="bulkPrintBtn" disabled>
                    <i class="bi bi-printer me-1"></i>Print Selected
                </button>
            </form>
        </div>
        {{ $orders->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
const selectAll = document.getElementById('selectAll');
selectAll.addEventListener('change', () => {
    document.querySelectorAll('.order-check').forEach(c => c.checked = selectAll.checked);
    updateBulk();
});
document.querySelectorAll('.order-check').forEach(c => c.addEventListener('change', updateBulk));

function updateBulk() {
    const checked = [...document.querySelectorAll('.order-check:checked')];
    const btn  = document.getElementById('bulkPrintBtn');
    const form = document.getElementById('bulkInputs');
    btn.disabled = !checked.length;
    form.innerHTML = checked.map((c,i) => `<input type="hidden" name="order_ids[${i}]" value="${c.value}">`).join('');
}
</script>
@endpush
@endsection
