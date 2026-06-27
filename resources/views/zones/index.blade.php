@extends('layouts.app')
@section('title', 'Zones')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Zones</li>
    </ol>
@endsection

@push('styles')
    <style>
        /* ═══════════════════════════════════════════════════════════
       ZONES INDEX — matching Executives / Daily Audit aesthetic
       ═══════════════════════════════════════════════════════════ */

        /* ── Page Header ── */
        .zone-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 28px;
        }

        .zone-page-title {
            font-size: 1.55rem;
            font-weight: 900;
            color: #0d0f1c;
            letter-spacing: -0.03em;
            margin: 0 0 4px;
            line-height: 1.2;
        }

        .zone-page-subtitle {
            font-size: .82rem;
            color: #64748b;
            margin: 0;
        }

        .zone-page-subtitle strong {
            color: #6366f1;
            font-weight: 700;
        }

        /* ── Toolbar right ── */
        .zone-toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-zone-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 40px;
            padding: 0 20px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            font-size: .84rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 4px 14px -2px rgba(99, 102, 241, .40);
        }

        .btn-zone-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px -4px rgba(99, 102, 241, .55);
            color: #fff;
        }

        /* ── Stat Cards ── */
        .zone-stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 26px;
        }

        @media (max-width: 992px) {
            .zone-stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .zone-stats-row {
                grid-template-columns: 1fr;
            }
        }

        .zone-stat-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef0f8;
            padding: 22px 22px 18px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 6px rgba(15, 23, 42, .04);
            transition: box-shadow .2s, transform .2s;
        }

        .zone-stat-card:hover {
            box-shadow: 0 6px 24px rgba(15, 23, 42, .08);
            transform: translateY(-2px);
        }

        .zone-stat-label {
            font-size: .67rem;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 10px;
        }

        .zone-stat-value {
            font-size: 2.1rem;
            font-weight: 900;
            letter-spacing: -0.04em;
            line-height: 1;
            margin-bottom: 6px;
        }

        .zone-stat-value.blue {
            color: #1e1f2e;
        }

        .zone-stat-value.green {
            color: #10b981;
        }

        .zone-stat-value.gold {
            color: #f59e0b;
        }

        .zone-stat-value.violet {
            color: #6366f1;
        }

        .zone-stat-note {
            font-size: .72rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .zone-stat-icon {
            position: absolute;
            top: 18px;
            right: 18px;
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
        }

        .zone-stat-icon.blue {
            background: rgba(99, 102, 241, .10);
            color: #6366f1;
        }

        .zone-stat-icon.green {
            background: rgba(16, 185, 129, .10);
            color: #10b981;
        }

        .zone-stat-icon.gold {
            background: rgba(245, 158, 11, .10);
            color: #f59e0b;
        }

        .zone-stat-icon.violet {
            background: rgba(99, 102, 241, .10);
            color: #6366f1;
        }

        /* ── Table Card ── */
        .zone-table-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #eef0f8;
            box-shadow: 0 1px 8px rgba(15, 23, 42, .04);
            overflow: hidden;
        }

        .zone-table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 18px 22px 16px;
            border-bottom: 1px solid #f1f3fb;
        }

        .zone-table-title {
            font-size: .92rem;
            font-weight: 800;
            color: #0d0f1c;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .zone-count-pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 11px;
            border-radius: 100px;
            background: #f1f0ff;
            color: #6366f1;
            font-size: .72rem;
            font-weight: 800;
        }

        /* Search */
        .zone-search-box {
            position: relative;
            display: flex;
            align-items: center;
            width: 240px;
            height: 38px;
            background: #f8f9fc;
            border: 1.5px solid #edf0f7;
            border-radius: 10px;
            padding: 0 8px 0 12px;
            transition: all .2s;
        }

        .zone-search-box:focus-within {
            background: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .10);
        }

        .zone-search-box i {
            font-size: .75rem;
            color: #b0b8d1;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .zone-search-box input {
            border: none;
            outline: none;
            flex: 1;
            background: transparent;
            font-size: .82rem;
            color: #1e1f2e;
        }

        .zone-search-box input::placeholder {
            color: #b0b8d1;
        }

        .zone-search-clear {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #edf0f7;
            color: #94a3b8;
            font-size: .58rem;
            flex-shrink: 0;
            text-decoration: none;
            transition: all .15s;
        }

        .zone-search-clear:hover {
            background: #e2e8f0;
            color: #475569;
        }

        /* ── Table ── */
        .zone-table {
            width: 100%;
            border-collapse: collapse;
        }

        .zone-table thead th {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .09em;
            color: #a0a8c0;
            font-weight: 800;
            padding: 11px 16px;
            background: #fbfbfe;
            border-bottom: 1px solid #f0f2fa;
            white-space: nowrap;
        }

        .zone-table thead th:first-child {
            padding-left: 22px;
        }

        .zone-table thead th:last-child {
            padding-right: 22px;
        }

        .zone-table tbody tr {
            transition: background .12s;
        }

        .zone-table tbody tr:hover {
            background: rgba(99, 102, 241, .025);
        }

        .zone-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f5f6fc;
            vertical-align: middle;
            font-size: .82rem;
            color: #374151;
        }

        .zone-table tbody td:first-child {
            padding-left: 22px;
        }

        .zone-table tbody td:last-child {
            padding-right: 22px;
        }

        .zone-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Row # */
        .zone-row-num {
            font-size: .7rem;
            color: #cbd5e1;
            font-weight: 700;
        }

        /* Zone icon avatar */
        .zone-icon-avatar {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: linear-gradient(140deg, #6366f1, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px -4px rgba(99, 102, 241, .50);
        }

        .zone-name-text {
            font-weight: 700;
            color: #1e1f2e;
            font-size: .85rem;
            line-height: 1.2;
        }

        /* Code pill */
        .zone-code-pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 7px;
            background: rgba(99, 102, 241, .08);
            color: #6366f1;
            font-size: .72rem;
            font-weight: 800;
            font-family: monospace;
            letter-spacing: .04em;
        }

        /* Company text */
        .zone-company-text {
            font-weight: 600;
            font-size: .8rem;
            color: #1e1f2e;
        }

        /* Exec count */
        .zone-exec-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            padding: 4px 10px;
            background: rgba(99, 102, 241, .08);
            color: #6366f1;
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 800;
        }

        /* Status badge */
        .zone-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 11px;
            border-radius: 100px;
            font-size: .69rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .zone-status-badge .dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Action buttons */
        .zone-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: none;
            font-size: .72rem;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
        }

        .zone-action-btn:hover {
            transform: translateY(-1px);
            filter: brightness(.93);
        }

        .zone-action-edit {
            background: rgba(245, 158, 11, .12);
            color: #f59e0b;
        }

        .zone-action-delete {
            background: rgba(239, 68, 68, .10);
            color: #ef4444;
        }

        /* Empty */
        .zone-empty {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .zone-empty i {
            font-size: 2.5rem;
            margin-bottom: 14px;
            opacity: .3;
            display: block;
        }

        .zone-empty p {
            font-size: .88rem;
            margin: 0;
        }

        /* ── Pagination ── */
        .zone-pagination-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 20px;
            padding: 16px 22px;
            background: #fff;
            border-radius: 16px;
            border: 1px solid #eef0f8;
            box-shadow: 0 1px 6px rgba(15, 23, 42, .04);
        }

        .zone-pagination-info {
            font-size: .78rem;
            color: #94a3b8;
        }

        .zone-pagination-info strong {
            color: #374151;
            font-weight: 700;
        }

        .zone-pagination-controls {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        .zone-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 10px;
            background: #fff;
            border: 1.5px solid #e8eaf2;
            color: #374151;
            font-size: .8rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all .18s ease;
            white-space: nowrap;
        }

        .zone-page-btn:hover:not([disabled]):not(.zone-page-active) {
            border-color: #6366f1;
            color: #6366f1;
            background: #fafaff;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(99, 102, 241, .12);
            text-decoration: none;
        }

        .zone-page-btn.zone-page-active {
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            border-color: transparent;
            color: #fff;
            font-weight: 800;
            box-shadow: 0 4px 14px -2px rgba(99, 102, 241, .45);
            cursor: default;
        }

        .zone-page-btn.zone-page-nav {
            padding: 0 14px;
            font-size: .78rem;
        }

        .zone-page-btn[disabled] {
            opacity: .38;
            cursor: not-allowed;
            pointer-events: none;
        }

        .zone-page-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 36px;
            color: #b0b8d1;
            font-size: .82rem;
            font-weight: 700;
            letter-spacing: .05em;
        }

        /* ── Modals ── */
        .zone-modal-content {
            border-radius: 22px !important;
            border: none !important;
            overflow: hidden;
            box-shadow: 0 28px 64px -12px rgba(15, 23, 42, .28);
        }

        .zone-modal-header {
            border-bottom: 1px solid #f0f2fa !important;
            padding: 22px 28px !important;
            background: linear-gradient(135deg, rgba(99, 102, 241, .07), rgba(124, 58, 237, .03));
        }

        .zone-modal-title {
            font-weight: 800;
            font-size: 1rem;
            color: #0d0f1c;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
        }

        .zone-modal-title i {
            color: #6366f1;
        }

        .zone-modal-body {
            padding: 24px 28px 8px !important;
        }

        .zone-modal-footer {
            border-top: 1px solid #f0f2fa !important;
            padding: 18px 28px !important;
            gap: 10px;
        }

        .zone-modal-label {
            display: block;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #64748b;
            margin-bottom: 6px;
        }

        .zone-modal-input {
            display: block;
            width: 100%;
            height: 42px;
            padding: 0 14px;
            background: #f8f9fc;
            border: 1.5px solid #edf0f7;
            border-radius: 11px;
            font-size: .85rem;
            color: #1e1f2e;
            transition: all .2s;
            outline: none;
        }

        .zone-modal-input:focus {
            background: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .10);
        }

        .zone-modal-input::placeholder {
            color: #b0b8d1;
        }

        .zone-modal-select {
            display: block;
            width: 100%;
            height: 42px;
            padding: 0 14px;
            background: #f8f9fc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%236366f1' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") no-repeat right 12px center;
            border: 1.5px solid #edf0f7;
            border-radius: 11px;
            font-size: .85rem;
            color: #1e1f2e;
            appearance: none;
            transition: all .2s;
            outline: none;
            cursor: pointer;
        }

        .zone-modal-select:focus {
            background-color: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .10);
        }

        .btn-zone-ghost {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 42px;
            padding: 0 18px;
            background: #fff;
            border: 1.5px solid #e8eaf2;
            border-radius: 12px;
            color: #64748b;
            font-weight: 600;
            font-size: .84rem;
            text-decoration: none;
            transition: all .2s;
            cursor: pointer;
        }

        .btn-zone-ghost:hover {
            background: #f8f9fc;
            border-color: #cbd5e1;
            color: #374151;
        }

        .btn-zone-apply {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 22px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            color: #fff;
            font-weight: 700;
            font-size: .85rem;
            box-shadow: 0 6px 18px -4px rgba(99, 102, 241, .40);
            transition: all .2s;
            cursor: pointer;
        }

        .btn-zone-apply:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 26px -4px rgba(99, 102, 241, .5);
            color: #fff;
        }

        /* Backdrop blur */
        .modal-backdrop {
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
            background-color: rgba(15, 23, 42, .45) !important;
            opacity: 1 !important;
        }

        @media (max-width: 768px) {
            .zone-page-header {
                flex-direction: column;
            }

            .zone-toolbar-right {
                width: 100%;
                justify-content: flex-end;
            }

            .zone-search-box {
                width: 100%;
            }

            .zone-pagination-bar {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }

        .zone-analytics-trigger {
            color: var(--pms-accent, #6366f1);
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.15s;
        }
        .zone-analytics-trigger:hover {
            color: var(--pms-accent-hover, #4f46e5);
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')

    @php
        $totalZones = $zones->total();
        $activeZones = $zones->getCollection()->where('status', 'active')->count();
        $inactiveZones = $zones->getCollection()->where('status', 'inactive')->count();
        $totalExecs = $zones->getCollection()->sum('executives_count');
    @endphp

    {{-- ══ PAGE HEADER ══════════════════════════════════════════ --}}
    <div class="zone-page-header">
        <div>
            <h1 class="zone-page-title">Zones</h1>
            <p class="zone-page-subtitle">Geographic zones for executive assignment — <strong>{{ $totalZones }}
                    total</strong></p>
        </div>
        <div class="zone-toolbar-right">
            <button type="button" class="btn-zone-primary" data-bs-toggle="modal" data-bs-target="#addZoneModal">
                <i class="fa-solid fa-plus"></i> Add Zone
            </button>
        </div>
    </div>

    {{-- ══ STAT CARDS ══════════════════════════════════════════ --}}
    <div class="zone-stats-row">
        <div class="zone-stat-card">
            <div class="zone-stat-icon blue"><i class="fa-solid fa-map-location-dot"></i></div>
            <div class="zone-stat-label">Total Zones</div>
            <div class="zone-stat-value blue">{{ $totalZones }}</div>
            <div class="zone-stat-note"><i class="fa-solid fa-circle-info" style="font-size:.6rem;"></i> All zones</div>
        </div>
        <div class="zone-stat-card">
            <div class="zone-stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
            <div class="zone-stat-label">Active</div>
            <div class="zone-stat-value green">{{ $activeZones }}</div>
            <div class="zone-stat-note"><i class="fa-solid fa-circle" style="font-size:.45rem;color:#10b981;"></i> Currently
                active</div>
        </div>
        <div class="zone-stat-card">
            <div class="zone-stat-icon gold"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="zone-stat-label">Inactive</div>
            <div class="zone-stat-value gold">{{ $inactiveZones }}</div>
            <div class="zone-stat-note"><i class="fa-solid fa-circle" style="font-size:.45rem;color:#f59e0b;"></i> Disabled
                zones</div>
        </div>
        <div class="zone-stat-card">
            <div class="zone-stat-icon violet"><i class="fa-solid fa-users"></i></div>
            <div class="zone-stat-label">Total Executives</div>
            <div class="zone-stat-value violet">{{ $totalExecs }}</div>
            <div class="zone-stat-note"><i class="fa-solid fa-equals" style="font-size:.6rem;"></i> Across all zones</div>
        </div>
    </div>

    {{-- ══ TABLE CARD ══════════════════════════════════════════ --}}
    <div class="zone-table-card">
        <div class="zone-table-card-header">
            <div class="zone-table-title">
                Zones
                <span class="zone-count-pill">{{ $zones->total() }} entries</span>
            </div>
            <div class="zone-search-box">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="zoneSearchInput" placeholder="Search zones…" autocomplete="off">
            </div>
        </div>

        <div class="zone-table-scroll">
        <table class="zone-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Zone</th>
                    <th>Code</th>
                    <th>Company</th>
                    <th class="text-center">Executives</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="zoneTableBody">
                @forelse($zones as $i => $zone)
                    <tr class="zone-row" data-name="{{ strtolower($zone->name) }} {{ strtolower($zone->company->name ?? '') }}">
                        <td><span class="zone-row-num">#{{ $zones->firstItem() + $i }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="zone-icon-avatar">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <a href="#" class="zone-analytics-trigger zone-name-text" data-id="{{ $zone->id }}" data-name="{{ $zone->name }}">{{ $zone->name }}</a>
                            </div>
                        </td>
                        <td>
                            @if($zone->code)
                                <span class="zone-code-pill">{{ $zone->code }}</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td><span class="zone-company-text">{{ $zone->company->name ?? '—' }}</span></td>
                        <td class="text-center">
                            <span class="zone-exec-count">{{ $zone->executives_count ?? 0 }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $isActive = $zone->status === 'active';
                            @endphp
                            <span class="zone-status-badge"
                                style="background:{{ $isActive ? 'rgba(16,185,129,.10)' : 'rgba(239,68,68,.08)' }};color:{{ $isActive ? '#10b981' : '#ef4444' }};">
                                <span class="dot"></span>{{ ucfirst($zone->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <button type="button" class="zone-action-btn zone-action-edit"
                                    onclick="editZone({{ $zone->id }}, '{{ addslashes($zone->name) }}', '{{ $zone->code ?? '' }}', '{{ $zone->status }}')"
                                    title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form id="del-zone-{{ $zone->id }}" action="{{ route('zones.destroy', $zone) }}" method="POST"
                                    style="display:inline;">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button" class="zone-action-btn zone-action-delete"
                                    data-confirm-delete="{{ $zone->name }}" data-form-id="del-zone-{{ $zone->id }}"
                                    title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="zone-empty">
                                <i class="fa-solid fa-map-location-dot"></i>
                                <p>No zones yet. <a href="#" data-bs-toggle="modal" data-bs-target="#addZoneModal"
                                        style="color:#6366f1;font-weight:700;">Add the first one →</a></p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- ══ PAGINATION ══════════════════════════════════════════ --}}
    @if($zones->hasPages())
        <div class="zone-pagination-bar d-flex justify-content-between align-items-center mt-3">
            <div class="zone-pagination-info">
                Showing <strong>{{ $zones->firstItem() }}</strong>–<strong>{{ $zones->lastItem() }}</strong>
                of <strong>{{ $zones->total() }}</strong> zones
            </div>
            <div class="zone-pagination-controls">
                {{ $zones->links() }}
            </div>
        </div>
    @endif


    {{-- ══ ADD ZONE MODAL ══════════════════════════════════════ --}}
    <div class="modal fade" id="addZoneModal" tabindex="-1" aria-labelledby="addZoneModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content zone-modal-content">
                <div class="modal-header zone-modal-header">
                    <h5 class="zone-modal-title" id="addZoneModalLabel">
                        <i class="fa-solid fa-map-location-dot"></i> Add Zone
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('zones.store') }}" method="POST">
                    @csrf
                    <div class="modal-body zone-modal-body">
                        <div class="mb-3">
                            <label class="zone-modal-label">Company <span class="text-danger">*</span></label>
                            <select name="company_id" class="zone-modal-select" required>
                                <option value="">— Select Company —</option>
                                @foreach($companies as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="zone-modal-label">Zone Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="zone-modal-input" required placeholder="e.g. North Zone">
                        </div>
                        <div class="mb-3">
                            <label class="zone-modal-label">Code</label>
                            <input type="text" name="code" class="zone-modal-input" placeholder="e.g. NZ"
                                style="text-transform:uppercase;">
                        </div>
                        <div class="mb-3">
                            <label class="zone-modal-label">Status</label>
                            <select name="status" class="zone-modal-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer zone-modal-footer">
                        <button type="button" class="btn-zone-ghost" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i> Cancel
                        </button>
                        <button type="submit" class="btn-zone-apply">
                            <i class="fa-solid fa-save"></i> Save Zone
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══ EDIT ZONE MODAL ══════════════════════════════════════ --}}
    <div class="modal fade" id="editZoneModal" tabindex="-1" aria-labelledby="editZoneModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content zone-modal-content">
                <div class="modal-header zone-modal-header">
                    <h5 class="zone-modal-title" id="editZoneModalLabel">
                        <i class="fa-solid fa-pen"></i> Edit Zone
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editZoneForm" action="" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body zone-modal-body">
                        <div class="mb-3">
                            <label class="zone-modal-label">Zone Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editZoneName" class="zone-modal-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="zone-modal-label">Code</label>
                            <input type="text" name="code" id="editZoneCode" class="zone-modal-input" placeholder="e.g. NZ"
                                style="text-transform:uppercase;">
                        </div>
                        <div class="mb-3">
                            <label class="zone-modal-label">Status</label>
                            <select name="status" id="editZoneStatus" class="zone-modal-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer zone-modal-footer">
                        <button type="button" class="btn-zone-ghost" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i> Cancel
                        </button>
                        <button type="submit" class="btn-zone-apply">
                            <i class="fa-solid fa-check"></i> Update Zone
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══ ZONE ANALYTICS MODAL ════════════════════════════════ --}}
    <div class="modal fade" id="zoneAnalyticsModal" tabindex="-1" aria-labelledby="zoneAnalyticsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px; border:none; box-shadow:var(--shadow-xl); background:#fff;">
                <div class="modal-header" style="border-bottom:1px solid rgba(99, 102, 241, 0.08); padding:20px 28px;">
                    <h5 class="modal-title" id="zoneAnalyticsModalLabel" style="font-weight:800; color:var(--pms-text-primary); display:flex; align-items:center; gap:10px;">
                        <i class="fa-solid fa-chart-line" style="color:var(--pms-accent);"></i>
                        <span id="zaModalTitle">Zone Analytics</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:28px;">
                    {{-- Loader --}}
                    <div id="zaLoader" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 small">Loading zone metrics...</p>
                    </div>

                    {{-- Content wrapper --}}
                    <div id="zaContent" style="display:none;">
                        {{-- Score Cards Grid --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-6">
                                <div class="p-3 text-center" style="background:#f0fdfa; border:1px solid #ccfbf1; border-radius:12px;">
                                    <div class="small text-muted font-weight-bold" style="font-size:0.7rem; text-transform:uppercase;">Positive</div>
                                    <div id="zaTotalPositive" class="h4 font-weight-extrabold mb-0 mt-1" style="color:#0d9488; font-weight:800;">0</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-3 text-center" style="background:#fff1f2; border:1px solid #ffe4e6; border-radius:12px;">
                                    <div class="small text-muted font-weight-bold" style="font-size:0.7rem; text-transform:uppercase;">Negative</div>
                                    <div id="zaTotalNegative" class="h4 font-weight-extrabold mb-0 mt-1" style="color:#e11d48; font-weight:800;">0</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-3 text-center" style="background:#f0f9ff; border:1px solid #e0f2fe; border-radius:12px;">
                                    <div class="small text-muted font-weight-bold" style="font-size:0.7rem; text-transform:uppercase;">Recovery</div>
                                    <div id="zaTotalRecovery" class="h4 font-weight-extrabold mb-0 mt-1" style="color:#0ea5e9; font-weight:800;">0</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-3 text-center" style="background:#f5f3ff; border:1px solid #e0e7ff; border-radius:12px;">
                                    <div class="small text-muted font-weight-bold" style="font-size:0.7rem; text-transform:uppercase;">Net Score</div>
                                    <div id="zaTotalNet" class="h4 font-weight-extrabold mb-0 mt-1" style="color:#6366f1; font-weight:800;">0</div>
                                </div>
                            </div>
                        </div>

                        {{-- Graph Container --}}
                        <div style="background:#fafbff; border:1px solid rgba(99, 102, 241, 0.08); border-radius:16px; padding:20px;">
                            <h6 style="font-weight:700; color:var(--pms-text-secondary); margin-bottom:15px; font-size:0.83rem;">Daily Performance Score Trend</h6>
                            <div style="height:250px; position:relative;">
                                <canvas id="zoneAnalyticsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Zone analytics modal and chart loader ──
        let zoneChartInstance = null;

        document.querySelectorAll('.zone-analytics-trigger').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                const zoneId = this.dataset.id;
                const zoneName = this.dataset.name;
                
                // Open modal
                const modalEl = document.getElementById('zoneAnalyticsModal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                
                // Show loader, hide content
                document.getElementById('zaLoader').style.display = 'block';
                document.getElementById('zaContent').style.display = 'none';
                document.getElementById('zaModalTitle').textContent = 'Zone Analytics — ' + zoneName;
                
                // Fetch data
                fetch(`/zones/${zoneId}/analytics`)
                    .then(res => res.json())
                    .then(data => {
                        // Hide loader, show content
                        document.getElementById('zaLoader').style.display = 'none';
                        document.getElementById('zaContent').style.display = 'block';
                        
                        // Populate totals
                        document.getElementById('zaTotalPositive').textContent = '+' + data.totals.positive.toLocaleString();
                        document.getElementById('zaTotalNegative').textContent = '-' + data.totals.negative.toLocaleString();
                        document.getElementById('zaTotalRecovery').textContent = '+' + data.totals.recovery.toLocaleString();
                        
                        const net = data.totals.net_score;
                        const netEl = document.getElementById('zaTotalNet');
                        netEl.textContent = (net >= 0 ? '+' : '') + net.toLocaleString();
                        netEl.style.color = net >= 0 ? '#6366f1' : '#e11d48';
                        
                        // Draw chart
                        const ctx = document.getElementById('zoneAnalyticsChart').getContext('2d');
                        if (zoneChartInstance) {
                            zoneChartInstance.destroy();
                        }
                        
                        const labels = data.trend.map(t => {
                            const parts = t.date.split('-');
                            return parts[2] + '/' + parts[1];
                        });
                        const scores = data.trend.map(t => t.net_score);
                        
                        zoneChartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Net Score',
                                    data: scores,
                                    borderColor: '#6366f1',
                                    borderWidth: 3,
                                    backgroundColor: 'rgba(99, 102, 241, 0.04)',
                                    fill: true,
                                    tension: 0.3,
                                    pointBackgroundColor: '#6366f1',
                                    pointBorderColor: '#fff',
                                    pointHoverRadius: 6,
                                    pointRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: { backgroundColor: '#fff', titleColor: '#0f172a', bodyColor: '#475569', borderColor: '#e4e8f0', borderWidth: 1 }
                                },
                                scales: {
                                    x: { grid: { display: false } },
                                    y: { grid: { color: '#f1f5f9' } }
                                }
                            }
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        document.getElementById('zaLoader').innerHTML = '<div class="text-danger"><i class="fa-solid fa-circle-exclamation fs-3"></i><p class="mt-2">Failed to load zone analytics. Please try again.</p></div>';
                    });
            });
        });

        // ── Edit zone — populate modal ──
        function editZone(id, name, code, status) {
            document.getElementById('editZoneForm').action = `/zones/${id}`;
            document.getElementById('editZoneName').value = name;
            document.getElementById('editZoneCode').value = code;
            document.getElementById('editZoneStatus').value = status;
            new bootstrap.Modal(document.getElementById('editZoneModal')).show();
        }

        // ── Client-side search filter ──
        document.getElementById('zoneSearchInput')?.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#zoneTableBody .zone-row').forEach(row => {
                row.style.display = row.dataset.name.includes(q) ? '' : 'none';
            });
        });

        // ── Code input uppercase ──
        document.querySelectorAll('input[name="code"]').forEach(el => {
            el.addEventListener('input', function () { this.value = this.value.toUpperCase(); });
        });
    </script>
@endpush