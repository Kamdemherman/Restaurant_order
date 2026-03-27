{{-- FILE: resources/views/admin/categories/create.blade.php --}}
@extends('layouts.admin')
@section('title','New Category')
@section('content')
<div style="max-width:600px">
    <h5 class="fw-bold mb-4">New Category</h5>
    <div class="card p-4">
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-medium">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order',0) }}" min="0">
                </div>
                <div class="col-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm px-4 fw-medium" style="background:#e94560;color:#fff">Save Category</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
