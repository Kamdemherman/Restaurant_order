{{-- FILE: resources/views/admin/coupons/create.blade.php --}}
@extends('layouts.admin')
@section('title','New Coupon')
@section('content')
<div style="max-width:550px">
    <h5 class="fw-bold mb-4">Create Coupon</h5>
    <div class="card p-4">
        <form method="POST" action="{{ route('admin.coupons.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Coupon Code <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" name="code" id="couponCode" class="form-control text-uppercase"
                           value="{{ old('code', strtoupper(Str::random(8))) }}" required maxlength="50"
                           style="letter-spacing:.1em;font-weight:600">
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="document.getElementById('couponCode').value = Math.random().toString(36).substring(2,10).toUpperCase()">
                        ↻ Generate
                    </button>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-medium">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount (€)</option>
                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label fw-medium">Value <span class="text-danger">*</span></label>
                    <input type="number" name="value" class="form-control" step="0.01" min="0.01"
                           value="{{ old('value') }}" required placeholder="e.g. 10">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-medium">Minimum Order (€)</label>
                    <input type="number" name="min_order_amount" class="form-control" step="0.01" min="0"
                           value="{{ old('min_order_amount', 0) }}">
                </div>
                <div class="col-6">
                    <label class="form-label fw-medium">Max Discount (€)</label>
                    <input type="number" name="max_discount_amount" class="form-control" step="0.01" min="0"
                           value="{{ old('max_discount_amount') }}" placeholder="Leave blank = no limit">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Expiry Date</label>
                <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
            </div>

            <div class="mb-3 form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_single_use" id="single_use" value="1"
                       {{ old('is_single_use', 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="single_use">
                    Single-use (one-time discount coupon)
                </label>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium">Internal Notes</label>
                <textarea name="notes" class="form-control" rows="2"
                          placeholder="e.g. Welcome coupon for user X">{{ old('notes') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn px-4 fw-medium" style="background:#e94560;color:#fff">
                    Create Coupon
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
