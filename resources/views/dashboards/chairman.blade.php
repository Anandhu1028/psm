@extends('layouts.app')

@section('title', 'Chairman Dashboard')
@section('page_title', 'Chairman Strategic Dashboard')

@section('content')

{{-- KPI Summary Row --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="glass-card p-4 text-center h-100">
            <i class="fa-solid fa-star fa-2x text-warning mb-3"></i>
            <div class="stat-card-value text-warning">{{ $promotionEligible->count() }}</div>
            <div class="stat-card-label">Promotion Eligible</div>
            <small class="text-secondary">Platinum tier executives</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-4 text-center h-100 border border-danger border-opacity-25">
            <i class="fa-solid fa-triangle-exclamation fa-2x text-danger mb-3"></i>
            <div class="stat-card-value text-danger">{{ $reviewZone->count() }}</div>
            <div class="stat-card-label">Review Zone</div>
            <small class="text-secondary">Negative score executives</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-4 text-center h-100 border border-warning border-opacity-25">
            <i class="fa-solid fa-bell fa-2x text-warning mb-3"></i>
            <div class="stat-card-value text-warning">{{ $escalations->count() }}</div>
            <div class="stat-card-label">Open Escalations</div>
            <small class="text-secondary">Requiring attention</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-4 text-center h-100">
            <i class="fa-solid fa-users fa-2x text-primary mb-3"></i>
            <div class="stat-card-value text-primary">{{ $totalActive }}</div>
            <div class="stat-card-label">Active Executives</div>
            <small class="text-secondary">{{ $pipCount }} on PIP currently</small>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Platinum Performers (Promotion Eligible) --}}
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-1"><i class="fa-solid fa-star text-warning me-2"></i>Promotion Eligible — Platinum Tier</h5>
            <p class="text-secondary small mb-4">Executives achieving 1200+ cumulative score</p>
            @forelse($promotionEligible as $exec)
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-medal text-warning"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $exec->name }}</div>
                        <small class="text-secondary">{{ $exec->employee_id }} &bull; {{ $exec->zone->name }}</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold fs-5 text-warning">{{ $exec->current_score }}</div>
                    <small class="text-secondary">points</small>
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <i class="fa-solid fa-star fa-2x text-secondary mb-2 d-block"></i>
                <p class="text-secondary">No executives have reached Platinum tier yet.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Open Escalations --}}
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100 border border-warning border-opacity-25">
            <h5 class="fw-bold mb-1 text-danger"><i class="fa-solid fa-bell text-danger me-2"></i>Open Escalation Alerts</h5>
            <p class="text-secondary small mb-4">System-detected performance flags requiring action</p>
            @forelse($escalations as $esc)
            <div class="d-flex justify-content-between align-items-start py-3 border-bottom">
                <div>
                    <div class="fw-bold">{{ $esc->executive->name }}
                        <span class="badge ms-2 
                            {{ $esc->severity === 'high' ? 'bg-danger' : ($esc->severity === 'medium' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ strtoupper($esc->severity) }}
                        </span>
                    </div>
                    <small class="text-secondary d-block">{{ $esc->executive->employee_id }}</small>
                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                        {{ str_replace('_', ' ', strtoupper($esc->type)) }}
                    </small>
                    <p class="text-secondary small mt-1 mb-0">{{ Str::limit($esc->trigger_reason, 80) }}</p>
                </div>
                <div class="text-end ms-3">
                    <small class="text-secondary">{{ $esc->created_at->diffForHumans() }}</small>
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <i class="fa-solid fa-circle-check fa-2x text-success mb-2 d-block"></i>
                <p class="text-secondary">No open escalations at this time.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Review Zone Executives --}}
<div class="glass-card p-4">
    <h5 class="fw-bold mb-1 text-danger"><i class="fa-solid fa-circle-xmark text-danger me-2"></i>Review Zone Executives</h5>
    <p class="text-secondary small mb-4">Executives with negative cumulative score — immediate attention required</p>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Zone</th>
                    <th>Probation End</th>
                    <th>Current Score</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviewZone as $exec)
                <tr>
                    <td class="font-monospace fw-bold text-danger">{{ $exec->employee_id }}</td>
                    <td class="fw-semibold">{{ $exec->name }}</td>
                    <td>{{ $exec->zone->name }}</td>
                    <td>{{ $exec->probation_end_date?->toDateString() ?? 'N/A' }}</td>
                    <td>
                        <span class="fw-bold text-danger fs-6">{{ $exec->current_score }}</span>
                        <span class="tier-badge tier-review_zone ms-2">Review Zone</span>
                    </td>
                    <td>
                        <a href="{{ route('executives.scorecard', $exec->id) }}" class="btn btn-outline-danger btn-sm rounded-3">
                            <i class="fa-solid fa-eye me-1"></i> View Scorecard
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-secondary">
                        <i class="fa-solid fa-circle-check text-success me-2"></i>No executives currently in Review Zone.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
