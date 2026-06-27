<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — CRO Performance Management</title>
    <meta name="description" content="TIMS & FOCUZ CRO Performance Management System">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/air-datepicker@3.6.0/air-datepicker.css">

    {{-- Custom PMS Styles --}}
    <link href="{{ asset('css/pms.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="pms-body">

{{-- Sidebar --}}
<aside class="pms-sidebar" id="pmsSidebar">
    <div class="pms-sidebar-brand">
        <div class="brand-icon">
            <i class="fa-solid fa-cube"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name">CRO PMS</span>
            <span class="brand-sub">Performance Management</span>
        </div>
    </div>

    <nav class="pms-nav">
        <div class="nav-section-label">Main</div>

        <a href="{{ route('dashboard') }}" class="pms-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high nav-icon"></i>
            <span>Dashboard</span>
        </a>

        <!-- <a href="{{ route('daily_audit.create') }}" class="pms-nav-link {{ request()->routeIs('daily_audit.create') ? 'active' : '' }}">
            <i class="fa-solid fa-circle-plus nav-icon"></i>
            <span>Enter Daily Audit</span>
        </a> -->

        <a href="{{ route('daily_audit.index') }}" class="pms-nav-link {{ request()->routeIs('daily_audit.index') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-list nav-icon"></i>
            <span>Daily Audit</span>
        </a>

        <div class="nav-section-label mt-3">Analytics</div>

        <a href="{{ route('leaderboards.index') }}" class="pms-nav-link {{ request()->routeIs('leaderboards.*') ? 'active' : '' }}">
            <i class="fa-solid fa-trophy nav-icon"></i>
            <span>Leaderboard</span>
        </a>

        <a href="{{ route('point_history.index') }}" class="pms-nav-link {{ request()->routeIs('point_history.*') ? 'active' : '' }}">
            <i class="fa-solid fa-coins nav-icon"></i>
            <span>Point History</span>
        </a>

        <a href="{{ route('reports.index') }}" class="pms-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-lines nav-icon"></i>
            <span>Reports</span>
        </a>

        <div class="nav-section-label mt-3">Management</div>

        <a href="{{ route('executives.index') }}" class="pms-nav-link {{ request()->routeIs('executives.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users nav-icon"></i>
            <span>Executives</span>
        </a>

        <a href="{{ route('zones.index') }}" class="pms-nav-link {{ request()->routeIs('zones.*') ? 'active' : '' }}">
            <i class="fa-solid fa-map-location-dot nav-icon"></i>
            <span>Zones</span>
        </a>

        <a href="{{ route('companies.index') }}" class="pms-nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
            <i class="fa-solid fa-building nav-icon"></i>
            <span>Companies</span>
        </a>

        @can('manage_users')
        <a href="{{ route('users.index') }}" class="pms-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear nav-icon"></i>
            <span>Users</span>
        </a>
        @endcan
    </nav>

    <div class="pms-sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-role">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</span>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>

{{-- Main Content --}}
<div class="pms-main" id="pmsMain">

    {{-- Top Bar --}}
    <header class="pms-topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <nav aria-label="breadcrumb" class="topbar-breadcrumb">
                @yield('breadcrumb')
            </nav>
        </div>
        <div class="topbar-right">
            <div class="topbar-date">
                <i class="fa-regular fa-calendar-days me-1"></i>
                <span>{{ now()->format('D, d M Y') }}</span>
            </div>
            <div class="topbar-divider"></div>
            
        </div>
    </header>

    {{-- Flash Messages --}}
    <div class="pms-alerts-container">
        @if(session('success'))
            <div class="alert pms-alert pms-alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert pms-alert pms-alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert pms-alert pms-alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <strong>Validation errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Page Content --}}
    <main class="pms-content">
        @yield('content')
    </main>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.6.0/air-datepicker.js"></script>

<script>
const CSRF = '{{ csrf_token() }}';

// Sidebar toggle
document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('pmsSidebar').classList.toggle('collapsed');
    document.getElementById('pmsMain').classList.toggle('expanded');
});

// Auto-dismiss alerts
setTimeout(() => {
    document.querySelectorAll('.pms-alert').forEach(el => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
        if (bsAlert) bsAlert.close();
    });
}, 5000);

// Delete confirmations
document.querySelectorAll('[data-confirm-delete]').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.getElementById(this.dataset.formId);
        const name = this.dataset.confirmDelete;
        Swal.fire({
            title: 'Confirm Delete',
            html: `Are you sure you want to delete <strong>${name}</strong>? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef4444',
            background: '#1e2130',
            color: '#e2e8f0',
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>

@stack('scripts')
</body>
</html>
