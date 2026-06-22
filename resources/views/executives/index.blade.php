@extends('layouts.app')

@section('title', 'Executives Roster')
@section('page_title', 'Executive Roster')
@section('page_subtitle', 'Manage and monitor all CRO executives')

@section('page_actions')
<div class="d-flex align-items-center gap-2">
    @can('manage_executives')
    <button class="tims-add-user-btn" type="button" data-bs-toggle="modal" data-bs-target="#addExecutiveModal">
        <i class="fa-solid fa-plus"></i>
        <span>Add Executive</span>
    </button>
    @endcan
</div>
@endsection

@section('content')

{{-- ── Collapsible Search & Filter Bar ── --}}


{{-- ── Table Controls Row (User Management Mockup style) ── --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold m-0 text-white" style="font-size: 18px;">All Executives ({{ $executives->total() }})</h5>
    </div>
    <div class="d-flex align-items-center gap-2">

     <!-- Active University Switcher -->
                    <form action="{{ route('active_university.switch') }}" method="POST" id="global-univ-switcher" class="m-0">
                        @csrf
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-dark border-secondary border-opacity-10 text-secondary" style="font-size: 11px;"><i class="fa-solid fa-graduation-cap"></i></span>
                            <select name="university_id" class="form-select bg-dark text-white border-secondary border-opacity-10" style="font-size: 11.5px; border-radius: 0 10px 10px 0;" onchange="document.getElementById('global-univ-switcher').submit();">
                                <option value="">All Universities (TIMS)</option>
                                @foreach($allUniversities as $uni)
                                    <option value="{{ $uni->id }}" {{ $activeUniversity && $activeUniversity->id == $uni->id ? 'selected' : '' }}>
                                        {{ $uni->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
        <!-- Filter Toggle -->
        <button class="tims-table-control-btn"
        type="button"
        data-bs-toggle="modal"
        data-bs-target="#filterModal">
    <i class="fa-solid fa-sliders"></i>
    <span>Filters</span>
</button>
        <!-- Export Button -->
        <a href="#" class="tims-table-control-btn" title="Export Data" onclick="alert('Exporting data as CSV...'); return false;">
            <i class="fa-solid fa-download"></i>
            <span>Export</span>
        </a>
    </div>
</div>

{{-- ── Roster Table ── --}}
<div class="tims-roster-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="executivesTable">
            <thead>
                <tr>
                    <th>Emp ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Zone</th>
                    <th>Academy</th>
                    <th>Status</th>
                    <th>Tier</th>
                    <th>Productivity Score</th>
                    <th>Probation End</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($executives as $exec)
                <tr>
                    {{-- Employee ID --}}
                    <td>
                        <a href="{{ route('executives.scorecard', $exec->id) }}" class="tims-emp-id">
                            {{ $exec->employee_id }}
                        </a>
                    </td>

                    {{-- User (Avatar + Name) --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="tims-table-avatar">
                                {{ strtoupper(substr($exec->name, 0, 2)) }}
                            </div>
                            <span class="fw-bold text-white fs-13.5">{{ $exec->name }}</span>
                        </div>
                    </td>

                    {{-- Email --}}
                    <td>
                        <span class="text-secondary fs-13">{{ $exec->email }}</span>
                    </td>

                    {{-- Zone --}}
                    <td>
                        <span class="badge bg-secondary-subtle">
                            {{ $exec->zone->name ?? '—' }}
                        </span>
                    </td>

                    {{-- University --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($exec->university)
                                @if($exec->university->logo_url)
                                    <img src="{{ $exec->university->logo_url }}" 
                                         alt="{{ $exec->university->name }}" 
                                         class="rounded-circle border border-secondary border-opacity-10" 
                                         style="width: 24px; height: 24px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white font-monospace" 
                                         style="width: 24px; height: 24px; background: linear-gradient(135deg, {{ $exec->university->theme_color }}, #111827); border: 1px solid rgba(255,255,255,0.1); font-size: 10px; flex-shrink: 0;">
                                        {{ $exec->university->initials }}
                                    </div>
                                @endif
                                <span class="text-secondary small">{{ $exec->university->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </td>

                    {{-- Status --}}
                    <td>
                        @if($exec->status === 'active')
                            <span class="tims-status-badge active">
                                <i class="fa-regular fa-circle-check"></i> Active
                            </span>
                        @elseif($exec->status === 'probation')
                            <span class="tims-status-badge idle">
                                <i class="fa-regular fa-clock"></i> Probation
                            </span>
                        @else
                            <span class="tims-status-badge inactive">
                                <i class="fa-regular fa-circle-xmark"></i> Expired
                            </span>
                        @endif
                    </td>

                    {{-- Tier --}}
                    <td>
                        <span class="tier-badge tier-{{ $exec->current_tier }}">
                            {{ ucwords(str_replace('_', ' ', $exec->current_tier)) }}
                        </span>
                    </td>

                    {{-- Productivity Score (Progress Bar) --}}
                    <td>
                        @php
                            $scorePercent = max(0, min(100, $exec->current_score));
                        @endphp
                        <div class="d-flex align-items-center gap-2" style="max-width: 140px;">
                            <div class="tims-table-progress-track flex-grow-1">
                                @if($exec->current_score > 0)
                                    <div class="tims-table-progress-bar" style="width: {{ $scorePercent }}%;"></div>
                                @else
                                    <div class="tims-table-progress-bar bg-danger" style="width: 0%;"></div>
                                @endif
                            </div>
                            <span class="tims-score-num text-white {{ $exec->current_score < 0 ? 'text-danger' : '' }}">
                                {{ $exec->current_score }}
                            </span>
                        </div>
                    </td>

                    {{-- Probation End --}}
                    <td>
                        @if($exec->probation_end_date)
                            @if($exec->probation_end_date->isPast())
                                <span class="tims-probation-expired">
                                    {{ $exec->probation_end_date->toDateString() }}
                                </span>
                                <span class="badge-expired-chip">EXPIRED</span>
                                <span class="tims-time-hint">{{ $exec->probation_end_date->diffForHumans() }}</span>
                            @else
                                <span class="tims-probation-normal">
                                    {{ $exec->probation_end_date->toDateString() }}
                                </span>
                                <span class="tims-time-hint">{{ $exec->probation_end_date->diffForHumans() }}</span>
                            @endif
                        @else
                            <span class="text-secondary">—</span>
                        @endif
                    </td>

                    {{-- Actions Dropdown --}}
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-link text-secondary p-0 border-0 tims-action-dots-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#viewExecutiveModal{{ $exec->id }}">
                                        <i class="fa-regular fa-eye me-2 text-info"></i> View Profile
                                    </button>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('executives.scorecard', $exec->id) }}">
                                        <i class="fa-regular fa-id-card me-2 text-primary"></i> View Points
                                    </a>
                                </li>
                                @can('manage_executives')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('executives.destroy', $exec->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete executive {{ $exec->name }}? This will permanently delete all logs, scorecard history, and PIP records.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="fa-regular fa-trash-can me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <i class="fa-solid fa-users-slash fa-2x mb-3 d-block" style="color:#3e3f56;"></i>
                        <span style="color:#5e6273; font-size:13px;">No executives found matching your criteria.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Footer --}}
    <div class="tims-table-footer">
        <div>
            @if($executives->hasPages())
                {{ $executives->withQueryString()->links('pagination::bootstrap-5') }}
            @else
                <nav>
                    <ul class="pagination">
                        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                        <li class="page-item active"><span class="page-link">1</span></li>
                        <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                    </ul>
                </nav>
            @endif
        </div>
        <div class="tims-pagination-info">
            Page {{ $executives->currentPage() }} of {{ $executives->lastPage() }}
        </div>
    </div>
</div>

{{-- ── View Details Modals (Loop) ── --}}
@foreach($executives as $exec)
<div class="modal fade" id="viewExecutiveModal{{ $exec->id }}" tabindex="-1" aria-labelledby="viewExecutiveModalLabel{{ $exec->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #0f1322; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; box-shadow: 0 15px 50px rgba(0,0,0,0.8);">
            <div class="modal-header border-bottom border-secondary border-opacity-10 px-4 py-3">
                <h5 class="modal-title fw-bold text-white" id="viewExecutiveModalLabel{{ $exec->id }}">
                    <i class="fa-regular fa-user text-primary me-2"></i>Executive Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="tims-avatar-circle-dark mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.5rem; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-2)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; box-shadow: 0 5px 15px rgba(139,92,246,0.3);">
                        {{ strtoupper(substr($exec->name, 0, 2)) }}
                    </div>
                    <h4 class="fw-bold text-white mb-1">{{ $exec->name }}</h4>
                    <span class="badge bg-secondary-subtle mb-3">{{ $exec->employee_id }}</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Email</small>
                            <span class="text-white text-truncate d-block" style="font-size: 13px;" title="{{ $exec->email }}">{{ $exec->email }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Phone</small>
                            <span class="text-white d-block" style="font-size: 13px;">{{ $exec->phone }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Zone</small>
                            <span class="text-white d-block" style="font-size: 13px;">{{ $exec->zone->name ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Department</small>
                            <span class="text-white d-block" style="font-size: 13px;">{{ $exec->department->name ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">University</small>
                            <span class="text-white d-block" style="font-size: 13px;">{{ $exec->university->name ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Status</small>
                            <div class="mt-1">
                                @if($exec->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($exec->status === 'probation')
                                    <span class="badge bg-warning">Probation</span>
                                @else
                                    <span class="badge bg-danger">Expired</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Current Tier</small>
                            <div class="mt-1">
                                <span class="tier-badge tier-{{ $exec->current_tier }}">
                                    {{ ucwords(str_replace('_', ' ', $exec->current_tier)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Current Score</small>
                            <span class="fw-bold d-block mt-1 {{ $exec->current_score >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $exec->current_score }} pts
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Date Joined</small>
                            <span class="text-white d-block" style="font-size: 13px;">{{ $exec->date_joined ? $exec->date_joined->toDateString() : '—' }}</span>
                        </div>
                    </div>
                    @if($exec->probation_end_date)
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Probation End Date</small>
                            <span class="text-white" style="font-size: 13px;">{{ $exec->probation_end_date->toDateString() }}</span>
                            <span class="small ms-2 {{ $exec->probation_end_date->isPast() ? 'text-danger fw-bold' : 'text-secondary' }}">
                                ({{ $exec->probation_end_date->isPast() ? 'Expired' : 'Active' }} · {{ $exec->probation_end_date->diffForHumans() }})
                            </span>
                        </div>
                    </div>
                    @endif
                    @if($exec->reportingManager)
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                            <small class="text-secondary d-block mb-1">Reporting Manager</small>
                            <span class="text-white" style="font-size: 13px;">{{ $exec->reportingManager->name }}</span>
                            <small class="text-secondary ms-2">({{ $exec->reportingManager->email }})</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-top border-secondary border-opacity-10 px-4 py-3">
                <a href="{{ route('executives.scorecard', $exec->id) }}" class="btn btn-primary rounded-3 px-4 fw-semibold">
                    <i class="fa-regular fa-id-card me-2"></i>Full Scorecard
                </a>
                <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- ── Modal: Add Executive ── --}}
@can('manage_executives')
<div class="modal fade" id="addExecutiveModal" tabindex="-1" aria-labelledby="addExecutiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: #0f1322; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; box-shadow: 0 15px 50px rgba(0,0,0,0.8);">
            <div class="modal-header border-bottom border-secondary border-opacity-10 px-4 py-3">
                <h5 class="modal-title fw-bold text-white" id="addExecutiveModalLabel">
                    <i class="fa-solid fa-user-plus text-primary me-2"></i>Add New Executive
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('executives.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">University Assignment</label>
                            <select name="university_id" class="form-select" required>
                                <option value="">Select University...</option>
                                @foreach($universities as $uni)
                                <option value="{{ $uni->id }}" {{ session('active_university_id') == $uni->id ? 'selected' : '' }}>
                                    {{ $uni->name }} ({{ $uni->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" placeholder="e.g. EMP005" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. John Smith" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. john@tims.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. +919988776655" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Zone</label>
                            <select name="zone_id" class="form-select" required>
                                <option value="">Select Zone...</option>
                                @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">Select Department (Optional)...</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Date Joined</label>
                            <input type="date" name="date_joined" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Probation End Date</label>
                            <input type="date" name="probation_end_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Reporting Manager</label>
                            <select name="reporting_manager_id" class="form-select">
                                <option value="">Select Manager (Optional)...</option>
                                @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->roles->first()?->name ?? 'User' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="probation">Probation</option>
                                <option value="active">Active</option>
                                <option value="inactive">Expired (Inactive)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-10 px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-5 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Save Executive
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan


<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">

            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">
                    <i class="fa-solid fa-sliders me-2"></i>
                    Advanced Filters
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <form method="GET" action="{{ route('executives.index') }}">

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label text-secondary">
                                Search
                            </label>

                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   value="{{ request('search') }}"
                                   placeholder="Name, Email, Employee ID">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary">
                                Zone
                            </label>

                            <select name="zone_id" class="form-select">
                                <option value="">All Zones</option>

                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}"
                                        {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary">
                                University
                            </label>

                            <select name="university_id" class="form-select">
                                <option value="">All Universities</option>

                                @foreach($universities as $uni)
                                    <option value="{{ $uni->id }}"
                                        {{ request('university_id') == $uni->id ? 'selected' : '' }}>
                                        {{ $uni->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary">
                                Tier
                            </label>

                            <select name="tier" class="form-select">
                                <option value="">All Tiers</option>
                                <option value="platinum">Platinum</option>
                                <option value="gold">Gold</option>
                                <option value="silver">Silver</option>
                                <option value="bronze">Bronze</option>
                                <option value="review_zone">Review Zone</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary">
                                Status
                            </label>

                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="probation">Probation</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-secondary">
                                From Date
                            </label>

                            <input type="date"
                                   name="from_date"
                                   value="{{ request('from_date') }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-secondary">
                                To Date
                            </label>

                            <input type="date"
                                   name="to_date"
                                   value="{{ request('to_date') }}"
                                   class="form-control">
                        </div>

                    </div>

                </div>

                <div class="modal-footer border-secondary">

                    <a href="{{ route('executives.index') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i>
                        Apply Filters
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
@endsection