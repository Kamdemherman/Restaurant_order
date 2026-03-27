{{-- FILE: resources/views/admin/menus/create.blade.php (also used for edit) --}}
@extends('layouts.admin')
@section('title', isset($menu) ? 'Edit: '.$menu->name : 'New Menu Item')
@section('content')
<div style="max-width:750px">
    <h5 class="fw-bold mb-4">{{ isset($menu) ? 'Edit Menu Item' : 'New Menu Item' }}</h5>
    <div class="card p-4">
        <form method="POST"
              action="{{ isset($menu) ? route('admin.menus.update',$menu) : route('admin.menus.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if(isset($menu)) @method('PUT') @endif

            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-medium">Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $menu->name ?? '') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (old('category_id', $menu->category_id ?? '') == $cat->id) ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $menu->description ?? '') }}</textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Price (€) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" step="0.01" min="0"
                           value="{{ old('price', $menu->price ?? '') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Prep Time (mins)</label>
                    <input type="number" name="preparation_time" class="form-control" min="1"
                           value="{{ old('preparation_time', $menu->preparation_time ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" min="0"
                           value="{{ old('sort_order', $menu->sort_order ?? 0) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Image</label>
                @if(isset($menu) && $menu->image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($menu->image) }}" class="rounded" style="height:80px;object-fit:cover">
                        <small class="text-muted ms-2">Current image</small>
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Allergens</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['Gluten','Dairy','Eggs','Nuts','Soy','Fish','Shellfish','Sesame'] as $allergen)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allergens[]"
                               value="{{ $allergen }}" id="al_{{ $allergen }}"
                               {{ in_array($allergen, old('allergens', $menu->allergens ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="al_{{ $allergen }}">{{ $allergen }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-auto">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1"
                               {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_available">Available to order</label>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                               {{ old('is_featured', $menu->is_featured ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">Featured item</label>
                    </div>
                </div>
            </div>

            {{-- Add-ons --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-medium mb-0">Add-ons / Extras</label>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addAddonRow()">
                        <i class="bi bi-plus"></i> Add Row
                    </button>
                </div>
                <div id="addonsContainer">
                    @if(isset($menu) && $menu->addons->count())
                        @foreach($menu->addons as $addon)
                        <div class="row g-2 mb-2 addon-row">
                            <div class="col-7">
                                <input type="text" name="addons[][name]" class="form-control form-control-sm"
                                       placeholder="e.g. Extra cheese" value="{{ $addon->name }}">
                            </div>
                            <div class="col-3">
                                <input type="number" name="addons[][price]" class="form-control form-control-sm"
                                       placeholder="€0.00" step="0.01" min="0" value="{{ $addon->price }}">
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.addon-row').remove()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn px-4 fw-medium" style="background:#e94560;color:#fff">
                    {{ isset($menu) ? 'Update Item' : 'Create Item' }}
                </button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function addAddonRow() {
    document.getElementById('addonsContainer').insertAdjacentHTML('beforeend', `
        <div class="row g-2 mb-2 addon-row">
            <div class="col-7"><input type="text" name="addons[][name]" class="form-control form-control-sm" placeholder="e.g. Extra cheese"></div>
            <div class="col-3"><input type="number" name="addons[][price]" class="form-control form-control-sm" placeholder="€0.00" step="0.01" min="0" value="0"></div>
            <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.addon-row').remove()"><i class="bi bi-trash"></i></button></div>
        </div>`);
}
</script>
@endpush
@endsection
