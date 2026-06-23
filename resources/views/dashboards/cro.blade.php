@extends('layouts.app')
@section('title', 'CRO Dashboard')
@section('page_title', 'CRO Operations Dashboard')

@section('page_subtitle', "Here is today's report and performances")

@section('page_actions')
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="#" class="tims-header-control-pill d-flex align-items-center">
            <i class="fa-regular fa-calendar me-2"></i>
            <span>Jun 1 - Jun 30</span>
            <span style="opacity: 0.2; margin: 0 8px;">|</span>
            <span class="me-1">Monthly</span>
            <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
        </a>
        <a href="#" class="tims-header-control-pill">
            <span>All Segment</span>
            <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
        </a>
        <a href="#" class="btn-ai-assistant">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span>AI Assistant</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="row g-4 mb-5">
        <!-- Total Executives -->
        <div class="col-md-6 col-lg-4 col-xl">
            <div class="tims-metric-card h-100">
                <div class="card-header-row">
                    <span class="card-label">Total Executives</span>

                </div>
                <div class="card-value">{{ $totalExecs }}</div>
                <div class="card-trend-row text-success">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>{{ $activeExecs }} Active Roster</span>
                </div>
            </div>
        </div>
        <!-- Today's Calls -->
        <div class="col-md-6 col-lg-4 col-xl">
            <div class="tims-metric-card h-100">
                <div class="card-header-row">
                    <span class="card-label">Today's Calls</span>

                </div>
                <div class="card-value">{{ $todayCalls }}</div>
                <div class="card-trend-row text-success">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>+12% from average</span>
                </div>
            </div>
        </div>
        <!-- Today's Meetings -->
        <div class="col-md-6 col-lg-4 col-xl">
            <div class="tims-metric-card h-100">
                <div class="card-header-row">
                    <span class="card-label">Today's Meetings</span>

                </div>
                <div class="card-value">{{ $todayMeetingsAttended }}/{{ $todayMeetingsArranged }}</div>
                <div class="card-trend-row text-secondary">
                    <i class="fa-regular fa-handshake"></i>
                    <span>Attended / Arranged</span>
                </div>
            </div>
        </div>
        <!-- Review Zone -->
        <div class="col-md-6 col-lg-4 col-xl">
            <div class="tims-metric-card h-100 border border-danger border-opacity-25">
                <div class="card-header-row">
                    <span class="card-label text-danger">Review Zone</span>

                </div>
                <div class="card-value text-danger">{{ $reviewZoneCount }}</div>
                <div class="card-trend-row text-danger">
                    <i class="fa-solid fa-arrow-trend-down"></i>
                    <span>Negative points balance</span>
                </div>
            </div>
        </div>
        <!-- PIP Count -->
        <div class="col-md-6 col-lg-4 col-xl">
            <div class="tims-metric-card h-100 border border-warning border-opacity-25">
                <div class="card-header-row">
                    <span class="card-label text-warning">Active PIPs</span>

                </div>
                <div class="card-value text-warning">{{ $activePipCount }}</div>
                <div class="card-trend-row text-warning">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Monitoring progress</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Chart: Line/Bar mixed -->
        <div class="col-lg-8">
            <div class="tims-filter-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0 text-white">Employees Performance</h5>
                    <div class="dropdown">
                        <button class="tims-header-control-pill border-0 dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Weekly</span>
                            <i class="fa-solid fa-chevron-down ms-1" style="font-size: 10px;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="#">Weekly</a></li>
                            <li><a class="dropdown-item" href="#">Monthly</a></li>
                        </ul>
                    </div>
                </div>
                <div style="height: 320px;">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Chart: Tiers distribution with center label and side legend -->
        <div class="col-lg-4">
            <div class="tims-filter-card p-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0 text-white">Tiers Distribution</h5>
                    <span class="text-secondary" style="font-size: 12px; font-weight: 500;">
                        <i class="fa-regular fa-calendar-check me-1"></i> Today
                    </span>
                </div>

                <div class="row align-items-center">
                    <!-- Doughnut Canvas with center number -->
                    <div class="col-6">
                        <div class="position-relative" style="height: 180px; width: 180px; margin: 0 auto;">
                            <canvas id="tiersChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center"
                                style="pointer-events: none;">
                                <div class="fw-bold text-white fs-3" style="line-height: 1;">{{ $totalExecs }}</div>
                                <div class="text-muted text-uppercase mt-1" style="font-size: 9px; letter-spacing: 0.05em;">
                                    Total Execs</div>
                            </div>
                        </div>
                    </div>

                    <!-- Side Legend List -->
                    <div class="col-6">
                        <div class="d-flex flex-column gap-2 justify-content-center h-100 ps-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                    <span
                                        style="width: 8px; height: 8px; border-radius: 50%; background-color: #22d3ee; display: inline-block; box-shadow: 0 0 6px #22d3ee;"></span>
                                    <span class="text-secondary">Platinum</span>
                                </span>
                                <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['platinum'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                    <span
                                        style="width: 8px; height: 8px; border-radius: 50%; background-color: #facc15; display: inline-block; box-shadow: 0 0 6px #facc15;"></span>
                                    <span class="text-secondary">Gold</span>
                                </span>
                                <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['gold'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                    <span
                                        style="width: 8px; height: 8px; border-radius: 50%; background-color: #cbd5e1; display: inline-block; box-shadow: 0 0 6px #cbd5e1;"></span>
                                    <span class="text-secondary">Silver</span>
                                </span>
                                <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['silver'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                    <span
                                        style="width: 8px; height: 8px; border-radius: 50%; background-color: #f97316; display: inline-block; box-shadow: 0 0 6px #f97316;"></span>
                                    <span class="text-secondary">Bronze</span>
                                </span>
                                <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['bronze'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                    <span
                                        style="width: 8px; height: 8px; border-radius: 50%; background-color: #f87171; display: inline-block; box-shadow: 0 0 6px #f87171;"></span>
                                    <span class="text-secondary">Review Zone</span>
                                </span>
                                <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['review_zone'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-2 border-top border-secondary-subtle"
                    style="border-top-color: rgba(255,255,255,0.04) !important;">
                    <a href="{{ route('executives.index') }}"
                        class="btn btn-outline-secondary w-100 py-2 rounded-3 text-white border-secondary-subtle"
                        style="font-size: 13px; font-weight: 500; border-color: rgba(255,255,255,0.08) !important;">
                        View Full Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Score Breakdown Row ────────────────────────────────────────────── --}}
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white m-0">
                    <i class="fa-solid fa-chart-simple me-2 text-purple-400" style="color:#a78bfa"></i>
                    Today's Score Breakdown
                </h5>
                <span class="text-secondary" style="font-size:12px">
                    <i class="fa-regular fa-calendar-check me-1"></i>
                    {{ now()->format('M d, Y') }} &bull; {{ $scoreBreakdown['logs'] }} logs submitted
                </span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tims-metric-card h-100" style="border-left: 3px solid #10b981;">
                <div class="card-label text-success mb-1">Positive Points</div>
                <div class="card-value text-success">+{{ $scoreBreakdown['positive'] }}</div>
                <div class="card-trend-row text-secondary"><i class="fa-solid fa-plus-circle me-1"></i>Performance rewards</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tims-metric-card h-100" style="border-left: 3px solid #f87171;">
                <div class="card-label text-danger mb-1">Violation Deductions</div>
                <div class="card-value text-danger">-{{ $scoreBreakdown['negative'] }}</div>
                <div class="card-trend-row text-secondary"><i class="fa-solid fa-minus-circle me-1"></i>Conduct & KPI violations</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tims-metric-card h-100" style="border-left: 3px solid #facc15;">
                <div class="card-label text-warning mb-1">Recovery Points</div>
                <div class="card-value text-warning">+{{ $scoreBreakdown['recovery'] }}</div>
                <div class="card-trend-row text-secondary"><i class="fa-solid fa-rotate me-1"></i>{{ $recoveryToday['execs_recovered'] }} execs recovered</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tims-metric-card h-100" style="border-left: 3px solid #8b5cf6;">
                <div class="card-label" style="color:#a78bfa">Net Score Impact</div>
                <div class="card-value" style="color:#a78bfa">{{ $scoreBreakdown['net'] >= 0 ? '+' : '' }}{{ $scoreBreakdown['net'] }}</div>
                <div class="card-trend-row text-secondary"><i class="fa-solid fa-calculator me-1"></i>Positive − Violations + Recovery</div>
            </div>
        </div>
    </div>

    {{-- ── KPI Compliance + Top Performers Row ───────────────────────────── --}}
    <div class="row g-4 mb-5">
        {{-- KPI Compliance Widget --}}
        <div class="col-lg-5">
            <div class="tims-filter-card p-4 h-100">
                <h6 class="fw-bold text-white mb-4">
                    <i class="fa-solid fa-bullseye me-2" style="color:#38bdf8"></i>KPI Compliance Today
                </h6>
                <div class="d-flex flex-column gap-3">
                    {{-- Calls KPI --}}
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:13px; color:#94a3b8">Calls KPI (≥40 calls)</span>
                            <span class="fw-bold text-white" style="font-size:13px">{{ $kpiCompliance['calls_kpi_pct'] }}%</span>
                        </div>
                        <div class="progress" style="height:6px; background:rgba(255,255,255,0.05); border-radius:4px;">
                            <div class="progress-bar" style="width:{{ $kpiCompliance['calls_kpi_pct'] }}%; background:linear-gradient(90deg,#8b5cf6,#6366f1); border-radius:4px;"></div>
                        </div>
                    </div>
                    {{-- Meetings KPI --}}
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:13px; color:#94a3b8">Meetings KPI (≥1 attended)</span>
                            <span class="fw-bold text-white" style="font-size:13px">{{ $kpiCompliance['meetings_kpi_pct'] }}%</span>
                        </div>
                        <div class="progress" style="height:6px; background:rgba(255,255,255,0.05); border-radius:4px;">
                            <div class="progress-bar" style="width:{{ $kpiCompliance['meetings_kpi_pct'] }}%; background:linear-gradient(90deg,#10b981,#34d399); border-radius:4px;"></div>
                        </div>
                    </div>
                    {{-- CRM KPI --}}
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:13px; color:#94a3b8">CRM Disposition Correct</span>
                            <span class="fw-bold text-white" style="font-size:13px">{{ $kpiCompliance['crm_kpi_pct'] }}%</span>
                        </div>
                        <div class="progress" style="height:6px; background:rgba(255,255,255,0.05); border-radius:4px;">
                            <div class="progress-bar" style="width:{{ $kpiCompliance['crm_kpi_pct'] }}%; background:linear-gradient(90deg,#facc15,#fbbf24); border-radius:4px;"></div>
                        </div>
                    </div>
                    {{-- First Contact KPI --}}
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:13px; color:#94a3b8">First Contact ≤45 min</span>
                            <span class="fw-bold text-white" style="font-size:13px">{{ $kpiCompliance['first_contact_pct'] }}%</span>
                        </div>
                        <div class="progress" style="height:6px; background:rgba(255,255,255,0.05); border-radius:4px;">
                            <div class="progress-bar" style="width:{{ $kpiCompliance['first_contact_pct'] }}%; background:linear-gradient(90deg,#f97316,#fb923c); border-radius:4px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Performers Leaderboard --}}
        <div class="col-lg-7">
            <div class="tims-filter-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-white m-0">
                        <i class="fa-solid fa-trophy me-2" style="color:#facc15"></i>Top Performers
                    </h6>
                    <a href="{{ route('executives.index') }}" style="font-size:12px; color:#6366f1;">View all →</a>
                </div>
                @forelse($topPerformers as $idx => $exec)
                    <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded-3" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.04);">
                        <div class="fw-bold text-center rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:28px; height:28px; font-size:11px;
                            @if($idx === 0) background:#facc15; color:#000;
                            @elseif($idx === 1) background:#cbd5e1; color:#000;
                            @elseif($idx === 2) background:#f97316; color:#fff;
                            @else background:rgba(255,255,255,0.08); color:#94a3b8; @endif">
                            {{ $idx + 1 }}
                        </div>
                        @if($exec->photo)
                            <img src="{{ asset('storage/' . $exec->photo) }}" alt="{{ $exec->name }}"
                                class="rounded-circle flex-shrink-0"
                                style="width:32px; height:32px; object-fit:cover; border:2px solid rgba(255,255,255,0.1);">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                style="width:32px; height:32px; background:linear-gradient(135deg,#6366f1,#8b5cf6); font-size:11px; color:#fff;">
                                {{ strtoupper(substr($exec->name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-white" style="font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $exec->name }}</div>
                            <div style="font-size:11px; color:#64748b;">{{ $exec->university?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold" style="font-size:15px; color:#a78bfa;">{{ number_format($exec->current_score) }}</div>
                            <span class="badge text-uppercase" style="font-size:9px; letter-spacing:0.05em;
                                @if($exec->current_tier === 'platinum') background:rgba(34,211,238,0.15); color:#22d3ee;
                                @elseif($exec->current_tier === 'gold') background:rgba(250,204,21,0.15); color:#facc15;
                                @elseif($exec->current_tier === 'silver') background:rgba(203,213,225,0.15); color:#cbd5e1;
                                @elseif($exec->current_tier === 'bronze') background:rgba(249,115,22,0.15); color:#f97316;
                                @else background:rgba(248,113,113,0.15); color:#f87171; @endif">
                                {{ $exec->current_tier ?? '—' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-secondary text-center py-4" style="font-size:13px;">No active executives yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Streak Leaders + Escalations + Quick Links ─────────────────────── --}}
    <div class="row g-4 mb-5">
        {{-- Streak Leaders --}}
        <div class="col-lg-4">
            <div class="tims-filter-card p-4 h-100">
                <h6 class="fw-bold text-white mb-4">
                    <i class="fa-solid fa-fire me-2" style="color:#f97316"></i>Streak Leaders
                </h6>
                @forelse($streakLeaders as $exec)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                            style="width:36px; height:36px; background:linear-gradient(135deg,#f97316,#ef4444); color:#fff; font-size:12px;">
                            {{ strtoupper(substr($exec->name, 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-white" style="font-size:13px;">{{ $exec->name }}</div>
                            <div style="font-size:11px; color:#64748b;">Call streak: {{ $exec->call_streak_count }} days</div>
                        </div>
                        <div class="d-flex align-items-center gap-1" style="color:#f97316; font-size:13px; font-weight:700;">
                            <i class="fa-solid fa-fire"></i> {{ $exec->call_streak_count }}
                        </div>
                    </div>
                @empty
                    <div class="text-secondary text-center py-4" style="font-size:13px;">No streak data yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Recovery Widget --}}
        <div class="col-lg-4">
            <div class="tims-filter-card p-4 h-100">
                <h6 class="fw-bold text-white mb-4">
                    <i class="fa-solid fa-rotate me-2" style="color:#facc15"></i>Recovery Today
                </h6>
                <div class="text-center py-2">
                    <div class="fw-bold mb-1" style="font-size:48px; color:#facc15; line-height:1;">
                        +{{ $recoveryToday['total_recovery'] }}
                    </div>
                    <div class="text-secondary mb-3" style="font-size:13px;">pts recovered today</div>
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill"
                        style="background:rgba(250,204,21,0.08); border:1px solid rgba(250,204,21,0.2);">
                        <i class="fa-solid fa-users" style="color:#facc15; font-size:12px;"></i>
                        <span style="color:#facc15; font-size:13px; font-weight:600;">{{ $recoveryToday['execs_recovered'] }}</span>
                        <span style="color:#94a3b8; font-size:12px;">executives recovered</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links + Alerts --}}
        <div class="col-lg-4">
            <div class="tims-filter-card p-4 h-100">
                <h6 class="fw-bold text-white mb-4">
                    <i class="fa-solid fa-bolt me-2" style="color:#8b5cf6"></i>Quick Actions
                </h6>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('daily_logs.create') }}"
                        class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                        style="background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2); transition:background 0.2s;"
                        onmouseover="this.style.background='rgba(99,102,241,0.18)'" onmouseout="this.style.background='rgba(99,102,241,0.1)'">
                        <i class="fa-solid fa-plus-circle" style="color:#6366f1; width:16px;"></i>
                        <span class="text-white" style="font-size:13px;">New Daily Log</span>
                    </a>
                    <a href="{{ route('executives.index') }}"
                        class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                        style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); transition:background 0.2s;"
                        onmouseover="this.style.background='rgba(16,185,129,0.15)'" onmouseout="this.style.background='rgba(16,185,129,0.08)'">
                        <i class="fa-solid fa-users" style="color:#10b981; width:16px;"></i>
                        <span class="text-white" style="font-size:13px;">Executive </span>
                    </a>
                    <a href="{{ route('admin.university_rules.index') }}"
                        class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                        style="background:rgba(139,92,246,0.08); border:1px solid rgba(139,92,246,0.2); transition:background 0.2s;"
                        onmouseover="this.style.background='rgba(139,92,246,0.15)'" onmouseout="this.style.background='rgba(139,92,246,0.08)'">
                        <i class="fa-solid fa-gears" style="color:#8b5cf6; width:16px;"></i>
                        <span class="text-white" style="font-size:13px;">Rule Engine</span>
                    </a>
                    <a href="{{ route('reports.index') }}"
                        class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                        style="background:rgba(250,204,21,0.08); border:1px solid rgba(250,204,21,0.2); transition:background 0.2s;"
                        onmouseover="this.style.background='rgba(250,204,21,0.15)'" onmouseout="this.style.background='rgba(250,204,21,0.08)'">
                        <i class="fa-solid fa-file-chart-column" style="color:#facc15; width:16px;"></i>
                        <span class="text-white" style="font-size:13px;">Reports</span>
                    </a>
                    @if($openEscalations > 0)
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3"
                        style="background:rgba(248,113,113,0.1); border:1px solid rgba(248,113,113,0.25);">
                        <i class="fa-solid fa-triangle-exclamation" style="color:#f87171; width:16px;"></i>
                        <span style="color:#f87171; font-size:13px; font-weight:600;">{{ $openEscalations }} Open Escalation{{ $openEscalations > 1 ? 's' : '' }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        // Trends Chart
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        const pastWeekData = @json($pastWeek);
        const labels = Object.keys(pastWeekData);
        const calls = labels.map(date => pastWeekData[date].calls);
        const arranged = labels.map(date => pastWeekData[date].arranged);
        const attended = labels.map(date => pastWeekData[date].attended);

        // Create gradient for calls bar chart
        const callsGradient = trendsCtx.createLinearGradient(0, 0, 0, 300);
        callsGradient.addColorStop(0, 'rgba(139, 92, 246, 0.7)');
        callsGradient.addColorStop(1, 'rgba(139, 92, 246, 0.05)');

        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Connected Calls',
                        data: calls,
                        backgroundColor: callsGradient,
                        borderColor: '#8b5cf6',
                        borderWidth: 0,
                        borderRadius: 6,
                        barPercentage: 0.5,
                        order: 2
                    },
                    {
                        label: 'Arranged Meetings',
                        data: arranged,
                        borderColor: '#facc15',
                        backgroundColor: 'transparent',
                        tension: 0.3,
                        borderWidth: 2,
                        borderDash: [4, 4],
                        pointRadius: 2,
                        order: 1
                    },
                    {
                        label: 'Attended Meetings',
                        data: attended,
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        tension: 0.3,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointBackgroundColor: '#10b981',
                        order: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#121217',
                        titleColor: '#ffffff',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255, 255, 255, 0.08)',
                        borderWidth: 1,
                        titleFont: { family: 'Inter', weight: 'bold' },
                        bodyFont: { family: 'Inter' },
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#888d96', font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.02)', drawBorder: false },
                        ticks: { color: '#888d96', font: { family: 'Inter', size: 11 } }
                    }
                }
            }
        });

        // Tiers Distribution Doughnut Chart
        const tiersCtx = document.getElementById('tiersChart').getContext('2d');
        const tierData = @json($tiers);

        new Chart(tiersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Platinum', 'Gold', 'Silver', 'Bronze', 'Review Zone'],
                datasets: [{
                    data: [
                        tierData.platinum,
                        tierData.gold,
                        tierData.silver,
                        tierData.bronze,
                        tierData.review_zone
                    ],
                    backgroundColor: ['#22d3ee', '#facc15', '#cbd5e1', '#f97316', '#f87171'],
                    borderWidth: 3,
                    borderColor: '#121217',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%', // Sleek thin doughnut ring
                plugins: {
                    legend: {
                        display: false // Hide default legend to use our custom side list
                    },
                    tooltip: {
                        backgroundColor: '#121217',
                        titleColor: '#ffffff',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255, 255, 255, 0.08)',
                        borderWidth: 1,
                        titleFont: { family: 'Inter', weight: 'bold' },
                        bodyFont: { family: 'Inter' },
                        padding: 10,
                        cornerRadius: 8
                    }
                }
            }
        });
    </script>
@endsection