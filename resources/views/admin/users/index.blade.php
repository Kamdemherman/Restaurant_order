{{-- FILE: resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')
@section('title','Customers')
@section('content')

<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or email…" value="{{ request('search') }}">
        </div>
        <div class="col-sm-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All statuses</option>
                @foreach(['pending','approved','suspended'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2"><button class="btn btn-sm btn-primary w-100">Filter</button></div>
        <div class="col-sm-2"><a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a></div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr>
                <th>Name</th><th>Email</th><th>Phone</th>
                <th>Status</th><th>Orders</th><th>Spent</th><th>Joined</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($users as $user)
            <tr>
                <td class="fw-medium">{{ $user->name }}</td>
                <td class="text-muted small">{{ $user->email }}</td>
                <td class="text-muted small">{{ $user->phone ?? '—' }}</td>
                <td>
                    <span class="badge {{ match($user->status){ 'approved'=>'bg-success','pending'=>'bg-warning text-dark','suspended'=>'bg-danger',default=>'bg-secondary' } }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td>{{ $user->orders_count }}</td>
                <td>€{{ number_format($user->orders_sum_total ?? 0, 2) }}</td>
                <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-5">No customers found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
