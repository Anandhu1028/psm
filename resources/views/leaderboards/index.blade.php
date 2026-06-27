@extends('layouts.app')
@section('title', 'Leaderboard')
@section('breadcrumb')
<ol class="breadcrumb"><li class="breadcrumb-item active">Leaderboard</li></ol>
@endsection

@push('styles')
<style>
/* ══════════════════════════════════════════════════
   LEADERBOARD — ULTRA PREMIUM LIGHT  (v3)
   Ref: podium pedestals + gem scores + clean table
   ══════════════════════════════════════════════════ */

.lb-shell { max-width: 98%; margin: 0 auto; font-family: 'Inter',sans-serif; }

/* ── Top Bar ─────────────────────────────────── */
.lb-topbar {
    display: flex; align-items: center;
    justify-content: space-between;
    gap: 16px; flex-wrap: wrap;
    margin-bottom: 26px;
}
.lb-page-title {
    font-size: 1.6rem; font-weight: 900;
    color: #0f0f1a; letter-spacing: -0.04em; margin: 0 0 3px;
}
.lb-page-sub { font-size: 0.78rem; color: #94a3b8; font-weight: 500; margin: 0; }
.lb-topbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* Period toggle pill */
.lb-toggle {
    display: flex; background: #f1f5f9;
    border-radius: 100px; padding: 4px; gap: 2px;
}
.lb-toggle-btn {
    height: 32px; padding: 0 18px;
    border-radius: 100px; font-size: 0.78rem; font-weight: 700;
    border: none; cursor: pointer; transition: all 0.2s;
    color: #64748b; background: transparent;
}
.lb-toggle-btn.active {
    background: #fff; color: #4f46e5;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
}

/* Action buttons */
.btn-lb {
    display: inline-flex; align-items: center; gap: 7px;
    height: 38px; padding: 0 18px;
    border-radius: 10px; font-size: 0.81rem; font-weight: 600;
    cursor: pointer; text-decoration: none; border: none;
    transition: all 0.18s; white-space: nowrap;
}
.btn-lb-outline {
    background: #fff; color: #374151;
    border: 1px solid rgba(99, 102, 241, 0.15) !important;
}
.btn-lb-outline:hover { border-color: #6366f1 !important; color: #6366f1; text-decoration: none; transform: translateY(-1px); }
.btn-lb-primary { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3); }
.btn-lb-primary:hover { background: linear-gradient(135deg, #4f46e5, #3b82f6); color: #fff; text-decoration: none; transform: translateY(-2px); }

/* ── Filter Panel ─────────────────────────────── */
.lb-filter-panel {
    background: #fff; border-radius: 16px;
    border: 1px solid rgba(99, 102, 241, 0.08);
    padding: 20px 24px; margin-bottom: 24px;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.04);
    display: none; animation: slideDown 0.22s ease;
}
.lb-filter-panel.show { display: block; }
@keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
.lb-filter-panel .form-label { font-size: 0.63rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em; color: #94a3b8; margin-bottom: 5px; }
.lb-filter-panel .form-select,
.lb-filter-panel .form-control { height: 38px; border-radius: 9px !important; font-size: 0.81rem !important; border: 1.5px solid #edf0f7 !important; background: #fafbff !important; color: #2d3748 !important; box-shadow: none !important; }
.lb-filter-panel .form-select:focus,
.lb-filter-panel .form-control:focus { border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99,102,241,0.09) !important; }

/* ── Podium Section ───────────────────────────── */
.lb-podium-wrap {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.03) 0%, rgba(139, 92, 246, 0.03) 100%), #ffffff;
    border-radius: 24px;
    border: 1px solid rgba(99, 102, 241, 0.08);
    padding: 50px 40px 0;
    margin-bottom: 26px;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.04), inset 0 1px 0 #ffffff;
    position: relative;
}
.lb-podium-wrap::after {
    content: '';
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 40px;
    background: linear-gradient(to top, rgba(255,255,255,0.5), transparent);
    pointer-events: none;
}

/* Stage */
.lb-stage {
    display: flex; align-items: flex-end;
    justify-content: center; gap: 0;
    position: relative; z-index: 1;
}

/* Individual slot */
.lb-ps { text-align: center; display: flex; flex-direction: column; align-items: center; }
.lb-ps-1 { order: 2; flex: 0 0 280px; }
.lb-ps-2 { order: 1; flex: 0 0 220px; }
.lb-ps-3 { order: 3; flex: 0 0 220px; }

/* Avatar (large, rounded square like ref) */
.lb-ps-avatar {
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 900; color: #fff;
    border: 4px solid #fff;
    position: relative;
    margin: 0 auto 12px;
    font-size: 1.1rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.14);
}
.lb-ps-1 .lb-ps-avatar {
    width: 90px; height: 90px; font-size: 1.35rem;
    background: linear-gradient(135deg, #6366f1, #7c3aed);
    box-shadow: 0 12px 40px rgba(99, 102, 241, 0.35);
}
.lb-ps-1 .lb-ps-avatar::before {
    content: '👑';
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 1.8rem;
    filter: drop-shadow(0 4px 6px rgba(245, 158, 11, 0.3));
    animation: floatCrown 3s ease-in-out infinite;
    z-index: 10;
}
@keyframes floatCrown {
    0%, 100% { transform: translate(-50%, 0) rotate(-4deg); }
    50% { transform: translate(-50%, -6px) rotate(4deg); }
}

.lb-ps-2 .lb-ps-avatar {
    width: 72px; height: 72px;
    background: linear-gradient(135deg, #94a3b8, #64748b);
    box-shadow: 0 8px 24px rgba(100,116,139,0.22);
}
.lb-ps-3 .lb-ps-avatar {
    width: 72px; height: 72px;
    background: linear-gradient(135deg, #f97316, #ea580c);
    box-shadow: 0 8px 24px rgba(249,115,22,0.22);
}

.lb-ps-name {
    font-size: 0.9rem; font-weight: 800; color: #1e1f2e;
    letter-spacing: -0.02em; margin-bottom: 4px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    max-width: 180px;
}
.lb-ps-1 .lb-ps-name { font-size: 1.05rem; max-width: 240px; }
.lb-ps-zone { font-size: 0.65rem; color: #94a3b8; font-weight: 600; margin-bottom: 14px; display: flex; align-items: center; justify-content: center; gap: 3px; }

/* Score gem pill */
.lb-gem-score {
    display: inline-flex; align-items: center; gap: 7px;
    margin-bottom: 16px;
}
.lb-gem-icon { font-size: 1.1rem; line-height: 1; }
.lb-ps-1 .lb-gem-icon { font-size: 1.3rem; }
.lb-gem-val {
    font-size: 1.4rem; font-weight: 900;
    letter-spacing: -0.04em; color: #1e1f2e;
}
.lb-ps-1 .lb-gem-val { font-size: 1.8rem; color: #4f46e5; }
.lb-ps-2 .lb-gem-val { color: #475569; }
.lb-ps-3 .lb-gem-val { color: #ea580c; }
.lb-gem-lbl { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-top: -2px; }

/* Pedestal block (like the reference) */
.lb-pedestal {
    width: 100%;
    border-radius: 16px 16px 0 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: flex-start;
    padding-top: 18px; gap: 4px;
    backdrop-filter: blur(8px);
}
.lb-ps-1 .lb-pedestal {
    height: 120px;
    background: linear-gradient(180deg, rgba(245, 158, 11, 0.12) 0%, rgba(245, 158, 11, 0.03) 100%);
    border: 1.5px solid rgba(245, 158, 11, 0.25);
    border-bottom: none;
    box-shadow: 0 -4px 20px rgba(245,158,11,0.06);
}
.lb-ps-2 .lb-pedestal {
    height: 88px;
    background: linear-gradient(180deg, rgba(148, 163, 184, 0.12) 0%, rgba(148, 163, 184, 0.03) 100%);
    border: 1.5px solid rgba(148, 163, 184, 0.25);
    border-bottom: none;
    box-shadow: 0 -4px 14px rgba(148,163,184,0.06);
}
.lb-ps-3 .lb-pedestal {
    height: 66px;
    background: linear-gradient(180deg, rgba(249, 115, 22, 0.12) 0%, rgba(249, 115, 22, 0.03) 100%);
    border: 1.5px solid rgba(249, 115, 22, 0.25);
    border-bottom: none;
    box-shadow: 0 -4px 14px rgba(249,115,22,0.06);
}
.lb-pedestal-trophy { font-size: 1.3rem; line-height: 1; }
.lb-ps-1 .lb-pedestal-trophy { font-size: 1.6rem; }
.lb-pedestal-num {
    font-size: 0.7rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.08em;
}
.lb-ps-1 .lb-pedestal-num { color: #b45309; }
.lb-ps-2 .lb-pedestal-num { color: #475569; }
.lb-ps-3 .lb-pedestal-num { color: #c2410c; }

/* ── Rankings Table Card ─────────────────────── */
.lb-tbl-card {
    background: #fff; border-radius: 20px;
    border: 1px solid rgba(99, 102, 241, 0.08);
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.04);
}
.lb-tbl-head {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 20px 28px 18px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(to right, #fafbff, #fff);
    gap: 12px; flex-wrap: wrap;
}
.lb-tbl-head-left { display: flex; align-items: center; gap: 10px; }
.lb-tbl-icon {
    width: 38px; height: 38px; border-radius: 11px;
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    display: flex; align-items: center; justify-content: center;
    color: #4f46e5; font-size: 0.85rem;
}
.lb-tbl-title { font-size: 0.94rem; font-weight: 800; color: #1e1f2e; }
.lb-count-pill {
    height: 22px; padding: 0 10px;
    background: #eff6ff; border: 1px solid #dbeafe;
    border-radius: 100px; font-size: 0.64rem; font-weight: 800; color: #2563eb;
    display: inline-flex; align-items: center;
}
.lb-period-pill {
    height: 30px; padding: 0 13px;
    background: #fffbeb; border: 1px solid #fef3c7;
    border-radius: 8px; font-size: 0.72rem; font-weight: 600; color: #b45309;
    display: inline-flex; align-items: center; gap: 5px;
}

/* Table */
.lb-table { width: 100%; border-collapse: collapse; font-size: 0.83rem; }
.lb-table thead tr {
    background: linear-gradient(to right, #fafbff, #f8f9ff);
    border-bottom: 2px solid #f0f2fa;
}
.lb-table thead th {
    padding: 13px 18px; font-size: 0.59rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.1em; color: #b0b8d1;
    white-space: nowrap;
}
.lb-table thead th:first-child { padding-left: 28px; }
.lb-table thead th:last-child  { padding-right: 28px; }
.lb-table tbody tr { border-bottom: 1px solid #f7f8fc; transition: background 0.13s; }
.lb-table tbody tr:last-child { border-bottom: none; }
.lb-table tbody tr:hover { background: rgba(99, 102, 241, 0.02) !important; }
.lb-table tbody td { padding: 15px 18px; vertical-align: middle; color: #374151; }
.lb-table tbody td:first-child { padding-left: 28px; }
.lb-table tbody td:last-child  { padding-right: 28px; }

/* Top row accents */
.lb-tr-1 { background: linear-gradient(to right,#fffef2,#fff) !important; }
.lb-tr-2 { background: linear-gradient(to right,#fafbfe,#fff) !important; }
.lb-tr-3 { background: linear-gradient(to right,#fffcf8,#fff) !important; }
.lb-tr-1 td:first-child { border-left: 3px solid #f59e0b; }
.lb-tr-2 td:first-child { border-left: 3px solid #94a3b8; }
.lb-tr-3 td:first-child { border-left: 3px solid #f97316; }

/* Rank circle */
.lb-rank-circle {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; font-weight: 900; flex-shrink: 0;
}
.lbr-1 { background: rgba(245, 158, 11, 0.12); color: #b45309; border: 1.5px solid rgba(245, 158, 11, 0.25); }
.lbr-2 { background: rgba(148, 163, 184, 0.12); color: #475569; border: 1.5px solid rgba(148, 163, 184, 0.25); }
.lbr-3 { background: rgba(249, 115, 22, 0.12); color: #c2410c; border: 1.5px solid rgba(249, 115, 22, 0.25); }
.lbr-n { background: #f8f9fc; color: #94a3b8; border: 1.5px solid #edf0f7; font-size: 0.76rem; }

/* Executive cell */
.lb-exec-cell { display: flex; align-items: center; gap: 12px; }
.lb-exec-av {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #7c3aed 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.68rem; font-weight: 800; color: #fff; flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(99,102,241,0.22);
}
.lb-exec-name {
    font-size: 0.86rem; font-weight: 700; color: #1e1f2e;
    text-decoration: none; display: block; transition: color 0.15s; white-space: nowrap;
}
.lb-exec-name:hover { color: #4f46e5; text-decoration: none; }
.lb-exec-id { font-size: 0.62rem; color: #c4c9d9; font-family: 'Consolas',monospace; margin-top: 1px; }

/* Zone cell */
.lb-zone-v { font-size: 0.79rem; font-weight: 600; color: #374151; }
.lb-co-v   { font-size: 0.64rem; color: #94a3b8; font-weight: 500; margin-top: 2px; }

/* Chips */
.lbc {
    display: inline-flex; align-items: center; gap: 3px;
    height: 26px; padding: 0 9px; border-radius: 7px;
    font-size: 0.74rem; font-weight: 800; white-space: nowrap;
}
.lbc-pos { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.lbc-neg { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
.lbc-rec { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }

/* Gem net score cell */
.lb-net-cell {
    display: flex; align-items: center; justify-content: flex-end;
    gap: 6px;
}
.lb-net-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 26px;
    padding: 0 12px;
    border-radius: 100px;
    font-size: 0.81rem;
    font-weight: 800;
    white-space: nowrap;
}
.lb-net-badge.pos {
    background: #e6fbf7;
    color: #0d9488;
    border: 1px solid #ccfbf1;
}
.lb-net-badge.neg {
    background: #fdf2f4;
    color: #e11d48;
    border: 1px solid #ffe4e6;
}

/* Tier */
.lb-tier-badge {
    display: inline-flex; align-items: center;
    height: 24px; padding: 0 10px; border-radius: 7px;
    font-size: 0.65rem; font-weight: 700; white-space: nowrap;
}

/* KPI */
.lb-kpi { font-size: 0.78rem; font-weight: 700; color: #374151; white-space: nowrap; }
.lb-kpi span { color: #94a3b8; font-weight: 500; }

/* Empty */
.lb-empty { text-align: center; padding: 80px 24px; }
.lb-empty-icon { font-size: 3rem; margin-bottom: 14px; }
.lb-empty-h { font-size: 0.96rem; font-weight: 800; color: #1e1f2e; margin-bottom: 5px; }
.lb-empty-p { font-size: 0.8rem; color: #94a3b8; }
.lb-empty-p a { color: #4f46e5; font-weight: 600; text-decoration: none; }

/* Pagination strip */
.lb-pager {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 28px; border-top: 1px solid #f0f2fa;
    background: linear-gradient(to right,#fafbff,#fff);
    flex-wrap: wrap; gap: 10px;
}
.lb-pager-info { font-size: 0.73rem; color: #94a3b8; font-weight: 500; }
.lb-pager nav ul { margin: 0; }

/* Responsive */
@media (max-width:900px) {
    .lb-ps-1 { flex: 0 0 200px; }
    .lb-ps-2, .lb-ps-3 { flex: 0 0 160px; }
    .lb-table thead th:nth-child(n+5) { display: none; }
    .lb-table tbody td:nth-child(n+5) { display: none; }
}
@media (max-width:600px) {
    .lb-podium-wrap { padding: 24px 12px 0; }
    .lb-stage { gap: 0; }
    .lb-ps-1 { flex: 0 0 140px; }
    .lb-ps-2, .lb-ps-3 { flex: 0 0 110px; }
    .lb-topbar { flex-direction: column; align-items: flex-start; }
}
</style>
@endpush

@section('content')
<div class="lb-shell">

{{-- ══ TOP BAR ══ --}}
<div class="lb-topbar">
    <div>
        <h1 class="lb-page-title">🏆 Leaderboard</h1>
        <p class="lb-page-sub">Rankings for {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</p>
    </div>
    <div class="lb-topbar-right">
        {{-- Period toggle --}}
        
        {{-- Filter --}}
        <button class="btn-lb btn-lb-outline" type="button" onclick="document.getElementById('lbFilter').classList.toggle('show')">
            <i class="fa-solid fa-sliders"></i> Filter
        </button>
        {{-- Refresh --}}
        @can('manage_executives')
        <form action="{{ route('leaderboards.refresh') }}" method="POST" style="display:inline;">
            @csrf
            <input type="hidden" name="year"  value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <button type="submit" class="btn-lb btn-lb-outline">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh
            </button>
        </form>
        @endcan
        {{-- Export --}}
        <a href="{{ route('reports.export', ['type'=>'monthly','year'=>$year,'month'=>$month]) }}" class="btn-lb btn-lb-primary">
            <i class="fa-solid fa-file-pdf"></i> Export
        </a>
    </div>
</div>

{{-- ══ FILTER PANEL ══ --}}
<div class="lb-filter-panel" id="lbFilter">
    <form method="GET" action="{{ route('leaderboards.index') }}">
        <input type="hidden" name="year" id="yearField" value="{{ $year }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-select">
                    <option value="">All Companies</option>
                    @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Zone</label>
                <select name="zone_id" class="form-select">
                    <option value="">All Zones</option>
                    @foreach($zones as $z)
                    <option value="{{ $z->id }}" {{ request('zone_id')==$z->id?'selected':'' }}>{{ $z->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <select name="month" class="form-select" id="monthSelect">
                    @foreach($months as $m)
                    <option value="{{ $m['month'] }}" data-year="{{ $m['year'] }}"
                        {{ $month==$m['month'] && $year==$m['year'] ? 'selected' : '' }}>
                        {{ $m['label'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn-lb btn-lb-primary flex-grow-1">
                    <i class="fa-solid fa-filter"></i> Apply
                </button>
                <a href="{{ route('leaderboards.index') }}" class="btn-lb btn-lb-outline" title="Reset">
                    <i class="fa-solid fa-rotate"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ══ PODIUM ══ --}}
@if($entries->count() >= 3)
@php
    $e1 = $entries->get(0);
    $e2 = $entries->get(1);
    $e3 = $entries->get(2);
    $av = fn($e) => strtoupper(implode('', array_map(fn($w)=>substr($w,0,1), array_slice(explode(' ',trim($e->executive->name)),0,2))));
@endphp
<div class="lb-podium-wrap">
    <div class="lb-stage">

        {{-- 2nd --}}
        <div class="lb-ps lb-ps-2">
            <div class="lb-ps-avatar">{{ $av($e2) }}</div>
            <div class="lb-ps-name">{{ $e2->executive->name }}</div>
            <div class="lb-ps-zone">
                <i class="fa-solid fa-location-dot" style="color:#c4b5fd;font-size:.6rem;"></i>
                {{ $e2->executive->zone->name ?? '—' }}
            </div>
            <div class="lb-gem-score">
                <div>
                    <div class="lb-gem-val">{{ number_format($e2->total_score) }}</div>
                    <div class="lb-gem-lbl">Points</div>
                </div>
            </div>
            <div class="lb-pedestal">
                <div class="lb-pedestal-trophy">🥈</div>
                <div class="lb-pedestal-num">2nd Place</div>
            </div>
        </div>

        {{-- 1st --}}
        <div class="lb-ps lb-ps-1">
            <div class="lb-ps-avatar">{{ $av($e1) }}</div>
            <div class="lb-ps-name">{{ $e1->executive->name }}</div>
            <div class="lb-ps-zone">
                <i class="fa-solid fa-location-dot" style="color:#c4b5fd;font-size:.6rem;"></i>
                {{ $e1->executive->zone->name ?? '—' }}
            </div>
            <div class="lb-gem-score">
                <div>
                    <div class="lb-gem-val">{{ number_format($e1->total_score) }}</div>
                    <div class="lb-gem-lbl">Points</div>
                </div>
            </div>
            <div class="lb-pedestal">
                <div class="lb-pedestal-trophy">🏆</div>
                <div class="lb-pedestal-num">1st Place</div>
            </div>
        </div>

        {{-- 3rd --}}
        <div class="lb-ps lb-ps-3">
            <div class="lb-ps-avatar">{{ $av($e3) }}</div>
            <div class="lb-ps-name">{{ $e3->executive->name }}</div>
            <div class="lb-ps-zone">
                <i class="fa-solid fa-location-dot" style="color:#c4b5fd;font-size:.6rem;"></i>
                {{ $e3->executive->zone->name ?? '—' }}
            </div>
            <div class="lb-gem-score">
                <div>
                    <div class="lb-gem-val">{{ number_format($e3->total_score) }}</div>
                    <div class="lb-gem-lbl">Points</div>
                </div>
            </div>
            <div class="lb-pedestal">
                <div class="lb-pedestal-trophy">🥉</div>
                <div class="lb-pedestal-num">3rd Place</div>
            </div>
        </div>

    </div>
</div>
@endif

{{-- ══ FULL RANKINGS TABLE ══ --}}
<div class="lb-tbl-card">
    <div class="lb-tbl-head">
        <div class="lb-tbl-head-left">
            <div class="lb-tbl-icon"><i class="fa-solid fa-list-ol"></i></div>
            <span class="lb-tbl-title">Full Rankings</span>
            <span class="lb-count-pill">{{ $entries->total() ?? $entries->count() }} executives</span>
        </div>
        <span class="lb-period-pill">
            <i class="fa-regular fa-calendar" style="color:#f59e0b;"></i>
            {{ \Carbon\Carbon::create($year,$month)->format('F Y') }}
        </span>
    </div>

    <div class="lb-table-scroll">
        <table class="lb-table">
            <thead>
                <tr>
                    <th style="width:60px;">Rank</th>
                    <th style="min-width:200px;">Executive</th>
                    <th style="min-width:150px;">Zone / Company</th>
                    <th style="text-align:center;">
                        <i class="fa-solid fa-plus" style="color:#10b981;margin-right:3px;font-size:.5rem;"></i>Positive
                    </th>
                    <th style="text-align:center;">
                        <i class="fa-solid fa-minus" style="color:#f43f5e;margin-right:3px;font-size:.5rem;"></i>Negative
                    </th>
                    <th style="text-align:center;">
                        <i class="fa-solid fa-rotate-right" style="color:#3b82f6;margin-right:3px;font-size:.5rem;"></i>Recovery
                    </th>
                    <th style="text-align:center;min-width:90px;">Tier</th>
                    <th style="text-align:center;min-width:90px;">KPI Days</th>
                    <th style="text-align:right;min-width:110px;">Net Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                @php
                    $words    = explode(' ', trim($entry->executive->name));
                    $initials = implode('', array_map(fn($w) => strtoupper(substr($w,0,1)), array_slice($words,0,2)));
                    $trCls    = $entry->rank===1?'lb-tr-1':($entry->rank===2?'lb-tr-2':($entry->rank===3?'lb-tr-3':''));
                    $rkCls    = $entry->rank===1?'lbr-1':($entry->rank===2?'lbr-2':($entry->rank===3?'lbr-3':'lbr-n'));
                    $netPos   = $entry->total_score >= 0;
                @endphp
                <tr class="{{ $trCls }}">
                    {{-- Rank --}}
                    <td>
                        <div class="lb-rank-circle {{ $rkCls }}">
                            @if($entry->rank <= 3)
                                {{ ['🥇','🥈','🥉'][$entry->rank-1] }}
                            @else
                                {{ $entry->rank }}
                            @endif
                        </div>
                    </td>
                    {{-- Executive --}}
                    <td>
                        <div class="lb-exec-cell">
                            <div class="lb-exec-av">{{ $initials }}</div>
                            <div>
                                <a href="{{ route('executives.show', $entry->executive_id) }}" class="lb-exec-name">
                                    {{ $entry->executive->name }}
                                </a>
                                <div class="lb-exec-id">{{ $entry->executive->employee_id }}</div>
                            </div>
                        </div>
                    </td>
                    {{-- Zone --}}
                    <td>
                        <div class="lb-zone-v">
                            <i class="fa-solid fa-location-dot" style="color:#c4b5fd;font-size:.6rem;margin-right:3px;"></i>
                            {{ $entry->executive->zone->name ?? '—' }}
                        </div>
                        <div class="lb-co-v">{{ $entry->company->name }}</div>
                    </td>
                    {{-- Positive --}}
                    <td style="text-align:center;">
                        <span class="lbc lbc-pos">
                            <i class="fa-solid fa-plus" style="font-size:.5rem;"></i>{{ $entry->positive_points }}
                        </span>
                    </td>
                    {{-- Negative --}}
                    <td style="text-align:center;">
                        <span class="lbc lbc-neg">
                            <i class="fa-solid fa-minus" style="font-size:.5rem;"></i>{{ $entry->negative_points }}
                        </span>
                    </td>
                    {{-- Recovery --}}
                    <td style="text-align:center;">
                        <span class="lbc lbc-rec">
                            <i class="fa-solid fa-rotate-right" style="font-size:.5rem;"></i>{{ $entry->recovery_points }}
                        </span>
                    </td>
                    {{-- Tier --}}
                    <td style="text-align:center;">
                        <span class="lb-tier-badge"
                            style="background:var(--pms-tier-{{ $entry->executive->current_tier }}-bg,#f5f3ff);
                                   color:var(--pms-tier-{{ $entry->executive->current_tier }}-color,#4f46e5);">
                            {{ $entry->executive->tier_label }}
                        </span>
                    </td>
                    {{-- KPI Days --}}
                    <td style="text-align:center;">
                        <div class="lb-kpi">
                            <strong>{{ $entry->kpi_passed_days ?? 0 }}</strong>
                            <span> / {{ $entry->total_audit_days ?? 0 }}d</span>
                        </div>
                    </td>
                    {{-- Net Score --}}
                    <td style="text-align:right;">
                        <div class="lb-net-cell">
                            <span class="lb-net-badge {{ $netPos ? 'pos' : 'neg' }}">
                                💎 {{ $netPos ? '+' : '-' }}{{ number_format(abs($entry->total_score)) }}
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding:0;border:none;">
                        <div class="lb-empty">
                            <div class="lb-empty-icon">🏆</div>
                            <div class="lb-empty-h">No rankings yet for this period</div>
                            <p class="lb-empty-p">
                                <a href="#" onclick="event.preventDefault();document.querySelector('form[action*=refresh]').submit();">
                                    Click here to generate rankings.
                                </a>
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($entries->hasPages())
    <div class="lb-pager">
        <span class="lb-pager-info">
            Showing {{ $entries->firstItem() }}–{{ $entries->lastItem() }} of {{ $entries->total() }} executives
        </span>
        <div class="lb-pager-nav">
            {{ $entries->links() }}
        </div>
    </div>
    @endif
</div>

</div>{{-- /lb-shell --}}
@endsection

@push('scripts')
<script>
// Month select syncs year field
document.getElementById('monthSelect').addEventListener('change', function () {
    document.getElementById('yearField').value = this.options[this.selectedIndex].dataset.year;
});
// Period toggle (visual only for now)
document.getElementById('toggleMonthly').addEventListener('click', function () {
    this.classList.add('active');
    document.getElementById('toggleAllTime').classList.remove('active');
});
document.getElementById('toggleAllTime').addEventListener('click', function () {
    this.classList.add('active');
    document.getElementById('toggleMonthly').classList.remove('active');
});
</script>
@endpush