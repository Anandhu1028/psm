@extends('layouts.app')

@section('title', 'GM Dashboard')
@section('page_title', 'General Manager Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- Top Performers -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="fa-solid fa-arrow-trend-up text-success me-2"></i>Top Performers</h5>
            <div class="list-group list-group-flush">
                @foreach($topPerformers as $exec)
                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                    <div>
                        <div class="fw-bold">{{ $exec->name }}</div>
                        <small class="text-secondary">{{ $exec->employee_id }} ({{ $exec->zone->name }})</small>
                    </div>
                    <div class="text-end">
                        <span class="tier-badge tier-{{ $exec->current_tier }}">{{ $exec->current_tier }}</span>
                        <div class="fw-bold text-success">{{ $exec->current_score }} pts</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom Performers -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100 border border-danger border-opacity-10 bg-danger-subtle bg-opacity-5">
            <h5 class="fw-bold mb-4 text-danger"><i class="fa-solid fa-arrow-trend-down text-danger me-2"></i>Bottom Performers</h5>
            <div class="list-group list-group-flush">
                @foreach($bottomPerformers as $exec)
                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                    <div>
                        <div class="fw-bold">{{ $exec->name }}</div>
                        <small class="text-secondary">{{ $exec->employee_id }} ({{ $exec->zone->name }})</small>
                    </div>
                    <div class="text-end">
                        <span class="tier-badge tier-{{ $exec->current_tier }}">{{ $exec->current_tier }}</span>
                        <div class="fw-bold text-danger">{{ $exec->current_score }} pts</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Zone Head to Head -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4">Zone Rankings (Score Average)</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                            <th>Zone</th>
                            <th>Average score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($zoneStats as $stat)
                        <tr>
                            <td class="fw-bold">{{ $stat->name }}</td>
                            <td>
                                <span class="fw-bold text-primary">{{ round($stat->executives_avg_current_score, 1) }} pts</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active PIP Records -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4">Active PIP Records under Monitoring</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                            <th>Executive</th>
                            <th>Target</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activePips as $pip)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $pip->executive->name }}</div>
                                <small class="text-secondary">Ends: {{ $pip->end_date->toDateString() }}</small>
                            </td>
                            <td class="fw-bold text-primary">{{ $pip->target_score }} pts</td>
                            <td><span class="badge bg-warning">{{ $pip->status }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-secondary">No active PIP campaigns.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
