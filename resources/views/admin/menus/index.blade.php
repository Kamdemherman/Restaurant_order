{{-- FILE: resources/views/admin/menus/index.blade.php --}}
@extends('layouts.admin')
@section('title','Menu Items')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Menu Items</h5>
    <a href="{{ route('admin.menus.create') }}" class="btn btn-sm" style="background:#e94560;color:#fff">
        <i class="bi bi-plus-lg me-1"></i>New Item
    </a>
</div>

<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search items…" value="{{ request('search') }}">
        </div>
        <div class="col-sm-4">
            <select name="category" class="form-select form-select-sm">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category')==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3"><button class="btn btn-sm btn-primary w-100">Filter</button></div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr>
                <th>Item</th><th>Category</th><th>Price</th>
                <th>Available</th><th>Featured</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($items as $item)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}" class="rounded"
                                 style="width:40px;height:40px;object-fit:cover">
                        @else
                            <div class="rounded d-flex align-items-center justify-content-center text-muted"
                                 style="width:40px;height:40px;background:#f0ede8">🍴</div>
                        @endif
                        <span class="fw-medium">{{ $item->name }}</span>
                    </div>
                </td>
                <td class="text-muted small">{{ $item->category->name }}</td>
                <td class="fw-semibold">€{{ number_format($item->price,2) }}</td>
                <td>
                    <button class="btn btn-sm {{ $item->is_available ? 'btn-success' : 'btn-outline-secondary' }} toggle-availability"
                            data-id="{{ $item->id }}" data-url="{{ route('admin.menus.toggle', $item) }}">
                        {{ $item->is_available ? 'Available' : 'Hidden' }}
                    </button>
                </td>
                <td>
                    <span class="badge {{ $item->is_featured ? 'bg-warning text-dark' : 'bg-light text-muted' }}">
                        {{ $item->is_featured ? '⭐ Featured' : '—' }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.menus.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.menus.destroy', $item) }}"
                              onsubmit="return confirm('Delete this item?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-5">No items found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $items->withQueryString()->links() }}</div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.toggle-availability').forEach(btn => {
    btn.addEventListener('click', async () => {
        const res  = await fetch(btn.dataset.url, {
            method:'POST',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
        });
        const data = await res.json();
        btn.textContent  = data.available ? 'Available' : 'Hidden';
        btn.className    = `btn btn-sm ${data.available ? 'btn-success' : 'btn-outline-secondary'} toggle-availability`;
    });
});
</script>
@endpush
@endsection
