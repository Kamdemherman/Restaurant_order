<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin – @yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --sidebar-w: 240px; --brand: #1a1a2e; --accent: #e94560; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }
        .sidebar { width: var(--sidebar-w); min-height: 100vh; background: var(--brand);
                   position: fixed; top: 0; left: 0; z-index: 100; overflow-y: auto; }
        .sidebar-brand { padding: 1.2rem 1rem; font-size: 1.1rem; font-weight: 700;
                         color: #fff; border-bottom: 1px solid rgba(255,255,255,.1); }
        .sidebar-brand span { color: var(--accent); }
        .sidebar .nav-link { color: rgba(255,255,255,.7); padding: .55rem 1rem;
                             border-radius: 6px; margin: 1px 8px; font-size: .875rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active
                             { background: var(--accent); color: #fff; }
        .sidebar .nav-link i { width: 18px; margin-right: 6px; }
        .sidebar-section { padding: .4rem 1rem .1rem; font-size: .7rem;
                           color: rgba(255,255,255,.35); text-transform: uppercase; letter-spacing: .08em; }
        .main-content { margin-left: var(--sidebar-w); }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0;
                  padding: .6rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); border-radius: 10px; }
        .stat-card { border-left: 4px solid var(--accent); }
        .table th { font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
        .badge-pending   { background:#ffc107; color:#000; }
        .badge-confirmed { background:#0dcaf0; color:#000; }
        .badge-preparing { background:#0d6efd; }
        .badge-ready     { background:#198754; }
        .badge-delivered { background:#6c757d; }
        .badge-cancelled { background:#dc3545; }
        @media(max-width:768px){
            .sidebar { transform: translateX(-100%); transition: .3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">🍽 <span>{{ config('app.name') }}</span><br>
        <small style="font-size:.7rem;color:rgba(255,255,255,.4);font-weight:400">Admin Panel</small>
    </div>
    <ul class="nav flex-column pt-2">
        <li><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>

        <div class="sidebar-section">Orders</div>
        <li><a class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"
               href="{{ route('admin.orders.index') }}"><i class="bi bi-bag-check"></i> All Orders
               @php $pending = \App\Models\Order::where('status','pending')->count(); @endphp
               @if($pending)<span class="badge bg-danger float-end">{{ $pending }}</span>@endif
           </a></li>

        <div class="sidebar-section">Menu</div>
        <li><a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}"
               href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i> Categories</a></li>
        <li><a class="nav-link {{ request()->routeIs('admin.menus*') ? 'active' : '' }}"
               href="{{ route('admin.menus.index') }}"><i class="bi bi-menu-button-wide"></i> Menu Items</a></li>

        <div class="sidebar-section">Customers</div>
        <li><a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Customers
               @php $pendingUsers = \App\Models\User::where('status','pending')->count(); @endphp
               @if($pendingUsers)<span class="badge bg-warning text-dark float-end">{{ $pendingUsers }}</span>@endif
           </a></li>

        <div class="sidebar-section">Marketing</div>
        <li><a class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}"
               href="{{ route('admin.coupons.index') }}"><i class="bi bi-ticket-perforated"></i> Coupons</a></li>
        <li><a class="nav-link {{ request()->routeIs('admin.newsletter*') ? 'active' : '' }}"
               href="{{ route('admin.newsletter.index') }}"><i class="bi bi-envelope-at"></i> Newsletter</a></li>

        <div class="sidebar-section">Reports</div>
        <li><a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}"
               href="{{ route('admin.reports.sales') }}"><i class="bi bi-bar-chart-line"></i> Sales Report</a></li>

        <div class="sidebar-section">Settings</div>
        <li><a class="nav-link {{ request()->routeIs('admin.printer*') ? 'active' : '' }}"
               href="{{ route('admin.printer.index') }}"><i class="bi bi-printer"></i> Printer</a></li>
    </ul>

    <div class="p-3 mt-auto" style="border-top:1px solid rgba(255,255,255,.1);margin-top:auto">
        <div class="text-white-50 small mb-2">{{ auth()->user()->name }}</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-outline-light w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</nav>

{{-- Main content --}}
<div class="main-content">
    <div class="topbar">
        <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list fs-5"></i>
        </button>
        <h6 class="mb-0 fw-semibold">@yield('title', 'Dashboard')</h6>
        <small class="text-muted">{{ now()->format('D, d M Y') }}</small>
    </div>

    <div class="p-3 p-md-4">
        {{-- Flash --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-2">
                @foreach($errors->all() as $e)<div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
