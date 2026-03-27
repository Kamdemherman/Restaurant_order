{{-- FILE: resources/views/admin/newsletter/index.blade.php --}}
@extends('layouts.admin')
@section('title','Newsletter Subscribers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Newsletter Subscribers</h5>
    <a href="{{ route('admin.newsletter.export') }}" class="btn btn-sm btn-success">
        <i class="bi bi-download me-1"></i>Export CSV
    </a>
</div>

<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-4">
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="active"   {{ request('status')==='active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Unsubscribed</option>
            </select>
        </div>
        <div class="col-sm-2"><button class="btn btn-sm btn-primary w-100">Filter</button></div>
    </form>
</div>

<div class="card">
    <div class="p-3 border-bottom bg-light d-flex justify-content-between">
        <span class="small fw-medium">{{ $subscriptions->total() }} subscriber(s)</span>
        <span class="small text-muted">{{ $subscriptions->where('is_active', true)->count() }} active on this page</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr>
                <th>Email</th><th>Name</th><th>Status</th>
                <th>Confirmed</th><th>Unsubscribed</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($subscriptions as $sub)
            <tr>
                <td>{{ $sub->email }}</td>
                <td class="text-muted">{{ $sub->name ?? '—' }}</td>
                <td>
                    <span class="badge {{ $sub->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $sub->is_active ? 'Active' : 'Unsubscribed' }}
                    </span>
                </td>
                <td class="text-muted small">{{ $sub->confirmed_at?->format('d/m/Y') ?? '—' }}</td>
                <td class="text-muted small">{{ $sub->unsubscribed_at?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}"
                          onsubmit="return confirm('Remove subscriber?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-5">No subscribers found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $subscriptions->withQueryString()->links() }}</div>
</div>
@endsection
