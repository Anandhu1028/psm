@extends('layouts.app')

@section('title', $executive->name . ' — Scorecard')
@section('page_title', 'Executive Scorecard')

@section('content')

@php
    // Decorative accent per tier — purely visual, falls back to blue for unknown tiers.
    $tierColors = [
        'unranked' => '#6b7280',
        'bronze'   => '#c2703d',
        'silver'   => '#94a3b8',
        'gold'     => '#eab308',
        'platinum' => '#38bdf8',
        'diamond'  => '#a78bfa',
    ];
    $ringColor = $tierColors[$executive->current_tier] ?? '#2563eb';

    // Map a -100..+100 balance onto a 0..100% gauge.
    $scorePct = max(0, min(100, ($executive->current_score + 100) / 2));
@endphp

<div class="xs-scorecard">

    {{-- Header Profile Card --}}
    <div class="glass-card xs-hero p-4 mb-4" style="--ring: {{ $ringColor }};">
        <div class="row align-items-center g-4">
            <div class="col-auto">
                <div class="rounded-circle d-flex align-items-center justify-content-center xs-avatar"
                     style="width: 80px; height: 80px; font-size: 1.7rem; font-weight: 800;
                            color: {{ $ringColor }};
                            background-color: {{ $ringColor }}1f;
                            box-shadow: 0 0 0 3px {{ $ringColor }}40, 0 0 28px -8px {{ $ringColor }};">
                    {{ strtoupper(substr($executive->name, 0, 2)) }}
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h3 class="fw-bold m-0">{{ $executive->name }}</h3>
                    <span class="tier-badge tier-{{ $executive->current_tier }}">
                        {{ str_replace('_', ' ', ucwords($executive->current_tier)) }}
                    </span>
                    @if($executive->status === 'active')
                        <span class="badge bg-success-subtle text-success border border-success-subtle xs-status">
                            <span class="xs-dot bg-success"></span>Active
                        </span>
                    @elseif($executive->status === 'probation')
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle xs-status">
                            <span class="xs-dot bg-warning"></span>Probation
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border xs-status">
                            <span class="xs-dot bg-secondary"></span>Inactive
                        </span>
                    @endif
                </div>
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    <span class="xs-chip"><i class="fa-solid fa-id-badge"></i>{{ $executive->employee_id }}</span>
                    <span class="xs-chip"><i class="fa-solid fa-envelope"></i>{{ $executive->email }}</span>
                    <span class="xs-chip"><i class="fa-solid fa-phone"></i>{{ $executive->phone }}</span>
                    <span class="xs-chip"><i class="fa-solid fa-map-marker-alt"></i>{{ $executive->zone->name ?? 'N/A' }}</span>
                    <span class="xs-chip"><i class="fa-solid fa-calendar"></i>Joined {{ $executive->date_joined->format('d M Y') }}</span>
                </div>
            </div>
            <div class="col-auto">
                <div class="xs-score-ring" style="--pct: {{ $scorePct }}; --ring-clr: {{ $executive->current_score >= 0 ? '#22c55e' : '#ef4444' }};">
                    <div class="xs-score-ring-inner">
                        <div class="fw-bold {{ $executive->current_score >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.6rem; line-height:1;">
                            {{ $executive->current_score >= 0 ? '+' : '' }}{{ $executive->current_score }}
                        </div>
                        <small class="text-secondary text-uppercase" style="font-size: 0.62rem; letter-spacing: .04em;">points</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Score Transactions Ledger --}}
        <div class="col-lg-7">
            <div class="glass-card xs-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold m-0">
                        <span class="xs-icon-chip text-primary"><i class="fa-solid fa-receipt"></i></span>
                        Score Transaction Ledger
                    </h5>
                    @if($executive->scoreTransactions->isNotEmpty())
                        <small class="text-secondary">{{ $executive->scoreTransactions->count() }} entries</small>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle xs-table mb-0">
                        <thead class="text-uppercase font-monospace" style="font-size: 0.68rem;">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th class="text-end">Points</th>
                                <th class="text-end">Balance</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($executive->scoreTransactions as $tx)
                            <tr class="{{ $tx->type === 'credit' ? 'xs-row-credit' : 'xs-row-debit' }}">
                                <td class="text-secondary small text-nowrap">{{ $tx->transaction_date->format('d M') }}</td>
                                <td>
                                    <span class="badge {{ $tx->type === 'credit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border xs-type-badge">
                                        <i class="fa-solid {{ $tx->type === 'credit' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                        {{ strtoupper($tx->type) }}
                                    </span>
                                </td>
                                <td class="fw-bold text-end {{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->type === 'credit' ? '+' : '-' }}{{ $tx->points }}
                                </td>
                                <td class="fw-semibold font-monospace text-end">{{ $tx->running_total }}</td>
                                <td class="text-secondary small" title="{{ $tx->description }}">{{ Str::limit($tx->description, 45) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary">
                                    <i class="fa-regular fa-folder-open d-block mb-2" style="font-size: 1.4rem; opacity:.5;"></i>
                                    No transactions recorded yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Violations + Tier History --}}
        <div class="col-lg-5">
            {{-- Tier Progression --}}
            <div class="glass-card xs-card p-4 mb-4">
                <h5 class="fw-bold mb-3">
                    <span class="xs-icon-chip text-warning"><i class="fa-solid fa-arrow-up-right-dots"></i></span>
                    Tier History
                </h5>
                @forelse($executive->tierHistories->sortByDesc('changed_at') as $th)
                    @php $changedAt = \Carbon\Carbon::parse($th->changed_at); @endphp
                    <div class="xs-timeline-item {{ $loop->last ? 'xs-timeline-item-last' : '' }}">
                        <span class="xs-timeline-dot {{ $loop->first ? 'xs-timeline-dot-current' : '' }}"></span>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                            <div>
                                <span class="tier-badge tier-{{ $th->old_tier }}" style="font-size: 0.65rem;">{{ str_replace('_',' ',$th->old_tier) }}</span>
                                <i class="fa-solid fa-arrow-right text-secondary mx-2" style="font-size: 0.7rem;"></i>
                                <span class="tier-badge tier-{{ $th->new_tier }}" style="font-size: 0.65rem;">{{ str_replace('_',' ',$th->new_tier) }}</span>
                            </div>
                            <small class="text-secondary" title="{{ $changedAt->format('d M Y') }}">{{ $changedAt->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-secondary small mb-0">
                        <i class="fa-regular fa-clock me-1"></i>No tier changes recorded.
                    </p>
                @endforelse
            </div>

            {{-- Active Violations --}}
            <div class="glass-card xs-card p-4">
                <h5 class="fw-bold mb-3">
                    <span class="xs-icon-chip text-danger"><i class="fa-solid fa-ban"></i></span>
                    Violations
                </h5>
                @forelse($executive->violations->take(5) as $v)
                <div class="xs-list-item {{ $v->status === 'active' ? 'xs-accent-danger' : 'xs-accent-muted' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="fw-semibold text-capitalize">{{ str_replace('_',' ',$v->violation_type) }}</span>
                            <span class="badge {{ $v->status === 'active' ? 'bg-danger' : 'bg-secondary' }} ms-2">{{ $v->status }}</span>
                            <div class="text-danger small fw-semibold mt-1">−{{ $v->points_deducted }} pts</div>
                        </div>
                        <small class="text-secondary text-nowrap">{{ $v->date_committed->format('d M Y') }}</small>
                    </div>
                </div>
                @empty
                <p class="text-secondary small mb-0">
                    <i class="fa-solid fa-circle-check text-success me-1"></i>No violations on record.
                </p>
                @endforelse
                @if($executive->violations->count() > 5)
                    <div class="text-center mt-2">
                        <small class="text-secondary">+{{ $executive->violations->count() - 5 }} more on file</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Active PIP + Escalations --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="glass-card xs-card p-4 h-100">
                <h5 class="fw-bold mb-3">
                    <span class="xs-icon-chip text-info"><i class="fa-solid fa-chart-line"></i></span>
                    PIP Records
                </h5>
                @forelse($executive->pipRecords as $pip)
                    @php
                        $totalDays = max(1, $pip->start_date->diffInDays($pip->end_date));
                        $elapsedDays = min($totalDays, max(0, $pip->start_date->diffInDays(now())));
                        $elapsedPct = round(($elapsedDays / $totalDays) * 100);
                        $gap = $pip->target_score - $executive->current_score;
                    @endphp
                    <div class="xs-list-item xs-accent-info">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-semibold">Target: {{ $pip->target_score }} pts</div>
                                <small class="text-secondary">{{ $pip->start_date->toDateString() }} → {{ $pip->end_date->toDateString() }}</small>
                            </div>
                            <span class="badge {{ $pip->status === 'active' ? 'bg-warning text-dark' : ($pip->status === 'completed' ? 'bg-success' : 'bg-danger') }}">
                                {{ $pip->status }}
                            </span>
                        </div>
                        @if($pip->status === 'active')
                            <div class="xs-progress mb-1">
                                <div class="xs-progress-bar" style="width: {{ $elapsedPct }}%;"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-secondary">{{ $elapsedPct }}% of period elapsed</small>
                                <small class="{{ $gap > 0 ? 'text-warning' : 'text-success' }}">
                                    {{ $gap > 0 ? $gap . ' pts below target' : 'Target met' }}
                                </small>
                            </div>
                        @endif
                    </div>
                @empty
                <p class="text-secondary small mb-0">
                    <i class="fa-solid fa-circle-check text-success me-1"></i>No PIP plans initiated.
                </p>
                @endforelse
            </div>
        </div>
        <div class="col-lg-6">
            <div class="glass-card xs-card p-4 h-100">
                <h5 class="fw-bold mb-3">
                    <span class="xs-icon-chip text-warning"><i class="fa-solid fa-bell"></i></span>
                    Escalations
                </h5>
                @forelse($executive->escalations as $esc)
                    @php
                        $escAccent = $esc->status === 'open' ? 'xs-accent-danger' : ($esc->status === 'resolved' ? 'xs-accent-success' : 'xs-accent-warning');
                    @endphp
                    <div class="xs-list-item {{ $escAccent }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold text-capitalize">{{ str_replace('_',' ',$esc->type) }}</div>
                                <small class="text-secondary">{{ Str::limit($esc->trigger_reason, 55) }}</small>
                            </div>
                            <span class="badge {{ $esc->status === 'open' ? 'bg-danger' : ($esc->status === 'resolved' ? 'bg-success' : 'bg-warning text-dark') }}">
                                {{ $esc->status }}
                            </span>
                        </div>
                    </div>
                @empty
                <p class="text-secondary small mb-0">
                    <i class="fa-solid fa-circle-check text-success me-1"></i>No escalations raised.
                </p>
                @endforelse
            </div>
        </div>
    </div>

</div>

<style>
.xs-scorecard {
    --xs-accent: #2563eb;
}

/* Hero */
.xs-hero {
    position: relative;
    overflow: hidden;
}
.xs-hero::before {
    content: "";
    position: absolute;
    top: -60px;
    right: -60px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--ring, var(--xs-accent)) 0%, transparent 70%);
    opacity: 0.18;
    pointer-events: none;
}
.xs-status { display: inline-flex; align-items: center; gap: .4rem; }
.xs-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }

/* Contact chips */
.xs-chip {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .3rem .65rem;
    border-radius: 999px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    font-size: .78rem;
    color: var(--bs-secondary-color, #94a3b8);
}
.xs-chip i { color: var(--xs-accent); font-size: .72rem; }

/* Score gauge */
.xs-score-ring {
    width: 104px;
    height: 104px;
    border-radius: 50%;
    background: conic-gradient(var(--ring-clr) calc(var(--pct) * 1%), rgba(255,255,255,.08) 0);
    display: flex;
    align-items: center;
    justify-content: center;
}
.xs-score-ring-inner {
    width: 82px;
    height: 82px;
    border-radius: 50%;
    background: rgba(10,10,18,.75);
    backdrop-filter: blur(6px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Section icon chips */
.xs-icon-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: currentColor;
    margin-right: .5rem;
    vertical-align: middle;
}
.xs-icon-chip i { color: #fff; filter: brightness(0) invert(1); }
.xs-icon-chip { background-color: rgba(255,255,255,.06); }

/* Cards */
.xs-card { transition: transform .15s ease, box-shadow .15s ease; }
.xs-card:hover { transform: translateY(-2px); }

/* Table */
.xs-table thead th { color: var(--bs-secondary-color, #94a3b8); border-bottom: 1px solid rgba(255,255,255,.08); }
.xs-table td { border-bottom: 1px solid rgba(255,255,255,.06); }
.xs-table tbody tr { transition: background-color .12s ease; }
.xs-table tbody tr:hover { background-color: rgba(255,255,255,.03); }
.xs-type-badge { display: inline-flex; align-items: center; gap: .3rem; font-size: .68rem; }
.xs-type-badge i { font-size: .6rem; }

/* Timeline */
.xs-timeline-item {
    position: relative;
    padding: .65rem 0 .65rem 1.4rem;
    border-bottom: 1px dashed rgba(255,255,255,.08);
}
.xs-timeline-item-last { border-bottom: none; }
.xs-timeline-item::before {
    content: "";
    position: absolute;
    left: 5px;
    top: 0;
    bottom: -1px;
    width: 1px;
    background: rgba(255,255,255,.12);
}
.xs-timeline-item-last::before { bottom: 50%; }
.xs-timeline-dot {
    position: absolute;
    left: 1px;
    top: .9rem;
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: rgba(255,255,255,.25);
}
.xs-timeline-dot-current {
    background: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.25);
}

/* Generic accented list item, used for violations / PIP / escalations */
.xs-list-item {
    padding: .65rem .75rem;
    margin-bottom: .5rem;
    border-radius: 10px;
    background: rgba(255,255,255,.025);
    border-left: 3px solid transparent;
}
.xs-list-item:last-child { margin-bottom: 0; }
.xs-accent-danger  { border-left-color: #ef4444; }
.xs-accent-warning { border-left-color: #eab308; }
.xs-accent-success { border-left-color: #22c55e; }
.xs-accent-info    { border-left-color: #38bdf8; }
.xs-accent-muted   { border-left-color: rgba(255,255,255,.15); }

/* PIP progress bar */
.xs-progress {
    height: 6px;
    border-radius: 999px;
    background: rgba(255,255,255,.08);
    overflow: hidden;
}
.xs-progress-bar {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #38bdf8, #2563eb);
}

@media (prefers-reduced-motion: reduce) {
    .xs-scorecard * { transition: none !important; }
}
</style>

@endsection