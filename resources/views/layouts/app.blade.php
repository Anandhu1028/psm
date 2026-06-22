@php
    $activeUniversity = null;
    if (session()->has('active_university_id')) {
        $activeUniversity = \App\Models\University::find(session('active_university_id'));
    }
    $allUniversities = \App\Models\University::all();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TIMS CRO PMS') - TIMS CRO Performance Management System</title>

    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- App CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')

    @if($activeUniversity)
    <style>
        :root {
            --primary: {{ $activeUniversity->theme_color }} !important;
            --primary-glow: {{ $activeUniversity->theme_color }}33 !important;
            --primary-soft: {{ $activeUniversity->theme_color }}0f !important;
        }
    </style>
    @endif
</head>
<body>

<div class="container-fluid">
    <div class="row g-0">

        <!-- ══════════════════════════════════════════════
             GRADIENT SIDEBAR
         ══════════════════════════════════════════════ -->
        <nav class="col-md-3 col-lg-2 d-md-block tims-sidebar collapse px-0 py-0 d-flex flex-column">
            <div class="tims-sidebar-inner">

                <!-- Brand (Dynamic) -->
                <div class="tims-brand-container">
                    @if($activeUniversity)
                        @if($activeUniversity->logo_url)
                            <img src="{{ $activeUniversity->logo_url }}" 
                                 alt="{{ $activeUniversity->name }}" 
                                 class="rounded-circle border border-secondary border-opacity-25" 
                                 style="width: 42px; height: 42px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" 
                                 style="width: 42px; height: 42px; background: linear-gradient(135deg, {{ $activeUniversity->theme_color }}, #111827); border: 1px solid rgba(255,255,255,0.1); font-size: 1.2rem; flex-shrink:0;">
                                {{ $activeUniversity->initials }}
                            </div>
                        @endif
                        <div class="tims-brand-info ms-2">
                            <span class="tims-brand-title text-truncate" style="max-width: 140px;" title="{{ $activeUniversity->name }}">{{ $activeUniversity->name }}</span>
                            <span class="tims-brand-subtitle">{{ $activeUniversity->code }} CRM</span>
                        </div>
                    @else
                        <div class="tims-brand-logo-box">
                            <i class="fa-solid fa-wind"></i>
                        </div>
                        <div class="tims-brand-info ms-2">
                            <span class="tims-brand-title">TIMS</span>
                            <span class="tims-brand-subtitle">Tims CRM</span>
                        </div>
                    @endif
                </div>

                <!-- Navigation -->
                <ul class="nav flex-column gap-2 flex-grow-1">
                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('dashboard') || Route::is('home') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-table-cells-large"></i>
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('admin.universities.*') ? 'active' : '' }}"
                           href="{{ route('admin.universities.index') }}">
                            <i class="fa-regular fa-building"></i>
                            Company
                            <i class="fa-solid fa-chevron-right tims-nav-chevron"></i>
                        </a>
                    </li>

                    @can('manage_executives')
                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('executives.*') ? 'active' : '' }}"
                           href="{{ route('executives.index') }}">
                            <i class="fa-regular fa-address-book"></i>
                            Executives
                            <i class="fa-solid fa-chevron-right tims-nav-chevron"></i>
                        </a>
                    </li>
                    @endcan

                    @can('enter_daily_logs')
                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('daily_logs.*') ? 'active' : '' }}"
                           href="{{ route('daily_logs.index') }}">
                            <i class="fa-regular fa-calendar-check"></i>
                            Daily Performance
                        </a>
                    </li>
                    @endcan

                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('meetings.*') ? 'active' : '' }}"
                           href="{{ route('meetings.index') }}">
                            <i class="fa-regular fa-handshake"></i>
                            Meeting Tracker
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('audits.*') ? 'active' : '' }}"
                           href="{{ route('audits.index') }}">
                            <i class="fa-regular fa-clipboard"></i>
                            Weekly Audits
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('pips.*') ? 'active' : '' }}"
                           href="{{ route('pips.index') }}">
                            <i class="fa-regular fa-folder-open"></i>
                            PIP Module
                            <i class="fa-solid fa-chevron-right tims-nav-chevron"></i>
                        </a>
                    </li>

                    <hr class="tims-sidebar-divider">

                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('reports.*') ? 'active' : '' }}"
                           href="{{ route('reports.index') }}">
                            <i class="fa-regular fa-file-lines"></i>
                            Reports Center
                            <i class="fa-solid fa-chevron-right tims-nav-chevron"></i>
                        </a>
                    </li>

                    @can('configure_rules')
                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('admin.universities.*') ? 'active' : '' }}"
                           href="{{ route('admin.universities.index') }}">
                            <i class="fa-solid fa-graduation-cap"></i>
                            University Master
                            <i class="fa-solid fa-chevron-right tims-nav-chevron"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="tims-nav-link {{ Route::is('admin.rules.*') ? 'active' : '' }}"
                           href="{{ route('admin.rules.index') }}">
                            <i class="fa-regular fa-compass"></i>
                            Points Settings
                            <i class="fa-solid fa-chevron-right tims-nav-chevron"></i>
                        </a>
                    </li>
                    @endcan
                </ul>

                <!-- Profile card (Image 2 style) -->
                <div class="sidebar-bottom">
                    <div class="tims-profile-card">
                        <div class="tims-profile-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="tims-profile-info">
                            <span class="tims-profile-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                                @csrf
                            </form>
                            <button type="button" class="tims-profile-logout-btn" onclick="document.getElementById('logout-form').submit();">
                                Logout
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </nav>

        <!-- ══════════════════════════════════════════════
             MAIN CONTENT
         ══════════════════════════════════════════════ -->
        <main class="col-md-9 ms-sm-auto col-lg-10 min-vh-100 tims-main-content">

            <!-- Top Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <!-- Search & Switcher -->
                <!-- <div class="d-none d-lg-flex align-items-center gap-3">
                    <div class="tims-search-input-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" class="form-control tims-search-input" placeholder="Search something...">
                        <button class="btn btn-search-pill" type="button">Search</button>
                    </div>

                   
                </div> -->
                <div class="d-block d-lg-none"></div>

              
            </div>

            <!-- Page Title Row -->
            <div class="d-flex justify-content-between align-items-md-center align-items-start flex-column flex-md-row mb-5 gap-3">
                <div>
                    <h1 class="fw-bold m-0" style="font-size:26px;">@yield('page_title', 'Performance Dashboard')</h1>
                    <small class="text-secondary mt-1 d-block">@yield('page_subtitle', "Here is today's report and performances")</small>
                </div>
                @yield('page_actions')
            </div>

            <!-- Flash Alerts -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error') || $errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ session('error') ?? $errors->first('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Page content -->
            @yield('content')

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@yield('scripts')
</body>
</html>
