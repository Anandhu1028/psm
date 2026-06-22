@extends('layouts.app')

@section('title', 'Daily Performance Logs')
@section('page_title', 'Daily Performance Logs')

@section('styles')
    <style>
        /* ═══════════════════════════════════════════════════════════════
       DESIGN TOKENS
    ═══════════════════════════════════════════════════════════════ */
        :root {
            --dl-bg-base: #080C1A;
            --dl-bg-surface: #0D1224;
            --dl-bg-elevated: #111829;
            --dl-bg-overlay: #151D30;
            --dl-blue: #3B7BFF;
            --dl-blue-dim: rgba(59, 123, 255, 0.12);
            --dl-violet: #7C3AED;
            --dl-violet-dim: rgba(124, 58, 237, 0.12);
            --dl-cyan: #06B6D4;
            --dl-amber: #F59E0B;
            --dl-amber-dim: rgba(245, 158, 11, 0.12);
            --dl-emerald: #10B981;
            --dl-emerald-dim: rgba(16, 185, 129, 0.12);
            --dl-rose: #F43F5E;
            --dl-rose-dim: rgba(244, 63, 94, 0.12);
            --dl-border: rgba(255, 255, 255, 0.07);
            --dl-border-bright: rgba(255, 255, 255, 0.13);
            --dl-text-primary: #F0F4FF;
            --dl-text-secondary: rgba(240, 244, 255, 0.52);
            --dl-text-muted: rgba(240, 244, 255, 0.32);
            --dl-radius-sm: 8px;
            --dl-radius-md: 12px;
            --dl-radius-lg: 16px;
        }

        body {
            background: var(--dl-bg-base) !important;
            color: var(--dl-text-primary) !important;
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }

        /* ════════════════════════════════════════════
       GLASS CARD
    ════════════════════════════════════════════ */
        .dl-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.045) 0%, rgba(255, 255, 255, 0.012) 100%) !important;
            border: 1px solid var(--dl-border) !important;
            border-radius: var(--dl-radius-lg) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.38), 0 1px 3px rgba(0, 0, 0, 0.22) !important;
            transition: border-color .2s ease;
        }

        .dl-card:hover {
            border-color: var(--dl-border-bright) !important;
        }

        /* ════════════════════════════════════════════
       PAGE HEADER
    ════════════════════════════════════════════ */
        .dl-page-hdr {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 22px;
            flex-wrap: wrap;
        }

        .dl-hdr-actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
            align-items: center;
            flex-shrink: 0;
        }

        /* ── HEADER BUTTONS ── */
        .dl-btn {
            height: 37px;
            padding: 0 15px;
            border-radius: var(--dl-radius-sm);
            font-size: 12.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
            border: none;
            text-decoration: none !important;
            line-height: 1;
            font-family: inherit;
        }

        .dl-btn-ghost {
            background: var(--dl-bg-elevated);
            border: 1px solid var(--dl-border-bright) !important;
            color: var(--dl-text-primary) !important;
        }

        .dl-btn-ghost:hover {
            background: var(--dl-bg-overlay);
            border-color: var(--dl-blue) !important;
            color: #fff !important;
            box-shadow: 0 0 0 3px var(--dl-blue-dim);
        }

        .dl-btn-green {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3) !important;
            color: var(--dl-emerald) !important;
        }

        .dl-btn-green:hover {
            background: rgba(16, 185, 129, 0.18);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .1);
            color: var(--dl-emerald) !important;
        }

        .dl-btn-primary {
            background: linear-gradient(135deg, var(--dl-blue) 0%, var(--dl-violet) 100%);
            color: #fff !important;
            box-shadow: 0 4px 14px rgba(59, 123, 255, 0.38);
        }

        .dl-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(59, 123, 255, 0.52);
            color: #fff !important;
        }

        /* ════════════════════════════════════════════
       FILTER / SEARCH
    ════════════════════════════════════════════ */
        .dl-search-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .dl-search-wrap {
            position: relative;
            width: 270px;
        }

        .dl-search-wrap i {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dl-text-muted);
            font-size: 12px;
            pointer-events: none;
            z-index: 2;
        }

        .dl-search-input {
            width: 100% !important;
            height: 35px !important;
            background: var(--dl-bg-elevated) !important;
            border: 1px solid var(--dl-border) !important;
            border-radius: var(--dl-radius-sm) !important;
            color: #fff !important;
            font-size: 13px !important;
            padding: 0 12px 0 33px !important;
            outline: none !important;
            font-family: inherit !important;
            transition: border-color .15s, box-shadow .15s;
        }

        .dl-search-input:focus {
            border-color: var(--dl-blue) !important;
            box-shadow: 0 0 0 3px var(--dl-blue-dim) !important;
        }

        .dl-search-input::placeholder { color: var(--dl-text-muted) !important; }

        .dl-fb-input {
            height: 33px !important;
            background: var(--dl-bg-overlay) !important;
            border: 1px solid var(--dl-border) !important;
            color: #fff !important;
            border-radius: var(--dl-radius-sm) !important;
            font-size: 12px !important;
            padding: 0 10px !important;
            min-width: 118px;
            transition: border-color .15s, box-shadow .15s;
            font-family: inherit !important;
            outline: none !important;
        }

        .dl-fb-input:focus {
            border-color: var(--dl-blue) !important;
            box-shadow: 0 0 0 3px var(--dl-blue-dim) !important;
        }

        .dl-fb-input option { background: #111829 !important; color: #fff !important; }
        .dl-fb-input::placeholder { color: var(--dl-text-muted) !important; }

        .dl-fb-btn {
            height: 33px;
            padding: 0 13px;
            border-radius: var(--dl-radius-sm);
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all .18s;
            border: none;
            white-space: nowrap;
            font-family: inherit;
            text-decoration: none !important;
        }

        .dl-fb-apply { background: var(--dl-blue); color: #fff !important; box-shadow: 0 2px 10px rgba(59,123,255,.3); }
        .dl-fb-apply:hover { background: #2d6ae8; }
        .dl-fb-reset { background: var(--dl-bg-overlay); border: 1px solid var(--dl-border) !important; color: var(--dl-text-secondary) !important; }
        .dl-fb-reset:hover { border-color: var(--dl-border-bright) !important; color: #fff !important; }

        /* ════════════════════════════════════════════
       LEADERBOARD — NEW CARD STYLE (matches screenshot)
    ════════════════════════════════════════════ */
        .dl-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 14px;
        }

        .dl-divider span {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--dl-text-muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        .dl-divider::before, .dl-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--dl-border);
        }

        .dl-lb-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 11px;
            margin-bottom: 23px;
        }

        @media (max-width: 1199px) { .dl-lb-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 767px)  { .dl-lb-grid { grid-template-columns: 1fr 1fr; } }

        /* The card itself */
        .dl-lb2-card {
            position: relative;
            border-radius: var(--dl-radius-lg) !important;
            border: 1px solid var(--lbc-border, rgba(255,255,255,.08)) !important;
            background: var(--lbc-bg, linear-gradient(135deg,rgba(16,185,129,.08) 0%,rgba(6,182,212,.04) 100%)) !important;
            box-shadow: 0 0 0 1px var(--lbc-glow, transparent), 0 8px 32px rgba(0,0,0,.35) !important;
            padding: 16px 16px 14px;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }

        .dl-lb2-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 0 1px var(--lbc-glow, transparent), 0 14px 40px rgba(0,0,0,.45) !important;
        }

        /* Glowing top border stripe */
        .dl-lb2-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: var(--lbc-stripe, linear-gradient(90deg,#10B981,#06B6D4));
            border-radius: var(--dl-radius-lg) var(--dl-radius-lg) 0 0;
        }

        /* Chart area (bottom-right absolutely positioned) */
        .dl-lb2-chart {
            position: absolute;
            bottom: 0; right: 0;
            width: 120px;
            height: 60px;
            pointer-events: none;
        }

        /* Eyebrow */
        .dl-lb2-eyebrow {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--lbc-accent, #10B981);
            margin-bottom: 13px;
        }

        /* Person row */
        .dl-lb2-person {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .dl-lb2-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--lbc-avatar, linear-gradient(135deg,#10B981,#34d399));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            position: relative;
            z-index: 2;
        }

        .dl-lb2-name {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .dl-lb2-zone {
            font-size: 10.5px;
            color: var(--dl-text-muted);
            margin-top: 2px;
        }

        /* Score */
        .dl-lb2-score {
            font-size: 22px;
            font-weight: 800;
            color: var(--lbc-score-color, #10B981);
            font-variant-numeric: tabular-nums;
            letter-spacing: -.03em;
            line-height: 1;
            position: relative;
            z-index: 2;
        }

        .dl-lb2-score-sub {
            font-size: 11px;
            color: var(--dl-text-muted);
            font-weight: 500;
            margin-left: 3px;
        }

        /* Pulse ring for top performer */
        .dl-pulse-wrap {
            position: relative;
            display: inline-block;
        }

        .dl-pulse-wrap::after {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            border: 2px solid var(--dl-emerald);
            opacity: 0;
            animation: dl-pulse 2.4s ease-out infinite;
        }

        @keyframes dl-pulse {
            0%   { opacity: .55; transform: scale(1); }
            70%  { opacity: 0;   transform: scale(1.48); }
            100% { opacity: 0;   transform: scale(1.48); }
        }

        /* ════════════════════════════════════════════
       TABLE
    ════════════════════════════════════════════ */
        .dl-tbl-wrap {
            overflow-x: auto;
        }

        .dl-tbl-wrap::-webkit-scrollbar { height: 4px; }
        .dl-tbl-wrap::-webkit-scrollbar-track { background: transparent; }
        .dl-tbl-wrap::-webkit-scrollbar-thumb { background: rgba(255,255,255,.09); border-radius: 4px; }

        .dl-table {
            width: 100%;
            min-width: 1260px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .dl-table thead th {
            background: var(--dl-bg-elevated);
            color: var(--dl-text-muted);
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .085em;
            text-transform: uppercase;
            padding: 11px 13px;
            white-space: nowrap;
            border-bottom: 1px solid var(--dl-border);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .dl-table thead th:first-child { padding-left: 20px; }

        .dl-table tbody tr { transition: background .13s; }
        .dl-table tbody tr:not(:last-child) td { border-bottom: 1px solid var(--dl-border); }
        .dl-table tbody tr:hover td { background: rgba(59,123,255,0.035) !important; }

        .dl-table tbody td {
            padding: 12px 13px;
            font-size: 13px;
            color: var(--dl-text-primary);
            white-space: nowrap;
            vertical-align: middle;
            background: transparent;
        }

        .dl-table tbody td:first-child { padding-left: 20px; }

        /* DATE CELL */
        .dl-date-main { font-size: 13px; font-weight: 600; color: #fff; font-variant-numeric: tabular-nums; }
        .dl-date-day  { font-size: 10.5px; color: var(--dl-text-muted); margin-top: 2px; }

        /* EXEC CELL */
        .dl-exec-avatar {
            width: 31px; height: 31px; border-radius: 50%;
            background: linear-gradient(135deg, var(--dl-blue), var(--dl-violet));
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
        }

        .dl-exec-name { font-size: 13px; font-weight: 600; color: #fff; }
        .dl-exec-id   { font-size: 10px; color: var(--dl-text-muted); font-family: 'Courier New', monospace; }

        /* UNI CELL */
        .dl-uni-name { font-size: 12px; font-weight: 600; color: var(--dl-text-primary); }
        .dl-uni-code { font-size: 10px; color: var(--dl-text-muted); }

        /* ZONE BADGE */
        .dl-zone-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px;
            background: var(--dl-blue-dim); color: var(--dl-blue);
            border: 1px solid rgba(59,123,255,.22);
        }

        /* CALLS BADGE */
        .dl-calls {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 700; padding: 4px 10px;
            border-radius: 7px; font-variant-numeric: tabular-nums;
        }

        .dl-calls-g { background: var(--dl-emerald-dim); color: var(--dl-emerald); border: 1px solid rgba(16,185,129,.22); }
        .dl-calls-y { background: var(--dl-amber-dim);   color: var(--dl-amber);   border: 1px solid rgba(245,158,11,.22); }
        .dl-calls-r { background: var(--dl-rose-dim);    color: var(--dl-rose);    border: 1px solid rgba(244,63,94,.22); }

        /* MEETINGS CELL */
        .dl-meet-ratio { font-size: 13px; font-weight: 600; line-height: 1; }
        .dl-meet-prog {
            width: 56px; height: 3px; border-radius: 2px;
            background: rgba(255,255,255,.08); margin-top: 5px; overflow: hidden;
        }
        .dl-meet-fill { height: 100%; border-radius: 2px; }

        /* KPI DOTS */
        .dl-kpi-row { display: flex; gap: 4px; align-items: center; justify-content: center; }
        .dl-kpi-dot {
            width: 22px; height: 22px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 9px; cursor: default;
        }
        .dl-kpi-pass { background: var(--dl-emerald-dim); color: var(--dl-emerald); border: 1px solid rgba(16,185,129,.28); }
        .dl-kpi-fail { background: rgba(255,255,255,.04); color: var(--dl-text-muted); border: 1px solid rgba(255,255,255,.07); }
        .dl-kpi-sub  { font-size: 10px; color: var(--dl-text-muted); margin-top: 3px; text-align: center; }

        /* VIOLATION */
        .dl-vio-yes {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 6px;
            background: var(--dl-rose-dim); color: var(--dl-rose);
            border: 1px solid rgba(244,63,94,.22);
        }
        .dl-vio-no { color: var(--dl-text-muted); font-size: 13px; }

        /* SCORE CELL */
        .dl-score-net { font-size: 18px; font-weight: 700; font-variant-numeric: tabular-nums; line-height: 1; }
        .dl-score-pos { color: var(--dl-emerald); }
        .dl-score-neg { color: var(--dl-rose); }

        /* TIER BADGE */
        .dl-tier {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 20px;
            letter-spacing: .045em; text-transform: uppercase;
        }
        .dl-tier-platinum { background: linear-gradient(135deg,rgba(168,85,247,.17),rgba(59,123,255,.1)); color: #c4b5fd; border: 1px solid rgba(168,85,247,.32); }
        .dl-tier-gold     { background: rgba(245,158,11,.13); color: #fbbf24; border: 1px solid rgba(245,158,11,.32); }
        .dl-tier-silver   { background: rgba(148,163,184,.11); color: #94a3b8; border: 1px solid rgba(148,163,184,.28); }
        .dl-tier-bronze   { background: rgba(180,83,9,.13); color: #d97706; border: 1px solid rgba(180,83,9,.3); }
        .dl-tier-review   { background: var(--dl-rose-dim); color: var(--dl-rose); border: 1px solid rgba(244,63,94,.28); }

        /* ACTION BTN */
        .dl-action-btn {
            width: 30px; height: 30px; border-radius: 7px;
            background: var(--dl-bg-overlay); border: 1px solid var(--dl-border);
            color: var(--dl-text-secondary);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 12px; cursor: pointer; transition: all .15s;
        }
        .dl-action-btn:hover { border-color: var(--dl-blue); color: var(--dl-blue); background: var(--dl-blue-dim); }

        /* DROPDOWN */
        .dl-dropdown-menu {
            background: var(--dl-bg-elevated) !important;
            border: 1px solid var(--dl-border-bright) !important;
            border-radius: var(--dl-radius-md) !important;
            box-shadow: 0 16px 40px rgba(0,0,0,.55) !important;
            padding: 6px !important;
            min-width: 178px !important;
        }
        .dl-dropdown-menu .dropdown-item {
            color: var(--dl-text-secondary) !important;
            font-size: 12.5px !important;
            border-radius: 7px !important;
            padding: 8px 12px !important;
            display: flex; align-items: center; gap: 9px;
            transition: background .12s, color .12s; font-family: inherit;
        }
        .dl-dropdown-menu .dropdown-item:hover { background: var(--dl-blue-dim) !important; color: #fff !important; }
        .dl-dropdown-menu .dropdown-item i { width: 13px; text-align: center; }
        .dl-dropdown-menu .dropdown-divider { border-color: var(--dl-border) !important; margin: 4px 0 !important; }

        /* PAGINATION */
        .dl-pag-wrap {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 20px; border-top: 1px solid var(--dl-border);
        }
        .dl-pag-info { font-size: 12px; color: var(--dl-text-muted); }
        .dl-pag-wrap .pagination .page-link {
            background: var(--dl-bg-elevated) !important;
            border: 1px solid var(--dl-border) !important;
            color: var(--dl-text-secondary) !important;
            font-size: 12px !important; border-radius: 7px !important;
            margin: 0 2px; min-width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center; transition: all .15s;
        }
        .dl-pag-wrap .pagination .page-link:hover { border-color: var(--dl-blue) !important; color: var(--dl-blue) !important; }
        .dl-pag-wrap .pagination .page-item.active .page-link {
            background: var(--dl-blue) !important; border-color: var(--dl-blue) !important;
            color: #fff !important; box-shadow: 0 2px 10px rgba(59,123,255,.4);
        }

        /* EMPTY STATE */
        .dl-empty {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 68px 20px; text-align: center;
        }
        .dl-empty-icon {
            width: 78px; height: 78px; border-radius: 50%;
            background: var(--dl-blue-dim); border: 1px solid rgba(59,123,255,.16);
            display: flex; align-items: center; justify-content: center; margin-bottom: 18px;
        }
        .dl-empty-icon i { font-size: 28px; color: var(--dl-blue); opacity: .65; }
        .dl-empty h4 { font-size: 16px; font-weight: 600; color: var(--dl-text-primary); margin-bottom: 6px; }
        .dl-empty p  { font-size: 13px; color: var(--dl-text-muted); margin-bottom: 18px; max-width: 340px; }

        /* OFFCANVAS */
        .offcanvas { background: var(--dl-bg-elevated) !important; border-left: 1px solid var(--dl-border-bright) !important; width: 310px !important; }
        .offcanvas-header { border-bottom: 1px solid var(--dl-border) !important; }
        .offcanvas-title  { font-size: 14px; font-weight: 600; color: #fff; }
        .dl-oc-label { font-size: 12px; font-weight: 600; color: var(--dl-text-secondary); margin-bottom: 5px; display: block; }
    </style>
@endsection

@section('content')

    {{-- PAGE HEADER --}}
    <div class="dl-page-hdr">
        <div>
            <div class="dl-search-row" style="margin-top:12px;">
                <div class="dl-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="dlGlobalSearch" class="dl-search-input"
                        placeholder="Search by name, ID, university, zone…">
                </div>
            </div>
        </div>
        <div class="dl-hdr-actions">
            <div style="position:relative;">
                <button type="button" id="dateFilterBtn" class="dl-btn dl-btn-ghost"
                    style="background:rgba(59,123,255,.08);border-color:rgba(59,123,255,.3)!important;color:var(--dl-blue)!important;">
                    <i class="fa-regular fa-calendar-check"></i>
                    @if($dateFrom === $dateTo)
                        {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
                    @endif
                </button>
                <input type="date" id="dateFilterInput" value="{{ $dateFrom }}"
                    style="position:absolute;opacity:0;width:1px;height:1px;pointer-events:none;">
            </div>
            <button class="dl-btn dl-btn-ghost" data-bs-toggle="offcanvas" data-bs-target="#dlFilterCanvas">
                <i class="fa-solid fa-sliders"></i> Filter
            </button>
            <button class="dl-btn dl-btn-green">
                <i class="fa-solid fa-file-excel"></i> Excel
            </button>
            <a href="{{ route('daily_logs.create') }}" class="dl-btn dl-btn-ghost">
                <i class="fa-solid fa-plus"></i> Add Log
            </a>
        </div>
    </div>

    {{-- LEADERBOARD --}}
    <div class="dl-divider">
        <span><i class="fa-solid fa-ranking-star" style="margin-right:6px;"></i>Today's Leaderboard</span>
    </div>

    <div class="dl-lb-grid">

        {{-- ① TOP PERFORMER --}}
        <div class="dl-lb2-card" style="
            --lbc-border: rgba(16,185,129,.30);
            --lbc-bg: linear-gradient(135deg,rgba(16,185,129,.09) 0%,rgba(6,182,212,.04) 100%);
            --lbc-glow: rgba(16,185,129,.18);
            --lbc-stripe: linear-gradient(90deg,#10B981,#34d399);
            --lbc-accent: #10B981;
            --lbc-avatar: linear-gradient(135deg,#10B981,#34d399);
            --lbc-score-color: #10B981;">

            {{-- sparkline chart (bottom-right) --}}
            <svg class="dl-lb2-chart" viewBox="0 0 120 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="grad-tp" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#10B981" stop-opacity=".35"/>
                        <stop offset="100%" stop-color="#10B981" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <polygon points="0,55 20,42 40,38 60,28 80,22 100,14 120,8 120,60 0,60" fill="url(#grad-tp)"/>
                <polyline points="0,55 20,42 40,38 60,28 80,22 100,14 120,8"
                          fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            <div class="dl-lb2-eyebrow">
                <i class="fa-solid fa-crown"></i> Top Performer
            </div>

            @if(isset($analytics['top_performer']))
                @php
                    $tp  = $analytics['top_performer'];
                    $tpI = collect(explode(' ', $tp->executive->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                @endphp
                <div class="dl-lb2-person">
                    <div class="dl-pulse-wrap">
                        <div class="dl-lb2-avatar">{{ $tpI }}</div>
                    </div>
                    <div>
                        <div class="dl-lb2-name">{{ $tp->executive->name }}</div>
                        <div class="dl-lb2-zone">{{ $tp->executive->zone->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="dl-lb2-score">+{{ $tp->calculated_score }}</div>
            @else
                <div style="font-size:12px;color:var(--dl-text-muted);padding:14px 0;">No data for today</div>
            @endif
        </div>

        {{-- ② NEEDS ATTENTION --}}
        <div class="dl-lb2-card" style="
            --lbc-border: rgba(244,63,94,.28);
            --lbc-bg: linear-gradient(135deg,rgba(244,63,94,.09) 0%,rgba(251,113,133,.03) 100%);
            --lbc-glow: rgba(244,63,94,.15);
            --lbc-stripe: linear-gradient(90deg,#F43F5E,#fb7185);
            --lbc-accent: #F43F5E;
            --lbc-avatar: linear-gradient(135deg,#F43F5E,#fb7185);
            --lbc-score-color: #F43F5E;">

            <svg class="dl-lb2-chart" viewBox="0 0 120 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="grad-na" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#F43F5E" stop-opacity=".30"/>
                        <stop offset="100%" stop-color="#F43F5E" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <polygon points="0,20 20,30 40,24 60,38 80,44 100,50 120,52 120,60 0,60" fill="url(#grad-na)"/>
                <polyline points="0,20 20,30 40,24 60,38 80,44 100,50 120,52"
                          fill="none" stroke="#F43F5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            <div class="dl-lb2-eyebrow">
                <i class="fa-solid fa-arrow-trend-down"></i> Needs Attention
            </div>

            @if(isset($analytics['lowest_performer']))
                @php
                    $lp  = $analytics['lowest_performer'];
                    $lpI = collect(explode(' ', $lp->executive->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                @endphp
                <div class="dl-lb2-person">
                    <div class="dl-lb2-avatar">{{ $lpI }}</div>
                    <div>
                        <div class="dl-lb2-name">{{ $lp->executive->name }}</div>
                        <div class="dl-lb2-zone">{{ $lp->executive->zone->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="dl-lb2-score">{{ $lp->calculated_score >= 0 ? '+' : '' }}{{ $lp->calculated_score }}</div>
            @else
                <div style="font-size:12px;color:var(--dl-text-muted);padding:14px 0;">No data for today</div>
            @endif
        </div>

        {{-- ③ MOST CALLS — bar chart --}}
        <div class="dl-lb2-card" style="
            --lbc-border: rgba(6,182,212,.28);
            --lbc-bg: linear-gradient(135deg,rgba(6,182,212,.09) 0%,rgba(59,123,255,.04) 100%);
            --lbc-glow: rgba(6,182,212,.15);
            --lbc-stripe: linear-gradient(90deg,#06B6D4,#3B7BFF);
            --lbc-accent: #06B6D4;
            --lbc-avatar: linear-gradient(135deg,#06B6D4,#3B7BFF);
            --lbc-score-color: #06B6D4;">

            {{-- bar chart (bottom-right) --}}
            <svg class="dl-lb2-chart" viewBox="0 0 120 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <g fill="#06B6D4" opacity=".55">
                    <rect x="10" y="40" width="10" height="20" rx="3"/>
                    <rect x="26" y="30" width="10" height="30" rx="3"/>
                    <rect x="42" y="22" width="10" height="38" rx="3"/>
                    <rect x="58" y="14" width="10" height="46" rx="3"/>
                    <rect x="74" y="20" width="10" height="40" rx="3"/>
                    <rect x="90" y="10" width="10" height="50" rx="3"/>
                    <rect x="106" y="5"  width="10" height="55" rx="3"/>
                </g>
            </svg>

            <div class="dl-lb2-eyebrow">
                <i class="fa-solid fa-phone-volume"></i> Most Calls
            </div>

            @if(isset($analytics['most_calls']))
                @php
                    $mc  = $analytics['most_calls'];
                    $mcI = collect(explode(' ', $mc->executive->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                @endphp
                <div class="dl-lb2-person">
                    <div class="dl-lb2-avatar">{{ $mcI }}</div>
                    <div>
                        <div class="dl-lb2-name">{{ $mc->executive->name }}</div>
                        <div class="dl-lb2-zone">{{ $mc->executive->zone->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="dl-lb2-score">
                    {{ $mc->connected_calls }}
                    <span class="dl-lb2-score-sub">calls</span>
                </div>
            @else
                <div style="font-size:12px;color:var(--dl-text-muted);padding:14px 0;">No data for today</div>
            @endif
        </div>

        {{-- ④ MOST MEETINGS — bar chart --}}
        <div class="dl-lb2-card" style="
            --lbc-border: rgba(124,58,237,.28);
            --lbc-bg: linear-gradient(135deg,rgba(124,58,237,.09) 0%,rgba(168,85,247,.04) 100%);
            --lbc-glow: rgba(124,58,237,.15);
            --lbc-stripe: linear-gradient(90deg,#7C3AED,#a855f7);
            --lbc-accent: #7C3AED;
            --lbc-avatar: linear-gradient(135deg,#7C3AED,#a855f7);
            --lbc-score-color: #a855f7;">

            <svg class="dl-lb2-chart" viewBox="0 0 120 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <g opacity=".55">
                    <rect x="10" y="35" width="10" height="25" rx="3" fill="#7C3AED"/>
                    <rect x="26" y="25" width="10" height="35" rx="3" fill="#8B47EF"/>
                    <rect x="42" y="18" width="10" height="42" rx="3" fill="#9654F1"/>
                    <rect x="58" y="10" width="10" height="50" rx="3" fill="#a855f7"/>
                    <rect x="74" y="16" width="10" height="44" rx="3" fill="#9654F1"/>
                    <rect x="90" y="8"  width="10" height="52" rx="3" fill="#a855f7"/>
                    <rect x="106" y="4" width="10" height="56" rx="3" fill="#bf7dff"/>
                </g>
            </svg>

            <div class="dl-lb2-eyebrow">
                <i class="fa-regular fa-handshake"></i> Most Meetings
            </div>

            @if(isset($analytics['most_meetings']))
                @php
                    $mm  = $analytics['most_meetings'];
                    $mmI = collect(explode(' ', $mm->executive->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                @endphp
                <div class="dl-lb2-person">
                    <div class="dl-lb2-avatar">{{ $mmI }}</div>
                    <div>
                        <div class="dl-lb2-name">{{ $mm->executive->name }}</div>
                        <div class="dl-lb2-zone">{{ $mm->executive->zone->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="dl-lb2-score">
                    {{ $mm->meetings_attended }}
                    <span class="dl-lb2-score-sub">/ {{ $mm->meetings_arranged }} met</span>
                </div>
            @else
                <div style="font-size:12px;color:var(--dl-text-muted);padding:14px 0;">No data for today</div>
            @endif
        </div>

        {{-- ⑤ MOST VIOLATIONS — dashed line chart --}}
        <div class="dl-lb2-card" style="
            --lbc-border: rgba(245,158,11,.28);
            --lbc-bg: linear-gradient(135deg,rgba(245,158,11,.09) 0%,rgba(251,191,36,.03) 100%);
            --lbc-glow: rgba(245,158,11,.15);
            --lbc-stripe: linear-gradient(90deg,#F59E0B,#fbbf24);
            --lbc-accent: #F59E0B;
            --lbc-avatar: linear-gradient(135deg,#F59E0B,#fbbf24);
            --lbc-score-color: #F59E0B;">

            <svg class="dl-lb2-chart" viewBox="0 0 120 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="grad-mv" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#F59E0B" stop-opacity=".28"/>
                        <stop offset="100%" stop-color="#F59E0B" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                {{-- Flat/dashed line representing violations --}}
                <line x1="8" y1="30" x2="112" y2="30" stroke="#F59E0B" stroke-width="1.5"
                      stroke-dasharray="6 4" opacity=".5"/>
                {{-- Dots at violation events --}}
                <circle cx="24"  cy="22" r="3.5" fill="#F59E0B" opacity=".75"/>
                <circle cx="52"  cy="18" r="3.5" fill="#F59E0B" opacity=".75"/>
                <circle cx="80"  cy="26" r="3.5" fill="#F59E0B" opacity=".75"/>
                <circle cx="104" cy="14" r="3.5" fill="#F59E0B" opacity=".75"/>
                {{-- Vertical drops --}}
                <line x1="24"  y1="22" x2="24"  y2="60" stroke="#F59E0B" stroke-width="1" stroke-dasharray="3 3" opacity=".25"/>
                <line x1="52"  y1="18" x2="52"  y2="60" stroke="#F59E0B" stroke-width="1" stroke-dasharray="3 3" opacity=".25"/>
                <line x1="80"  y1="26" x2="80"  y2="60" stroke="#F59E0B" stroke-width="1" stroke-dasharray="3 3" opacity=".25"/>
                <line x1="104" y1="14" x2="104" y2="60" stroke="#F59E0B" stroke-width="1" stroke-dasharray="3 3" opacity=".25"/>
            </svg>

            <div class="dl-lb2-eyebrow">
                <i class="fa-solid fa-triangle-exclamation"></i> Most Violations
            </div>

            @if(isset($analytics['most_violations']))
                @php
                    $mv  = $analytics['most_violations'];
                    $mvI = collect(explode(' ', $mv->executive->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                @endphp
                <div class="dl-lb2-person">
                    <div class="dl-lb2-avatar">{{ $mvI }}</div>
                    <div>
                        <div class="dl-lb2-name">{{ $mv->executive->name }}</div>
                        <div class="dl-lb2-zone">{{ $mv->executive->zone->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="dl-lb2-score">
                    {{ $mv->violations_count ?? '—' }}
                    <span class="dl-lb2-score-sub">violations</span>
                </div>
            @else
                <div style="font-size:12px;color:var(--dl-text-muted);padding:14px 0;">No data for today</div>
            @endif
        </div>

    </div>


    {{-- MAIN TABLE --}}
    <div class="dl-card" style="padding:0;overflow:hidden;">
        <div class="dl-tbl-wrap">
            <table class="dl-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Executive</th>
                        <th>University</th>
                        <th>Zone</th>
                        <th style="text-align:center;">Calls</th>
                        <th style="text-align:center;">Meetings</th>
                        <th style="text-align:center;">KPIs</th>
                        <th style="text-align:center;">Violations</th>
                        <th style="text-align:center;">Score</th>
                        <th style="text-align:center;">Tier</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="dlTableBody">

                    @forelse($logs as $log)
                        @php
                            $calls   = $log->connected_calls;
                            $callCls = $calls >= 65 ? 'dl-calls-g' : ($calls >= 40 ? 'dl-calls-y' : 'dl-calls-r');

                            $arranged = $log->meetings_arranged;
                            $attended = $log->meetings_attended;
                            $meetPct  = $arranged > 0 ? round(($attended / $arranged) * 100) : 0;
                            $meetColor= $meetPct >= 75 ? '#10B981' : ($meetPct >= 40 ? '#F59E0B' : '#F43F5E');

                            $kpi1 = $log->first_contact_within_45_min;
                            $kpi2 = $log->all_leads_followed_up;
                            $kpi3 = $log->crm_disposition_correct;
                            $kpi4 = $log->warm_lead_converted;
                            $kpiN = (int)$kpi1 + (int)$kpi2 + (int)$kpi3 + (int)$kpi4;

                            $score = $log->calculated_score ?? 0;

                            $initials = collect(explode(' ', $log->executive->name))
                                ->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');

                            $execTier  = strtolower($log->executive->current_tier ?? 'bronze');
                            $tierMap   = ['platinum'=>'Platinum','gold'=>'Gold','silver'=>'Silver','bronze'=>'Bronze','review_zone'=>'Review Zone'];
                            $tier      = in_array($execTier, array_keys($tierMap)) ? $execTier : 'bronze';
                            if ($tier === 'review_zone') $tier = 'review';
                            $tierLabel = $tierMap[$execTier] ?? 'Bronze';

                            $hasViolation = ($log->violation_status === 'active');

                            $searchData = strtolower(
                                ($log->executive->name ?? '') . ' ' .
                                ($log->executive->employee_id ?? '') . ' ' .
                                ($log->executive->zone->name ?? '') . ' ' .
                                ($log->executive->university->name ?? '')
                            );
                        @endphp
                        <tr data-search="{{ $searchData }}">

                            {{-- DATE --}}
                            <td>
                                <div class="dl-date-main">{{ $log->date->format('d M Y') }}</div>
                                <div class="dl-date-day">{{ $log->date->format('l') }}</div>
                            </td>

                            {{-- EXECUTIVE --}}
                            <td>
                                <a href="{{ route('executives.scorecard', $log->executive_id) }}"
                                    style="text-decoration:none;display:flex;align-items:center;gap:9px;">
                                    <div class="dl-exec-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="dl-exec-name">{{ $log->executive->name }}</div>
                                        <div class="dl-exec-id">{{ $log->executive->employee_id ?? '—' }}</div>
                                    </div>
                                </a>
                            </td>

                            {{-- UNIVERSITY --}}
                            <td>
                                <div class="dl-uni-name">{{ $log->executive->university->name ?? '—' }}</div>
                                <div class="dl-uni-code">{{ $log->executive->university->code ?? '' }}</div>
                            </td>

                            {{-- ZONE --}}
                            <td>
                                <span class="dl-zone-badge">
                                    <i class="fa-solid fa-location-dot" style="font-size:9px;"></i>
                                    {{ $log->executive->zone->name ?? '—' }}
                                </span>
                            </td>

                            {{-- CALLS --}}
                            <td style="text-align:center;">
                                <span class="dl-calls {{ $callCls }}">
                                    <i class="fa-solid fa-phone" style="font-size:10px;"></i>{{ $calls }}
                                </span>
                            </td>

                            {{-- MEETINGS --}}
                            <td style="text-align:center;">
                                <div class="dl-meet-ratio">
                                    <span style="color:#fff;font-weight:700;">{{ $attended }}</span>
                                    <span style="color:rgba(240,244,255,.35);font-size:12px;"> / {{ $arranged }}</span>
                                </div>
                                <div class="dl-meet-prog">
                                    <div class="dl-meet-fill" style="width:{{ $meetPct }}%;background:{{ $meetColor }};"></div>
                                </div>
                            </td>

                            {{-- KPIs --}}
                            <td style="text-align:center;">
                                <div class="dl-kpi-row">
                                    <div class="dl-kpi-dot {{ $kpi1 ? 'dl-kpi-pass' : 'dl-kpi-fail' }}" data-bs-toggle="tooltip"
                                        title="{{ $kpi1 ? '✓' : '✗' }} First contact ≤45 min">
                                        <i class="fa-solid fa-clock" style="font-size:9px;"></i>
                                    </div>
                                    <div class="dl-kpi-dot {{ $kpi2 ? 'dl-kpi-pass' : 'dl-kpi-fail' }}" data-bs-toggle="tooltip"
                                        title="{{ $kpi2 ? '✓' : '✗' }} All leads followed up">
                                        <i class="fa-solid fa-phone-volume" style="font-size:9px;"></i>
                                    </div>
                                    <div class="dl-kpi-dot {{ $kpi3 ? 'dl-kpi-pass' : 'dl-kpi-fail' }}" data-bs-toggle="tooltip"
                                        title="{{ $kpi3 ? '✓' : '✗' }} CRM disposition accurate">
                                        <i class="fa-solid fa-database" style="font-size:9px;"></i>
                                    </div>
                                    <div class="dl-kpi-dot {{ $kpi4 ? 'dl-kpi-pass' : 'dl-kpi-fail' }}" data-bs-toggle="tooltip"
                                        title="{{ $kpi4 ? '✓' : '✗' }} Warm lead converted">
                                        <i class="fa-solid fa-fire" style="font-size:9px;"></i>
                                    </div>
                                </div>
                                <div class="dl-kpi-sub">{{ $kpiN }}/4 met</div>
                            </td>

                            {{-- VIOLATIONS --}}
                            <td style="text-align:center;">
                                @if($hasViolation)
                                    <span class="dl-vio-yes">
                                        <i class="fa-solid fa-triangle-exclamation" style="font-size:10px;"></i>Yes
                                    </span>
                                @else
                                    <span class="dl-vio-no">—</span>
                                @endif
                            </td>

                            {{-- SCORE --}}
                            <td style="text-align:center;">
                                <div class="dl-score-net {{ $score >= 0 ? 'dl-score-pos' : 'dl-score-neg' }}">
                                    {{ $score >= 0 ? '+' : '' }}{{ $score }}
                                </div>
                            </td>

                            {{-- TIER --}}
                            <td style="text-align:center;">
                                <span class="dl-tier dl-tier-{{ $tier }}">
                                    @if($tier === 'platinum')<i class="fa-solid fa-gem" style="font-size:9px;"></i>
                                    @elseif($tier === 'gold')<i class="fa-solid fa-trophy" style="font-size:9px;"></i>
                                    @elseif($tier === 'silver')<i class="fa-solid fa-medal" style="font-size:9px;"></i>
                                    @elseif($tier === 'bronze')<i class="fa-solid fa-award" style="font-size:9px;"></i>
                                    @else<i class="fa-solid fa-flag" style="font-size:9px;"></i>
                                    @endif
                                    {{ $tierLabel }}
                                </span>
                            </td>

                            {{-- ACTIONS --}}
                            <td style="text-align:center;">
                                <div class="dropdown">
                                    <button class="dl-action-btn" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end dl-dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('executives.scorecard', $log->executive_id) }}">
                                                <i class="fa-solid fa-chart-line"></i>View Score</a></li>
                                        <li><a class="dropdown-item" href="">
                                                <i class="fa-regular fa-pen-to-square"></i>Edit Log</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('executives.scorecard', $log->executive_id) }}">
                                                <i class="fa-solid fa-user"></i>Executive Profile</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="dl-empty">
                                    <div class="dl-empty-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
                                    <h4>No performance logs found</h4>
                                    <p>No entries match your current filters. Try adjusting your criteria or log today's performance.</p>
                                    @can('enter_daily_logs')
                                        <a href="{{ route('daily_logs.create') }}" class="dl-btn dl-btn-primary"
                                            style="text-decoration:none;">
                                            <i class="fa-solid fa-plus"></i>Log Today's Performance
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($logs->hasPages())
            <div class="dl-pag-wrap">
                <span class="dl-pag-info">
                    Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} records
                </span>
                {{ $logs->links() }}
            </div>
        @endif
    </div>


    {{-- OFFCANVAS FILTER --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="dlFilterCanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="fa-solid fa-sliders me-2" style="color:#3B7BFF;"></i>Advanced Filters</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form method="GET" action="{{ route('daily_logs.index') }}">

                <div class="mb-3">
                    <label class="dl-oc-label">Quick Date</label>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        <a href="{{ route('daily_logs.index', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                            class="dl-fb-btn"
                            style="background:var(--dl-blue-dim);color:var(--dl-blue);border:1px solid rgba(59,123,255,.25);font-size:11px;height:28px;padding:0 10px;">Today</a>
                        <a href="{{ route('daily_logs.index', ['date_from' => now()->subDay()->toDateString(), 'date_to' => now()->subDay()->toDateString()]) }}"
                            class="dl-fb-btn"
                            style="background:var(--dl-bg-overlay);color:var(--dl-text-secondary);border:1px solid var(--dl-border);font-size:11px;height:28px;padding:0 10px;">Yesterday</a>
                        <a href="{{ route('daily_logs.index', ['date_from' => now()->startOfWeek()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                            class="dl-fb-btn"
                            style="background:var(--dl-bg-overlay);color:var(--dl-text-secondary);border:1px solid var(--dl-border);font-size:11px;height:28px;padding:0 10px;">This Week</a>
                        <a href="{{ route('daily_logs.index', ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                            class="dl-fb-btn"
                            style="background:var(--dl-bg-overlay);color:var(--dl-text-secondary);border:1px solid var(--dl-border);font-size:11px;height:28px;padding:0 10px;">This Month</a>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="dl-oc-label">Date From</label>
                    <input type="date" name="date_from" class="dl-fb-input" style="width:100%;min-width:unset;" value="{{ $dateFrom }}">
                </div>
                <div class="mb-3">
                    <label class="dl-oc-label">Date To</label>
                    <input type="date" name="date_to" class="dl-fb-input" style="width:100%;min-width:unset;" value="{{ $dateTo }}">
                </div>
                <div class="mb-3">
                    <label class="dl-oc-label">University</label>
                    <select name="university_id" class="dl-fb-input" style="width:100%;min-width:unset;">
                        <option value="">All Universities</option>
                        @foreach($universities ?? [] as $uni)
                            <option value="{{ $uni->id }}" {{ request('university_id') == $uni->id ? 'selected' : '' }}>{{ $uni->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="dl-oc-label">Zone</label>
                    <select name="zone_id" class="dl-fb-input" style="width:100%;min-width:unset;">
                        <option value="">All Zones</option>
                        @foreach($zones ?? [] as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="dl-oc-label">Executive</label>
                    <select name="executive_id" class="dl-fb-input" style="width:100%;min-width:unset;">
                        <option value="">All Executives</option>
                        @foreach($executives ?? [] as $exec)
                            <option value="{{ $exec->id }}" {{ request('executive_id') == $exec->id ? 'selected' : '' }}>{{ $exec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="dl-oc-label">Tier</label>
                    <select name="tier" class="dl-fb-input" style="width:100%;min-width:unset;">
                        <option value="">All Tiers</option>
                        <option value="platinum"    {{ request('tier') == 'platinum'    ? 'selected' : '' }}>Platinum</option>
                        <option value="gold"        {{ request('tier') == 'gold'        ? 'selected' : '' }}>Gold</option>
                        <option value="silver"      {{ request('tier') == 'silver'      ? 'selected' : '' }}>Silver</option>
                        <option value="bronze"      {{ request('tier') == 'bronze'      ? 'selected' : '' }}>Bronze</option>
                        <option value="review_zone" {{ request('tier') == 'review_zone' ? 'selected' : '' }}>Review Zone</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="dl-oc-label">KPI Status</label>
                    <select name="kpi_status" class="dl-fb-input" style="width:100%;min-width:unset;">
                        <option value="">Any</option>
                        <option value="passed"  {{ request('kpi_status') == 'passed'  ? 'selected' : '' }}>Passed</option>
                        <option value="partial" {{ request('kpi_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="failed"  {{ request('kpi_status') == 'failed'  ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="dl-oc-label">Violation Status</label>
                    <select name="violation_status" class="dl-fb-input" style="width:100%;min-width:unset;">
                        <option value="">Any</option>
                        <option value="active" {{ request('violation_status') == 'active' ? 'selected' : '' }}>With Violations</option>
                        <option value="none"   {{ request('violation_status') == 'none'   ? 'selected' : '' }}>Clean Records</option>
                    </select>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="dl-fb-btn dl-fb-apply" style="flex:1;justify-content:center;">
                        <i class="fa-solid fa-magnifying-glass"></i>Apply Filters
                    </button>
                    <a href="{{ route('daily_logs.index') }}" class="dl-fb-btn dl-fb-reset" style="padding:0 13px;">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Bootstrap tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el, { placement: 'top', trigger: 'hover' });
            });

            // Real-time client-side search
            var searchEl = document.getElementById('dlGlobalSearch');
            var rows     = document.querySelectorAll('#dlTableBody tr[data-search]');

            if (searchEl) {
                searchEl.addEventListener('input', function () {
                    var q = this.value.toLowerCase().trim();
                    rows.forEach(function (row) {
                        var hay = row.getAttribute('data-search') || '';
                        row.style.display = (!q || hay.indexOf(q) !== -1) ? '' : 'none';
                    });
                });
            }

            // Date filter chip
            var dateBtn   = document.getElementById('dateFilterBtn');
            var dateInput = document.getElementById('dateFilterInput');

            if (dateBtn && dateInput) {
                dateBtn.addEventListener('click', function () {
                    if (dateInput.showPicker) {
                        dateInput.showPicker();
                    } else {
                        dateInput.click();
                    }
                });

                dateInput.addEventListener('change', function () {
                    if (!this.value) return;
                    var url = new URL(window.location.href);
                    url.searchParams.set('date_from', this.value);
                    url.searchParams.set('date_to',   this.value);
                    window.location.href = url.toString();
                });
            }
        });
    </script>
@endsection