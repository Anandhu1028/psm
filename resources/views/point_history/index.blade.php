@extends('layouts.app')
@section('title', 'Point History')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Point History</li>
    </ol>
@endsection

@push('styles')
    <style>
        /* ── Page Header ── */
        .ph-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 36px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .ph-page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0d0f1c;
            letter-spacing: -0.04em;
            line-height: 1.1;
            margin: 0 0 5px;
        }

        .ph-page-sub {
            font-size: 0.82rem;
            color: #94a3b8;
            font-weight: 450;
        }

        .ph-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Filter Button */
        .btn-ph-filter {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 20px;
            background: #fff;
            border: 1.5px solid #e8eaf2;
            border-radius: 12px;
            color: #4a5568;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            white-space: nowrap;
        }

        .btn-ph-filter:hover {
            border-color: #6366f1;
            color: #4f46e5;
            background: #fafaff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
            transform: translateY(-1px);
        }

        .btn-ph-filter.active {
            border-color: #6366f1;
            color: #4f46e5;
            background: #f5f3ff;
        }

        .ph-filter-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            background: #6366f1;
            color: #fff;
            border-radius: 10px;
            font-size: 0.66rem;
            font-weight: 800;
            line-height: 1;
        }

        /* Export Button */
        .btn-ph-export {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 22px;
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

        .btn-ph-export:hover {
            background: #4f46e5;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45);
            text-decoration: none;
        }

        .btn-ph-export:active {
            transform: translateY(0);
        }

        /* ── Summary Cards ── */
        .ph-stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 28px;
        }

        .ph-stat {
            background: #fff;
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid rgba(226, 232, 240, 0.7);
            transition: .22s;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ph-stat:hover {
            box-shadow: 0 6px 28px rgba(0, 0, 0, .08);
            transform: translateY(-2px);
        }

        .ph-stat::after {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            opacity: 0.07;
            pointer-events: none;
        }

        .ph-stat-credits::after {
            background: #10b981;
        }

        .ph-stat-debits::after {
            background: #f43f5e;
        }

        .ph-stat-txns::after {
            background: #6366f1;
        }

        .ph-stat-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .ph-stat-label {
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
        }

        .ph-stat-icon-wrap {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .ph-stat-credits .ph-stat-icon-wrap {
            background: #ecfdf5;
            color: #10b981;
        }

        .ph-stat-debits .ph-stat-icon-wrap {
            background: #fff1f2;
            color: #f43f5e;
        }

        .ph-stat-txns .ph-stat-icon-wrap {
            background: #eef2ff;
            color: #6366f1;
        }

        .ph-stat-value {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: -0.045em;
            line-height: 1;
        }

        .ph-stat-credits .ph-stat-value {
            color: #059669;
        }

        .ph-stat-debits .ph-stat-value {
            color: #e11d48;
        }

        .ph-stat-txns .ph-stat-value {
            color: #3730a3;
        }

        .ph-stat-foot {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.67rem;
            color: #b0b8d1;
            font-weight: 500;
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
        }

        .ph-stat-foot i {
            font-size: 0.65rem;
        }

        /* ── Active Filter Pills ── */
        .ph-pills-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .ph-pills-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #cbd5e1;
        }

        .ph-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 28px;
            padding: 0 12px;
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            border-radius: 100px;
            font-size: 0.73rem;
            font-weight: 600;
            color: #5b21b6;
        }

        .ph-pill-x {
            background: none;
            border: none;
            cursor: pointer;
            color: #7c3aed;
            padding: 0;
            line-height: 1;
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            margin-left: 2px;
            transition: color 0.15s;
        }

        .ph-pill-x:hover {
            color: #e11d48;
        }

        .ph-pills-clear {
            font-size: 0.72rem;
            font-weight: 600;
            color: #e11d48;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-left: 4px;
            padding: 4px 8px;
            border-radius: 8px;
            transition: background 0.15s;
        }

        .ph-pills-clear:hover {
            background: #fff1f2;
            color: #e11d48;
            text-decoration: none;
        }

        /* ── Main Table Card ── */
        .ph-table-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, 0.055), 0 1px 4px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        /* Table Toolbar */
        .ph-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 28px;
            border-bottom: 1px solid #f1f5f9;
            gap: 16px;
            flex-wrap: wrap;
        }

        .ph-toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ph-toolbar-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0d0f1c;
            letter-spacing: -0.02em;
        }

        .ph-toolbar-count {
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

        .ph-search-wrap {
            display: flex;
            align-items: center;
            gap: 9px;
            background: #f8f9fc;
            border: 1.5px solid #edf0f7;
            border-radius: 10px;
            padding: 0 14px;
            height: 38px;
            min-width: 220px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .ph-search-wrap:focus-within {
            border-color: #6366f1;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
        }

        .ph-search-wrap i {
            color: #c4b5fd;
            font-size: 0.82rem;
        }

        .ph-search-input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.82rem;
            color: #334155;
            width: 100%;
            font-family: inherit;
        }

        .ph-search-input::placeholder {
            color: #c4b5fd;
        }

        /* Table */
        /* Make the whole page shell fit the viewport and keep scrolling inside the table only */
        .ph-shell {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .ph-table-card {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
        }

        .ph-table-scroll {
            /* allow both axes, but keep table area scrollable */
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            flex: 1 1 auto;
            min-height: 0;
        }

        .ph-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.83rem;
            min-width: 860px;
        }

        .ph-tbl thead tr {
            background: #fafbff;
            border-bottom: 1px solid #f0f2fa;
        }

        .ph-tbl thead th {
            padding: 13px 16px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b0b8d1;
            white-space: nowrap;
            user-select: none;
        }

        .ph-tbl thead th:first-child {
            padding-left: 28px;
        }

        .ph-tbl thead th:last-child {
            padding-right: 28px;
        }

        .ph-tbl tbody tr {
            border-bottom: 1px solid #f7f8fc;
            transition: background 0.15s ease;
        }

        .ph-tbl tbody tr:last-child {
            border-bottom: none;
        }

        .ph-tbl tbody tr:hover {
            background: #fafaff;
        }

        .ph-tbl tbody td {
            padding: 15px 16px;
            vertical-align: middle;
            color: #374151;
        }

        .ph-tbl tbody td:first-child {
            padding-left: 28px;
        }

        .ph-tbl tbody td:last-child {
            padding-right: 28px;
        }

        /* Row: Executive */
        .ph-exec {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .ph-avatar {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.68rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            letter-spacing: 0.03em;
            box-shadow: 0 3px 10px rgba(99, 102, 241, 0.22);
        }

        .ph-exec-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e1f2e;
            text-decoration: none;
            letter-spacing: -0.01em;
            display: block;
            transition: color 0.15s;
        }

        .ph-exec-name:hover {
            color: #4f46e5;
            text-decoration: none;
        }

        .ph-exec-id {
            font-size: 0.65rem;
            color: #b0b8d1;
            font-weight: 500;
            font-family: 'SF Mono', 'Consolas', monospace;
        }

        /* Row: Date */
        .ph-date {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #4a5568;
            white-space: nowrap;
        }

        .ph-date i {
            color: #c4b5fd;
        }

        /* Row: Company */
        .ph-company {
            font-size: 0.83rem;
            font-weight: 600;
            color: #2d3748;
        }

        .ph-zone {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 3px;
        }

        /* Category / Type Chips */
        .ph-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            height: 26px;
            padding: 0 11px;
            border-radius: 8px;
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .ph-chip-positive {
            background: #ecfdf5;
            color: #059669;
            border: 1.5px solid #a7f3d0;
        }

        .ph-chip-negative {
            background: #fff1f2;
            color: #e11d48;
            border: 1.5px solid #fecdd3;
        }

        .ph-chip-recovery {
            background: #eff6ff;
            color: #2563eb;
            border: 1.5px solid #bfdbfe;
        }

        .ph-chip-kpi {
            background: #fffbeb;
            color: #d97706;
            border: 1.5px solid #fde68a;
        }

        .ph-chip-neutral {
            background: #f8f9fc;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
        }

        .ph-chip-credit {
            background: #ecfdf5;
            color: #059669;
            border: 1.5px solid #a7f3d0;
        }

        .ph-chip-debit {
            background: #fff1f2;
            color: #e11d48;
            border: 1.5px solid #fecdd3;
        }

        /* Points */
        .ph-points-credit {
            font-size: 0.92rem;
            font-weight: 900;
            color: #059669;
            letter-spacing: -0.02em;
        }

        .ph-points-debit {
            font-size: 0.92rem;
            font-weight: 900;
            color: #e11d48;
            letter-spacing: -0.02em;
        }

        /* Balance */
        .ph-balance {
            font-size: 0.82rem;
            font-weight: 700;
            color: #4a5568;
            font-family: 'SF Mono', 'Consolas', monospace;
        }

        /* Description */
        .ph-desc {
            font-size: 0.8rem;
            color: #4a5568;
            max-width: 200px;
            line-height: 1.4;
        }

        /* Empty State */
        .ph-empty {
            text-align: center;
            padding: 80px 24px;
        }

        .ph-empty-blob {
            width: 72px;
            height: 72px;
            margin: 0 auto 18px;
            background: #eef2ff;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #c4b5fd;
        }

        .ph-empty-h {
            font-size: 1rem;
            font-weight: 800;
            color: #1e1f2e;
            margin-bottom: 6px;
        }

        .ph-empty-p {
            font-size: 0.82rem;
            color: #94a3b8;
        }

        /* Pagination */
        .ph-pager {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 28px;
            border-top: 1px solid #f0f2fa;
            background: #fafbff;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ph-pager-info {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* ── FILTER MODAL ── */
        .ph-modal-bg {
            position: fixed;
            inset: 0;
            background: rgba(10, 12, 30, 0.4);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .ph-modal-bg.open {
            display: flex;
        }

        .ph-modal {
            background: #fff;
            border-radius: 22px;
            width: 100%;
            max-width: 640px;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.18), 0 2px 12px rgba(0, 0, 0, 0.06);
            overflow: visible;
            animation: phModalIn 0.28s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        @keyframes phModalIn {
            from {
                opacity: 0;
                transform: scale(0.94) translateY(-12px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .ph-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 28px 20px;
            background: #fafaff;
            border-bottom: 1px solid #f0f2fa;
        }

        .ph-modal-head-left {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .ph-modal-illo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.28);
            flex-shrink: 0;
        }

        .ph-modal-title {
            font-size: 1.02rem;
            font-weight: 800;
            color: #0d0f1c;
            letter-spacing: -0.02em;
        }

        .ph-modal-sub {
            font-size: 0.73rem;
            color: #94a3b8;
            margin-top: 1px;
        }

        .ph-modal-close-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #fff;
            border: 1.5px solid #e8eaf2;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.18s;
            font-size: 0.9rem;
        }

        .ph-modal-close-btn:hover {
            background: #fee2e6;
            border-color: #fca5a5;
            color: #e11d48;
        }

        .ph-modal-body {
            padding: 28px;
            overflow: visible;
        }

        .ph-modal-section {
            margin-bottom: 24px;
        }

        .ph-modal-section:last-child {
            margin-bottom: 0;
        }

        .ph-modal-section-label {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #b0b8d1;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .ph-modal-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f0f2fa;
        }

        .ph-modal-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 28px 24px;
            border-top: 1px solid #f0f2fa;
            gap: 10px;
        }

        .ph-modal-foot-right {
            display: flex;
            gap: 8px;
        }

        .ph-modal .form-select,
        .ph-modal .form-control {
            height: 42px;
            border-radius: 11px !important;
            font-size: 0.83rem !important;
            border: 1.5px solid #edf0f7 !important;
            background: #fafbff !important;
            color: #2d3748 !important;
            transition: all 0.2s ease;
            box-shadow: none !important;
        }

        .ph-modal .select2-container {
            width: 100% !important;
        }

        .ph-modal .select2-dropdown {
            z-index: 2200;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.16);
        }

        .ph-modal .select2-container--bootstrap-5 .select2-selection--single {
            min-height: 42px;
            height: 42px;
            border-radius: 11px !important;
            border: 1.5px solid #edf0f7 !important;
            background: #fafbff !important;
        }

        .ph-modal .select2-container--bootstrap-5 .select2-selection__rendered {
            line-height: 40px;
            padding-left: 14px;
        }

        .ph-modal .form-select:focus,
        .ph-modal .form-control:focus {
            border-color: #6366f1 !important;
            background: #fff !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
        }

        .ph-modal .form-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 7px;
        }

        .btn-ph-modal-reset {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 40px;
            padding: 0 18px;
            background: #fff;
            border: 1.5px solid #e8eaf2;
            border-radius: 11px;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.18s;
        }

        .btn-ph-modal-reset:hover {
            border-color: #f43f5e;
            color: #e11d48;
            background: #fff1f2;
            text-decoration: none;
        }

        .btn-ph-modal-cancel {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 40px;
            padding: 0 18px;
            background: #fff;
            border: 1.5px solid #e8eaf2;
            border-radius: 11px;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s;
        }

        .btn-ph-modal-cancel:hover {
            background: #f8f9fc;
            border-color: #cbd5e1;
        }

        .btn-ph-modal-apply {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 40px;
            padding: 0 22px;
            background: #6366f1;
            border: none;
            border-radius: 11px;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-ph-modal-apply:hover {
            background: #4f46e5;
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.4);
            transform: translateY(-1px);
        }

        @media (max-width: 1024px) {
            .ph-stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .ph-stats-row {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .ph-stat-value {
                font-size: 1.75rem;
            }

            .ph-modal {
                border-radius: 18px;
            }

            .ph-modal-body {
                padding: 20px;
            }

            .ph-modal-head {
                padding: 18px 20px 16px;
            }

            .ph-modal-foot {
                padding: 14px 20px 18px;
                flex-direction: column;
            }

            .ph-modal-foot-right {
                width: 100%;
            }

            .btn-ph-modal-apply {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')

    @php
        $filterKeys = ['company_id', 'executive_id', 'category', 'type', 'date_from', 'date_to'];
        $activeFilters = collect($filterKeys)->filter(fn($k) => request($k))->count();
    @endphp

    <div class="ph-shell">

        {{-- ══ PAGE HEADER ══════════════════════════════════════════════ --}}
        <div class="ph-header">
            <div>
                <h1 class="ph-page-title">Point History</h1>
                <p class="ph-page-sub">Immutable ledger of all point transactions</p>
            </div>
            <div class="ph-header-actions">
                <button class="btn-ph-filter {{ $activeFilters ? 'active' : '' }}" id="phOpenFilter" type="button">
                    <i class="fa-solid fa-sliders-h"></i>
                    Filters
                    @if($activeFilters)
                        <span class="ph-filter-badge">{{ $activeFilters }}</span>
                    @endif
                </button>
                <a href="{{ route('point_history.export', array_merge(request()->all(), ['format' => 'xlsx'])) }}"
                    class="btn-ph-export">
                    <i class="fa-solid fa-file-excel"></i>
                    Export Excel
                </a>
            </div>
        </div>

        {{-- ══ ACTIVE FILTER PILLS ══════════════════════════════════════ --}}
        @if($activeFilters)
            <div class="ph-pills-row">
                <span class="ph-pills-label">Filters</span>
                @if(request('company_id'))
                    @php $cn = $companies->firstWhere('id', request('company_id'))?->name; @endphp
                    <span class="ph-pill">
                        <i class="fa-solid fa-building" style="font-size:.6rem;"></i>{{ $cn }}
                        <button class="ph-pill-x" onclick="phClearFilter('company_id')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('executive_id'))
                    @php $en = $executives->firstWhere('id', request('executive_id'))?->name; @endphp
                    <span class="ph-pill">
                        <i class="fa-solid fa-user" style="font-size:.6rem;"></i>{{ $en }}
                        <button class="ph-pill-x" onclick="phClearFilter('executive_id')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('category'))
                    <span class="ph-pill">
                        <i class="fa-solid fa-tag" style="font-size:.6rem;"></i>{{ ucfirst(request('category')) }}
                        <button class="ph-pill-x" onclick="phClearFilter('category')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('type'))
                    <span class="ph-pill">
                        <i class="fa-solid fa-arrow-right-arrow-left" style="font-size:.6rem;"></i>{{ ucfirst(request('type')) }}
                        <button class="ph-pill-x" onclick="phClearFilter('type')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('date_from'))
                    <span class="ph-pill">
                        <i class="fa-regular fa-calendar" style="font-size:.6rem;"></i>From {{ request('date_from') }}
                        <button class="ph-pill-x" onclick="phClearFilter('date_from')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('date_to'))
                    <span class="ph-pill">
                        <i class="fa-regular fa-calendar" style="font-size:.6rem;"></i>To {{ request('date_to') }}
                        <button class="ph-pill-x" onclick="phClearFilter('date_to')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                <a href="{{ route('point_history.index') }}" class="ph-pills-clear">
                    <i class="fa-solid fa-rotate-left"></i> Clear all
                </a>
            </div>
        @endif

        {{-- ══ SUMMARY STAT CARDS ═══════════════════════════════════════ --}}
        <div class="ph-stats-row">

            <div class="ph-stat ph-stat-credits">
                <div class="ph-stat-head">
                    <div class="ph-stat-label">Total Credits</div>
                    <div class="ph-stat-icon-wrap"><i class="fa-solid fa-circle-plus"></i></div>
                </div>
                <div class="ph-stat-value">+{{ number_format($summary->total_credits ?? 0) }}</div>
                <div class="ph-stat-foot">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    Filtered credit points
                </div>
            </div>

            <div class="ph-stat ph-stat-debits">
                <div class="ph-stat-head">
                    <div class="ph-stat-label">Total Debits</div>
                    <div class="ph-stat-icon-wrap"><i class="fa-solid fa-circle-minus"></i></div>
                </div>
                <div class="ph-stat-value">-{{ number_format($summary->total_debits ?? 0) }}</div>
                <div class="ph-stat-foot">
                    <i class="fa-solid fa-arrow-trend-down"></i>
                    Filtered debit points
                </div>
            </div>

            <div class="ph-stat ph-stat-txns">
                <div class="ph-stat-head">
                    <div class="ph-stat-label">Total Transactions</div>
                    <div class="ph-stat-icon-wrap"><i class="fa-solid fa-receipt"></i></div>
                </div>
                <div class="ph-stat-value">{{ number_format($summary->total_transactions ?? 0) }}</div>
                <div class="ph-stat-foot">
                    <i class="fa-solid fa-list-check"></i>
                    Matching records
                </div>
            </div>

        </div>

        {{-- ══ TABLE CARD ═══════════════════════════════════════════════ --}}
        <div class="ph-table-card">

            <div class="ph-toolbar">
                <div class="ph-toolbar-left">
                    <span class="ph-toolbar-title">Transactions</span>
                    <span class="ph-toolbar-count">{{ $transactions->total() ?? $transactions->count() }} entries</span>
                </div>
                <div class="ph-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input class="ph-search-input" id="phQuickSearch" type="text" placeholder="Search transactions…">
                </div>
            </div>

            <div class="ph-table-scroll">
                <table class="ph-tbl" id="phTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Executive</th>
                            <th>Company / Zone</th>
                            <th>Description</th>
                            <th style="text-align:center;">Category</th>
                            <th style="text-align:center;">Type</th>
                            <th style="text-align:right;">Points</th>
                            <th style="text-align:right;">Balance After</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            @php
                                $words = explode(' ', trim($tx->executive->name));
                                $initials = implode('', array_map(fn($w) => strtoupper(substr($w, 0, 1)), array_slice($words, 0, 2)));
                                $catClass = match ($tx->category) {
                                    'positive' => 'ph-chip-positive',
                                    'negative' => 'ph-chip-negative',
                                    'recovery' => 'ph-chip-recovery',
                                    'kpi' => 'ph-chip-kpi',
                                    default => 'ph-chip-neutral',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="ph-date"><i
                                            class="fa-regular fa-calendar"></i>{{ $tx->audit_date->format('d M Y') }}</span>
                                </td>

                                <td>
                                    <div class="ph-exec">
                                        <div class="ph-avatar">{{ $initials }}</div>
                                        <div>
                                            <a href="{{ route('executives.show', $tx->executive_id) }}"
                                                class="ph-exec-name">{{ $tx->executive->name }}</a>
                                            <div class="ph-exec-id">{{ $tx->executive->employee_id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="ph-company">{{ $tx->executive->company->name ?? '—' }}</div>
                                    <div class="ph-zone"><i
                                            class="fa-solid fa-location-dot"></i>{{ $tx->executive->zone->name ?? '—' }}</div>
                                </td>

                                <td><span class="ph-desc">{{ $tx->description }}</span></td>

                                <td style="text-align:center;">
                                    <span class="ph-chip {{ $catClass }}">{{ ucfirst($tx->category) }}</span>
                                </td>

                                <td style="text-align:center;">
                                    <span class="ph-chip {{ $tx->type === 'credit' ? 'ph-chip-credit' : 'ph-chip-debit' }}">
                                        <i class="fa-solid {{ $tx->type === 'credit' ? 'fa-arrow-up' : 'fa-arrow-down' }}"
                                            style="font-size:0.6rem;"></i>
                                        {{ ucfirst($tx->type) }}
                                    </span>
                                </td>

                                <td style="text-align:right;">
                                    <span class="{{ $tx->type === 'credit' ? 'ph-points-credit' : 'ph-points-debit' }}">
                                        {{ $tx->type === 'credit' ? '+' : '-' }}{{ number_format($tx->points) }}
                                    </span>
                                </td>

                                <td style="text-align:right;">
                                    <span class="ph-balance">{{ number_format($tx->balance_after ?? 0) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding:0;border:none;">
                                    <div class="ph-empty">
                                        <div class="ph-empty-blob"><i class="fa-solid fa-coins"></i></div>
                                        <div class="ph-empty-h">No transactions found</div>
                                        <p class="ph-empty-p">Try adjusting your filters to find matching records.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="ph-pager">
                    <span class="ph-pager-info">
                        Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}
                        records
                    </span>
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif

        </div>
    </div>{{-- /ph-shell --}}

    {{-- ══ FILTER MODAL ════════════════════════════════════════════ --}}
    <div class="ph-modal-bg" id="phFilterModalBg">
        <div class="ph-modal" role="dialog" aria-modal="true" aria-labelledby="phFilterTitle">

            <div class="ph-modal-head">
                <div class="ph-modal-head-left">
                    <div class="ph-modal-illo"><i class="fa-solid fa-sliders-h"></i></div>
                    <div>
                        <div class="ph-modal-title" id="phFilterTitle">Filter Transactions</div>
                        <div class="ph-modal-sub">Narrow down by any combination of fields</div>
                    </div>
                </div>
                <button class="ph-modal-close-btn" id="phCloseModal" type="button" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('point_history.index') }}" id="phFilterForm">
                <div class="ph-modal-body">

                    <div class="ph-modal-section">
                        <div class="ph-modal-section-label"><i class="fa-solid fa-building"></i> Organisation</div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-select">
                                    <option value="">All Companies</option>
                                    @foreach($companies as $c)
                                        <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ph-modal-section">
                        <div class="ph-modal-section-label"><i class="fa-solid fa-user"></i> Executive & Transaction</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Executive</label>
                                <select name="executive_id" class="form-select select2-ph-modal">
                                    <option value="">All</option>
                                    @foreach($executives as $e)
                                        <option value="{{ $e->id }}" {{ request('executive_id') == $e->id ? 'selected' : '' }}>
                                            {{ $e->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach(['positive', 'negative', 'recovery', 'kpi'] as $cat)
                                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                            {{ ucfirst($cat) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>↑ Credit
                                    </option>
                                    <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>↓ Debit</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ph-modal-section">
                        <div class="ph-modal-section-label"><i class="fa-regular fa-calendar"></i> Date Range</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="ph-modal-foot">
                    <a href="{{ route('point_history.index') }}" class="btn-ph-modal-reset">
                        <i class="fa-solid fa-rotate-left"></i> Reset All
                    </a>
                    <div class="ph-modal-foot-right">
                        <button type="button" class="btn-ph-modal-cancel" id="phCancelModal">Cancel</button>
                        <button type="submit" class="btn-ph-modal-apply"><i class="fa-solid fa-filter"></i> Apply
                            Filters</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Modal ─────────────────────────────────────────────────
        const phBg = document.getElementById('phFilterModalBg');
        const phOpenBtn = document.getElementById('phOpenFilter');
        const phCloseBtn = document.getElementById('phCloseModal');
        const phCancelBtn = document.getElementById('phCancelModal');

        const phOpenModal = () => { phBg.classList.add('open'); document.body.style.overflow = 'hidden'; };
        const phCloseModal = () => { phBg.classList.remove('open'); document.body.style.overflow = ''; };

        phOpenBtn.addEventListener('click', phOpenModal);
        phCloseBtn.addEventListener('click', phCloseModal);
        phCancelBtn.addEventListener('click', phCloseModal);
        phBg.addEventListener('click', e => { if (e.target === phBg) phCloseModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') phCloseModal(); });

        // ── Clear single filter ───────────────────────────────────
        function phClearFilter(key) {
            const url = new URL(window.location.href);
            url.searchParams.delete(key);
            window.location.href = url.toString();
        }

        // ── Quick search ──────────────────────────────────────────
        document.getElementById('phQuickSearch').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#phTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        // ── Select2 ───────────────────────────────────────────────
        $(function () {
            $('.select2-ph-modal').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'All Executives',
                dropdownParent: $('#phFilterModalBg')
            });
        });
    </script>
@endpush