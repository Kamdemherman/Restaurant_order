{{-- FILE: resources/views/admin/categories/edit.blade.php --}}
@extends('layouts.admin')
@section('title','Edit: '.$category->name)
@section('content')
<div style="max-width:600px">
    <h5 class="fw-bold mb-4">Edit Category</h5>
    <div class="card p-4">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Image</label>
                @if($category->image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($category->image) }}" class="rounded" style="height:80px;object-fit:cover">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-medium">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="{{ old('sort_order', $category->sort_order) }}" min="0">
                </div>
                <div class="col-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ $category->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn px-4 fw-medium" style="background:#e94560;color:#fff">Update Category</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
