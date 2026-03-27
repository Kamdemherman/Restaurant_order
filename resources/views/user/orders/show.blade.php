{{-- FILE: resources/views/user/orders/show.blade.php --}}
@extends('layouts.app')
@section('title','Order '.$order->order_number)
@section('content')
<div class="container py-4" style="max-width:700px">
    <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted small mb-3 d-block">
        <i class="bi bi-arrow-left me-1"></i> Back to orders
    </a>

    <div class="card p-4 mb-3">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h5 class="fw-bold mb-0">#{{ $order->order_number }}</h5>
                <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
            </div>
            <span class="badge badge-{{ $order->status }} rounded-pill px-3 py-2 fs-6">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        {{-- Order timeline --}}
        @php
        $steps = ['pending'=>0,'confirmed'=>1,'preparing'=>2,'ready'=>3,'delivered'=>4];
        $current = $steps[$order->status] ?? 0;
        $cancelled = $order->status === 'cancelled';
        @endphp
        @if(!$cancelled)
        <div class="d-flex justify-content-between mb-4 position-relative" style="padding:0 20px">
            <div style="position:absolute;top:14px;left:30px;right:30px;height:3px;background:#dee2e6;z-index:0">
                <div style="width:{{ $current * 25 }}%;background:#e94560;height:100%;transition:.3s"></div>
            </div>
            @foreach(['Placed','Confirmed','Preparing','Ready','Delivered'] as $si => $step)
            <div class="text-center" style="z-index:1;flex:1">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1 fw-bold"
                     style="width:30px;height:30px;font-size:.75rem;
                     background:{{ $si <= $current ? '#e94560' : '#dee2e6' }};
                     color:{{ $si <= $current ? '#fff' : '#888' }}">
                    {{ $si + 1 }}
                </div>
                <p style="font-size:.65rem;color:{{ $si <= $current ? '#e94560' : '#999' }}" class="mb-0">{{ $step }}</p>
            </div>
            @endforeach
        </div>
        @endif

        <h6 class="fw-bold mb-3">Items Ordered</h6>
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
                <span>− €{{ number_format($order->discount_amount,2) }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2 mt-1">
                <span>Total</span>
                <span style="color:#e94560">€{{ number_format($order->total,2) }}</span>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt me-1 text-danger"></i>Delivery Address</h6>
        <p class="mb-0 text-muted">{{ $order->delivery_address }}</p>
        <p class="mt-2 mb-0"><i class="bi bi-cash-coin me-1 text-success"></i> Cash on Delivery</p>
        @if($order->notes)
        <p class="mt-2 mb-0"><i class="bi bi-chat-left-text me-1"></i> {{ $order->notes }}</p>
        @endif
    </div>
</div>
@endsection
