@extends('layouts.app')

@section('title', 'PIP Records')
@section('page_title', 'Performance Improvement Plans')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-secondary m-0">Track executives placed on Performance Improvement Plans (PIP).</p>
    @can('review_pips')
    <a href="{{ route('pips.create') }}" class="btn btn-warning rounded-3 px-4 text-white fw-semibold">
        <i class="fa-solid fa-file-signature me-2"></i>Launch New PIP
    </a>
    @endcan
</div>

{{-- Status Summary --}}
<div class="row g-4 mb-4">
    @php
        $active   = $pips->where('status','active')->count();
        $completed = $pips->where('status','completed')->count();
        $failed   = $pips->where('status','failed')->count();
        $extended = $pips->where('status','extended')->count();
    @endphp
    <div class="col-md-3">
        <div class="glass-card p-4 text-center border-start border-4 border-warning">
            <div class="stat-card-value text-warning">{{ $active }}</div>
            <div class="stat-card-label">Active PIPs</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-4 text-center border-start border-4 border-success">
            <div class="stat-card-value text-success">{{ $completed }}</div>
            <div class="stat-card-label">Completed</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-4 text-center border-start border-4 border-danger">
            <div class="stat-card-value text-danger">{{ $failed }}</div>
            <div class="stat-card-label">Failed</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-4 text-center border-start border-4 border-info">
            <div class="stat-card-value text-info">{{ $extended }}</div>
            <div class="stat-card-label">Extended</div>
        </div>
    </div>
</div>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase font-monospace" style="font-size: 0.72rem;">
                <tr>
                    <th>Executive</th>
                    <th>Zone</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Target Score</th>
                    <th>Current Score</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pips as $pip)
                @php
                    $currentScore = $pip->executive->current_score;
                    $progress = $pip->target_score > 0
                        ? min(100, max(0, round(($currentScore / $pip->target_score) * 100)))
                        : 0;
                    $progressColor = $progress >= 100 ? 'success' : ($progress >= 50 ? 'warning' : 'danger');
                    $daysLeft = now()->diffInDays($pip->end_date, false);
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('executives.scorecard', $pip->executive_id) }}" class="text-decoration-none fw-bold">
                            {{ $pip->executive->name }}
                        </a>
                        <div class="text-secondary" style="font-size: 0.75rem;">{{ $pip->executive->employee_id }}</div>
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary border">{{ $pip->executive->zone->name ?? '—' }}</span></td>
                    <td><span class="fw-semibold">{{ $pip->start_date->toDateString() }}</span></td>
                    <td>
                        <span class="fw-semibold {{ $daysLeft < 7 && $pip->status === 'active' ? 'text-danger' : '' }}">
                            {{ $pip->end_date->toDateString() }}
                        </span>
                        @if($pip->status === 'active')
                        <div class="small {{ $daysLeft >= 0 ? 'text-secondary' : 'text-danger fw-bold' }}">
                            {{ $daysLeft >= 0 ? $daysLeft . ' days left' : abs($daysLeft) . ' days OVERDUE' }}
                        </div>
                        @endif
                    </td>
                    <td class="fw-bold text-primary">{{ $pip->target_score }} pts</td>
                    <td class="fw-bold {{ $currentScore >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $currentScore }} pts
                    </td>
                    <td style="min-width: 120px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar bg-{{ $progressColor }}" style="width: {{ $progress }}%; border-radius: 4px;"></div>
                            </div>
                            <small class="fw-bold text-{{ $progressColor }}">{{ $progress }}%</small>
                        </div>
                    </td>
                    <td>
                        @php
                            $statusColors = ['active'=>'warning','completed'=>'success','failed'=>'danger','extended'=>'info'];
                            $sc = $statusColors[$pip->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $sc }}-subtle text-{{ $sc }} border border-{{ $sc }}-subtle text-capitalize">
                            {{ $pip->status }}
                        </span>
                    </td>
                    <td><small class="text-secondary">{{ $pip->remarks ? Str::limit($pip->remarks, 35) : '—' }}</small></td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-secondary">
                        <i class="fa-solid fa-file-circle-check fa-2x mb-3 d-block text-muted"></i>
                        No Performance Improvement Plans found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pips->hasPages())
    <div class="d-flex justify-content-center mt-4">{{ $pips->links() }}</div>
    @endif
</div>
@endsection
