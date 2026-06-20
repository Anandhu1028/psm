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
                    <button class="tims-header-control-pill border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <div class="position-absolute top-50 start-50 translate-middle text-center" style="pointer-events: none;">
                            <div class="fw-bold text-white fs-3" style="line-height: 1;">{{ $totalExecs }}</div>
                            <div class="text-muted text-uppercase mt-1" style="font-size: 9px; letter-spacing: 0.05em;">Total Execs</div>
                        </div>
                    </div>
                </div>
                
                <!-- Side Legend List -->
                <div class="col-6">
                    <div class="d-flex flex-column gap-2 justify-content-center h-100 ps-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #22d3ee; display: inline-block; box-shadow: 0 0 6px #22d3ee;"></span>
                                <span class="text-secondary">Platinum</span>
                            </span>
                            <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['platinum'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #facc15; display: inline-block; box-shadow: 0 0 6px #facc15;"></span>
                                <span class="text-secondary">Gold</span>
                            </span>
                            <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['gold'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #cbd5e1; display: inline-block; box-shadow: 0 0 6px #cbd5e1;"></span>
                                <span class="text-secondary">Silver</span>
                            </span>
                            <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['silver'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #f97316; display: inline-block; box-shadow: 0 0 6px #f97316;"></span>
                                <span class="text-secondary">Bronze</span>
                            </span>
                            <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['bronze'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #f87171; display: inline-block; box-shadow: 0 0 6px #f87171;"></span>
                                <span class="text-secondary">Review Zone</span>
                            </span>
                            <span class="text-white fw-bold" style="font-size: 12px;">{{ $tiers['review_zone'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3 pt-2 border-top border-secondary-subtle" style="border-top-color: rgba(255,255,255,0.04) !important;">
                <a href="{{ route('executives.index') }}" class="btn btn-outline-secondary w-100 py-2 rounded-3 text-white border-secondary-subtle" style="font-size: 13px; font-weight: 500; border-color: rgba(255,255,255,0.08) !important;">
                    View Full Details
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Table: Recent Daily Logs -->
<div class="tims-roster-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold m-0 text-white">Recent Daily Performance Logs</h5>
            <small class="text-secondary">Recent records updated by the CRO team</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('daily_logs.create') }}" class="btn btn-primary btn-add-executive py-2">
                <i class="fa-solid fa-plus me-1"></i> Log Daily Data
            </a>
            <a href="{{ route('daily_logs.index') }}" class="btn tims-header-control-pill" style="padding: 10px 16px !important;">
                View All
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Executive</th>
                    <th>Zone</th>
                    <th>Calls</th>
                    <th>Meetings</th>
                    <th>Daily Score</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                <tr>
                    <td class="fw-semibold text-white">{{ $log->date->toDateString() }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="tims-table-avatar">
                                {{ strtoupper(substr($log->executive->name, 0, 2)) }}
                            </div>
                            <div class="text-start">
                                <div class="tims-exec-name">{{ $log->executive->name }}</div>
                                <span class="tims-exec-email" style="font-family: var(--font-mono); font-size: 11px;">#{{ $log->executive->employee_id }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="d-inline-flex align-items-center gap-2">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #8b5cf6; display: inline-block; box-shadow: 0 0 6px #8b5cf6;"></span>
                            <span>{{ $log->executive->zone->name }}</span>
                        </span>
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $log->connected_calls }}</span></td>
                    <td class="text-white">{{ $log->meetings_attended }}/{{ $log->meetings_arranged }}</td>
                    <td>
                        @if($log->calculated_score > 0)
                            <span class="tims-score-positive">▲ {{ $log->calculated_score }}</span>
                        @elseif($log->calculated_score < 0)
                            <span class="tims-score-negative">▼ {{ abs($log->calculated_score) }}</span>
                        @else
                            <span class="tims-score-zero">0</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-link text-secondary p-0 border-0 tims-action-dots-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('executives.scorecard', $log->executive_id) }}">
                                        <i class="fa-regular fa-id-card me-2 text-primary"></i> Scorecard
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-secondary">No logs entered for today.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
