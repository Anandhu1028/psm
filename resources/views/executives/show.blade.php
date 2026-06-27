@extends('layouts.app')
@section('title', $executive->name . ' — Profile')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('executives.index') }}">Executives</a></li>
        <li class="breadcrumb-item active">{{ $executive->name }}</li>
    </ol>
@endsection

@push('styles')
    <style>
        .ep-shell * {
            box-sizing: border-box;
        }

        /* ══════════════════════ Header ══════════════════════ */
        .ep-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, 0.7);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 16px;
        }

        .ep-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .ep-header-avatar {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            flex-shrink: 0;
            background: linear-gradient(135deg, #4338ca, #a5b4fc);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.03em;
            box-shadow: 0 4px 14px rgba(67, 56, 202, 0.25);
        }

        .ep-header-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0d0f1c;
            letter-spacing: -0.03em;
            margin: 0 0 2px;
            line-height: 1.2;
        }

        .ep-header-sub {
            font-size: 0.8rem;
            color: #94a3b8;
            font-weight: 500;
            margin: 0;
        }

        .ep-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ep-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 38px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.18s ease;
            border: 1.5px solid transparent;
            white-space: nowrap;
        }

        .ep-btn-secondary {
            background: #fff;
            color: #4a5568;
            border-color: #e8eaf2;
        }

        .ep-btn-secondary:hover {
            background: #f8f9fc;
            border-color: #c4b5fd;
            color: #4f46e5;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .ep-btn-primary {
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.28);
        }

        .ep-btn-primary:hover {
            background: #4338ca;
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        /* ══════════════════════ Card system ══════════════════════ */
        .ep-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.7);
            overflow: hidden;
            margin-bottom: 16px;
            transition: box-shadow 0.2s ease;
        }

        .ep-card:last-child {
            margin-bottom: 0;
        }

        .ep-card:hover {
            box-shadow: 0 6px 24px rgba(15, 23, 42, 0.05);
        }

        .ep-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 18px;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbff;
        }

        .ep-card-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .ep-card-title {
            font-size: 0.82rem;
            font-weight: 800;
            color: #1e1f2e;
            letter-spacing: -0.01em;
        }

        .ep-card-link {
            margin-left: auto;
            font-size: 0.74rem;
            font-weight: 700;
            color: #6366f1;
            text-decoration: none;
            white-space: nowrap;
        }

        .ep-card-link:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        .ep-card-body {
            padding: 16px 18px;
        }

        /* ══════════════════════ Main grid ══════════════════════ */
        .ep-main-grid {
            display: grid;
            grid-template-columns: minmax(250px, 300px) minmax(0, 1fr);
            gap: 16px;
            align-items: start;
        }

        .ep-col-left,
        .ep-col-right {
            min-width: 0;
        }

        /* ── Profile card ── */
        .ep-profile-body {
            text-align: center;
            padding: 6px 4px 4px;
        }

        .ep-profile-avatar {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            margin: 0 auto 12px;
            background: linear-gradient(135deg, #4338ca, #a5b4fc);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.03em;
            box-shadow: 0 4px 14px rgba(67, 56, 202, 0.25);
        }

        .ep-profile-name {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1e1f2e;
            letter-spacing: -0.02em;
        }

        .ep-profile-sub {
            font-size: 0.68rem;
            color: #b0b8d1;
            font-weight: 500;
            font-family: 'SF Mono', 'Consolas', monospace;
            margin-top: 2px;
            margin-bottom: 12px;
        }

        .ep-tier-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 28px;
            padding: 0 14px;
            border-radius: 100px;
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            border: 1px solid;
            margin-bottom: 16px;
        }

        /* 2-column info grid: Company|Zone, Mobile|Email, Joined|Status */
        .ep-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            text-align: left;
        }

        .ep-info-item {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 10px;
            border-radius: 10px;
            background: #fafbff;
            border: 1px solid #f0f2fa;
            min-width: 0;
        }

        .ep-info-icon {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            background: #eef2ff;
            color: #6366f1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .ep-info-text {
            min-width: 0;
        }

        .ep-info-lbl {
            font-size: 0.56rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #b0b8d1;
            margin-bottom: 1px;
        }

        .ep-info-val {
            font-size: 0.78rem;
            color: #1e1f2e;
            font-weight: 600;
            word-break: break-word;
            line-height: 1.25;
        }

        /* ── Score summary ── */
        .ep-score-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .ep-score-tile {
            border-radius: 12px;
            padding: 13px 10px;
            text-align: center;
        }

        .ep-score-val {
            font-size: 1.15rem;
            font-weight: 900;
            letter-spacing: -0.04em;
            line-height: 1;
        }

        .ep-score-lbl {
            font-size: 0.58rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-top: 5px;
        }

        .ep-score-total {
            background: #eef2ff;
        }

        .ep-score-total .ep-score-val {
            color: #3730a3;
        }

        .ep-score-total .ep-score-lbl {
            color: #a5b4fc;
        }

        .ep-score-monthly {
            background: #ecfdf5;
        }

        .ep-score-monthly .ep-score-val {
            color: #059669;
        }

        .ep-score-monthly .ep-score-lbl {
            color: #6ee7b7;
        }

        .ep-score-call {
            background: #fffbeb;
        }

        .ep-score-call .ep-score-val {
            color: #d97706;
        }

        .ep-score-call .ep-score-lbl {
            color: #fcd34d;
        }

        .ep-score-meeting {
            background: #eff6ff;
        }

        .ep-score-meeting .ep-score-val {
            color: #2563eb;
        }

        .ep-score-meeting .ep-score-lbl {
            color: #93c5fd;
        }

        /* ── Chart card ── */
        .ep-chart-wrap {
            position: relative;
            padding: 6px 4px 0;
            height: 240px;
        }

        .ep-chart-empty {
            position: absolute;
            inset: 0;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #b0b8d1;
            gap: 6px;
        }

        .ep-chart-empty i {
            font-size: 1.4rem;
        }

        .ep-chart-empty p {
            font-size: 0.8rem;
            margin: 0;
            font-weight: 500;
        }

        /* ── Split row (Recent audits + Tier history) ── */
        .ep-split-row {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 16px;
            align-items: start;
        }

        /* ── Tables (shared) ── */
        .ep-table-wrap {
            overflow-x: auto;
        }

        .ep-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.79rem;
        }

        .ep-table thead tr {
            background: #fafbff;
            border-bottom: 1px solid #f0f2fa;
        }

        .ep-table thead th {
            padding: 9px 16px;
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #b0b8d1;
            white-space: nowrap;
        }

        .ep-table thead th:first-child {
            padding-left: 18px;
        }

        .ep-table thead th:last-child {
            padding-right: 18px;
        }

        .ep-table tbody tr {
            border-bottom: 1px solid #f7f8fc;
            transition: background 0.12s;
        }

        .ep-table tbody tr:last-child {
            border-bottom: none;
        }

        .ep-table tbody tr:hover {
            background: #fafaff;
        }

        .ep-table tbody td {
            padding: 11px 16px;
            vertical-align: middle;
            color: #374151;
        }

        .ep-table tbody td:first-child {
            padding-left: 18px;
        }

        .ep-table tbody td:last-child {
            padding-right: 18px;
        }

        .ep-table .text-center {
            text-align: center;
        }

        .ep-table .text-end {
            text-align: right;
        }

        .ep-scroll-area {
            max-height: 320px;
            overflow-y: auto;
        }

        .ep-scroll-area::-webkit-scrollbar {
            width: 8px;
        }

        .ep-scroll-area::-webkit-scrollbar-track {
            background: #fafbff;
        }

        .ep-scroll-area::-webkit-scrollbar-thumb {
            background: #d8deee;
            border-radius: 8px;
        }

        .ep-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #c4b5fd;
        }

        .ep-link-date {
            color: #6366f1;
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
        }

        .ep-link-date:hover {
            text-decoration: underline;
        }

        .ep-num-cell {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #475569;
            font-weight: 600;
        }

        .ep-num-cell i {
            color: #c7ccdb;
            font-size: 0.65rem;
        }

        .ep-score-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 56px;
            height: 26px;
            padding: 0 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .ep-kpi-dot {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            font-size: 0.62rem;
            font-weight: 700;
        }

        .ep-kpi-pass {
            background: #d1fae5;
            color: #059669;
        }

        .ep-kpi-fail {
            background: #fee2e2;
            color: #e11d48;
        }

        .ep-kpi-none {
            background: #f1f5f9;
            color: #cbd5e1;
        }

        .ep-cat-badge {
            display: inline-flex;
            align-items: center;
            height: 22px;
            padding: 0 9px;
            background: #f8f9fc;
            border: 1px solid #edf0f7;
            border-radius: 6px;
            font-size: 0.63rem;
            font-weight: 700;
            color: #64748b;
            text-transform: capitalize;
        }

        /* ── Empty state ── */
        .ep-empty {
            text-align: center;
            padding: 32px 16px;
            color: #b0b8d1;
        }

        .ep-empty i {
            font-size: 1.3rem;
            margin-bottom: 8px;
            display: block;
        }

        .ep-empty p {
            font-size: 0.78rem;
            margin: 0;
        }

        /* ══════════════════════ Tier history — modern timeline ══════════════════════ */
        .ep-timeline {
            position: relative;
            padding-left: 24px;
        }

        .ep-timeline::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 4px;
            bottom: 4px;
            width: 2px;
            background: linear-gradient(to bottom, #e0e7ff, #f1f5f9);
            border-radius: 2px;
        }

        .ep-timeline-item {
            position: relative;
            padding-bottom: 12px;
        }

        .ep-timeline-item:last-child {
            padding-bottom: 0;
        }

        .ep-timeline-dot {
            position: absolute;
            left: -24px;
            top: 50%;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            transform: translateY(-50%);
            background: #fff;
            border: 3px solid #6366f1;
            box-shadow: 0 0 0 3px #eef2ff;
        }

        .ep-timeline-content {
            padding: 11px 13px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ep-timeline-tiers {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ep-timeline-chip {
            display: inline-flex;
            align-items: center;
            height: 23px;
            padding: 0 10px;
            border-radius: 100px;
            font-size: 0.66rem;
            font-weight: 800;
            border: 1px solid;
            white-space: nowrap;
        }

        .ep-timeline-arrow {
            color: #cbd5e1;
            font-size: 0.7rem;
        }

        .ep-timeline-date {
            font-size: 0.68rem;
            color: #94a3b8;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            margin-left: auto;
        }

        .ep-timeline-date i {
            font-size: 0.62rem;
            color: #c7ccdb;
        }

        /* ══════════════════════ Transactions pagination ══════════════════════ */
        .ep-tx-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 18px;
            border-top: 1px solid #f0f2fa;
            background: #fafbff;
            flex-wrap: wrap;
        }

        .ep-tx-footer-info {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .ep-tx-footer-info strong {
            color: #4a5568;
            font-weight: 700;
        }

        .ep-pager {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ep-pager-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 30px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1.5px solid #e8eaf2;
            background: #fff;
            color: #4a5568;
            font-size: 0.74rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .ep-pager-btn:hover:not(:disabled) {
            background: #f5f3ff;
            border-color: #c4b5fd;
            color: #4f46e5;
        }

        .ep-pager-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .ep-pager-label {
            font-size: 0.74rem;
            font-weight: 700;
            color: #1e1f2e;
            white-space: nowrap;
            min-width: 70px;
            text-align: center;
        }

        @media (max-width: 1199px) {
            .ep-main-grid {
                grid-template-columns: 1fr;
            }

            .ep-split-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575px) {
            .ep-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .ep-score-grid {
                grid-template-columns: 1fr 1fr;
            }

            .ep-info-grid {
                grid-template-columns: 1fr;
            }

            .ep-timeline-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .ep-timeline-date {
                margin-left: 0;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $tierPalette = [
            'platinum' => ['bg' => '#eef2ff', 'fg' => '#4338ca', 'bd' => '#c7d2fe'],
            'gold' => ['bg' => '#fffbeb', 'fg' => '#b45309', 'bd' => '#fde68a'],
            'silver' => ['bg' => '#f8fafc', 'fg' => '#475569', 'bd' => '#e2e8f0'],
            'bronze' => ['bg' => '#fff7ed', 'fg' => '#c2410c', 'bd' => '#fed7aa'],
        ];
        $tierStyle = function ($key) use ($tierPalette) {
            $k = strtolower((string) $key);
            if ($k === '') {
                return ['bg' => '#f8fafc', 'fg' => '#94a3b8', 'bd' => '#e2e8f0'];
            }
            if (stripos($k, 'review') !== false || stripos($k, 'probation') !== false) {
                return ['bg' => '#fff1f2', 'fg' => '#e11d48', 'bd' => '#fecdd3'];
            }
            return $tierPalette[$k] ?? ['bg' => '#eff6ff', 'fg' => '#2563eb', 'bd' => '#bfdbfe'];
        };
        $execTierStyle = $tierStyle($executive->current_tier);

        $perPage = 8;
        $txCount = $executive->pointTransactions->count();
        $txTotalPages = max(1, (int) ceil($txCount / $perPage));

        $isActive = $executive->status === 'active';
    @endphp

    <div class="ep-shell">

        {{-- HEADER --}}
        <div class="ep-header">
            <div class="ep-header-left">
                <div class="ep-header-avatar">{{ strtoupper(substr($executive->name, 0, 2)) }}</div>
                <div>
                    <h1 class="ep-header-name">{{ $executive->name }}</h1>
                    <p class="ep-header-sub">{{ $executive->employee_id }} · {{ $executive->company->name }} ·
                        {{ $executive->zone->name }}</p>
                </div>
            </div>
            <div class="ep-header-actions">
                @can('manage_executives')
                    <a href="{{ route('executives.edit', $executive) }}" class="ep-btn ep-btn-secondary">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                @endcan
                <a href="{{ route('daily_audit.create') }}?executive_id={{ $executive->id }}" class="ep-btn ep-btn-primary">
                    <i class="fa-solid fa-plus"></i> Enter Audit
                </a>
            </div>
        </div>

        <div class="ep-main-grid">

            {{-- ───── LEFT COLUMN ───── --}}
            <div class="ep-col-left">

                {{-- Profile card --}}
                <div class="ep-card">
                    <div class="ep-card-body ep-profile-body">
                        <div class="ep-profile-avatar">{{ strtoupper(substr($executive->name, 0, 2)) }}</div>
                        <div class="ep-profile-name">{{ $executive->name }}</div>
                        <div class="ep-profile-sub">{{ $executive->employee_id }}</div>

                        <div>
                            <span class="ep-tier-badge"
                                style="background:{{ $execTierStyle['bg'] }};color:{{ $execTierStyle['fg'] }};border-color:{{ $execTierStyle['bd'] }};">
                                <i class="fa-solid fa-medal"></i> {{ $executive->tier_label }}
                            </span>
                        </div>

                        {{-- 2-column info grid: Company | Zone · Mobile | Email · Joined | Status --}}
                        <div class="ep-info-grid">
                            <div class="ep-info-item">
                                <div class="ep-info-icon"><i class="fa-solid fa-building"></i></div>
                                <div class="ep-info-text">
                                    <div class="ep-info-lbl">Company</div>
                                    <div class="ep-info-val">{{ $executive->company->name }}</div>
                                </div>
                            </div>
                            <div class="ep-info-item">
                                <div class="ep-info-icon"><i class="fa-solid fa-map-marker-alt"></i></div>
                                <div class="ep-info-text">
                                    <div class="ep-info-lbl">Zone</div>
                                    <div class="ep-info-val">{{ $executive->zone->name }}</div>
                                </div>
                            </div>

                            <div class="ep-info-item">
                                <div class="ep-info-icon"><i class="fa-solid fa-mobile-alt"></i></div>
                                <div class="ep-info-text">
                                    <div class="ep-info-lbl">Mobile</div>
                                    <div class="ep-info-val">{{ $executive->mobile ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="ep-info-item">
                                <div class="ep-info-icon"><i class="fa-solid fa-envelope"></i></div>
                                <div class="ep-info-text">
                                    <div class="ep-info-lbl">Email</div>
                                    <div class="ep-info-val">{{ $executive->email ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="ep-info-item">
                                <div class="ep-info-icon"><i class="fa-solid fa-calendar-alt"></i></div>
                                <div class="ep-info-text">
                                    <div class="ep-info-lbl">Joined</div>
                                    <div class="ep-info-val">{{ $executive->date_joined?->format('d M Y') ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="ep-info-item"
                                style="background:{{ $isActive ? '#ecfdf5' : '#fffbeb' }};border-color:{{ $isActive ? '#a7f3d0' : '#fde68a' }};">
                                <div class="ep-info-icon"
                                    style="background:#fff;color:{{ $isActive ? '#10b981' : '#f59e0b' }};">
                                    <i class="fa-solid fa-circle" style="font-size:0.45rem;"></i>
                                </div>
                                <div class="ep-info-text">
                                    <div class="ep-info-lbl">Status</div>
                                    <div class="ep-info-val" style="color:{{ $isActive ? '#059669' : '#d97706' }};">
                                        {{ ucfirst($executive->status) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Score Summary --}}
                <div class="ep-card">
                    <div class="ep-card-header">
                        <div class="ep-card-icon" style="background:#fffbeb;color:#f59e0b;"><i
                                class="fa-solid fa-coins"></i></div>
                        <span class="ep-card-title">Score Summary</span>
                    </div>
                    <div class="ep-card-body">
                        <div class="ep-score-grid">
                            <div class="ep-score-tile ep-score-total">
                                <div class="ep-score-val">{{ number_format($executive->current_score) }}</div>
                                <div class="ep-score-lbl">Total Score</div>
                            </div>
                            <div class="ep-score-tile ep-score-monthly">
                                <div class="ep-score-val">{{ number_format($executive->monthly_score) }}</div>
                                <div class="ep-score-lbl">Monthly Score</div>
                            </div>
                            <div class="ep-score-tile ep-score-call">
                                <div class="ep-score-val">{{ $executive->call_streak_count ?? 0 }}d</div>
                                <div class="ep-score-lbl">Call Streak</div>
                            </div>
                            <div class="ep-score-tile ep-score-meeting">
                                <div class="ep-score-val">{{ $executive->meeting_streak_count ?? 0 }}d</div>
                                <div class="ep-score-lbl">Mtg Streak</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ───── RIGHT COLUMN ───── --}}
            <div class="ep-col-right">

                {{-- Monthly Score Chart --}}
                <div class="ep-card">
                    <div class="ep-card-header">
                        <div class="ep-card-icon" style="background:#eef2ff;color:#6366f1;"><i
                                class="fa-solid fa-chart-line"></i></div>
                        <span class="ep-card-title">Monthly Score History</span>
                    </div>
                    <div class="ep-card-body">
                        <div class="ep-chart-wrap">
                            <canvas id="monthlyScoreChart"></canvas>
                            <div class="ep-chart-empty" id="epChartEmpty">
                                <i class="fa-solid fa-chart-line"></i>
                                <p>No score history yet</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ep-split-row">

                    {{-- Recent Audits --}}
                    <div class="ep-card">
                        <div class="ep-card-header">
                            <div class="ep-card-icon" style="background:#ecfdf5;color:#10b981;"><i
                                    class="fa-solid fa-clock-rotate-left"></i></div>
                            <span class="ep-card-title">Recent Audits</span>
                            <a href="{{ route('daily_audit.index', ['executive_id' => $executive->id]) }}"
                                class="ep-card-link">View All</a>
                        </div>
                        <div class="ep-scroll-area">
                            <div class="ep-table-wrap">
                                <table class="ep-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th class="text-center">Calls</th>
                                            <th class="text-center">Meetings</th>
                                            <th class="text-center">Score</th>
                                            <th class="text-center">KPI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($executive->dailyAudits as $audit)
                                            <tr>
                                                <td><a href="{{ route('daily_audit.show', $audit) }}"
                                                        class="ep-link-date">{{ $audit->audit_date->format('d M Y') }}</a></td>
                                                <td class="text-center">
                                                    <span class="ep-num-cell"><i
                                                            class="fa-solid fa-phone"></i>{{ $audit->connected_calls }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="ep-num-cell"><i
                                                            class="fa-solid fa-handshake"></i>{{ $audit->confirmed_meetings }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="ep-score-pill"
                                                        style="background:{{ $audit->final_score >= 0 ? '#ecfdf5' : '#fff1f2' }};color:{{ $audit->final_score >= 0 ? '#059669' : '#e11d48' }};">
                                                        {{ $audit->final_score >= 0 ? '+' : '' }}{{ $audit->final_score }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($audit->kpi_status === 'passed')
                                                        <span class="ep-kpi-dot ep-kpi-pass"><i
                                                                class="fa-solid fa-check"></i></span>
                                                    @elseif($audit->kpi_status === 'failed')
                                                        <span class="ep-kpi-dot ep-kpi-fail"><i
                                                                class="fa-solid fa-xmark"></i></span>
                                                    @else
                                                        <span class="ep-kpi-dot ep-kpi-none">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">
                                                    <div class="ep-empty"><i class="fa-solid fa-clipboard"></i>
                                                        <p>No audits yet</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Tier History — modern timeline, date right-aligned --}}
                    <div class="ep-card">
                        <div class="ep-card-header">
                            <div class="ep-card-icon" style="background:#f5f3ff;color:#7c3aed;"><i
                                    class="fa-solid fa-medal"></i></div>
                            <span class="ep-card-title">Tier History</span>
                        </div>
                        <div class="ep-card-body">
                            @forelse($executive->tierHistories as $th)
                                @php
                                    $newStyle = $tierStyle($th->new_tier);
                                    $prevStyle = $th->previous_tier ? $tierStyle($th->previous_tier) : null;
                                @endphp
                                <div class="ep-timeline">
                                    <div class="ep-timeline-item">
                                        <div class="ep-timeline-dot"
                                            style="border-color:{{ $newStyle['fg'] }};box-shadow:0 0 0 3px {{ $newStyle['bg'] }};">
                                        </div>
                                        <div class="ep-timeline-content">
                                            <div class="ep-timeline-tiers">
                                                @if($prevStyle)
                                                    <span class="ep-timeline-chip"
                                                        style="background:{{ $prevStyle['bg'] }};color:{{ $prevStyle['fg'] }};border-color:{{ $prevStyle['bd'] }};">{{ ucfirst($th->previous_tier) }}</span>
                                                    <i class="fa-solid fa-arrow-right ep-timeline-arrow"></i>
                                                @endif
                                                <span class="ep-timeline-chip"
                                                    style="background:{{ $newStyle['bg'] }};color:{{ $newStyle['fg'] }};border-color:{{ $newStyle['bd'] }};">{{ ucfirst($th->new_tier) }}</span>
                                            </div>
                                            <div class="ep-timeline-date"><i class="fa-solid fa-calendar"></i>
                                                {{ $th->changed_at->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="ep-empty"><i class="fa-solid fa-medal"></i>
                                    <p>No tier changes yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                {{-- Recent Point Transactions — scrollable + paginated --}}
                <div class="ep-card">
                    <div class="ep-card-header">
                        <div class="ep-card-icon" style="background:#fffbeb;color:#f59e0b;"><i
                                class="fa-solid fa-coins"></i></div>
                        <span class="ep-card-title">Recent Point Transactions</span>
                        @if($txCount)
                            <span class="ep-card-link" style="margin-right:8px;color:#94a3b8;font-weight:600;">{{ $txCount }}
                                total</span>
                        @endif
                        <a href="{{ route('point_history.index', ['executive_id' => $executive->id]) }}"
                            class="ep-card-link" style="margin-left:{{ $txCount ? '0' : 'auto' }};">Full History</a>
                    </div>

                    @if($txCount)
                        <div class="ep-scroll-area">
                            <div class="ep-table-wrap">
                                <table class="ep-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Category</th>
                                            <th class="text-end">Points</th>
                                        </tr>
                                    </thead>
                                    <tbody id="epTxBody">
                                        @foreach($executive->pointTransactions as $tx)
                                            <tr data-page="{{ intdiv($loop->index, $perPage) + 1 }}">
                                                <td style="font-size:0.75rem;color:#94a3b8;white-space:nowrap;">
                                                    {{ $tx->audit_date->format('d M Y') }}</td>
                                                <td style="font-size:0.8rem;">{{ $tx->description }}</td>
                                                <td><span class="ep-cat-badge">{{ ucfirst($tx->category) }}</span></td>
                                                <td class="text-end"
                                                    style="font-weight:700;color:{{ $tx->type === 'credit' ? '#059669' : '#e11d48' }};">
                                                    {{ $tx->type === 'credit' ? '+' : '-' }}{{ $tx->points }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="ep-tx-footer">
                            <span class="ep-tx-footer-info">Showing <strong>{{ min($perPage, $txCount) }}</strong> of
                                <strong>{{ $txCount }}</strong> per page</span>
                            <div class="ep-pager">
                                <button type="button" id="epTxPrev" class="ep-pager-btn"><i
                                        class="fa-solid fa-chevron-left"></i> Prev</button>
                                <span class="ep-pager-label">Page <span id="epTxPage">1</span> of {{ $txTotalPages }}</span>
                                <button type="button" id="epTxNext" class="ep-pager-btn">Next <i
                                        class="fa-solid fa-chevron-right"></i></button>
                            </div>
                        </div>
                    @else
                        <div class="ep-empty"><i class="fa-solid fa-coins"></i>
                            <p>No transactions yet</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const monthlyScores = @json($monthlyScores);
            const canvas = document.getElementById('monthlyScoreChart');
            const emptyEl = document.getElementById('epChartEmpty');

            if (!monthlyScores || !monthlyScores.length) {
                if (canvas) canvas.style.display = 'none';
                if (emptyEl) emptyEl.style.display = 'flex';
                return;
            }

            const sorted = monthlyScores.slice().reverse();
            const labels = sorted.map(s => {
                const d = new Date(s.year, s.month - 1);
                return d.toLocaleString('default', { month: 'short', year: '2-digit' });
            });
            const scores = sorted.map(s => Number(s.net_score) || 0);

            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(79,70,229,0.28)');
            gradient.addColorStop(1, 'rgba(79,70,229,0.02)');

            const maxScore = Math.max(...scores, 0);
            const minScore = Math.min(...scores, 0);
            const padding = Math.max(2, Math.round((maxScore - minScore) * 0.2));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Net Score',
                        data: scores,
                        borderColor: '#4f46e5',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#0f172a',
                            bodyColor: '#475569',
                            borderColor: '#e4e8f0',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: (item) => `Net Score: ${item.parsed.y >= 0 ? '+' : ''}${item.parsed.y}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
                        },
                        y: {
                            beginAtZero: minScore >= 0,
                            suggestedMin: minScore < 0 ? minScore - padding : undefined,
                            suggestedMax: maxScore + padding,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
                        }
                    }
                }
            });
        })();
    </script>

    <script>
        (function () {
            const totalPages = {{ $txTotalPages }};
            const rows = Array.from(document.querySelectorAll('#epTxBody tr[data-page]'));
            const pageLabel = document.getElementById('epTxPage');
            const prevBtn = document.getElementById('epTxPrev');
            const nextBtn = document.getElementById('epTxNext');
            let current = 1;

            function render() {
                rows.forEach(r => { r.style.display = (parseInt(r.dataset.page, 10) === current) ? '' : 'none'; });
                if (pageLabel) pageLabel.textContent = current;
                if (prevBtn) prevBtn.disabled = current <= 1;
                if (nextBtn) nextBtn.disabled = current >= totalPages;
            }
            if (prevBtn) prevBtn.addEventListener('click', () => { if (current > 1) { current--; render(); } });
            if (nextBtn) nextBtn.addEventListener('click', () => { if (current < totalPages) { current++; render(); } });
            if (rows.length) render();
        })();
    </script>
@endpush