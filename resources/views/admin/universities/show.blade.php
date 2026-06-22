@extends('layouts.app')

@section('title', $university->name . ' - Profile')
@section('page_title', $university->name)
@section('page_subtitle', 'Detailed statistics, metrics, and rule overrides')

@section('page_actions')
<div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.universities.dashboard', $university->id) }}" class="btn btn-outline-success rounded-3 px-4">
        <i class="fa-solid fa-chart-line me-1"></i> Dedicated Dashboard
    </a>
    <a href="{{ route('admin.universities.edit', $university->id) }}" class="btn btn-primary rounded-3 px-4">
        <i class="fa-regular fa-pen-to-square me-1"></i> Edit Profile
    </a>
</div>
@endsection

@section('content')

{{-- University Info Header Card --}}
<div class="glass-card p-4 mb-4">
    <div class="row align-items-center">
        <div class="col-md-2 text-center text-md-start">
            @if($university->logo_url)
                <img src="{{ $university->logo_url }}" 
                     alt="{{ $university->name }}" 
                     class="rounded-circle border border-secondary border-opacity-25" 
                     style="width: 100px; height: 100px; object-fit: cover;">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mx-auto shadow-sm" 
                     style="width: 100px; height: 100px; background: linear-gradient(135deg, {{ $university->theme_color }}, #111827); border: 1px solid rgba(255,255,255,0.1); font-size: 2.2rem;">
                    {{ $university->initials }}
                </div>
            @endif
        </div>
        
        <div class="col-md-7 text-center text-md-start mt-3 mt-md-0">
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                <h3 class="fw-bold text-white m-0">{{ $university->name }}</h3>
                <span class="badge" style="background: {{ $university->theme_color }}22; color: {{ $university->theme_color }}; border: 1px solid {{ $university->theme_color }}44;">
                    {{ $university->code }}
                </span>
            </div>
            <p class="text-secondary mb-3">{{ $university->description ?? 'No description has been written for this university yet.' }}</p>
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-3 small text-secondary">
                <span><i class="fa-regular fa-calendar me-1.5"></i>Created: {{ $university->created_at->toDateString() }}</span>
                <span class="d-flex align-items-center gap-1.5">
                    <span class="rounded-circle" style="width: 8px; height: 8px; background: {{ $university->theme_color }};"></span>
                    Theme Accent Color
                </span>
                <span>
                    @if($university->status === 'active')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">Active</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5">Inactive</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="col-md-3 text-center text-md-end mt-4 mt-md-0">
            <span class="text-secondary small d-block mb-1">Active Switch Context</span>
            @if(session('active_university_id') == $university->id)
                <span class="badge bg-success py-2 px-3 rounded-pill fw-semibold"><i class="fa-solid fa-toggle-on me-1.5"></i>Currently Active</span>
            @else
                <form action="{{ route('active_university.switch') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="university_id" value="{{ $university->id }}">
                    <button type="submit" class="btn btn-sm btn-outline-secondary px-3 py-1.5 rounded-pill"><i class="fa-solid fa-toggle-off me-1.5"></i>Switch to TIMS View</button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- Navigation Tabs --}}
<ul class="nav nav-tabs tims-nav-tabs border-0 mb-4" id="universityTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
            <i class="fa-solid fa-gauge-high me-2"></i>Overview
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="rules-tab" data-bs-toggle="tab" data-bs-target="#rules" type="button" role="tab" aria-controls="rules" aria-selected="false">
            <i class="fa-solid fa-sliders me-2"></i>Scoring & Threshold Rules
        </button>
    </li>
</ul>

<div class="tab-content" id="universityTabContent">
    
    {{-- OVERVIEW TAB --}}
    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
        
        {{-- Statistics Cards --}}
        <div class="row g-3 mb-4">
            {{-- Total Executives --}}
            <div class="col-md-4 col-lg-2.4 col-sm-6">
                <div class="glass-card p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="fa-solid fa-users fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-secondary small d-block">Total Counselors</span>
                        <h4 class="fw-bold text-white m-0">{{ $university->total_executives }}</h4>
                    </div>
                </div>
            </div>

            {{-- Active Executives --}}
            <div class="col-md-4 col-lg-2.4 col-sm-6">
                <div class="glass-card p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="fa-regular fa-circle-check fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-secondary small d-block">Active Counselors</span>
                        <h4 class="fw-bold text-white m-0">{{ $university->active_executives }}</h4>
                    </div>
                </div>
            </div>

            {{-- Total Rules --}}
            <div class="col-md-4 col-lg-2.4 col-sm-6">
                <div class="glass-card p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="fa-solid fa-list-check fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-secondary small d-block">Active Rules</span>
                        <h4 class="fw-bold text-white m-0">{{ $university->total_rules }}</h4>
                    </div>
                </div>
            </div>

            {{-- Current Tier Structure --}}
            <div class="col-md-6 col-lg-2.4 col-sm-6">
                <div class="glass-card p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="fa-solid fa-circle-nodes fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-secondary small d-block">Tier Thresholds</span>
                        <h6 class="fw-semibold text-white m-0">Plat: {{ $tierStructure['platinum'] }} | Gold: {{ $tierStructure['gold'] }}</h6>
                    </div>
                </div>
            </div>

            {{-- Monthly Performance --}}
            <div class="col-md-6 col-lg-2.4 col-sm-6">
                <div class="glass-card p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="fa-solid fa-chart-line fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-secondary small d-block">Avg Score (Month)</span>
                        <h4 class="fw-bold text-white m-0">{{ round($monthlyPerformance ?? 0, 1) }} pts</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row g-4 mb-5">
            {{-- Performance Trend --}}
            <div class="col-lg-8">
                <div class="glass-card p-4 h-100">
                    <h6 class="fw-bold text-white mb-4"><i class="fa-solid fa-trend-up text-primary me-2"></i>Performance Score Trend (Average Daily Score)</h6>
                    <div style="height: 300px;">
                        <canvas id="performanceTrendChart"></canvas>
                    </div>
                </div>
            </div>
            
            {{-- Tier Distribution --}}
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100">
                    <h6 class="fw-bold text-white mb-4"><i class="fa-solid fa-chart-pie text-warning me-2"></i>Counselors Tier Distribution</h6>
                    <div style="height: 250px; position: relative;">
                        <canvas id="tierDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Zone Distribution --}}
            <div class="col-lg-12">
                <div class="glass-card p-4">
                    <h6 class="fw-bold text-white mb-4"><i class="fa-solid fa-map-location-dot text-info me-2"></i>Counselors Zonal Distribution</h6>
                    <div style="height: 200px;">
                        <canvas id="zoneDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- RULES TAB --}}
    <div class="tab-pane fade" id="rules" role="tabpanel" aria-labelledby="rules-tab">
        <form method="POST" action="{{ route('admin.universities.rules.update', $university->id) }}">
            @csrf
            
            @php
                $groups = $rules->groupBy('rule_group');
                $groupLabels = [
                    'calls'     => ['label' => 'Call Volume Points', 'icon' => 'fa-phone-volume', 'color' => 'primary'],
                    'meetings'  => ['label' => 'Meeting Points & Bonuses', 'icon' => 'fa-handshake', 'color' => 'success'],
                    'lead_mgmt' => ['label' => 'Lead Management KPIs', 'icon' => 'fa-list-check', 'color' => 'info'],
                    'conversion'=> ['label' => 'Conversion Bonuses', 'icon' => 'fa-fire', 'color' => 'warning'],
                    'recovery'  => ['label' => 'Recovery Bonuses', 'icon' => 'fa-rotate-right', 'color' => 'secondary'],
                    'violation' => ['label' => 'Violation Penalties (Negative)', 'icon' => 'fa-ban', 'color' => 'danger'],
                    'tier'      => ['label' => 'Tier Limits & Thresholds', 'icon' => 'fa-circle-nodes', 'color' => 'light'],
                    'pip'       => ['label' => 'PIP Module Thresholds', 'icon' => 'fa-folder-open', 'color' => 'warning'],
                    'escalation'=> ['label' => 'Escalation Thresholds', 'icon' => 'fa-bell', 'color' => 'danger'],
                ];
            @endphp

            @foreach($groups as $group => $groupRules)
            @php $meta = $groupLabels[$group] ?? ['label' => ucwords($group), 'icon' => 'fa-gear', 'color' => 'secondary']; @endphp
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-4">
                    <i class="fa-solid {{ $meta['icon'] }} text-{{ $meta['color'] }} me-2"></i>{{ $meta['label'] }}
                </h5>
                <div class="row g-3">
                    @foreach($groupRules as $rule)
                    <input type="hidden" name="rules[{{ $loop->parent->index * 100 + $loop->index }}][id]" value="{{ $rule->id }}">
                    <div class="col-lg-6">
                        <div class="p-3 border rounded-3 {{ !$rule->is_active ? 'opacity-50' : '' }}" style="background: rgba(0,0,0,0.02); border-color: rgba(255,255,255,0.05) !important;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="fw-semibold text-white">{{ $rule->rule_name }}</div>
                                    <small class="font-monospace text-secondary">{{ $rule->rule_key }}</small>
                                    @if($rule->description)
                                    <div class="text-secondary small mt-1">{{ $rule->description }}</div>
                                    @endif
                                </div>
                                <div class="form-check form-switch ms-3 flex-shrink-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="rules[{{ $loop->parent->index * 100 + $loop->index }}][is_active]"
                                           {{ $rule->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label small text-secondary">Active</label>
                                </div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text fw-bold border-0 {{ $rule->rule_value >= 0 ? 'bg-success bg-opacity-20 text-success' : 'bg-danger bg-opacity-20 text-danger' }}">
                                    {{ $rule->rule_value >= 0 ? '+' : '' }}
                                </span>
                                <input type="number"
                                       name="rules[{{ $loop->parent->index * 100 + $loop->index }}][rule_value]"
                                       class="form-control fw-bold font-monospace bg-dark bg-opacity-50 text-white border-secondary border-opacity-10"
                                       value="{{ $rule->rule_value }}"
                                       step="1"
                                       required>
                                <span class="input-group-text border-0 bg-dark text-secondary font-monospace">units</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-end gap-3 mt-2 mb-5">
                <button type="submit" class="btn btn-primary rounded-3 px-5 fw-semibold">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Save 
                </button>
            </div>
        </form>
    </div>

</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Performance Trend Chart
        const performanceCtx = document.getElementById('performanceTrendChart').getContext('2d');
        const trendData = {!! json_encode($performanceTrend) !!};
        
        new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: trendData.map(d => d.month),
                datasets: [{
                    label: 'Avg Performance Score',
                    data: trendData.map(d => parseFloat(d.avg_score)),
                    borderColor: '{{ $university->theme_color }}',
                    backgroundColor: '{{ $university->theme_color }}1a',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af' }
                    }
                }
            }
        });

        // 2. Tier Distribution Chart
        const tierCtx = document.getElementById('tierDistributionChart').getContext('2d');
        const tierData = {!! json_encode($tierDistribution) !!};
        const colorPalette = {
            platinum: '#c084fc',
            gold: '#f59e0b',
            silver: '#9ca3af',
            bronze: '#b45309',
            review_zone: '#ef4444'
        };

        new Chart(tierCtx, {
            type: 'doughnut',
            data: {
                labels: tierData.map(d => d.current_tier.charAt(0).toUpperCase() + d.current_tier.slice(1).replace('_', ' ')),
                datasets: [{
                    data: tierData.map(d => d.count),
                    backgroundColor: tierData.map(d => colorPalette[d.current_tier] || '#6b7280'),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#9ca3af', boxWidth: 12 }
                    }
                },
                cutout: '70%'
            }
        });

        // 3. Zone Distribution Chart
        const zoneCtx = document.getElementById('zoneDistributionChart').getContext('2d');
        const zoneData = {!! json_encode($zonesDistribution) !!};

        new Chart(zoneCtx, {
            type: 'bar',
            data: {
                labels: zoneData.map(d => d.zone_name),
                datasets: [{
                    label: 'Counselors',
                    data: zoneData.map(d => d.count),
                    backgroundColor: '{{ $university->theme_color }}99',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#9ca3af', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af' }
                    }
                }
            }
        });
    });
</script>
@endsection
