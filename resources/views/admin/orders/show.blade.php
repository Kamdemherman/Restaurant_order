{{-- FILE: resources/views/admin/orders/show.blade.php --}}
@extends('layouts.admin')
@section('title','Order '.$order->order_number)
@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="fw-bold mb-0">#{{ $order->order_number }}</h5>
                    <small class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</small>
                </div>
                <span class="badge badge-{{ $order->status }} fs-6 px-3 py-2">{{ ucfirst($order->status) }}</span>
            </div>

            <h6 class="fw-bold mb-3">Items</h6>
            @foreach($order->items as $item)
            <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                <div>
                    <p class="mb-0 fw-semibold">{{ $item->name }} × {{ $item->quantity }}</p>
                    @if($item->selected_addons)
                        <small class="text-muted">{{ collect($item->selected_addons)->pluck('name')->join(', ') }}</small>
                    @endif
                    @if($item->notes)<small class="text-muted d-block">{{ $item->notes }}</small>@endif
                </div>
                <span class="fw-semibold">€{{ number_format($item->subtotal,2) }}</span>
            </div>
            @endforeach

            <div class="mt-3">
                <div class="d-flex justify-content-between text-muted mb-1">
                    <span>Subtotal</span><span>€{{ number_format($order->subtotal,2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="d-flex justify-content-between text-success mb-1">
                    <span>Discount{{ $order->coupon ? " ({$order->coupon->code})" : '' }}</span>
                    <span>−€{{ number_format($order->discount_amount,2) }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2 mt-2">
                    <span>Total</span>
                    <span style="color:#e94560">€{{ number_format($order->total,2) }}</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <h6 class="fw-bold mb-2">Delivery Address</h6>
            <p class="mb-0">{{ $order->delivery_address }}</p>
            @if($order->notes)<p class="mt-2 text-muted small"><strong>Notes:</strong> {{ $order->notes }}</p>@endif
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Customer info --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3">Customer</h6>
            <p class="mb-1 fw-semibold">{{ $order->user->name }}</p>
            <p class="mb-1 text-muted small">{{ $order->user->email }}</p>
            <p class="mb-0 text-muted small">{{ $order->user->phone }}</p>
            <a href="{{ route('admin.users.show', $order->user) }}" class="btn btn-outline-secondary btn-sm mt-2">
                View Profile
            </a>
        </div>

        {{-- Update status --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3">Update Status</h6>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf @method('PUT')
                <select name="status" class="form-select mb-2">
                    @foreach(['pending','confirmed','preparing','ready','delivered','cancelled'] as $s)
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary w-100">Update Status</button>
            </form>
        </div>

        {{-- Print --}}
        <div class="card p-4">
            <h6 class="fw-bold mb-2">Receipt</h6>
            <p class="small text-muted mb-2">
                @if($order->is_printed)
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Printed {{ $order->printed_at?->format('d M H:i') }}
                @else
                    <i class="bi bi-circle text-muted me-1"></i> Not yet printed
                @endif
            </p>
            <form method="POST" action="{{ route('admin.orders.print', $order) }}">
                @csrf
                <button class="btn btn-outline-dark w-100">
                    <i class="bi bi-printer me-1"></i> Print Now
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
