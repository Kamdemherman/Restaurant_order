{{-- FILE: resources/views/user/orders/checkout.blade.php --}}
@extends('layouts.app')
@section('title','Checkout')
@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4" style="font-family:'Playfair Display',serif">
        <a href="{{ route('menu.index') }}" class="text-decoration-none text-dark me-2"><i class="bi bi-arrow-left"></i></a>
        Checkout
    </h3>

    <div class="row g-4">
        {{-- Cart summary --}}
        <div class="col-lg-7">
            <div class="card p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cart3 me-2 text-danger"></i>Your Order</h6>
                <div id="cartItems"><p class="text-muted">Loading cart…</p></div>
                <hr>
                <div class="d-flex justify-content-between fw-semibold fs-5">
                    <span>Total</span>
                    <span id="cartTotal" style="color:#e94560">€0.00</span>
                </div>
            </div>

            {{-- Coupon --}}
            <div class="card p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-ticket-perforated me-2 text-warning"></i>Discount Coupon</h6>
                <div class="input-group">
                    <input type="text" id="couponInput" class="form-control text-uppercase" placeholder="Enter coupon code">
                    <button class="btn btn-outline-secondary" onclick="applyCoupon()">Apply</button>
                </div>
                <div id="couponMsg" class="small mt-2"></div>
            </div>
        </div>

        {{-- Delivery details --}}
        <div class="col-lg-5">
            <div class="card p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt me-2 text-danger"></i>Delivery Details</h6>

                <form id="checkoutForm" method="POST" action="{{ route('orders.store') }}">
                    @csrf
                    <div id="cartInputs"></div>
                    <input type="hidden" name="coupon_code" id="couponCodeInput">
                    <input type="hidden" name="delivery_lat" id="deliveryLat">
                    <input type="hidden" name="delivery_lng" id="deliveryLng">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Delivery Address <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-12">
                                <input type="text" id="streetInput" class="form-control" required placeholder="Street address (e.g., 123 Main Street)">
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="cityInput" class="form-control" required placeholder="City">
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="postalInput" class="form-control" required placeholder="Postal code">
                            </div>
                        </div>
                        <input type="hidden" name="delivery_address" id="deliveryAddressHidden">
                    </div>

                    <div id="map" style="height:220px;border-radius:10px;overflow:hidden;border:1px solid #dee2e6" class="mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Order Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Allergies, special requests…"></textarea>
                    </div>

                    <div class="card bg-light p-3 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-cash-coin fs-4 text-success"></i>
                            <div>
                                <p class="mb-0 fw-semibold">Cash on Delivery</p>
                                <small class="text-muted">Pay when your order arrives</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded" style="background:#fff3cd">
                        <span class="fw-semibold">Total to pay:</span>
                        <span class="fw-bold fs-4" style="color:#e94560" id="finalTotal">€0.00</span>
                    </div>

                    <button type="submit" class="btn btn-accent w-100 py-3 fw-bold fs-5" id="placeOrderBtn" disabled>
                        <i class="bi bi-bag-check me-2"></i>Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart        = JSON.parse(localStorage.getItem('cart') || '[]');
let discount    = 0;
let subtotal    = 0;

function renderCart() {
    if (!cart.length) {
        document.getElementById('cartItems').innerHTML =
            `<div class="text-center py-4 text-muted">
                <i class="bi bi-cart-x fs-1"></i><p>Your cart is empty</p>
                <a href="{{ route('menu.index') }}" class="btn btn-accent btn-sm">Browse Menu</a>
            </div>`;
        document.getElementById('placeOrderBtn').disabled = true;
        return;
    }

    let html = '';
    subtotal = 0;
    cart.forEach((item, idx) => {
        const lineTot = item.price * item.quantity;
        subtotal += lineTot;
        html += `<div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
            <div class="flex-grow-1">
                <p class="mb-0 fw-semibold">${item.name}</p>
                ${item.addons?.length ? `<small class="text-muted">${item.addons.map(a=>a.name).join(', ')}</small>` : ''}
                ${item.notes ? `<small class="text-muted d-block">Note: ${item.notes}</small>` : ''}
                <small class="text-muted">€${item.price.toFixed(2)} each</small>
            </div>
            <div class="input-group input-group-sm" style="width:110px">
                <button class="btn btn-outline-secondary" onclick="updateQty(${idx},-1)">−</button>
                <span class="form-control text-center">${item.quantity}</span>
                <button class="btn btn-outline-secondary" onclick="updateQty(${idx},1)">+</button>
            </div>
            <span class="fw-bold" style="min-width:60px;text-align:right">€${lineTot.toFixed(2)}</span>
            <button class="btn btn-sm text-danger p-0 ms-1" onclick="removeItem(${idx})"><i class="bi bi-x-lg"></i></button>
        </div>`;
    });

    document.getElementById('cartItems').innerHTML = html;
    updateTotals();
    buildHiddenInputs();
    document.getElementById('placeOrderBtn').disabled = false;
}

function updateQty(idx, delta) {
    cart[idx].quantity = Math.max(1, cart[idx].quantity + delta);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
}

function removeItem(idx) {
    cart.splice(idx, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
}

function updateTotals() {
    const total = Math.max(0, subtotal - discount);
    document.getElementById('cartTotal').textContent  = '€' + subtotal.toFixed(2);
    document.getElementById('finalTotal').textContent = '€' + total.toFixed(2);
}

function buildHiddenInputs() {
    let html = '';
    cart.forEach((item, i) => {
        html += `<input type="hidden" name="items[${i}][id]" value="${item.id}">
                 <input type="hidden" name="items[${i}][quantity]" value="${item.quantity}">
                 <input type="hidden" name="items[${i}][notes]" value="${item.notes || ''}">`;
        item.addons?.forEach((a, j) => {
            html += `<input type="hidden" name="items[${i}][addons][${j}]" value="${a.id}">`;
        });
    });
    document.getElementById('cartInputs').innerHTML = html;
}

async function applyCoupon() {
    const code = document.getElementById('couponInput').value.trim();
    if (!code) return;
    try {
        const res  = await fetch('{{ route("coupon.apply") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({code, amount: subtotal}),
        });
        const data = await res.json();
        const msg  = document.getElementById('couponMsg');
        if (data.valid) {
            discount = data.discount;
            document.getElementById('couponCodeInput').value = code;
            msg.innerHTML = `<span class="text-success"><i class="bi bi-check-circle me-1"></i>${data.message} − €${discount.toFixed(2)}</span>`;
        } else {
            discount = 0;
            document.getElementById('couponCodeInput').value = '';
            msg.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle me-1"></i>${data.message}</span>`;
        }
        updateTotals();
    } catch(e) { console.error(e); }
}

// Google Maps address autocomplete
function initMap() {
    const defaultPos = { lat: 53.3498, lng: -6.2603 }; // Dublin default
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14, center: defaultPos,
        mapTypeControl: false, fullscreenControl: false,
    });
    const marker = new google.maps.Marker({ map, draggable: true, position: defaultPos });
    const input  = document.getElementById('streetInput');
    const autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (!place.geometry) return;
        map.setCenter(place.geometry.location);
        marker.setPosition(place.geometry.location);
        document.getElementById('deliveryLat').value = place.geometry.location.lat();
        document.getElementById('deliveryLng').value = place.geometry.location.lng();
        // Try to fill city and postal from place
        let city = '', postal = '';
        place.address_components.forEach(component => {
            if (component.types.includes('locality')) city = component.long_name;
            if (component.types.includes('postal_code')) postal = component.long_name;
        });
        document.getElementById('cityInput').value = city;
        document.getElementById('postalInput').value = postal;
        updateDeliveryAddress();
    });

    marker.addListener('dragend', () => {
        document.getElementById('deliveryLat').value = marker.getPosition().lat();
        document.getElementById('deliveryLng').value = marker.getPosition().lng();
    });

    // Try geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const ll = { lat: pos.coords.latitude, lng: pos.coords.longitude };
            map.setCenter(ll);
            marker.setPosition(ll);
            document.getElementById('deliveryLat').value = ll.lat;
            document.getElementById('deliveryLng').value = ll.lng;
        });
    }
}

function updateDeliveryAddress() {
    const street = document.getElementById('streetInput').value.trim();
    const city = document.getElementById('cityInput').value.trim();
    const postal = document.getElementById('postalInput').value.trim();
    const fullAddress = [street, city, postal].filter(Boolean).join(', ');
    document.getElementById('deliveryAddressHidden').value = fullAddress;
}

// Prevent double-submit
document.getElementById('checkoutForm').addEventListener('submit', function() {
    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Placing Order…';
    // Clear cart after submit
    localStorage.removeItem('cart');
});

// Update delivery address on input change
document.getElementById('streetInput').addEventListener('input', updateDeliveryAddress);
document.getElementById('cityInput').addEventListener('input', updateDeliveryAddress);
document.getElementById('postalInput').addEventListener('input', updateDeliveryAddress);

// Pre-fill address if available
if ('{{ $user->address }}') {
    document.getElementById('streetInput').value = '{{ $user->address }}';
    updateDeliveryAddress();
}

renderCart();
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places&callback=initMap" async defer></script>
@endpush
@endsection
