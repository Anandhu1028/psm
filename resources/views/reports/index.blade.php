@extends('layouts.app')
@section('title', 'Reports')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Reports</li>
    </ol>
@endsection

@push('styles')
    <style>
        /* ── Page Header ── */
        .rp-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 36px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .rp-page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0d0f1c;
            letter-spacing: -0.04em;
            line-height: 1.1;
            margin: 0 0 5px;
        }

        .rp-page-sub {
            font-size: 0.82rem;
            color: #94a3b8;
            font-weight: 450;
        }

        .rp-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Export Buttons */
        .btn-rp-pdf {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 20px;
            background: #fff1f2;
            border: 1.5px solid #fecdd3;
            border-radius: 12px;
            color: #e11d48;
            font-size: 0.84rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-rp-pdf:hover {
            background: #e11d48;
            color: #fff;
            border-color: #e11d48;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(225, 29, 72, 0.3);
            text-decoration: none;
        }

        .btn-rp-csv {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 20px;
            background: #3234b0;
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 0.84rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
            white-space: nowrap;
        }

        .btn-rp-csv:hover {
            background: #4f46e5;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45);
            text-decoration: none;
        }

        /* ── Layout ── */
        .rp-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .rp-layout {
                grid-template-columns: 1fr;
            }
        }

        /* ── Type Sidebar ── */
        .rp-type-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, 0.055), 0 1px 4px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.6);
            overflow: hidden;
        }

        .rp-type-card-head {
            padding: 18px 20px 14px;
            border-bottom: 1px solid #f0f2fa;
            background: #fafbff;
        }

        .rp-type-card-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b0b8d1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rp-type-list {
            padding: 12px;
        }

        .rp-type-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 12px;
            margin-bottom: 4px;
            text-decoration: none;
            transition: all 0.18s ease;
            border: 1.5px solid transparent;
        }

        .rp-type-item:last-child {
            margin-bottom: 0;
        }

        .rp-type-item:hover {
            background: #fafaff;
            border-color: #e0e7ff;
            text-decoration: none;
        }

        .rp-type-item.active {
            background: #f5f3ff;
            border-color: #6366f1;
        }

        .rp-type-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            flex-shrink: 0;
            border: 1px solid #edf0f7;
            background: #f8f9fc;
            transition: all 0.18s;
        }

        .rp-type-item.active .rp-type-icon {
            background: #6366f1;
            border-color: #6366f1;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .rp-type-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #4a5568;
            transition: color 0.15s;
            flex: 1;
            line-height: 1.2;
        }

        .rp-type-item.active .rp-type-label {
            color: #4f46e5;
            font-weight: 700;
        }

        .rp-type-arrow {
            font-size: 0.65rem;
            color: #c4b5fd;
            opacity: 0;
            transition: opacity 0.15s;
        }

        .rp-type-item.active .rp-type-arrow {
            opacity: 1;
        }

        .rp-type-item:hover .rp-type-arrow {
            opacity: 0.5;
        }

        /* ── Right Panel ── */
        .rp-right-panel {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Filter Bar Card */
        .rp-filter-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, 0.05), 0 1px 4px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.6);
            padding: 22px 24px;
        }

        .rp-filter-card-head {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        .rp-filter-card-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b0b8d1;
        }

        .rp-filter-card .form-select,
        .rp-filter-card .form-control {
            height: 40px;
            border-radius: 10px !important;
            font-size: 0.82rem !important;
            border: 1.5px solid #edf0f7 !important;
            background: #fafbff !important;
            color: #2d3748 !important;
            box-shadow: none !important;
            transition: all 0.2s;
        }

        .rp-filter-card .form-select:focus,
        .rp-filter-card .form-control:focus {
            border-color: #6366f1 !important;
            background: #fff !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08) !important;
        }

        .rp-filter-card .form-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 6px;
        }

        .btn-rp-generate {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 40px;
            padding: 0 20px;
            width: 100%;
            background: #6366f1;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-rp-generate:hover {
            background: #4f46e5;
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.4);
            transform: translateY(-1px);
        }

        /* ── Table Card ── */
        .rp-table-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, 0.055), 0 1px 4px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .rp-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 26px;
            border-bottom: 1px solid #f1f5f9;
            gap: 14px;
            flex-wrap: wrap;
        }

        .rp-toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rp-toolbar-title {
            font-size: 0.92rem;
            font-weight: 800;
            color: #0d0f1c;
            letter-spacing: -0.02em;
        }

        .rp-toolbar-count {
            display: inline-flex;
            align-items: center;
            height: 24px;
            padding: 0 10px;
            background: #f5f3ff;
            border: 1px solid #e0e7ff;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 800;
            color: #4f46e5;
        }

        .rp-search-wrap {
            display: flex;
            align-items: center;
            gap: 9px;
            background: #f8f9fc;
            border: 1.5px solid #edf0f7;
            border-radius: 10px;
            padding: 0 14px;
            height: 36px;
            min-width: 200px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .rp-search-wrap:focus-within {
            border-color: #6366f1;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
        }

        .rp-search-wrap i {
            color: #c4b5fd;
            font-size: 0.8rem;
        }

        .rp-search-input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.81rem;
            color: #334155;
            width: 100%;
            font-family: inherit;
        }

        .rp-search-input::placeholder {
            color: #c4b5fd;
        }

        /* Table */
        .rp-table-scroll {
            overflow-x: auto;
        }

        .rp-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.83rem;
        }

        .rp-tbl thead tr {
            background: #fafbff;
            border-bottom: 1px solid #f0f2fa;
        }

        .rp-tbl thead th {
            padding: 12px 16px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b0b8d1;
            white-space: nowrap;
            user-select: none;
        }

        .rp-tbl thead th:first-child {
            padding-left: 26px;
        }

        .rp-tbl thead th:last-child {
            padding-right: 26px;
        }

        .rp-tbl tbody tr {
            border-bottom: 1px solid #f7f8fc;
            transition: background 0.15s ease;
        }

        .rp-tbl tbody tr:last-child {
            border-bottom: none;
        }

        .rp-tbl tbody tr:hover {
            background: #fafaff;
        }

        .rp-tbl tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            color: #374151;
        }

        .rp-tbl tbody td:first-child {
            padding-left: 26px;
        }

        .rp-tbl tbody td:last-child {
            padding-right: 26px;
        }

        /* Shared row elements */
        .rp-exec {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rp-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.66rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(99, 102, 241, 0.22);
        }

        .rp-exec-name {
            font-size: 0.83rem;
            font-weight: 700;
            color: #1e1f2e;
            text-decoration: none;
            display: block;
            transition: color 0.15s;
        }

        .rp-exec-name:hover {
            color: #4f46e5;
            text-decoration: none;
        }

        .rp-exec-id {
            font-size: 0.63rem;
            color: #b0b8d1;
            font-weight: 500;
            font-family: 'SF Mono', 'Consolas', monospace;
        }

        .rp-date {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #4a5568;
            white-space: nowrap;
        }

        .rp-date i {
            color: #c4b5fd;
        }

        .rp-company {
            font-size: 0.82rem;
            font-weight: 600;
            color: #2d3748;
        }

        .rp-zone {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.64rem;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 2px;
        }

        .rp-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 28px;
            padding: 0 9px;
            background: #f8f9fc;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #374151;
            border: 1px solid #edf0f7;
        }

        .rp-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 3px;
            min-width: 52px;
            height: 28px;
            padding: 0 11px;
            border-radius: 8px;
            font-size: 0.76rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .rp-chip i {
            font-size: 0.58rem;
        }

        .rp-chip-pos {
            background: #ecfdf5;
            color: #059669;
            border: 1.5px solid #a7f3d0;
        }

        .rp-chip-neg {
            background: #fff1f2;
            color: #e11d48;
            border: 1.5px solid #fecdd3;
        }

        .rp-chip-net-p {
            background: #ecfdf5;
            color: #047857;
            border: 1.5px solid #6ee7b7;
            min-width: 60px;
        }

        .rp-chip-net-n {
            background: #fff1f2;
            color: #be123c;
            border: 1.5px solid #fecdd3;
            min-width: 60px;
        }

        .rp-chip-pass {
            background: #d1fae5;
            color: #065f46;
        }

        .rp-chip-fail {
            background: #fee2e2;
            color: #9f1239;
        }

        .rp-chip-neutral {
            background: #f1f5f9;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
        }

        .rp-chip-warn {
            background: #fff1f2;
            color: #e11d48;
            border: 1.5px solid #fecdd3;
        }

        .rp-score {
            font-size: 0.9rem;
            font-weight: 900;
            color: #3730a3;
            letter-spacing: -0.02em;
        }

        .rp-score-muted {
            font-size: 0.82rem;
            font-weight: 700;
            color: #64748b;
        }

        /* Tier badges */
        .rp-tier-gold {
            background: #fffbeb;
            color: #b45309;
            border: 1.5px solid #fde68a;
        }

        .rp-tier-silver {
            background: #f8fafc;
            color: #475569;
            border: 1.5px solid #cbd5e1;
        }

        .rp-tier-bronze {
            background: #fff7ed;
            color: #c2410c;
            border: 1.5px solid #fed7aa;
        }

        .rp-tier-standard {
            background: #f5f3ff;
            color: #5b21b6;
            border: 1.5px solid #ddd6fe;
        }

        /* Empty State */
        .rp-empty {
            text-align: center;
            padding: 70px 24px;
        }

        .rp-empty-blob {
            width: 68px;
            height: 68px;
            margin: 0 auto 16px;
            background: #eef2ff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            color: #c4b5fd;
        }

        .rp-empty-h {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1e1f2e;
            margin-bottom: 6px;
        }

        .rp-empty-p {
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
@endpush

@section('content')

    @php
        $reportTypes = [
            ['key' => 'daily', 'label' => 'Daily Audit Report', 'icon' => 'fa-calendar-day', 'color' => '#6366f1'],
            ['key' => 'executive', 'label' => 'Executive Summary', 'icon' => 'fa-user-tie', 'color' => '#06b6d4'],
            ['key' => 'zone', 'label' => 'Zone Performance', 'icon' => 'fa-map-location-dot', 'color' => '#10b981'],
            ['key' => 'violation', 'label' => 'Violations Report', 'icon' => 'fa-triangle-exclamation', 'color' => '#f43f5e'],
            ['key' => 'recovery', 'label' => 'Recovery Points', 'icon' => 'fa-rotate-right', 'color' => '#f59e0b'],
            ['key' => 'monthly', 'label' => 'Monthly Score History', 'icon' => 'fa-chart-bar', 'color' => '#8b5cf6'],
        ];
        $currentType = $type ?? 'daily';
        $rowCount = is_array($data) ? count($data) : (is_object($data) ? $data->count() : 0);
    @endphp

    <div>

        {{-- ══ PAGE HEADER ══════════════════════════════════════════════ --}}
        <div class="rp-header">
            <div>
                <h1 class="rp-page-title">Reports</h1>
                <p class="rp-page-sub">Generate and export performance reports</p>
            </div>
            <div class="rp-header-actions">
                <a href="{{ route('reports.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                    class="btn-rp-pdf">
                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('reports.export', array_merge(request()->all(), ['format' => 'csv'])) }}"
                    class="btn-rp-csv">
                    <i class="fa-solid fa-file-csv"></i> Export CSV
                </a>
            </div>
        </div>

        {{-- ══ MAIN LAYOUT ══════════════════════════════════════════════ --}}
        <div class="rp-layout">

            {{-- ── Type Sidebar ── --}}
            <div class="rp-type-card">
                <div class="rp-type-card-head">
                    <div class="rp-type-card-title">
                        <i class="fa-solid fa-list-check"></i> Report Type
                    </div>
                </div>
                <div class="rp-type-list">
                    @foreach($reportTypes as $t)
                        <a href="{{ route('reports.index', array_merge(request()->all(), ['type' => $t['key']])) }}"
                            class="rp-type-item {{ $currentType === $t['key'] ? 'active' : '' }}">
                            <div class="rp-type-icon" style="color:{{ $t['color'] }};">
                                <i class="fa-solid {{ $t['icon'] }}"></i>
                            </div>
                            <span class="rp-type-label">{{ $t['label'] }}</span>
                            <i class="fa-solid fa-chevron-right rp-type-arrow"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ── Right Panel ── --}}
            <div class="rp-right-panel">

                {{-- Filter Card --}}
                <div class="rp-filter-card">
                    <div class="rp-filter-card-head">
                        <i class="fa-solid fa-sliders-h" style="color:#c4b5fd;font-size:.8rem;"></i>
                        <span class="rp-filter-card-title">Filter & Generate</span>
                    </div>
                    <form method="GET" action="{{ route('reports.index') }}">
                        <input type="hidden" name="type" value="{{ $currentType }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-select">
                                    <option value="">All Companies</option>
                                    @foreach($companies as $c)
                                        <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Zone</label>
                                <select name="zone_id" class="form-select">
                                    <option value="">All Zones</option>
                                    @foreach($zones as $z)
                                        <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>
                                            {{ $z->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn-rp-generate">
                                    <i class="fa-solid fa-filter"></i> Generate
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Table Card --}}
                <div class="rp-table-card">
                    <div class="rp-toolbar">
                        <div class="rp-toolbar-left">
                            <span class="rp-toolbar-title">
                                {{ collect($reportTypes)->firstWhere('key', $currentType)['label'] ?? 'Report' }}
                            </span>
                            <span class="rp-toolbar-count">{{ $rowCount }} records</span>
                        </div>
                        <div class="rp-search-wrap">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input class="rp-search-input" id="rpQuickSearch" type="text" placeholder="Search results…">
                        </div>
                    </div>

                    <div class="rp-table-scroll">

                        @if(empty($data) || $rowCount === 0)
                            <div class="rp-empty">
                                <div class="rp-empty-blob"><i class="fa-solid fa-file-chart-column"></i></div>
                                <div class="rp-empty-h">No data found</div>
                                <p class="rp-empty-p">Adjust the filters above and click Generate to view results.</p>
                            </div>

                        @elseif($currentType === 'daily')
                            <table class="rp-tbl" id="rpTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Executive</th>
                                        <th>Company</th>
                                        <th style="text-align:center;">Calls</th>
                                        <th style="text-align:center;">Meetings</th>
                                        <th style="text-align:center;">Positive</th>
                                        <th style="text-align:center;">Negative</th>
                                        <th style="text-align:center;">Net</th>
                                        <th style="text-align:center;">KPI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $row)
                                        <tr>
                                            <td><span class="rp-date"><i
                                                        class="fa-regular fa-calendar"></i>{{ \Carbon\Carbon::parse($row['audit_date'])->format('d M Y') }}</span>
                                            </td>
                                            <td style="font-weight:700;font-size:.83rem;">{{ $row['executive']['name'] ?? '—' }}
                                            </td>
                                            <td>
                                                <div class="rp-company">{{ $row['executive']['company']['name'] ?? '—' }}</div>
                                            </td>
                                            <td style="text-align:center;"><span class="rp-num">{{ $row['connected_calls'] }}</span>
                                            </td>
                                            <td style="text-align:center;"><span
                                                    class="rp-num">{{ $row['confirmed_meetings'] }}</span></td>
                                            <td style="text-align:center;"><span class="rp-chip rp-chip-pos"><i
                                                        class="fa-solid fa-plus"></i>{{ $row['positive_points'] }}</span></td>
                                            <td style="text-align:center;"><span class="rp-chip rp-chip-neg"><i
                                                        class="fa-solid fa-minus"></i>{{ $row['negative_points'] }}</span></td>
                                            <td style="text-align:center;">
                                                <span
                                                    class="rp-chip {{ $row['final_score'] >= 0 ? 'rp-chip-net-p' : 'rp-chip-net-n' }}">
                                                    {{ $row['final_score'] >= 0 ? '+' : '' }}{{ $row['final_score'] }}
                                                </span>
                                            </td>
                                            <td style="text-align:center;">
                                                <span
                                                    class="rp-chip {{ $row['kpi_status'] === 'passed' ? 'rp-chip-pass' : 'rp-chip-fail' }}">
                                                    <i
                                                        class="fa-solid {{ $row['kpi_status'] === 'passed' ? 'fa-check' : 'fa-xmark' }}"></i>
                                                    {{ $row['kpi_status'] === 'passed' ? 'Pass' : 'Fail' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        @elseif($currentType === 'executive')
                            <table class="rp-tbl" id="rpTable">
                                <thead>
                                    <tr>
                                        <th>Executive</th>
                                        <th>Company / Zone</th>
                                        <th style="text-align:center;">Total Score</th>
                                        <th style="text-align:center;">Monthly</th>
                                        <th style="text-align:center;">Tier</th>
                                        <th style="text-align:center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $row)
                                        @php
                                            $words = explode(' ', trim($row['name']));
                                            $initials = implode('', array_map(fn($w) => strtoupper(substr($w, 0, 1)), array_slice($words, 0, 2)));
                                            $tierKey = strtolower(str_replace(' ', '_', $row['tier']));
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="rp-exec">
                                                    <div class="rp-avatar">{{ $initials }}</div>
                                                    <div>
                                                        <span class="rp-exec-name">{{ $row['name'] }}</span>
                                                        <div class="rp-exec-id">{{ $row['employee_id'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rp-company">{{ $row['company'] }}</div>
                                                <div class="rp-zone"><i class="fa-solid fa-location-dot"></i>{{ $row['zone'] }}
                                                </div>
                                            </td>
                                            <td style="text-align:center;"><span
                                                    class="rp-score">{{ number_format($row['current_score']) }}</span></td>
                                            <td style="text-align:center;"><span
                                                    class="rp-score-muted">{{ number_format($row['monthly_score']) }}</span></td>
                                            <td style="text-align:center;">
                                                <span class="rp-chip rp-tier-{{ $tierKey }}">{{ $row['tier'] }}</span>
                                            </td>
                                            <td style="text-align:center;">
                                                <span class="rp-chip rp-chip-pass">{{ ucfirst($row['status']) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        @elseif($currentType === 'zone')
                            <table class="rp-tbl" id="rpTable">
                                <thead>
                                    <tr>
                                        <th>Zone</th>
                                        <th>Company</th>
                                        <th style="text-align:center;">Executives</th>
                                        <th style="text-align:center;">Avg Score</th>
                                        <th style="text-align:center;">Total Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $row)
                                        <tr>
                                            <td style="font-weight:700;font-size:.85rem;">{{ $row->zone }}</td>
                                            <td>
                                                <div class="rp-company">{{ $row->company }}</div>
                                            </td>
                                            <td style="text-align:center;"><span class="rp-num">{{ $row->execs }}</span></td>
                                            <td style="text-align:center;"><span
                                                    class="rp-score">{{ number_format($row->avg_score, 1) }}</span></td>
                                            <td style="text-align:center;"><span
                                                    class="rp-score-muted">{{ number_format($row->total_score) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        @elseif(in_array($currentType, ['violation', 'recovery']))
                            <table class="rp-tbl" id="rpTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Executive</th>
                                        <th>Company</th>
                                        <th>Description</th>
                                        <th style="text-align:center;">Category</th>
                                        <th style="text-align:right;">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $row)
                                        @php
                                            $words = explode(' ', trim($row['executive']['name'] ?? ''));
                                            $initials = implode('', array_map(fn($w) => strtoupper(substr($w, 0, 1)), array_slice($words, 0, 2)));
                                        @endphp
                                        <tr>
                                            <td><span class="rp-date"><i
                                                        class="fa-regular fa-calendar"></i>{{ \Carbon\Carbon::parse($row['audit_date'])->format('d M Y') }}</span>
                                            </td>
                                            <td>
                                                <div class="rp-exec">
                                                    <div class="rp-avatar">{{ $initials }}</div>
                                                    <div>
                                                        <span class="rp-exec-name">{{ $row['executive']['name'] ?? '—' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rp-company">{{ $row['executive']['company']['name'] ?? '—' }}</div>
                                            </td>
                                            <td style="font-size:.8rem;max-width:180px;color:#4a5568;">{{ $row['rule_name'] ?? $row['description'] ?? '—' }}
                                            </td>
                                            <td style="text-align:center;">
                                                <span
                                                    class="rp-chip {{ $currentType === 'violation' ? 'rp-chip-warn' : 'rp-chip-pos' }}">
                                                    {{ ucfirst($row['category']) }}
                                                </span>
                                            </td>
                                            <td style="text-align:right;">
                                                <span
                                                    class="{{ $currentType === 'violation' ? 'rp-chip rp-chip-neg' : 'rp-chip rp-chip-pos' }}"
                                                    style="min-width:auto;padding:0 10px;">
                                                    {{ $currentType === 'violation' ? '-' : '+' }}{{ $row['points'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        @else
                            {{-- Monthly --}}
                            <table class="rp-tbl" id="rpTable">
                                <thead>
                                    <tr>
                                        <th>Executive</th>
                                        <th>Zone / Company</th>
                                        <th style="text-align:center;">Year</th>
                                        <th style="text-align:center;">Month</th>
                                        <th style="text-align:center;">Positive</th>
                                        <th style="text-align:center;">Negative</th>
                                        <th style="text-align:center;">Net Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $row)
                                        @php
                                            $words = explode(' ', trim($row->name));
                                            $initials = implode('', array_map(fn($w) => strtoupper(substr($w, 0, 1)), array_slice($words, 0, 2)));
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="rp-exec">
                                                    <div class="rp-avatar">{{ $initials }}</div>
                                                    <div><span class="rp-exec-name">{{ $row->name }}</span></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rp-company">{{ $row->company }}</div>
                                                <div class="rp-zone"><i class="fa-solid fa-location-dot"></i>{{ $row->zone }}</div>
                                            </td>
                                            <td style="text-align:center;font-weight:600;color:#64748b;">{{ $row->year }}</td>
                                            <td style="text-align:center;font-weight:600;color:#64748b;">
                                                {{ \Carbon\Carbon::create($row->year, $row->month)->format('M') }}</td>
                                            <td style="text-align:center;"><span class="rp-chip rp-chip-pos"><i
                                                        class="fa-solid fa-plus"></i>{{ $row->positive_points }}</span></td>
                                            <td style="text-align:center;"><span class="rp-chip rp-chip-neg"><i
                                                        class="fa-solid fa-minus"></i>{{ $row->negative_points }}</span></td>
                                            <td style="text-align:center;">
                                                <span
                                                    class="rp-chip {{ $row->net_score >= 0 ? 'rp-chip-net-p' : 'rp-chip-net-n' }}">
                                                    {{ $row->net_score >= 0 ? '+' : '' }}{{ $row->net_score }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                    </div>{{-- /rp-table-scroll --}}
                </div>{{-- /rp-table-card --}}

            </div>{{-- /rp-right-panel --}}
        </div>{{-- /rp-layout --}}
    </div>

@endsection

@push('scripts')
    <script>
        // ── Quick search ──────────────────────────────────────────
        const rpSearch = document.getElementById('rpQuickSearch');
        if (rpSearch) {
            rpSearch.addEventListener('input', function () {
                const q = this.value.toLowerCase();
                document.querySelectorAll('#rpTable tbody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        }
    </script>
@endpush