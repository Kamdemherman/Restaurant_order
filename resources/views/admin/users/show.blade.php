{{-- FILE: resources/views/admin/users/show.blade.php --}}
@extends('layouts.admin')
@section('title',$user->name)
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3">Customer Profile</h6>
            <p class="mb-1 fw-bold fs-5">{{ $user->name }}</p>
            <p class="mb-1 text-muted">{{ $user->email }}</p>
            <p class="mb-1 text-muted">{{ $user->phone ?? 'No phone' }}</p>
            <p class="mb-1 text-muted small">{{ $user->address ?? 'No address' }}</p>
            <p class="mb-3">
                <span class="badge {{ match($user->status){ 'approved'=>'bg-success','pending'=>'bg-warning text-dark','suspended'=>'bg-danger',default=>'bg-secondary' } }} px-3">
                    {{ ucfirst($user->status) }}
                </span>
            </p>
            <p class="small text-muted">Newsletter: {{ $user->newsletter_subscribed ? '✅ Subscribed' : '❌ Not subscribed' }}</p>
            <p class="small text-muted mb-3">Joined {{ $user->created_at->format('d M Y') }}</p>

            <div class="d-flex flex-column gap-2">
                @if($user->status === 'pending')
                <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                    @csrf
                    <button class="btn btn-success w-100"><i class="bi bi-check-lg me-1"></i>Approve Account</button>
                </form>
                @endif
                @if($user->status !== 'suspended')
                <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                    @csrf
                    <button class="btn btn-warning w-100"><i class="bi bi-pause-circle me-1"></i>Suspend Account</button>
                </form>
                @endif
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Delete this user? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Delete User</button>
                </form>
            </div>
        </div>

        <div class="card p-4">
            <h6 class="fw-bold mb-2">Spend Summary</h6>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total orders</span>
                <strong>{{ $user->orders->count() }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">Total spent</span>
                <strong>€{{ number_format($user->orders->where('status','!=','cancelled')->sum('total'),2) }}</strong>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Order History</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light"><tr>
                        <th>Order #</th><th>Status</th><th>Total</th><th>Date</th><th></th>
                    </tr></thead>
                    <tbody>
                    @forelse($user->orders as $order)
                    <tr>
                        <td><code>{{ $order->order_number }}</code></td>
                        <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td>€{{ number_format($order->total,2) }}</td>
                        <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-outline-secondary btn-sm">View</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-muted text-center">No orders yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
