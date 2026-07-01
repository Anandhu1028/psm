@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@push('styles')
<style>
/* Scoped to dashboard page to prevent side effects */

.pmsd-dashboard{
    font-feature-settings:"tnum" 1;
    position:relative;
}
.pmsd-dashboard::before{
    content:'';
    position:absolute; top:-40px; right:-80px; width:520px; height:520px;
    background:radial-gradient(circle, rgba(99,102,241,0.07), transparent 70%);
    pointer-events:none; z-index:0;
}

/* ── Hero banner ── */
.pmsd-hero{
    position:relative; overflow:hidden;
    border-radius:24px; margin-bottom:26px; padding:38px 42px;
    background:linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
    display:flex; align-items:flex-start; justify-content:space-between;
    gap:24px; flex-wrap:wrap;
    box-shadow:0 20px 45px -15px rgba(99, 102, 241, 0.45);
    border:1px solid rgba(255, 255, 255, 0.12);
}
.pmsd-hero::before {
    content:'';
    position:absolute; inset:0;
    background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size:20px 20px;
    pointer-events:none;
    z-index:0;
    border-radius:24px;
}
.pmsd-hero::after{
    content:''; position:absolute; border-radius:50%;
    width:260px; height:260px;
    background:radial-gradient(circle, rgba(219,39,119,0.22) 0%, transparent 70%);
    bottom:-70px; right:120px;
    pointer-events:none;
}
.pmsd-hero-watermark{
    position:absolute; right:18px; bottom:-18px;
    font-size:9.5rem; color:rgba(255,255,255,0.07);
    line-height:1; pointer-events:none; transform:rotate(-8deg);
}
.pmsd-hero-content{ position:relative; z-index:1; }
.pmsd-hero-eyebrow{
    display:inline-flex; align-items:center; gap:7px;
    font-size:0.68rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;
    color:rgba(255,255,255,0.75); margin-bottom:10px;
}
.pmsd-live-dot{
    width:7px; height:7px; border-radius:50%; background:#4ade80;
    box-shadow:0 0 0 0 rgba(74,222,128,0.6);
    animation:pmsdPulse 2s infinite;
}
@keyframes pmsdPulse{
    0%{ box-shadow:0 0 0 0 rgba(74,222,128,0.55); }
    70%{ box-shadow:0 0 0 6px rgba(74,222,128,0); }
    100%{ box-shadow:0 0 0 0 rgba(74,222,128,0); }
}
.pmsd-hero .pms-page-title{
    font-size:1.75rem; font-weight:800; letter-spacing:-0.03em;
    color:#fff; margin:0 0 6px; display:flex; align-items:center;
}
.pmsd-hero .pms-page-subtitle{
    font-size:0.86rem; color:rgba(255,255,255,0.75); font-weight:500; margin:0;
}
.pmsd-hero-actions{ position:relative; z-index:1; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.pmsd-hero .btn-pms-primary{
    display:inline-flex; align-items:center; height:42px; padding:0 18px;
    border-radius:12px; font-size:0.84rem; font-weight:700; border:none;
    background:#fff; color:#4338ca;
    box-shadow:0 8px 18px rgba(0,0,0,0.18);
    transition:all .18s ease;
}
.pmsd-hero .btn-pms-primary:hover{
    color:#4338ca; transform:translateY(-2px);
    box-shadow:0 12px 24px rgba(0,0,0,0.22);
}
.pmsd-hero .btn-pms-secondary{
    display:inline-flex; align-items:center; height:42px; padding:0 18px;
    border-radius:12px; font-size:0.84rem; font-weight:700;
    background:rgba(255,255,255,0.12); color:#fff;
    border:1.5px solid rgba(255,255,255,0.3);
    backdrop-filter:blur(4px);
    transition:all .18s ease;
}
.pmsd-hero .btn-pms-secondary:hover{
    background:rgba(255,255,255,0.2); color:#fff; transform:translateY(-1px);
    border-color:rgba(255,255,255,0.5);
}

/* ── Stat cards ── */
.pmsd-dashboard .stat-body{
    display:flex;
    flex-direction:column;
    justify-content:center;
    flex:1;
}
.pmsd-dashboard .stat-card::before{
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
}
.pmsd-accent-indigo::before{ background:linear-gradient(90deg,#4f46e5,#a5b4fc); }
.pmsd-accent-green::before { background:linear-gradient(90deg,#0d9488,#5ee7b7); }
.pmsd-accent-amber::before { background:linear-gradient(90deg,#d97706,#fcd34d); }
.pmsd-accent-blue::before  { background:linear-gradient(90deg,#0ea5e9,#93c5fd); }

.pmsd-accent-indigo:hover{ box-shadow:0 18px 30px -8px rgba(79,70,229,0.22) !important; transform:translateY(-3px); border-color: rgba(99, 102, 241, 0.25) !important; }
.pmsd-accent-green:hover { box-shadow:0 18px 30px -8px rgba(13,148,136,0.18) !important; transform:translateY(-3px); border-color: rgba(13, 148, 136, 0.25) !important; }
.pmsd-accent-amber:hover { box-shadow:0 18px 30px -8px rgba(217,119,6,0.18) !important; transform:translateY(-3px); border-color: rgba(217, 119, 6, 0.25) !important; }
.pmsd-accent-blue:hover  { box-shadow:0 18px 30px -8px rgba(14,165,233,0.18) !important; transform:translateY(-3px); border-color: rgba(14, 165, 233, 0.25) !important; }

.pmsd-dashboard .stat-card .stat-icon{
    width:52px;
    height:52px;
    border-radius:14px;
    margin:0;
    flex-shrink:0;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.25rem;
    color: #fff;
}
.pmsd-dashboard .stat-icon-accent  { background:linear-gradient(135deg,#6366f1,#4f46e5); box-shadow:0 6px 14px rgba(99,102,241,0.25); }
.pmsd-dashboard .stat-icon-success { background:linear-gradient(135deg,#0d9488,#0f766e); box-shadow:0 6px 14px rgba(13,148,136,0.22); }
.pmsd-dashboard .stat-icon-warning { background:linear-gradient(135deg,#d97706,#b45309); box-shadow:0 6px 14px rgba(217,119,6,0.22); }
.pmsd-dashboard .stat-icon-info    { background:linear-gradient(135deg,#0ea5e9,#0369a1); box-shadow:0 6px 14px rgba(14,165,233,0.22); }
.pmsd-dashboard .stat-icon-danger  { background:linear-gradient(135deg,#e11d48,#be123c); box-shadow:0 6px 14px rgba(225,29,72,0.22); }

.pmsd-dashboard .stat-body{ display:flex; flex-direction:column; flex:1; }
.pmsd-dashboard .stat-label{
    font-size:0.74rem; font-weight:600; color:var(--pms-text-muted,#94a3b8);
    margin-bottom:6px;
}
.pmsd-dashboard .stat-value{
    font-size:1.95rem; font-weight:800; letter-spacing:-0.03em; line-height:1;
    color:var(--pms-text-primary,#111827); margin-bottom:12px;
    font-variant-numeric:tabular-nums;
}
.pmsd-dashboard .stat-meta{ margin-top:auto; }

@keyframes pmsdFadeUp{
    from{ opacity:0; transform:translateY(10px); }
    to{ opacity:1; transform:translateY(0); }
}
.pmsd-dashboard .row > div:nth-child(1) .stat-card{ animation-delay:0ms; }
.pmsd-dashboard .row > div:nth-child(2) .stat-card{ animation-delay:60ms; }
.pmsd-dashboard .row > div:nth-child(3) .stat-card{ animation-delay:120ms; }
.pmsd-dashboard .row > div:nth-child(4) .stat-card{ animation-delay:180ms; }

@media (prefers-reduced-motion: reduce){
    .pmsd-dashboard .stat-card{ animation:none; }
    .pmsd-live-dot{ animation:none; }
}

/* trend / meta pill */
.pmsd-pill{
    display:inline-flex; align-items:center; gap:6px;
    height:24px; padding:0 10px; border-radius:100px;
    font-size:0.72rem; font-weight:700; white-space:nowrap;
}
.pmsd-pill i{ font-size:0.62rem; }
.pmsd-pill-pos{ background:#ecfdf5; color:#059669; }
.pmsd-pill-neg{ background:#fff1f2; color:#e11d48; }
.pmsd-pill-neutral{ background:#f5f3ff; color:#5b54c4; }

/* ── Generic cards ── */
.pmsd-dashboard .pms-card{
    position:relative; background:linear-gradient(180deg,#ffffff,#fbfbff); border-radius:18px; border:1px solid rgba(15,23,42,0.04) !important;
    padding:18px; box-shadow:0 8px 28px rgba(2,6,23,0.04); backdrop-filter: blur(4px);
}
.pmsd-dashboard .pms-card-header{
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; margin-bottom:18px; flex-wrap:wrap;
}
.pmsd-dashboard .pms-card-title{
    display:flex; align-items:center; gap:10px;
    font-size:0.92rem; font-weight:800; letter-spacing:-0.01em;
    color:var(--pms-text-primary,#111827);
}
.pmsd-dashboard .pms-card-title i{
    width:32px; height:32px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:0.8rem; flex-shrink:0; color:#fff;
}
.pmsd-icon-chip-indigo{ background:linear-gradient(135deg,#6366f1,#4f46e5); }
.pmsd-icon-chip-teal  { background:linear-gradient(135deg,#0d9488,#2dd4bf); }
.pmsd-icon-chip-gold  { background:linear-gradient(135deg,#d97706,#fbbf24); }
.pmsd-icon-chip-slate { background:linear-gradient(135deg,#475569,#94a3b8); }
.pmsd-icon-chip-rose  { background:linear-gradient(135deg,#db2777,#f472b6); }
.pmsd-icon-chip-violet{ background:linear-gradient(135deg,#7c3aed,#a78bfa); }

.pmsd-dashboard .pms-card-title small{
    display:block; font-size:0.68rem; font-weight:500; color:var(--pms-text-muted,#94a3b8);
    margin-top:1px;
}
.pmsd-dashboard .btn-pms-secondary.btn-sm{
    height:32px; padding:0 12px; border-radius:9px; font-size:0.72rem;
    display:inline-flex; align-items:center;
    background:#fff; color:var(--pms-text-primary,#1e293b);
    border:1.5px solid var(--pms-border,#e8eaf2);
    transition:all .15s ease;
}
.pmsd-dashboard .btn-pms-secondary.btn-sm:hover{
    border-color:#6366f1; color:#6366f1; background:#fafaff;
}
.pmsd-dashboard .chart-wrapper{ padding-top:4px; }

/* ── Breakdown mini-cards ── */
.pmsd-dashboard .pmsd-breakdown-card{
    background:#fff; border-radius:16px; border:1px solid rgba(99, 102, 241, 0.08);
    padding:18px 20px; height:100%; box-shadow:0 1px 2px rgba(99, 102, 241, 0.02);
    display:flex; align-items:center; gap:14px;
    transition:transform .18s ease, box-shadow .18s ease;
}
.pmsd-dashboard .pmsd-breakdown-card:hover{ transform:translateY(-2px); box-shadow:0 10px 22px rgba(99, 102, 241, 0.06); }
.pmsd-dashboard .pmsd-breakdown-card .stat-icon{
    width:44px; height:44px; border-radius:13px; font-size:1.1rem; margin-bottom:0;
}
.pmsd-dashboard .pmsd-breakdown-value{
    font-size:1.55rem; font-weight:800; letter-spacing:-0.02em; line-height:1.1;
    font-variant-numeric:tabular-nums;
}

/* ── Company overview rows ── */
.pmsd-dashboard .pmsd-company-row{
    padding:13px 14px; border-radius:14px;
    background:var(--pms-bg-elevated,#fafbff); border:1px solid rgba(99, 102, 241, 0.06);
    transition:all .15s ease;
}
.pmsd-dashboard .pmsd-company-row:hover{ border-color:rgba(99, 102, 241, 0.2); transform:translateX(2px); }
.pmsd-dashboard .pmsd-company-top{ display:flex; align-items:center; justify-content:space-between; }
.pmsd-dashboard .pmsd-company-badge{
    width:40px; height:40px; border-radius:11px;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-weight:800; font-size:0.85rem; flex-shrink:0;
    box-shadow:0 4px 10px rgba(0,0,0,0.12);
}
.pmsd-dashboard .pmsd-company-name{ font-weight:700; font-size:0.86rem; color:var(--pms-text-primary,#111827); }
.pmsd-dashboard .pmsd-company-strategy{ font-size:0.68rem; color:var(--pms-text-muted,#94a3b8); margin-top:1px; }
.pmsd-dashboard .pmsd-company-count{ font-size:1.05rem; font-weight:800; color:var(--pms-text-primary,#111827); line-height:1; }
.pmsd-dashboard .pmsd-company-count-label{ font-size:0.66rem; color:var(--pms-text-muted,#94a3b8); margin-top:2px; }

.pmsd-progress-track{
    width:100%; height:6px; border-radius:100px; background:rgba(99, 102, 241, 0.05); overflow:hidden; margin-top:11px;
}
.pmsd-progress-fill{
    height:100%; border-radius:100px; width:0;
    transition:width 1s cubic-bezier(.16,1,.3,1);
}

/* ── Tables ── */
.pmsd-dashboard .pms-table-wrapper{ overflow-x:auto; }
.pmsd-dashboard .pms-table{ width:100%; border-collapse:collapse; font-size:0.88rem; background:transparent; }
.pmsd-dashboard .pms-table thead th{
    font-size:0.68rem; font-weight:800; text-transform:uppercase; letter-spacing:0.09em;
    color:var(--pms-text-muted,#64748b); padding:12px 14px; border-bottom:1px solid rgba(226,232,240,0.7);
    white-space:nowrap; text-align:left; background:linear-gradient(180deg, #ffffff, #fbfbfd);
    position:sticky; top:0; z-index:10;
}
.pmsd-dashboard .pms-table tbody td{
    padding:12px 14px; vertical-align:middle; border-bottom:1px solid #f3f5f9; color:var(--pms-text-primary,#0f172a);
}
.pmsd-dashboard .pms-table tbody tr:last-child td{ border-bottom:none; }
.pmsd-dashboard .pms-table tbody tr{ transition:background .14s ease; }
.pmsd-dashboard .pms-table tbody tr:hover{ background:rgba(99, 102, 241, 0.035) !important; }

/* Zebra rows */
.pmsd-dashboard .pms-table tbody tr:nth-child(odd){ background:rgba(15,23,42,0.01); }

/* Scrollable table body for cards */
.pmsd-dashboard .pms-card .pms-table-wrapper{ max-height:340px; overflow:auto; padding-right:6px; }

/* Compact avatar and avatar spacing */
.pmsd-dashboard .pmsd-avatar-sm{ width:36px; height:36px; border-radius:8px; font-size:0.72rem; }

/* Table utilities */
.pmsd-dashboard .text-muted-small{ color:var(--pms-text-muted,#94a3b8); font-size:0.82rem; }

.pmsd-dashboard .pmsd-exec-cell{ display:flex; align-items:center; gap:9px; }
.pmsd-dashboard .pmsd-avatar-sm{
    width:30px; height:30px; border-radius:9px; flex-shrink:0;
    background:linear-gradient(135deg, #6366f1, #7c3aed);
    color:#fff; font-size:0.62rem; font-weight:800; letter-spacing:0.02em;
    display:flex; align-items:center; justify-content:center;
}
.pmsd-dashboard .pmsd-avatar-gold  { background:linear-gradient(135deg,#d97706,#fcd34d); box-shadow:0 0 0 2px #fef3c7; }
.pmsd-dashboard .pmsd-avatar-silver{ background:linear-gradient(135deg,#64748b,#cbd5e1); box-shadow:0 0 0 2px #f1f5f9; }
.pmsd-dashboard .pmsd-avatar-bronze{ background:linear-gradient(135deg,#c2410c,#fb923c); box-shadow:0 0 0 2px #ffedd5; }

.pmsd-dashboard .rank-badge{
    border-radius:8px; display:flex; align-items:center; justify-content:center;
    font-weight:800;
}
.pmsd-dashboard .rank-1{ background:rgba(245, 158, 11, 0.12); color:#b45309; border: 1.5px solid rgba(245, 158, 11, 0.25); }
.pmsd-dashboard .rank-2{ background:rgba(148, 163, 184, 0.12); color:#475569; border: 1.5px solid rgba(148, 163, 184, 0.25); }
.pmsd-dashboard .rank-3{ background:rgba(249, 115, 22, 0.12); color:#c2410c; border: 1.5px solid rgba(249, 115, 22, 0.25); }
.pmsd-dashboard .rank-other{ background:#f8f9fc; color:#b0b8d1; border: 1.5px solid #edf0f7; }

.pmsd-dashboard .badge{
    border-radius:7px; font-weight:700; font-size:0.68rem; padding:4px 9px;
}
.pmsd-dashboard .badge-status-draft{ background:#f1f5f9; color:#64748b; }

.pmsd-dashboard .pms-empty{ text-align:center; padding:34px 12px; color:var(--pms-text-muted,#94a3b8); }
.pmsd-dashboard .pms-empty i{ font-size:1.4rem; color:#d6d9ee; margin-bottom:8px; display:block; }
.pmsd-dashboard .pms-empty p{ margin:0; font-size:0.8rem; }

/* ── Zone performance ── */
.pmsd-dashboard .pmsd-zone-row{
    padding:12px 13px; border-radius:13px;
    background:var(--pms-bg-elevated,#fafbff); border:1px solid var(--pms-border,#edf0f7);
    margin-bottom:8px;
}
.pmsd-dashboard .pmsd-zone-row:last-child{ margin-bottom:0; }
.pmsd-dashboard .pmsd-zone-top{ display:flex; align-items:center; justify-content:space-between; }
.pmsd-dashboard .pmsd-zone-name{ font-size:0.82rem; font-weight:700; color:var(--pms-text-primary,#111827); }
.pmsd-dashboard .pmsd-zone-meta{ font-size:0.68rem; color:var(--pms-text-muted,#94a3b8); margin-top:1px; }
.pmsd-dashboard .pmsd-zone-score{ font-size:0.95rem; font-weight:800; color:#4f46e5; line-height:1; }
.pmsd-dashboard .pmsd-zone-score-label{ font-size:0.63rem; color:var(--pms-text-muted,#94a3b8); margin-top:2px; }

/* ── Donut card ── */
.pmsd-dashboard .pmsd-donut-wrap{
    position:relative; width:150px; height:150px; margin:8px auto 18px;
}
.pmsd-dashboard .pmsd-donut-wrap::before{
    content:''; position:absolute; inset:-14px; border-radius:50%;
    background:radial-gradient(circle, rgba(219,39,119,0.10), transparent 70%);
    z-index:0;
}
.pmsd-dashboard .pmsd-donut-wrap canvas{ position:relative; z-index:1; }
.pmsd-dashboard .pmsd-donut-center{
    position:absolute; inset:0; display:flex; flex-direction:column;
    align-items:center; justify-content:center; pointer-events:none; z-index:2;
}
.pmsd-dashboard .pmsd-donut-center .n{ font-size:1.55rem; font-weight:800; color:var(--pms-text-primary,#111827); line-height:1; }
.pmsd-dashboard .pmsd-donut-center .l{ font-size:0.6rem; color:var(--pms-text-muted,#94a3b8); font-weight:600; margin-top:3px; text-align:center; }
.pmsd-dashboard .pmsd-donut-legend{ display:flex; flex-wrap:wrap; gap:10px; justify-content:center; margin-bottom:16px; }
.pmsd-dashboard .pmsd-legend-item{ display:flex; align-items:center; gap:6px; font-size:0.74rem; color:#4a5568; font-weight:600; }
.pmsd-dashboard .pmsd-legend-dot{ width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.pmsd-dashboard .pmsd-donut-cta{
    display:flex; align-items:center; justify-content:center; gap:7px;
    width:100%; height:38px; border-radius:11px; border:1.5px solid var(--pms-border,#e8eaf2);
    background:var(--pms-bg-elevated,#fafbff); color:var(--pms-text-primary,#1e293b);
    font-size:0.78rem; font-weight:700; text-decoration:none; transition:all .15s ease;
}
.pmsd-dashboard .pmsd-donut-cta:hover{ border-color:#6366f1; color:#6366f1; background:#fafaff; }

@media (max-width: 768px){
    .pmsd-hero{ padding:26px 22px; }
    .pmsd-hero-watermark{ display:none; }
}

/* Responsive tweaks for small screens */
@media (max-width: 1200px){
    .pmsd-hero{ padding:26px; }
    .pmsd-hero .pms-page-title{ font-size:1.35rem; }
    .pmsd-dashboard .pms-card{ padding:14px; }
}

.pmsd-hero-actions a{ text-decoration:none; }

.btn-pms-primary{ display:inline-flex; gap:8px; align-items:center; height:44px; padding:0 16px; border-radius:12px; background:#4f46e5; color:#fff; font-weight:700; }
.btn-pms-primary i{ font-size:0.95rem }
.btn-pms-secondary{ display:inline-flex; gap:8px; align-items:center; height:44px; padding:0 16px; border-radius:12px; background:transparent; color:#4f46e5; border:1.5px solid rgba(79,70,229,0.12); font-weight:700; }

.pmsd-dashboard .pms-card .pms-card-header .pms-card-title small{ color:var(--pms-text-muted,#94a3b8); }

.pmsd-dashboard .pms-table thead th{ background: linear-gradient(180deg,#fbfbff,#ffffff); }

.pmsd-hero .pms-page-subtitle{ max-width:520px; }
</style>
@endpush

@section('content')
<div class="pmsd-dashboard">

{{-- Hero Header --}}
<div class="pmsd-hero">
    <i class="fa-solid fa-gauge-high pmsd-hero-watermark"></i>
    <div class="pmsd-hero-content">
        <div class="pmsd-hero-eyebrow"><span class="pmsd-live-dot"></span> Live · {{ now()->format('l, d F Y') }}</div>
        <h1 class="pms-page-title">
            <i class="fa-solid fa-gauge-high me-2"></i>
            Performance Dashboard
        </h1>
        <p class="pms-page-subtitle">A live overview of audits, scores and rankings across every company.</p>
    </div>
    <div class="pmsd-hero-actions">
        <a href="{{ route('daily_audit.create') }}" class="btn-pms-primary"><i class="fa-solid fa-circle-plus me-2"></i> New Audit</a>
        <a href="{{ route('reports.index', ['type'=>'monthly']) }}" class="btn-pms-secondary"><i class="fa-solid fa-file-export me-2"></i> Reports</a>
    </div>
</div>

{{-- Top Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card pmsd-accent-indigo">
            <div class="stat-icon stat-icon-accent"><i class="fa-solid fa-users"></i></div>
            <div class="stat-body">
                <div class="stat-label">Active Executives</div>
                <div class="stat-value"><span class="pmsd-count" data-target="{{ $totalExecutives }}">0</span></div>
                <div class="stat-meta">
                    <span class="pmsd-pill pmsd-pill-neutral">
                        <i class="fa-solid fa-building"></i> Across {{ $companies->count() }} companies
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card pmsd-accent-green">
            <div class="stat-icon stat-icon-success"><i class="fa-solid fa-clipboard-check"></i></div>
            <div class="stat-body">
                <div class="stat-label">Today's Audits</div>
                <div class="stat-value"><span class="pmsd-count" data-target="{{ $todayAudits }}">0</span></div>
                <div class="stat-meta">
                    <span class="pmsd-pill pmsd-pill-neutral">
                        <i class="fa-solid fa-calendar-day"></i> Submitted today
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card pmsd-accent-amber">
            <div class="stat-icon stat-icon-warning"><i class="fa-solid fa-coins"></i></div>
            <div class="stat-body">
                <div class="stat-label">Today's Points</div>
                <div class="stat-value">
                    <span class="pmsd-count" data-target="{{ $todayPoints['total_score'] }}" data-signed="1">0</span>
                </div>
                <div class="stat-meta">
                    @if($todayPoints['total_score'] >= 0)
                    <span class="pmsd-pill pmsd-pill-pos"><i class="fa-solid fa-arrow-trend-up"></i> Net score today</span>
                    @else
                    <span class="pmsd-pill pmsd-pill-neg"><i class="fa-solid fa-arrow-trend-down"></i> Net score today</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card pmsd-accent-blue">
            <div class="stat-icon stat-icon-info"><i class="fa-solid fa-building"></i></div>
            <div class="stat-body">
                <div class="stat-label">Companies</div>
                <div class="stat-value"><span class="pmsd-count" data-target="{{ $companies->count() }}">0</span></div>
                <div class="stat-meta">
                    <span class="pmsd-pill pmsd-pill-neutral">
                        <i class="fa-solid fa-circle-check"></i> TIMS + FOCUZ active
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Today Points Breakdown --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="pmsd-breakdown-card">
            <div class="stat-icon stat-icon-success">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            <div>
                <div class="stat-label">Positive Points Today</div>
                <div class="pmsd-breakdown-value" style="color:var(--pms-success,#059669);">+{{ $todayPoints['total_positive'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pmsd-breakdown-card">
            <div class="stat-icon stat-icon-danger">
                <i class="fa-solid fa-arrow-trend-down"></i>
            </div>
            <div>
                <div class="stat-label">Negative Points Today</div>
                <div class="pmsd-breakdown-value" style="color:var(--pms-danger,#e11d48);">-{{ $todayPoints['total_negative'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pmsd-breakdown-card">
            <div class="stat-icon stat-icon-info">
                <i class="fa-solid fa-rotate-right"></i>
            </div>
            <div>
                <div class="stat-label">Recovery Points Today</div>
                <div class="pmsd-breakdown-value" style="color:var(--pms-info,#2563eb);">+{{ $todayPoints['total_recovery'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Main Grid --}}
<div class="row g-3 mb-4">

    {{-- 6-Month Performance Chart --}}
    <div class="col-xl-7">
        <div class="pms-card h-100">
            <div class="pms-card-header">
                <div class="pms-card-title">
                    <i class="fa-solid fa-chart-column pmsd-icon-chip-indigo"></i>
                    <span>
                        6-Month Performance Trend
                        <small>Positive, negative &amp; recovery points</small>
                    </span>
                </div>
            </div>
            <div class="chart-wrapper" style="height:260px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Company Cards --}}
    <div class="col-xl-5">
        <div class="pms-card h-100">
            <div class="pms-card-header">
                <div class="pms-card-title">
                    <i class="fa-solid fa-building pmsd-icon-chip-teal"></i>
                    <span>Companies Overview</span>
                </div>
            </div>
            <div class="d-flex flex-column gap-2">
                @foreach($companies as $company)
                @php
                    $companyPct = $totalExecutives > 0 ? min(100, round(($company->executives_count / $totalExecutives) * 100)) : 0;
                @endphp
                <div class="pmsd-company-row">
                    <div class="pmsd-company-top">
                        <div class="d-flex align-items-center gap-3">
                            <div class="pmsd-company-badge" style="background:{{ $company->theme_color }};">
                                {{ substr($company->code,0,2) }}
                            </div>
                            <div>
                                <div class="pmsd-company-name">{{ $company->name }}</div>
                                <div class="pmsd-company-strategy">{{ ucfirst($company->calculation_strategy) }} Strategy</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="pmsd-company-count">{{ $company->executives_count }}</div>
                            <div class="pmsd-company-count-label">executives</div>
                        </div>
                    </div>
                    <div class="pmsd-progress-track">
                        <div class="pmsd-progress-fill" data-width="{{ $companyPct }}" style="background:{{ $company->theme_color }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Monthly Performance Summary --}}
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="pms-card">
            <div class="pms-card-header">
                <div class="pms-card-title">
                    <i class="fa-solid fa-bullseye pmsd-icon-chip-violet"></i>
                    <span>Monthly Admission Performance</span>
                </div>
            </div>
            <div class="row g-2 mb-3 align-items-center">
                <div class="col-md-9">
                    <div style="font-weight:700;color:var(--pms-text-primary);">
                        Showing: <span style="font-weight:600;color:var(--pms-text-muted);">{{ $selectedCompanyId ? $companies->firstWhere('id',$selectedCompanyId)->name : 'All Companies' }}</span>
                        · <span style="font-weight:600;color:var(--pms-text-muted);">{{ $selectedZoneId ? App\Models\Zone::find($selectedZoneId)?->name : 'All Zones' }}</span>
                        · <span style="font-weight:600;color:var(--pms-text-muted);">{{ \Carbon\Carbon::create()->month($selectedMonth)->format('M') }} {{ $selectedYear }}</span>
                    </div>
                    <div style="font-size:0.86rem;color:var(--pms-text-muted);margin-top:6px;">Monthly admission performance snapshot — use the Reports page to apply advanced filters.</div>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('reports.index', ['type'=>'monthly']) }}" class="btn btn-pms-secondary btn-sm">Open Reports</a>
                </div>
            </div>
            <div class="pms-table-wrapper">
                <table class="pms-table">
                    <thead>
                        <tr>
                            <th>Executive</th>
                            <th>Zone</th>
                            <th>Target</th>
                            <th>Admissions</th>
                            <th>Remaining</th>
                            <th>Achievement %</th>
                            <th>Rank</th>
                            <th>Eligible</th>
                            <th>Bonus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyPerformance as $row)
                        <tr>
                            <td>{{ $row['executive']->name }}</td>
                            <td>{{ $row['executive']->zone?->name ?? '—' }}</td>
                            <td>{{ $row['target'] }}</td>
                            <td>{{ $row['admissions'] }}</td>
                            <td>{{ $row['remaining'] }}</td>
                            <td>{{ number_format($row['achievement'], 2) }}%</td>
                            <td>{{ $row['rank'] ?? '—' }}</td>
                            <td>{{ $row['eligible'] ? 'YES' : 'NO' }}</td>
                            <td>{{ $row['eligible'] ? 'Pending' : '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="pms-empty"><i class="fa-solid fa-bullseye"></i><p>No monthly admission data yet</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Grid --}}
<div class="row g-3">

    {{-- Top Performers per Company --}}
    @foreach($companyLeaderboards as $cId => $lb)
    <div class="col-xl-6">
        <div class="pms-card">
            <div class="pms-card-header">
                <div class="pms-card-title">
                    <i class="fa-solid fa-trophy pmsd-icon-chip-gold"></i>
                    <span>{{ $lb['company']->name }} — Top Performers</span>
                </div>
                <a href="{{ route('leaderboards.index', ['company_id'=>$cId]) }}" class="btn btn-pms-secondary btn-sm">
                    View All
                </a>
            </div>
            <div class="pms-table-wrapper">
                <table class="pms-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Executive</th>
                            <th>Zone</th>
                            <th>Score</th>
                            <th>Tier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lb['top5'] as $i => $exec)
                        @php
                            $execWords = explode(' ', trim($exec->name));
                            $execInitials = implode('', array_map(fn($w) => strtoupper(substr($w,0,1)), array_slice($execWords,0,2)));
                            $avatarClass = $i===0 ? 'pmsd-avatar-gold' : ($i===1 ? 'pmsd-avatar-silver' : ($i===2 ? 'pmsd-avatar-bronze' : ''));
                        @endphp
                        <tr>
                            <td>
                                <div class="rank-badge {{ $i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'rank-other')) }}" style="width:26px;height:26px;font-size:.72rem;">
                                    {{ $i+1 }}
                                </div>
                            </td>
                            <td>
                                <div class="pmsd-exec-cell">
                                    <div class="pmsd-avatar-sm {{ $avatarClass }}">{{ $execInitials }}</div>
                                    <div>
                                        <div style="font-weight:600;color:var(--pms-text-primary);font-size:.83rem;">{{ $exec->name }}</div>
                                        <div style="font-size:.66rem;color:var(--pms-text-muted);">{{ $exec->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:.78rem;color:var(--pms-text-muted);">{{ $exec->zone->name }}</td>
                            <td>
                                <span style="font-weight:800;color:var(--pms-accent);">{{ number_format($exec->current_score) }}</span>
                            </td>
                            <td><span class="badge badge-tier-{{ $exec->current_tier }}">{{ $exec->tier_label }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="pms-empty"><i class="fa-solid fa-trophy"></i><p>No executives yet</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Recent Audits Feed --}}
    <div class="col-xl-8">
        <div class="pms-card">
            <div class="pms-card-header">
                <div class="pms-card-title">
                    <i class="fa-solid fa-clock-rotate-left pmsd-icon-chip-slate"></i>
                    <span>Recent Audit Activity</span>
                </div>
                <a href="{{ route('daily_audit.index') }}" class="btn btn-pms-secondary btn-sm">View All</a>
            </div>
            <div class="pms-table-wrapper">
                <table class="pms-table">
                    <thead>
                        <tr>
                            <th>Executive</th>
                            <th>Company</th>
                            <th>Date</th>
                            <th>Score</th>
                            <th>KPI</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAudits as $audit)
                        @php
                            $auditWords = explode(' ', trim($audit->executive->name));
                            $auditInitials = implode('', array_map(fn($w) => strtoupper(substr($w,0,1)), array_slice($auditWords,0,2)));
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('daily_audit.show', $audit) }}" class="pmsd-exec-cell" style="text-decoration:none;">
                                    <div class="pmsd-avatar-sm">{{ $auditInitials }}</div>
                                    <span style="font-weight:600;color:var(--pms-accent);font-size:.83rem;">{{ $audit->executive->name }}</span>
                                </a>
                            </td>
                            <td style="font-size:.78rem;color:var(--pms-text-muted);">{{ $audit->executive->company->name ?? '—' }}</td>
                            <td style="font-size:.78rem;color:var(--pms-text-muted);">{{ $audit->audit_date->format('d M Y') }}</td>
                            <td>
                                <span style="font-weight:800;color:{{ $audit->final_score>=0?'var(--pms-success)':'var(--pms-danger)' }};">
                                    {{ $audit->final_score >= 0 ? '+' : '' }}{{ $audit->final_score }}
                                </span>
                            </td>
                            <td>
                                @if($audit->kpi_status === 'passed')
                                    <span class="badge" style="background:var(--pms-success-subtle);color:var(--pms-success);">✓ Pass</span>
                                @elseif($audit->kpi_status === 'failed')
                                    <span class="badge" style="background:var(--pms-danger-subtle);color:var(--pms-danger);">✗ Fail</span>
                                @else
                                    <span class="badge badge-status-draft">Pending</span>
                                @endif
                            </td>
                            <td>{!! $audit->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="pms-empty"><i class="fa-solid fa-inbox"></i><p>No audits yet today</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right column: Audit Status Snapshot + Zone Performance --}}
    <div class="col-xl-4">
        <div class="d-flex flex-column gap-3 h-100">

            {{-- Audit Status Snapshot (donut) --}}
            @php
                $auditTotal = $recentAudits->count();
                $auditPassed = $recentAudits->where('kpi_status', 'passed')->count();
                $auditFailed = $recentAudits->where('kpi_status', 'failed')->count();
                $auditPending = max($auditTotal - $auditPassed - $auditFailed, 0);
            @endphp
            <div class="pms-card">
                <div class="pms-card-header" style="margin-bottom:6px;">
                    <div class="pms-card-title">
                        <i class="fa-solid fa-chart-pie pmsd-icon-chip-rose"></i>
                        <span>Audit Status Snapshot</span>
                    </div>
                </div>
                @if($auditTotal > 0)
                <div class="pmsd-donut-wrap">
                    <canvas id="auditStatusChart"></canvas>
                    <div class="pmsd-donut-center">
                        <div class="n">{{ $auditTotal }}</div>
                        <div class="l">Recent Audits</div>
                    </div>
                </div>
                <div class="pmsd-donut-legend">
                    <span class="pmsd-legend-item"><span class="pmsd-legend-dot" style="background:#059669;"></span>Passed {{ $auditPassed }}</span>
                    <span class="pmsd-legend-item"><span class="pmsd-legend-dot" style="background:#e11d48;"></span>Failed {{ $auditFailed }}</span>
                    <span class="pmsd-legend-item"><span class="pmsd-legend-dot" style="background:#d97706;"></span>Pending {{ $auditPending }}</span>
                </div>
                <a href="{{ route('daily_audit.index') }}" class="pmsd-donut-cta">
                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:.7rem;"></i> View All Audits
                </a>
                @else
                <div class="pms-empty" style="padding:24px 8px;"><i class="fa-solid fa-chart-pie"></i><p>No recent audits</p></div>
                @endif
            </div>

            {{-- Zone Performance --}}
           

        </div>
    </div>

</div>
</div>{{-- /pmsd-dashboard --}}
@endsection

@push('scripts')
<script>
/* Animated counters */
document.querySelectorAll('.pmsd-count').forEach(function (el) {
    const target = parseFloat(el.dataset.target || '0');
    const signed = el.dataset.signed === '1';
    const duration = 900;
    const start = performance.now();
    function frame(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = Math.round(target * eased);
        el.textContent = (signed && value >= 0 ? '+' : '') + value.toLocaleString();
        if (progress < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
});

/* Animate progress bars in on load */
requestAnimationFrame(function () {
    document.querySelectorAll('.pmsd-progress-fill').forEach(function (el) {
        const width = el.dataset.width || 0;
        requestAnimationFrame(function () { el.style.width = width + '%'; });
    });
});

/* 6-month performance chart with gradient fills */
const monthlyData = @json($monthlyChart);
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');

function makeGradient(ctx, colorTop, colorBottom) {
    const g = ctx.createLinearGradient(0, 0, 0, 240);
    g.addColorStop(0, colorTop);
    g.addColorStop(1, colorBottom);
    return g;
}

new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.label),
        datasets: [
            {
                label: 'Positive',
                data: monthlyData.map(d => d.positive),
                backgroundColor: makeGradient(monthlyCtx, 'rgba(5,150,105,0.55)', 'rgba(5,150,105,0.06)'),
                borderColor: '#059669',
                borderWidth: 2,
                borderRadius: 6,
            },
            {
                label: 'Negative',
                data: monthlyData.map(d => d.negative),
                backgroundColor: makeGradient(monthlyCtx, 'rgba(220,38,38,0.45)', 'rgba(220,38,38,0.05)'),
                borderColor: '#dc2626',
                borderWidth: 2,
                borderRadius: 6,
            },
            {
                label: 'Recovery',
                data: monthlyData.map(d => d.recovery),
                backgroundColor: makeGradient(monthlyCtx, 'rgba(2,132,199,0.45)', 'rgba(2,132,199,0.05)'),
                borderColor: '#0284c7',
                borderWidth: 2,
                borderRadius: 6,
            },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { font: { family: 'Inter', size: 11 }, color: '#64748b', padding: 16, boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle' } },
            tooltip: { backgroundColor: '#fff', titleColor: '#0f172a', bodyColor: '#475569', borderColor: '#e4e8f0', borderWidth: 1 }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' } },
            y: { grid: { color: '#f1f5f9', borderDash: [3,3] }, ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' } }
        }
    }
});

@if(($recentAudits->count() ?? 0) > 0)
const auditStatusCanvas = document.getElementById('auditStatusChart');
if (auditStatusCanvas) {
    new Chart(auditStatusCanvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Passed', 'Failed', 'Pending'],
            datasets: [{
                data: [{{ $auditPassed }}, {{ $auditFailed }}, {{ $auditPending }}],
                backgroundColor: ['#059669', '#e11d48', '#d97706'],
                borderWidth: 0,
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '74%',
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#fff', titleColor: '#0f172a', bodyColor: '#475569', borderColor: '#e4e8f0', borderWidth: 1 }
            }
        }
    });
}
@endif
</script>
@endpush