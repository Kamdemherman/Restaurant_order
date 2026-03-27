{{-- FILE: resources/views/user/orders/index.blade.php --}}
@extends('layouts.app')
@section('title','My Orders')
@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4" style="font-family:'Playfair Display',serif">My Orders</h3>

    @if($orders->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bag-x fs-1 d-block mb-3"></i>
            <p>You haven't placed any orders yet.</p>
            <a href="{{ route('menu.index') }}" class="btn btn-accent">Browse Menu</a>
        </div>
    @else
        <div class="row g-3">
            @foreach($orders as $order)
            <div class="col-12">
                <div class="card p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div>
                            <p class="fw-bold mb-0">#{{ $order->order_number }}</p>
                            <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                        </div>
                        <span class="badge badge-{{ $order->status }} rounded-pill px-3 py-2">
                            {{ ucfirst($order->status) }}
                        </span>
                        <div class="text-end">
                            <p class="fw-bold mb-0" style="color:#e94560">€{{ number_format($order->total,2) }}</p>
                            <small class="text-muted">{{ $order->items->count() }} item(s)</small>
                        </div>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                            View <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
