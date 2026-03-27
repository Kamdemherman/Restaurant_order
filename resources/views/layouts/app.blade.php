<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #1a1a2e;
            --brand-accent:  #e94560;
            --brand-gold:    #f5a623;
            --brand-light:   #f8f9fa;
        }
        body { font-family: 'Inter', sans-serif; background: #f5f5f0; }
        .navbar-brand { font-family: 'Playfair Display', serif; font-size: 1.4rem; }
        .navbar { background: var(--brand-primary) !important; }
        .btn-accent  { background: var(--brand-accent); color: #fff; border: none; }
        .btn-accent:hover { background: #c73652; color: #fff; }
        .badge-pending   { background: #ffc107; color:#000; }
        .badge-confirmed { background: #0dcaf0; }
        .badge-preparing { background: #0d6efd; }
        .badge-ready     { background: #198754; }
        .badge-delivered { background: #6c757d; }
        .badge-cancelled { background: #dc3545; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,.07); border-radius: 12px; }
        .sidebar { min-height: calc(100vh - 56px); background: var(--brand-primary); }
        .sidebar .nav-link { color: rgba(255,255,255,.75); padding: .6rem 1.2rem; border-radius: 8px; margin: 2px 8px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: var(--brand-accent); color: #fff; }
        .sidebar .nav-link i { width: 20px; }
        .menu-card:hover { transform: translateY(-3px); transition: .2s; box-shadow: 0 6px 20px rgba(0,0,0,.12); }
        .gdpr-banner { position: fixed; bottom: 0; left: 0; right: 0; z-index: 9999;
                       background: rgba(26,26,46,.97); color: #fff; padding: 1rem; }
        @media (max-width: 768px) {
            .sidebar { min-height: auto; }
            .table-responsive { font-size: .85rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('menu.index') }}">
            🍽 {{ config('app.name') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Admin Panel
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('menu.index') }}">
                                <i class="bi bi-grid"></i> Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('orders.index') }}">
                                <i class="bi bi-bag"></i> My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('orders.checkout') }}" id="cartLink">
                                <i class="bi bi-cart3"></i>
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="cartCount" style="display:none">0</span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-light ms-1">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-0 rounded-0 py-2" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show m-0 rounded-0 py-2" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Main Content --}}
@yield('content')

{{-- GDPR Cookie Banner --}}
@guest
@else
@endguest
<div id="gdprBanner" class="gdpr-banner" style="display:none;">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <p class="mb-0 small">
            🍪 We use essential session cookies only. No tracking, no advertising.
            <a href="#" class="text-warning">Privacy Policy</a>
        </p>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-warning" onclick="acceptGdpr()">Accept</button>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// GDPR banner
function acceptGdpr() {
    localStorage.setItem('gdpr_accepted', '1');
    document.getElementById('gdprBanner').style.display = 'none';
}
if (!localStorage.getItem('gdpr_accepted')) {
    document.getElementById('gdprBanner').style.display = 'block';
}

// Cart badge update
function updateCartBadge() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const count = cart.reduce((s, i) => s + i.quantity, 0);
    const badge = document.getElementById('cartCount');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    }
}
updateCartBadge();
</script>

@stack('scripts')
</body>
</html>
