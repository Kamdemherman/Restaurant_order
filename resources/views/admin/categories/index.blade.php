{{-- FILE: resources/views/admin/categories/index.blade.php --}}
@extends('layouts.admin')
@section('title','Categories')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Menu Categories</h5>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-sm" style="background:#e94560;color:#fff">
        <i class="bi bi-plus-lg me-1"></i>New Category
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr>
                <th>Name</th><th>Items</th><th>Status</th><th>Order</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($categories as $cat)
            <tr>
                <td>
                    @if($cat->image)<img src="{{ Storage::url($cat->image) }}" class="rounded me-2" style="width:32px;height:32px;object-fit:cover">@endif
                    <span class="fw-medium">{{ $cat->name }}</span>
                </td>
                <td>{{ $cat->menu_items_count }}</td>
                <td>
                    <span class="badge {{ $cat->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $cat->is_active ? 'Active' : 'Hidden' }}
                    </span>
                </td>
                <td>{{ $cat->sort_order }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                              onsubmit="return confirm('Delete category?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-5">No categories yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
