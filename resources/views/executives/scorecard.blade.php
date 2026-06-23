@extends('layouts.app')

@section('title', $executive->name . ' — Scorecard')
@section('page_title', 'Executive Scorecard')

@section('styles')
<style>
/* ═══════════════════════════════════════════════════
   DESIGN TOKENS
═══════════════════════════════════════════════════ */
:root {
    --sc-bg-base:     #080C1A;
    --sc-bg-surface:  #0D1224;
    --sc-bg-elevated: #111829;
    --sc-bg-overlay:  #151D30;
    --sc-border:      rgba(255,255,255,0.07);
    --sc-border-bright: rgba(255,255,255,0.13);
    --sc-text-primary:   #F0F4FF;
    --sc-text-secondary: rgba(240,244,255,0.60);
    --sc-text-muted:     rgba(240,244,255,0.38);
    --sc-blue:    #3B7BFF;
    --sc-violet:  #7C3AED;
    --sc-emerald: #10B981;
    --sc-rose:    #F43F5E;
    --sc-amber:   #F59E0B;
    --sc-cyan:    #06B6D4;
}

body {
    background: var(--sc-bg-base) !important;
    color: var(--sc-text-primary) !important;
    font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
}

/* ═══════════════════════════════════════════════════
   GLASS CARD
═══════════════════════════════════════════════════ */
.sc-card {
    background: linear-gradient(135deg,
        rgba(255,255,255,0.048) 0%,
        rgba(255,255,255,0.014) 100%) !important;
    border: 1px solid var(--sc-border) !important;
    border-radius: 18px !important;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 4px 32px rgba(0,0,0,0.40), 0 1px 4px rgba(0,0,0,0.24) !important;
    transition: border-color .2s, transform .2s;
}
.sc-card:hover {
    border-color: var(--sc-border-bright) !important;
    transform: translateY(-1px);
}

/* ═══════════════════════════════════════════════════
   HERO CARD
═══════════════════════════════════════════════════ */
.sc-hero {
    position: relative;
    overflow: hidden;
}
.sc-hero::before {
    content: "";
    position: absolute;
    top: -80px; right: -80px;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--ring, #3B7BFF) 0%, transparent 68%);
    opacity: 0.14;
    pointer-events: none;
}
.sc-hero::after {
    content: "";
    position: absolute;
    bottom: -40px; left: -40px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--sc-violet) 0%, transparent 70%);
    opacity: 0.07;
    pointer-events: none;
}

/* AVATAR */
.sc-avatar {
    width: 84px; height: 84px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem; font-weight: 800;
    position: relative; z-index: 2;
    flex-shrink: 0;
}

/* NAME + BADGES */
.sc-name {
    font-size: 1.45rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.02em;
    margin: 0;
}

/* STATUS PILL */
.sc-status {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700;
    padding: 3px 10px; border-radius: 20px;
    letter-spacing: .04em; text-transform: uppercase;
}
.sc-status-active   { background: rgba(16,185,129,.15); color: #34d399; border: 1px solid rgba(16,185,129,.3); }
.sc-status-probation{ background: rgba(245,158,11,.15); color: #fbbf24; border: 1px solid rgba(245,158,11,.3); }
.sc-status-inactive { background: rgba(255,255,255,.07); color: rgba(240,244,255,.5); border: 1px solid rgba(255,255,255,.12); }

.sc-status-dot {
    width: 6px; height: 6px; border-radius: 50%;
}

/* TIER BADGES */
.sc-tier {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 10px; font-weight: 800;
    padding: 3px 11px; border-radius: 20px;
    letter-spacing: .06em; text-transform: uppercase;
}
.sc-tier-platinum    { background: linear-gradient(135deg,rgba(168,85,247,.2),rgba(59,123,255,.12)); color: #c4b5fd; border: 1px solid rgba(168,85,247,.35); }
.sc-tier-gold        { background: rgba(234,179,8,.14); color: #fbbf24; border: 1px solid rgba(234,179,8,.35); }
.sc-tier-silver      { background: rgba(148,163,184,.12); color: #cbd5e1; border: 1px solid rgba(148,163,184,.3); }
.sc-tier-bronze      { background: rgba(194,112,61,.14); color: #f4956a; border: 1px solid rgba(194,112,61,.35); }
.sc-tier-review_zone,
.sc-tier-review-zone { background: rgba(244,63,94,.14); color: #fb7185; border: 1px solid rgba(244,63,94,.3); }
.sc-tier-unranked    { background: rgba(255,255,255,.07); color: rgba(240,244,255,.5); border: 1px solid rgba(255,255,255,.12); }
.sc-tier-none        { background: rgba(255,255,255,.07); color: rgba(240,244,255,.5); border: 1px solid rgba(255,255,255,.12); }

/* INFO CHIPS — FIXED: high-contrast text */
.sc-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 999px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.13);
    font-size: 12.5px;
    font-weight: 500;
    color: var(--sc-text-primary);   /* ← was near-invisible before */
}
.sc-chip i {
    color: var(--sc-blue);
    font-size: 11px;
    flex-shrink: 0;
}

/* SCORE RING */
.sc-score-ring {
    width: 112px; height: 112px;
    border-radius: 50%;
    background: conic-gradient(
        var(--ring-clr, #22c55e) calc(var(--pct, 50) * 1%),
        rgba(255,255,255,.07) 0
    );
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    position: relative;
}
.sc-score-ring::before {
    content: '';
    position: absolute;
    inset: 4px;
    border-radius: 50%;
    background: var(--sc-bg-surface);
}
.sc-score-ring-inner {
    position: relative;
    z-index: 2;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center;
}
.sc-score-val {
    font-size: 1.65rem;
    font-weight: 800;
    line-height: 1;
    font-variant-numeric: tabular-nums;
    letter-spacing: -.03em;
}
.sc-score-lbl {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--sc-text-muted);
    margin-top: 3px;
}

/* ═══════════════════════════════════════════════════
   SECTION HEADERS
═══════════════════════════════════════════════════ */
.sc-section-icon {
    width: 30px; height: 30px;
    border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    margin-right: 8px;
    flex-shrink: 0;
}
.sc-section-icon i { font-size: 13px; }
.sc-section-icon-blue    { background: rgba(59,123,255,.18); color: var(--sc-blue); }
.sc-section-icon-amber   { background: rgba(245,158,11,.18); color: var(--sc-amber); }
.sc-section-icon-rose    { background: rgba(244,63,94,.18);  color: var(--sc-rose); }
.sc-section-icon-cyan    { background: rgba(6,182,212,.18);  color: var(--sc-cyan); }
.sc-section-icon-emerald { background: rgba(16,185,129,.18); color: var(--sc-emerald); }

.sc-section-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--sc-text-primary);
    margin: 0;
    display: flex; align-items: center;
}

/* ═══════════════════════════════════════════════════
   LEDGER TABLE
═══════════════════════════════════════════════════ */
.sc-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.sc-table thead th {
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: var(--sc-text-muted);
    padding: 8px 12px;
    border-bottom: 1px solid var(--sc-border);
    background: var(--sc-bg-elevated);
    white-space: nowrap;
}
.sc-table thead th:first-child { border-radius: 10px 0 0 0; }
.sc-table thead th:last-child  { border-radius: 0 10px 0 0; }
.sc-table tbody td {
    padding: 11px 12px;
    font-size: 12.5px;
    color: var(--sc-text-primary);
    border-bottom: 1px solid rgba(255,255,255,.045);
    vertical-align: middle;
}
.sc-table tbody tr:last-child td { border-bottom: none; }
.sc-table tbody tr { transition: background .12s; }
.sc-table tbody tr:hover td { background: rgba(59,123,255,.035); }

/* TYPE BADGES */
.sc-type-credit {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 700;
    padding: 3px 9px; border-radius: 6px;
    background: rgba(16,185,129,.15); color: #34d399;
    border: 1px solid rgba(16,185,129,.28);
}
.sc-type-debit {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 700;
    padding: 3px 9px; border-radius: 6px;
    background: rgba(244,63,94,.15); color: #fb7185;
    border: 1px solid rgba(244,63,94,.28);
}
.sc-type-credit i, .sc-type-debit i { font-size: 9px; }

/* ═══════════════════════════════════════════════════
   TIMELINE (Tier History)
═══════════════════════════════════════════════════ */
.sc-timeline { padding: 0; list-style: none; margin: 0; }
.sc-timeline-item {
    position: relative;
    padding: 10px 0 10px 28px;
    border-bottom: 1px dashed rgba(255,255,255,.07);
}
.sc-timeline-item:last-child { border-bottom: none; padding-bottom: 0; }
.sc-timeline-item::before {
    content: '';
    position: absolute;
    left: 7px; top: 0; bottom: 0;
    width: 1px;
    background: rgba(255,255,255,.10);
}
.sc-timeline-item:last-child::before { bottom: 50%; }
.sc-timeline-dot {
    position: absolute;
    left: 3px; top: 14px;
    width: 9px; height: 9px;
    border-radius: 50%;
    background: rgba(255,255,255,.22);
    border: 1.5px solid rgba(255,255,255,.15);
    z-index: 2;
}
.sc-timeline-dot-current {
    background: var(--sc-blue);
    border-color: var(--sc-blue);
    box-shadow: 0 0 0 3px rgba(59,123,255,.25);
}

/* ═══════════════════════════════════════════════════
   LIST ITEMS (Violations / PIP / Escalations)
═══════════════════════════════════════════════════ */
.sc-list-item {
    padding: 10px 14px;
    margin-bottom: 8px;
    border-radius: 12px;
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.06);
    border-left: 3px solid transparent;
}
.sc-list-item:last-child { margin-bottom: 0; }
.sc-accent-rose    { border-left-color: #F43F5E; }
.sc-accent-amber   { border-left-color: #F59E0B; }
.sc-accent-emerald { border-left-color: #10B981; }
.sc-accent-cyan    { border-left-color: #06B6D4; }
.sc-accent-muted   { border-left-color: rgba(255,255,255,.15); }

/* PILL BADGES */
.sc-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 700;
    padding: 2px 8px; border-radius: 5px;
    letter-spacing: .04em; text-transform: uppercase;
}
.sc-badge-active   { background: rgba(244,63,94,.15); color: #fb7185; border: 1px solid rgba(244,63,94,.3); }
.sc-badge-resolved { background: rgba(16,185,129,.15); color: #34d399; border: 1px solid rgba(16,185,129,.3); }
.sc-badge-inactive { background: rgba(255,255,255,.07); color: rgba(240,244,255,.45); border: 1px solid rgba(255,255,255,.12); }
.sc-badge-open     { background: rgba(244,63,94,.15); color: #fb7185; border: 1px solid rgba(244,63,94,.3); }
.sc-badge-warning  { background: rgba(245,158,11,.15); color: #fbbf24; border: 1px solid rgba(245,158,11,.3); }
.sc-badge-success  { background: rgba(16,185,129,.15); color: #34d399; border: 1px solid rgba(16,185,129,.3); }
.sc-badge-pip-active    { background: rgba(245,158,11,.15); color: #fbbf24; border: 1px solid rgba(245,158,11,.3); }
.sc-badge-pip-completed { background: rgba(16,185,129,.15); color: #34d399; border: 1px solid rgba(16,185,129,.3); }
.sc-badge-pip-failed    { background: rgba(244,63,94,.15); color: #fb7185; border: 1px solid rgba(244,63,94,.3); }

/* PIP PROGRESS */
.sc-progress {
    height: 5px; border-radius: 999px;
    background: rgba(255,255,255,.08); overflow: hidden;
    margin: 8px 0 4px;
}
.sc-progress-bar {
    height: 100%; border-radius: 999px;
    background: linear-gradient(90deg, #06B6D4, #3B7BFF);
}

/* EMPTY STATES */
.sc-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 28px 16px; text-align: center;
    color: var(--sc-text-muted);
}
.sc-empty i { font-size: 1.4rem; margin-bottom: 8px; opacity: .45; }
.sc-empty span { font-size: 12.5px; }

/* DIVIDER */
.sc-divider {
    height: 1px;
    background: var(--sc-border);
    margin: 16px 0;
}

/* SCROLLBAR */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

@media (max-width: 768px) {
    .sc-hero-row { flex-direction: column; }
    .sc-score-ring { align-self: flex-start; }
}
@media (prefers-reduced-motion: reduce) {
    * { transition: none !important; }
}
</style>
@endsection

@section('content')

@php
    $tierColorMap = [
        'unranked'    => '#6b7280',
        'none'        => '#6b7280',
        'bronze'      => '#c2703d',
        'silver'      => '#94a3b8',
        'gold'        => '#eab308',
        'platinum'    => '#38bdf8',
        'diamond'     => '#a78bfa',
        'review_zone' => '#F43F5E',
    ];
    $ringColor = $tierColorMap[$executive->current_tier] ?? '#3B7BFF';
    $scorePct  = max(0, min(100, ($executive->current_score + 100) / 2));
    $scorePos  = $executive->current_score >= 0;

    // Normalise tier slug for CSS class
    $tierSlug = strtolower(str_replace(' ', '_', $executive->current_tier ?? 'unranked'));
    $tierLabel = str_replace('_', ' ', ucwords($tierSlug));
@endphp

<div style="max-width:1320px;margin:0 auto;">

    {{-- ══════════════════════════════════════════
         HERO CARD
    ══════════════════════════════════════════ --}}
    <div class="sc-card sc-hero p-4 mb-4" style="--ring: {{ $ringColor }};">
        <div class="d-flex align-items-center gap-4 flex-wrap sc-hero-row">

            {{-- Avatar --}}
            <div class="sc-avatar"
                 style="color: {{ $ringColor }};
                        background: {{ $ringColor }}22;
                        box-shadow: 0 0 0 3px {{ $ringColor }}44, 0 0 32px -10px {{ $ringColor }};">
                {{ strtoupper(substr($executive->name, 0, 2)) }}
            </div>

            {{-- Identity --}}
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <h3 class="sc-name">{{ $executive->name }}</h3>
                    <span class="sc-tier sc-tier-{{ $tierSlug }}">{{ $tierLabel }}</span>

                    @if($executive->status === 'active')
                        <span class="sc-status sc-status-active">
                            <span class="sc-status-dot" style="background:#10B981;"></span>Active
                        </span>
                    @elseif($executive->status === 'probation')
                        <span class="sc-status sc-status-probation">
                            <span class="sc-status-dot" style="background:#F59E0B;"></span>Probation
                        </span>
                    @else
                        <span class="sc-status sc-status-inactive">
                            <span class="sc-status-dot" style="background:#6b7280;"></span>Inactive
                        </span>
                    @endif
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <span class="sc-chip">
                        <i class="fa-solid fa-id-badge"></i>
                        {{ $executive->employee_id ?? 'N/A' }}
                    </span>
                    <span class="sc-chip">
                        <i class="fa-solid fa-envelope"></i>
                        {{ $executive->email ?? 'N/A' }}
                    </span>
                    <span class="sc-chip">
                        <i class="fa-solid fa-phone"></i>
                        {{ $executive->phone ?? 'N/A' }}
                    </span>
                    <span class="sc-chip">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $executive->zone->name ?? 'N/A' }}
                    </span>
                    <span class="sc-chip">
                        <i class="fa-solid fa-calendar-days"></i>
                        Joined {{ $executive->date_joined->format('d M Y') }}
                    </span>
                </div>
            </div>

            {{-- Score Ring --}}
            <div class="sc-score-ring"
                 style="--pct: {{ $scorePct }};
                        --ring-clr: {{ $scorePos ? '#22c55e' : '#ef4444' }};">
                <div class="sc-score-ring-inner">
                    <div class="sc-score-val" style="color: {{ $scorePos ? '#22c55e' : '#ef4444' }};">
                        {{ $scorePos ? '+' : '' }}{{ $executive->current_score }}
                    </div>
                    <div class="sc-score-lbl">points</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MIDDLE ROW: Ledger | Tier + Violations
    ══════════════════════════════════════════ --}}
    <div class="row g-4 mb-4">

        {{-- Score Transaction Ledger --}}
        <div class="col-lg-7">
            <div class="sc-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="sc-section-title">
                        <span class="sc-section-icon sc-section-icon-blue">
                            <i class="fa-solid fa-receipt"></i>
                        </span>
                        Score Transaction Ledger
                    </h5>
                    @if($executive->scoreTransactions->isNotEmpty())
                        <span style="font-size:11.5px;color:var(--sc-text-muted);">
                            {{ $executive->scoreTransactions->count() }} entries
                        </span>
                    @endif
                </div>

                <div style="overflow-x:auto;">
                    <table class="sc-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th style="text-align:right;">Points</th>
                                <th style="text-align:right;">Balance</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($executive->scoreTransactions as $tx)
                            <tr>
                                <td style="color:var(--sc-text-secondary);white-space:nowrap;font-size:12px;">
                                    {{ $tx->transaction_date->format('d M') }}
                                </td>
                                <td>
                                    @if($tx->type === 'credit')
                                        <span class="sc-type-credit">
                                            <i class="fa-solid fa-arrow-up"></i>Credit
                                        </span>
                                    @else
                                        <span class="sc-type-debit">
                                            <i class="fa-solid fa-arrow-down"></i>Debit
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:right;font-weight:700;font-variant-numeric:tabular-nums;
                                           color:{{ $tx->type === 'credit' ? '#22c55e' : '#f87171' }};">
                                    {{ $tx->type === 'credit' ? '+' : '-' }}{{ $tx->points }}
                                </td>
                                <td style="text-align:right;font-weight:600;font-variant-numeric:tabular-nums;
                                           color:var(--sc-text-primary);font-family:'Courier New',monospace;">
                                    {{ $tx->running_total }}
                                </td>
                                <td style="color:var(--sc-text-secondary);font-size:12px;max-width:220px;
                                           overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                    title="{{ $tx->description }}">
                                    {{ Str::limit($tx->description, 48) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="sc-empty">
                                        <i class="fa-regular fa-folder-open"></i>
                                        <span>No transactions recorded yet.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-5">

            {{-- Tier History --}}
            <div class="sc-card p-4 mb-4">
                <h5 class="sc-section-title mb-3">
                    <span class="sc-section-icon sc-section-icon-amber">
                        <i class="fa-solid fa-arrow-up-right-dots"></i>
                    </span>
                    Tier History
                </h5>

                @forelse($executive->tierHistories->sortByDesc('changed_at') as $th)
                    @php $changedAt = \Carbon\Carbon::parse($th->changed_at); @endphp
                    <div class="sc-timeline-item">
                        <span class="sc-timeline-dot {{ $loop->first ? 'sc-timeline-dot-current' : '' }}"></span>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <span class="sc-tier sc-tier-{{ strtolower(str_replace(' ','_',$th->old_tier)) }}"
                                      style="font-size:9.5px;padding:2px 8px;">
                                    {{ str_replace('_',' ', $th->old_tier) }}
                                </span>
                                <i class="fa-solid fa-arrow-right" style="font-size:9px;color:var(--sc-text-muted);"></i>
                                <span class="sc-tier sc-tier-{{ strtolower(str_replace(' ','_',$th->new_tier)) }}"
                                      style="font-size:9.5px;padding:2px 8px;">
                                    {{ str_replace('_',' ', $th->new_tier) }}
                                </span>
                            </div>
                            <small style="color:var(--sc-text-muted);font-size:11px;white-space:nowrap;"
                                   title="{{ $changedAt->format('d M Y H:i') }}">
                                {{ $changedAt->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="sc-empty" style="padding:16px;">
                        <i class="fa-regular fa-clock"></i>
                        <span>No tier changes recorded.</span>
                    </div>
                @endforelse
            </div>

            {{-- Violations --}}
            <div class="sc-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="sc-section-title">
                        <span class="sc-section-icon sc-section-icon-rose">
                            <i class="fa-solid fa-ban"></i>
                        </span>
                        Violations
                    </h5>
                    @if($executive->violations->count() > 0)
                        <span style="font-size:11px;color:var(--sc-text-muted);">
                            {{ $executive->violations->count() }} total
                        </span>
                    @endif
                </div>

                @forelse($executive->violations->take(5) as $v)
                    <div class="sc-list-item {{ $v->status === 'active' ? 'sc-accent-rose' : 'sc-accent-muted' }}">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--sc-text-primary);">
                                    {{ str_replace('_', ' ', ucfirst($v->violation_type)) }}
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="sc-badge {{ $v->status === 'active' ? 'sc-badge-active' : 'sc-badge-inactive' }}">
                                        {{ $v->status }}
                                    </span>
                                    <span style="font-size:11.5px;font-weight:700;color:#f87171;">
                                        −{{ $v->points_deducted }} pts
                                    </span>
                                </div>
                            </div>
                            <small style="color:var(--sc-text-muted);font-size:11px;white-space:nowrap;">
                                {{ $v->date_committed->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="sc-empty" style="padding:16px;">
                        <i class="fa-solid fa-circle-check" style="color:var(--sc-emerald);opacity:1;"></i>
                        <span>No violations on record.</span>
                    </div>
                @endforelse

                @if($executive->violations->count() > 5)
                    <div style="text-align:center;margin-top:10px;">
                        <small style="color:var(--sc-text-muted);">
                            +{{ $executive->violations->count() - 5 }} more on file
                        </small>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════
         BOTTOM ROW: PIP Records | Escalations
    ══════════════════════════════════════════ --}}
    <div class="row g-4">

        {{-- PIP Records --}}
        <!-- <div class="col-lg-6">
            <div class="sc-card p-4 h-100">
                <h5 class="sc-section-title mb-3">
                    <span class="sc-section-icon sc-section-icon-cyan">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                    PIP Records
                </h5>

                @forelse($executive->pipRecords as $pip)
                    @php
                        $totalDays   = max(1, $pip->start_date->diffInDays($pip->end_date));
                        $elapsedDays = min($totalDays, max(0, $pip->start_date->diffInDays(now())));
                        $elapsedPct  = round(($elapsedDays / $totalDays) * 100);
                        $gap         = $pip->target_score - $executive->current_score;

                        $pipBadgeClass = match($pip->status) {
                            'completed' => 'sc-badge-pip-completed',
                            'failed'    => 'sc-badge-pip-failed',
                            default     => 'sc-badge-pip-active',
                        };
                        $pipAccent = match($pip->status) {
                            'completed' => 'sc-accent-emerald',
                            'failed'    => 'sc-accent-rose',
                            default     => 'sc-accent-amber',
                        };
                    @endphp
                    <div class="sc-list-item {{ $pipAccent }}">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--sc-text-primary);">
                                    Target: {{ $pip->target_score }} pts
                                </div>
                                <small style="color:var(--sc-text-muted);font-size:11.5px;">
                                    {{ $pip->start_date->toDateString() }} → {{ $pip->end_date->toDateString() }}
                                </small>
                            </div>
                            <span class="sc-badge {{ $pipBadgeClass }}">{{ $pip->status }}</span>
                        </div>
                        @if($pip->status === 'active')
                            <div class="sc-progress">
                                <div class="sc-progress-bar" style="width:{{ $elapsedPct }}%;"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small style="color:var(--sc-text-muted);font-size:11px;">{{ $elapsedPct }}% elapsed</small>
                                <small style="font-size:11px;color:{{ $gap > 0 ? '#fbbf24' : '#22c55e' }};">
                                    {{ $gap > 0 ? $gap . ' pts below target' : 'Target met ✓' }}
                                </small>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="sc-empty">
                        <i class="fa-solid fa-circle-check" style="color:var(--sc-emerald);opacity:1;"></i>
                        <span>No PIP plans initiated.</span>
                    </div>
                @endforelse
            </div>
        </div> -->

        {{-- Escalations --}}
        <!-- <div class="col-lg-6">
            <div class="sc-card p-4 h-100">
                <h5 class="sc-section-title mb-3">
                    <span class="sc-section-icon sc-section-icon-amber">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    Escalations
                </h5>

                @forelse($executive->escalations as $esc)
                    @php
                        $escAccent = match($esc->status) {
                            'open'     => 'sc-accent-rose',
                            'resolved' => 'sc-accent-emerald',
                            default    => 'sc-accent-amber',
                        };
                        $escBadge = match($esc->status) {
                            'open'     => 'sc-badge-open',
                            'resolved' => 'sc-badge-resolved',
                            default    => 'sc-badge-warning',
                        };
                    @endphp
                    <div class="sc-list-item {{ $escAccent }}">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--sc-text-primary);">
                                    {{ str_replace('_', ' ', ucwords($esc->type)) }}
                                </div>
                                <small style="color:var(--sc-text-muted);font-size:11.5px;">
                                    {{ Str::limit($esc->trigger_reason, 58) }}
                                </small>
                            </div>
                            <span class="sc-badge {{ $escBadge }}">{{ $esc->status }}</span>
                        </div>
                    </div>
                @empty
                    <div class="sc-empty">
                        <i class="fa-solid fa-circle-check" style="color:var(--sc-emerald);opacity:1;"></i>
                        <span>No escalations raised.</span>
                    </div>
                @endforelse
            </div>
        </div> -->

    </div>

</div>
@endsection