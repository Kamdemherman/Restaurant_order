{{-- FILE: resources/views/admin/coupons/index.blade.php --}}
@extends('layouts.admin')
@section('title','Coupons')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Discount Coupons</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-sm" style="background:#e94560;color:#fff">
            <i class="bi bi-plus me-1"></i>New Coupon
        </a>
    </div>
</div>

{{-- Bulk generate --}}
<div class="card p-3 mb-3">
    <form method="POST" action="{{ route('admin.coupons.bulk') }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-auto"><label class="form-label fw-medium mb-1 small">Bulk Generate</label>
            <input type="number" name="count" class="form-control form-control-sm" placeholder="Qty" min="1" max="100" style="width:80px">
        </div>
        <div class="col-auto">
            <select name="type" class="form-select form-select-sm">
                <option value="fixed">Fixed (€)</option>
                <option value="percentage">Percentage (%)</option>
            </select>
        </div>
        <div class="col-auto">
            <input type="number" name="value" class="form-control form-control-sm" placeholder="Value" step="0.01" min="0" style="width:90px">
        </div>
        <div class="col-auto">
            <input type="date" name="expires_at" class="form-control form-control-sm">
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-outline-secondary">Generate</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr>
                <th>Code</th><th>Type</th><th>Value</th><th>Min Order</th>
                <th>Status</th><th>Used By</th><th>Expires</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($coupons as $coupon)
            <tr>
                <td><code class="fw-bold">{{ $coupon->code }}</code></td>
                <td>{{ ucfirst($coupon->type) }}</td>
                <td>{{ $coupon->type === 'percentage' ? $coupon->value.'%' : '€'.number_format($coupon->value,2) }}</td>
                <td>€{{ number_format($coupon->min_order_amount,2) }}</td>
                <td>
                    @if(!$coupon->is_active)
                        <span class="badge bg-secondary">Inactive</span>
                    @elseif($coupon->is_used)
                        <span class="badge bg-info">Used</span>
                    @elseif($coupon->expires_at && $coupon->expires_at->isPast())
                        <span class="badge bg-warning text-dark">Expired</span>
                    @else
                        <span class="badge bg-success">Active</span>
                    @endif
                </td>
                <td class="text-muted small">
                    {{ $coupon->usedBy?->name ?? '—' }}
                    @if($coupon->used_at)<br><small>{{ $coupon->used_at->format('d/m/Y') }}</small>@endif
                </td>
                <td class="text-muted small">{{ $coupon->expires_at?->format('d/m/Y') ?? '∞' }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}">
                            @csrf
                            <button class="btn btn-sm {{ $coupon->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                {{ $coupon->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}"
                              onsubmit="return confirm('Delete coupon?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-5">No coupons yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $coupons->withQueryString()->links() }}</div>
</div>
@endsection
