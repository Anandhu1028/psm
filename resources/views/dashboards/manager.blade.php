@extends('layouts.app')

@section('title', 'Zonal Manager Dashboard')
@section('page_title', 'Zonal Performance Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- Zone Head-to-Head Stats -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4">Zones Comparison (Score Average)</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                        <tr>
                            <th>Zone</th>
                            <th>Executives</th>
                            <th>Average Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($zoneStats as $stat)
                        <tr>
                            <td class="fw-bold">{{ $stat->name }}</td>
                            <td><span class="badge bg-secondary">{{ $stat->executives_count }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress w-100" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, max(0, $stat->executives_avg_current_score / 12)) }}%"></div>
                                    </div>
                                    <span class="fw-bold">{{ round($stat->executives_avg_current_score, 1) }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Top & Bottom Performers -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4">Top Performers in Assigned Zone</h5>
            <div class="list-group list-group-flush">
                @forelse($topPerformers as $exec)
                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $exec->name }}</div>
                            <small class="text-secondary">{{ $exec->employee_id }} ({{ $exec->zone->name }})</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="tier-badge tier-{{ $exec->current_tier }}">{{ $exec->current_tier }}</span>
                        <div class="fw-bold text-success">{{ $exec->current_score }} pts</div>
                    </div>
                </div>
                @empty
                <p class="text-secondary">No executive records found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="glass-card p-4">
    <h5 class="fw-bold mb-4">Assigned Executives Under Review</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                <tr>
                    <th>Emp ID</th>
                    <th>Name</th>
                    <th>Zone</th>
                    <th>Probation End</th>
                    <th>Tier</th>
                    <th>Score</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topPerformers->merge($bottomPerformers)->unique('id') as $exec)
                <tr>
                    <td class="font-monospace fw-bold">{{ $exec->employee_id }}</td>
                    <td>{{ $exec->name }}</td>
                    <td>{{ $exec->zone->name }}</td>
                    <td>{{ $exec->probation_end_date ? $exec->probation_end_date->toDateString() : 'N/A' }}</td>
                    <td><span class="tier-badge tier-{{ $exec->current_tier }}">{{ $exec->current_tier }}</span></td>
                    <td class="fw-bold {{ $exec->current_score >= 0 ? 'text-success' : 'text-danger' }}">{{ $exec->current_score }}</td>
                    <td>
                        <a href="{{ route('executives.scorecard', $exec->id) }}" class="btn btn-outline-primary btn-sm rounded-3">
                            <i class="fa-solid fa-eye me-1"></i> View Card
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-secondary">No executive records.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
