{{-- FILE: resources/views/user/menu/index.blade.php --}}
@extends('layouts.app')
@section('title','Menu')
@section('content')
<div class="container py-4">
    <div class="row mb-3">
        <div class="col">
            <h3 style="font-family:'Playfair Display',serif" class="fw-bold">Our Menu</h3>
        </div>
        <div class="col-auto">
            <a href="{{ route('orders.checkout') }}" class="btn btn-accent position-relative">
                <i class="bi bi-cart3 me-1"></i> Cart
                <span class="badge bg-light text-dark ms-1" id="cartCountBtn">0</span>
            </a>
        </div>
    </div>

    {{-- Category tabs --}}
    <ul class="nav nav-pills mb-4 flex-nowrap overflow-auto pb-2" id="catTabs">
        <li class="nav-item me-1">
            <a class="nav-link active" href="#" data-cat="all">All</a>
        </li>
        @foreach($categories as $cat)
        <li class="nav-item me-1">
            <a class="nav-link" href="#" data-cat="{{ $cat->id }}">{{ $cat->name }}</a>
        </li>
        @endforeach
    </ul>

    @foreach($categories as $cat)
    <div class="cat-section mb-5" data-cat-id="{{ $cat->id }}">
        <h5 class="fw-bold text-uppercase mb-3" style="letter-spacing:.05em;color:#555;font-size:.9rem;border-left:4px solid #e94560;padding-left:.7rem">
            {{ $cat->name }}
        </h5>
        <div class="row g-3">
            @foreach($cat->activeMenuItems as $item)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card menu-card h-100 cursor-pointer" onclick="openItemModal({{ $item->id }})">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" class="card-img-top"
                             style="height:160px;object-fit:cover;border-radius:12px 12px 0 0" alt="{{ $item->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center text-muted"
                             style="height:160px;background:#f0ede8;border-radius:12px 12px 0 0;font-size:2.5rem">🍴</div>
                    @endif
                    <div class="card-body p-3">
                        <h6 class="fw-semibold mb-1">{{ $item->name }}</h6>
                        @if($item->description)
                            <p class="text-muted small mb-2" style="line-height:1.3">{{ Str::limit($item->description,60) }}</p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold" style="color:#e94560">€{{ number_format($item->price,2) }}</span>
                            <button class="btn btn-sm btn-accent rounded-pill px-3"
                                    onclick="event.stopPropagation();quickAdd({{ $item->id }},'{{ addslashes($item->name) }}',{{ $item->price }})">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- Item detail modal --}}
<div class="modal fade" id="itemModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" id="itemModalContent">
        <div class="modal-body text-center py-5">
            <div class="spinner-border text-secondary"></div>
        </div>
    </div>
  </div>
</div>

{{-- Floating cart button (mobile) --}}
<div class="d-md-none position-fixed bottom-0 end-0 m-3" id="floatCart" style="display:none!important">
    <a href="{{ route('orders.checkout') }}" class="btn btn-accent btn-lg rounded-pill shadow-lg">
        <i class="bi bi-cart3"></i> View Cart (<span id="floatCartCount">0</span>)
    </a>
</div>

@php
$itemsJson = $categories->flatMap->activeMenuItems->map(fn($i) => [
    'id'     => $i->id,
    'name'   => $i->name,
    'price'  => (float)$i->price,
    'image'  => $i->image ? Storage::url($i->image) : null,
    'desc'   => $i->description,
    'addons' => $i->availableAddons->map(fn($a) => ['id'=>$a->id,'name'=>$a->name,'price'=>(float)$a->price]),
])->keyBy('id');
@endphp

@push('scripts')
<script>
const ITEMS = @json($itemsJson);
let cart = JSON.parse(localStorage.getItem('cart') || '[]');

function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
    const count = cart.reduce((s,i) => s + i.quantity, 0);
    document.querySelectorAll('#cartCount,#cartCountBtn,#floatCartCount').forEach(el => el && (el.textContent = count));
    const badge = document.getElementById('cartCount');
    if (badge) badge.style.display = count > 0 ? 'inline' : 'none';
    document.getElementById('floatCart').style.display = count > 0 ? 'block' : 'none';
}

function quickAdd(id, name, price) {
    const idx = cart.findIndex(i => i.id === id && !i.addons?.length);
    if (idx > -1) { cart[idx].quantity++; }
    else { cart.push({id, name, price, quantity:1, addons:[]}); }
    saveCart();
    // Feedback pulse
    event.target.classList.add('btn-success');
    setTimeout(() => event.target.classList.remove('btn-success'), 500);
}

function openItemModal(id) {
    const item = ITEMS[id];
    if (!item) return;
    const modal = new bootstrap.Modal(document.getElementById('itemModal'));
    let addonsHtml = '';
    if (item.addons && item.addons.length) {
        addonsHtml = '<div class="mt-3"><p class="fw-semibold mb-2">Add-ons:</p>';
        item.addons.forEach(a => {
            addonsHtml += `<div class="form-check">
                <input class="form-check-input addon-check" type="checkbox" data-addon-id="${a.id}"
                       data-addon-name="${a.name}" data-addon-price="${a.price}" id="addon_${a.id}">
                <label class="form-check-label" for="addon_${a.id}">${a.name}
                    ${a.price > 0 ? `<span class="text-muted ms-1">+€${a.price.toFixed(2)}</span>` : ''}
                </label>
            </div>`;
        });
        addonsHtml += '</div>';
    }
    document.getElementById('itemModalContent').innerHTML = `
        ${item.image ? `<img src="${item.image}" class="w-100" style="max-height:220px;object-fit:cover;border-radius:12px 12px 0 0">` : ''}
        <div class="modal-body">
            <h5 class="fw-bold">${item.name}</h5>
            ${item.desc ? `<p class="text-muted small">${item.desc}</p>` : ''}
            <p class="fw-bold fs-5" style="color:#e94560">€${item.price.toFixed(2)}</p>
            ${addonsHtml}
            <div class="d-flex align-items-center gap-3 mt-3">
                <div class="input-group" style="width:120px">
                    <button class="btn btn-outline-secondary" onclick="changeQty(-1)">−</button>
                    <input type="number" class="form-control text-center" id="modalQty" value="1" min="1" max="20">
                    <button class="btn btn-outline-secondary" onclick="changeQty(1)">+</button>
                </div>
                <button class="btn btn-accent flex-grow-1" onclick="addToCartFromModal(${id}, '${item.name.replace(/'/g,"\\'")}', ${item.price})">
                    <i class="bi bi-cart-plus me-1"></i> Add to Cart
                </button>
            </div>
            <div class="mt-2">
                <textarea class="form-control form-control-sm" id="itemNote" placeholder="Special instructions..." rows="2"></textarea>
            </div>
        </div>
    `;
    modal.show();
}

function changeQty(delta) {
    const input = document.getElementById('modalQty');
    input.value = Math.max(1, Math.min(20, parseInt(input.value) + delta));
}

function addToCartFromModal(id, name, basePrice) {
    const qty = parseInt(document.getElementById('modalQty').value) || 1;
    const note = document.getElementById('itemNote')?.value || '';
    const selectedAddons = [];
    let price = basePrice;
    document.querySelectorAll('.addon-check:checked').forEach(cb => {
        const addonPrice = parseFloat(cb.dataset.addonPrice);
        price += addonPrice;
        selectedAddons.push({id: parseInt(cb.dataset.addonId), name: cb.dataset.addonName, price: addonPrice});
    });

    const key = id + '_' + selectedAddons.map(a=>a.id).join('_');
    const idx  = cart.findIndex(i => i._key === key);
    if (idx > -1) { cart[idx].quantity += qty; }
    else { cart.push({_key: key, id, name, price, quantity: qty, addons: selectedAddons, notes: note}); }
    saveCart();
    bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
}

// Category filter tabs
document.querySelectorAll('#catTabs .nav-link').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('#catTabs .nav-link').forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        const cat = link.dataset.cat;
        document.querySelectorAll('.cat-section').forEach(sec => {
            sec.style.display = (cat === 'all' || sec.dataset.catId === cat) ? '' : 'none';
        });
    });
});

saveCart();
</script>
@endpush
@endsection
