@extends('layouts.app')

@section('title', $university->name . ' Dashboard')
@section('page_title', $university->name . ' Performance Dashboard')
@section('page_subtitle', 'Live tracking of counseling performance indices for ' . $university->name)

@section('page_actions')
<a href="{{ route('admin.universities.show', $university->id) }}" class="btn btn-outline-secondary rounded-3 px-4">
    <i class="fa-solid fa-graduation-cap me-1"></i> University Profile
</a>
@endsection

@section('content')

{{-- University Header banner --}}
<div class="glass-card p-4 mb-4" style="border-left: 4px solid {{ $university->theme_color }};">
    <div class="d-flex align-items-center gap-3">
        @if($university->logo_url)
            <img src="{{ $university->logo_url }}" 
                 alt="{{ $university->name }}" 
                 class="rounded-circle border border-secondary border-opacity-25" 
                 style="width: 60px; height: 60px; object-fit: cover;">
        @else
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" 
                 style="width: 60px; height: 60px; background: linear-gradient(135deg, {{ $university->theme_color }}, #111827); border: 1px solid rgba(255,255,255,0.1); font-size: 1.5rem;">
                {{ $university->initials }}
            </div>
        @endif
        <div>
            <h4 class="fw-bold text-white m-0">{{ $university->name }} Performance Metrics</h4>
            <p class="text-secondary small m-0">Dynamic grading configured specifically according to {{ $university->code }} policies.</p>
        </div>
    </div>
</div>

{{-- Top Metrics Row --}}
<div class="row g-3 mb-4">
    {{-- Total Counselors --}}
    <div class="col-md-3">
        <div class="glass-card p-4 text-center">
            <span class="text-secondary small d-block mb-1">Total Executives</span>
            <h2 class="fw-bold text-white m-0">{{ $totalExecs }}</h2>
        </div>
    </div>

    {{-- Active Score --}}
    <div class="col-md-3">
        <div class="glass-card p-4 text-center">
            <span class="text-secondary small d-block mb-1">Active Avg Score</span>
            <h2 class="fw-bold m-0" style="color: {{ $university->theme_color }}">{{ $activeScore }} <span class="fs-6 text-secondary">pts</span></h2>
        </div>
    </div>

    {{-- Top Performer --}}
    <div class="col-md-3">
        <div class="glass-card p-4 text-center">
            <span class="text-secondary small d-block mb-2">Top Performer</span>
            @if($topPerformer)
                <a href="{{ route('executives.scorecard', $topPerformer->id) }}" class="fw-bold text-white text-decoration-none d-block fs-15 text-truncate" title="{{ $topPerformer->name }}">
                    {{ $topPerformer->name }}
                </a>
                <span class="text-success small fw-semibold font-monospace">{{ $topPerformer->current_score }} pts</span>
            @else
                <span class="text-secondary">—</span>
            @endif
        </div>
    </div>

    {{-- Lowest Performer --}}
    <div class="col-md-3">
        <div class="glass-card p-4 text-center">
            <span class="text-secondary small d-block mb-2">Lowest Performer</span>
            @if($lowestPerformer)
                <a href="{{ route('executives.scorecard', $lowestPerformer->id) }}" class="fw-bold text-white text-decoration-none d-block fs-15 text-truncate" title="{{ $lowestPerformer->name }}">
                    {{ $lowestPerformer->name }}
                </a>
                <span class="text-danger small fw-semibold font-monospace">{{ $lowestPerformer->current_score }} pts</span>
            @else
                <span class="text-secondary">—</span>
            @endif
        </div>
    </div>
</div>

{{-- Current Tier Breakdown Section --}}
<div class="glass-card p-4 mb-5">
    <h5 class="fw-bold text-white mb-4"><i class="fa-solid fa-users-viewfinder text-info me-2"></i>Counselor Tier Breakdown</h5>
    
    <div class="row align-items-center g-4">
        {{-- Tier Chart --}}
        <div class="col-md-5">
            <div style="height: 250px;">
                <canvas id="tierBreakdownDoughnut"></canvas>
            </div>
        </div>

        {{-- Legend and Progress Listing --}}
        <div class="col-md-7">
            <div class="d-flex flex-column gap-3">
                @php
                    $colors = [
                        'platinum' => ['bg' => 'bg-info bg-opacity-25', 'text' => '#c084fc', 'label' => 'Platinum Tier'],
                        'gold' => ['bg' => 'bg-warning bg-opacity-25', 'text' => '#f59e0b', 'label' => 'Gold Tier'],
                        'silver' => ['bg' => 'bg-secondary bg-opacity-25', 'text' => '#9ca3af', 'label' => 'Silver Tier'],
                        'bronze' => ['bg' => 'bg-amber bg-opacity-25', 'text' => '#b45309', 'label' => 'Bronze Tier'],
                        'review_zone' => ['bg' => 'bg-danger bg-opacity-25', 'text' => '#ef4444', 'label' => 'Review Zone'],
                    ];
                @endphp

                @foreach($tiers as $tierKey => $count)
                    @php
                        $meta = $colors[$tierKey] ?? ['bg' => 'bg-secondary', 'text' => '#9ca3af', 'label' => 'Unknown'];
                        $percent = $totalExecs > 0 ? ($count / $totalExecs) * 100 : 0;
                    @endphp
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-white fs-13.5 d-flex align-items-center gap-2">
                                <span class="rounded-circle" style="width: 10px; height: 10px; background: {{ $meta['text'] }}"></span>
                                {{ $meta['label'] }}
                            </span>
                            <span class="small font-monospace text-secondary">{{ $count }} Counselor(s) ({{ round($percent, 1) }}%)</span>
                        </div>
                        <div class="progress rounded-pill bg-dark" style="height: 8px;">
                            <div class="progress-bar rounded-pill" role="progressbar" 
                                 style="width: {{ $percent }}%; background-color: {{ $meta['text'] }};" 
                                 aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('tierBreakdownDoughnut').getContext('2d');
        const counts = {!! json_encode(array_values($tiers)) !!};
        const labels = {!! json_encode(array_keys($tiers)) !!};
        const colorMap = {
            platinum: '#c084fc',
            gold: '#f59e0b',
            silver: '#9ca3af',
            bronze: '#b45309',
            review_zone: '#ef4444'
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1).replace('_', ' ')),
                datasets: [{
                    data: counts,
                    backgroundColor: labels.map(l => colorMap[l] || '#6b7280'),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '75%'
            }
        });
    });
</script>
@endsection
