{{-- FILE: resources/views/user/profile/edit.blade.php --}}
@extends('layouts.app')
@section('title','My Profile')
@section('content')
<div class="container py-4" style="max-width:700px">
    <h3 class="fw-bold mb-4" style="font-family:'Playfair Display',serif">My Profile</h3>

    {{-- Profile details --}}
    <div class="card p-4 mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-person me-2 text-primary"></i>Personal Information</h6>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Phone</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Default Delivery Address</label>
                    <input type="text" name="address" id="profileAddress" class="form-control"
                           value="{{ old('address', $user->address) }}">
                    <input type="hidden" name="lat" id="profileLat" value="{{ $user->lat }}">
                    <input type="hidden" name="lng" id="profileLng" value="{{ $user->lng }}">
                </div>
                <div class="col-12">
                    <div id="profileMap" style="height:200px;border-radius:10px;border:1px solid #dee2e6"></div>
                </div>
            </div>
            <button class="btn btn-accent mt-3 px-4">Save Changes</button>
        </form>
    </div>

    {{-- Password --}}
    <div class="card p-4 mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-lock me-2 text-warning"></i>Change Password</h6>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">New Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <button class="btn btn-warning mt-3 px-4">Update Password</button>
        </form>
    </div>

    {{-- Newsletter --}}
    <div class="card p-4 mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-envelope-at me-2 text-info"></i>Newsletter</h6>
        <form method="POST" action="{{ route('profile.newsletter') }}">
            @csrf @method('PUT')
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="newsletter_subscribed" id="newsletterToggle"
                       value="1" {{ $user->newsletter_subscribed ? 'checked' : '' }}>
                <label class="form-check-label" for="newsletterToggle">
                    Receive newsletters, offers and promotions
                </label>
            </div>
            <button class="btn btn-info text-white mt-3 px-4">Update Preference</button>
        </form>
    </div>

    {{-- GDPR --}}
    <div class="card p-4 border-warning">
        <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2 text-warning"></i>Your Data Rights (GDPR)</h6>
        <p class="small text-muted mb-3">Under GDPR you have the right to access and delete your personal data.</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('profile.gdpr.export') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-1"></i> Export My Data (JSON)
            </a>
            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash me-1"></i> Delete My Account
            </button>
        </div>
        <p class="small text-muted mt-2 mb-0">
            GDPR consent given on {{ $user->gdpr_consent_at?->format('d M Y') ?? 'N/A' }}
        </p>
    </div>
</div>

{{-- Delete account modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>This will permanently anonymise your personal data. Your order history will be retained for accounting purposes but unlinked from your identity.</p>
                <p class="fw-bold">Type <code>DELETE</code> to confirm:</p>
                <form method="POST" action="{{ route('profile.gdpr.delete') }}">
                    @csrf @method('DELETE')
                    <input type="text" name="confirmation" class="form-control mb-3" placeholder="DELETE">
                    <button type="submit" class="btn btn-danger w-100">Permanently Delete My Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function initProfileMap() {
    const lat = parseFloat(document.getElementById('profileLat').value) || 53.3498;
    const lng = parseFloat(document.getElementById('profileLng').value) || -6.2603;
    const pos = {lat, lng};
    const map = new google.maps.Map(document.getElementById('profileMap'), {
        zoom:14, center:pos, mapTypeControl:false, fullscreenControl:false
    });
    const marker = new google.maps.Marker({map, draggable:true, position:pos});
    const auto   = new google.maps.places.Autocomplete(document.getElementById('profileAddress'));
    auto.addListener('place_changed', () => {
        const p = auto.getPlace();
        if (!p.geometry) return;
        map.setCenter(p.geometry.location);
        marker.setPosition(p.geometry.location);
        document.getElementById('profileLat').value = p.geometry.location.lat();
        document.getElementById('profileLng').value = p.geometry.location.lng();
    });
    marker.addListener('dragend', () => {
        document.getElementById('profileLat').value = marker.getPosition().lat();
        document.getElementById('profileLng').value = marker.getPosition().lng();
    });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places&callback=initProfileMap" async defer></script>
@endpush
@endsection
