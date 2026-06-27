@extends('layouts.app')
@section('title', 'Audit History')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Audit </li>
    </ol>
@endsection

@push('styles')
    <style>
        /* ── Page Header ── */
        .ah-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 36px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .ah-page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0d0f1c;
            letter-spacing: -0.04em;
            line-height: 1.1;
            margin: 0 0 5px;
        }

        .ah-page-sub {
            font-size: 0.82rem;
            color: #94a3b8;
            font-weight: 450;
        }

        .ah-page-sub a {
            color: #6366f1;
            font-weight: 600;
            text-decoration: none;
        }

        .ah-page-sub a:hover {
            text-decoration: underline;
        }

        .ah-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ── Date Picker Button ── */
        .btn-ah-date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
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
            position: relative;
        }

        .btn-ah-date:hover {
            border-color: #6366f1;
            color: #4f46e5;
            background: #fafaff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
            transform: translateY(-1px);
        }

        .btn-ah-date.has-date {
            border-color: #6366f1;
            color: #4f46e5;
            background: #f5f3ff;
        }

        .btn-ah-date i { font-size: 0.88rem; }

        /* ── Filter Button ── */
        .btn-ah-filter {
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

        .btn-ah-filter:hover {
            border-color: #6366f1;
            color: #4f46e5;
            background: #fafaff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
            transform: translateY(-1px);
        }

        .btn-ah-filter.active {
            border-color: #6366f1;
            color: #4f46e5;
            background: #f5f3ff;
        }

        .ah-filter-badge {
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

        /* ── New Audit Button ── */
        .btn-ah-new {
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

        .btn-ah-new:hover {
            background: #4f46e5;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45);
            text-decoration: none;
        }

        .btn-ah-new:active { transform: translateY(0); }

        /* ── Summary Cards ── */
        .ah-stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 28px;
        }

        .ah-stat {
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

        .ah-stat:hover {
            box-shadow: 0 6px 28px rgba(0, 0, 0, .08);
            transform: translateY(-2px);
        }

        .ah-stat::after {
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

        .ah-stat-audits::after  { background: #6366f1; }
        .ah-stat-positive::after { background: #10b981; }
        .ah-stat-negative::after { background: #f43f5e; }
        .ah-stat-net::after      { background: #f59e0b; }

        .ah-stat-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .ah-stat-label {
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
        }

        .ah-stat-icon-wrap {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .ah-stat-audits  .ah-stat-icon-wrap { background: #eef2ff; color: #6366f1; }
        .ah-stat-positive .ah-stat-icon-wrap { background: #ecfdf5; color: #10b981; }
        .ah-stat-negative .ah-stat-icon-wrap { background: #fff1f2; color: #f43f5e; }
        .ah-stat-net     .ah-stat-icon-wrap { background: #fffbeb; color: #f59e0b; }

        .ah-stat-value {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: -0.045em;
            line-height: 1;
        }

        .ah-stat-audits  .ah-stat-value { color: #3730a3; }
        .ah-stat-positive .ah-stat-value { color: #059669; }
        .ah-stat-negative .ah-stat-value { color: #e11d48; }
        .ah-stat-net     .ah-stat-value { color: #d97706; }

        .ah-stat-foot {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.67rem;
            color: #b0b8d1;
            font-weight: 500;
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
        }

        .ah-stat-foot i { font-size: 0.65rem; }

        /* ── Active Filter Pills ── */
        .ah-pills-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .ah-pills-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #cbd5e1;
        }

        .ah-pill {
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

        .ah-pill-x {
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

        .ah-pill-x:hover { color: #e11d48; }

        .ah-pills-clear {
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

        .ah-pills-clear:hover {
            background: #fff1f2;
            color: #e11d48;
            text-decoration: none;
        }

        /* ── Main Table Card ── */
        .ah-table-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, 0.055), 0 1px 4px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        /* Table Toolbar */
        .ah-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 28px;
            border-bottom: 1px solid #f1f5f9;
            gap: 16px;
            flex-wrap: wrap;
        }

        .ah-toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ah-toolbar-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0d0f1c;
            letter-spacing: -0.02em;
        }

        .ah-toolbar-count {
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

        .ah-search-wrap {
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

        .ah-search-wrap:focus-within {
            border-color: #6366f1;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
        }

        .ah-search-wrap i { color: #c4b5fd; font-size: 0.82rem; }

        .ah-search-input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.82rem;
            color: #334155;
            width: 100%;
            font-family: inherit;
        }

        .ah-search-input::placeholder { color: #c4b5fd; }

        /* Table */
        .ah-table-scroll { overflow-x: auto; }

        .ah-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.83rem;
        }

        .ah-tbl thead tr {
            background: #fafbff;
            border-bottom: 1px solid #f0f2fa;
        }

        .ah-tbl thead th {
            padding: 13px 16px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b0b8d1;
            white-space: nowrap;
            user-select: none;
        }

        .ah-tbl thead th:first-child { padding-left: 28px; }
        .ah-tbl thead th:last-child  { padding-right: 28px; }

        .ah-tbl tbody tr {
            border-bottom: 1px solid #f7f8fc;
            transition: background 0.15s ease;
        }

        .ah-tbl tbody tr:last-child { border-bottom: none; }
        .ah-tbl tbody tr:hover { background: #fafaff; }

        .ah-tbl tbody td {
            padding: 16px 16px;
            vertical-align: middle;
            color: #374151;
        }

        .ah-tbl tbody td:first-child { padding-left: 28px; }
        .ah-tbl tbody td:last-child  { padding-right: 28px; }

        /* Row: ID */
        .ah-row-id {
            font-size: 0.72rem;
            font-weight: 700;
            color: #d4d8e8;
            font-family: 'SF Mono', 'Cascadia Code', 'Consolas', monospace;
            letter-spacing: 0.02em;
        }

        /* Row: Executive */
        .ah-exec { display: flex; align-items: center; gap: 11px; }

        .ah-avatar {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: linear-gradient(135deg, #00039f, #a5b4fc);
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

        .ah-exec-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e1f2e;
            text-decoration: none;
            letter-spacing: -0.01em;
            display: block;
            transition: color 0.15s;
        }

        .ah-exec-name:hover { color: #4f46e5; text-decoration: none; }

        .ah-exec-id {
            font-size: 0.65rem;
            color: #b0b8d1;
            font-weight: 500;
            font-family: 'SF Mono', 'Consolas', monospace;
        }

        /* Row: Company */
        .ah-company { font-size: 0.83rem; font-weight: 600; color: #2d3748; }

        .ah-zone {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 3px;
        }

        /* Row: Date */
        .ah-date {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #4a5568;
            white-space: nowrap;
        }

        .ah-date i { color: #c4b5fd; }

        /* Row: Stats */
        .ah-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 30px;
            padding: 0 10px;
            background: #f8f9fc;
            border-radius: 8px;
            font-size: 0.84rem;
            font-weight: 700;
            color: #374151;
            border: 1px solid #edf0f7;
        }

        /* Row: Score Chips */
        .ah-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 3px;
            min-width: 56px;
            height: 30px;
            padding: 0 12px;
            border-radius: 9px;
            font-size: 0.79rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }

        .ah-chip i { font-size: 0.6rem; }

        .ah-chip-pos { background: #ecfdf5; color: #059669; border: 1.5px solid #a7f3d0; }
        .ah-chip-neg { background: #fff1f2; color: #e11d48; border: 1.5px solid #fecdd3; }
        .ah-chip-rec { background: #eff6ff; color: #2563eb; border: 1.5px solid #bfdbfe; }

        .ah-chip-net-p {
            background: #ecfdf5;
            color: #047857;
            border: 1.5px solid #6ee7b7;
            font-size: 0.88rem;
            min-width: 64px;
        }

        .ah-chip-net-n {
            background: #fff1f2;
            color: #be123c;
            border: 1.5px solid #fecdd3;
            font-size: 0.88rem;
            min-width: 64px;
        }

        /* Row: KPI Badge */
        .ah-kpi {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            height: 28px;
            padding: 0 12px;
            border-radius: 9px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }

        .ah-kpi-pass { background: #d1fae5; color: #065f46; }
        .ah-kpi-fail { background: #fee2e2; color: #9f1239; }
        .ah-kpi-na   { background: #f1f5f9; color: #94a3b8; }

        /* Row: Actions */
        .ah-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .ah-btn-act {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.18s ease;
            flex-shrink: 0;
        }

        .ah-btn-view { background: #eef2ff; color: #4f46e5; }
        .ah-btn-view:hover {
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transform: translateY(-1px);
            text-decoration: none;
        }

        .ah-btn-del { background: #fff1f2; color: #e11d48; }
        .ah-btn-del:hover {
            background: #e11d48;
            color: #fff;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3);
            transform: translateY(-1px);
        }

        /* Empty State */
        .ah-empty { text-align: center; padding: 80px 24px; }

        .ah-empty-blob {
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

        .ah-empty-h { font-size: 1rem; font-weight: 800; color: #1e1f2e; margin-bottom: 6px; }
        .ah-empty-p { font-size: 0.82rem; color: #94a3b8; }
        .ah-empty-p a { color: #4f46e5; font-weight: 600; text-decoration: none; }

        /* ══════════════════════════════════════════
           Pagination Bar
        ══════════════════════════════════════════ */
        .ah-pager {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 28px;
            border-top: 1px solid #f0f2fa;
            background: #fafbff;
            flex-wrap: wrap;
            gap: 12px;
        }

        .ah-pager-info {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Hide Laravel's built-in "Showing X to Y of Z results" text */
        .ah-pager nav > div:first-child { display: none; }

        /* The <nav> and <ul> wrapper */
        .ah-pager nav { display: flex; align-items: center; }
        .ah-pager .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        /* Every page item */
        .ah-pager .pagination .page-item { display: flex; }

        /* Links and spans (disabled) */
        .ah-pager .pagination .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 10px;
            border: 1.5px solid #edf0f7;
            background: #fff;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 600;
            line-height: 1;
            text-decoration: none;
            transition: all 0.18s ease;
            white-space: nowrap;
        }

        /* Hover */
        .ah-pager .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
            background: #f5f3ff;
            border-color: #c4b5fd;
            color: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.12);
        }

        /* Active page */
        .ah-pager .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #4f46e5, #6d28d9);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }

        /* Disabled (prev/next when on first/last page) */
        .ah-pager .pagination .page-item.disabled .page-link {
            background: #f8f9fc;
            border-color: #edf0f7;
            color: #cbd5e1;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Prev / Next arrow buttons — slightly wider */
        .ah-pager .pagination .page-item:first-child .page-link,
        .ah-pager .pagination .page-item:last-child .page-link {
            padding: 0 14px;
            font-size: 0.75rem;
            letter-spacing: 0.01em;
        }

        /* Ellipsis (…) */
        .ah-pager .pagination .page-item .page-link[aria-disabled="true"],
        .ah-pager .pagination span.page-link {
            background: transparent;
            border-color: transparent;
            color: #cbd5e1;
            cursor: default;
            pointer-events: none;
        }

        /* ── FILTER MODAL ── */
        .ah-modal-bg {
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

        .ah-modal-bg.open { display: flex; }

        .ah-modal {
            background: #fff;
            border-radius: 22px;
            width: 100%;
            max-width: 620px;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.18), 0 2px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            animation: modalIn 0.28s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.94) translateY(-12px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .ah-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 28px 20px;
            background: #fafaff;
            border-bottom: 1px solid #f0f2fa;
        }

        .ah-modal-head-left { display: flex; align-items: center; gap: 13px; }

        .ah-modal-illo {
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

        .ah-modal-title {
            font-size: 1.02rem;
            font-weight: 800;
            color: #0d0f1c;
            letter-spacing: -0.02em;
        }

        .ah-modal-sub { font-size: 0.73rem; color: #94a3b8; margin-top: 1px; }

        .ah-modal-close-btn {
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

        .ah-modal-close-btn:hover {
            background: #fee2e6;
            border-color: #fca5a5;
            color: #e11d48;
        }

        .ah-modal-body { padding: 28px; }

        .ah-modal-section { margin-bottom: 24px; }
        .ah-modal-section:last-child { margin-bottom: 0; }

        .ah-modal-section-label {
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

        .ah-modal-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f0f2fa;
        }

        .ah-modal-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 28px 24px;
            border-top: 1px solid #f0f2fa;
            gap: 10px;
        }

        .ah-modal-foot-right { display: flex; gap: 8px; }

        /* Form overrides inside modal */
        .ah-modal .form-select,
        .ah-modal .form-control {
            height: 42px;
            border-radius: 11px !important;
            font-size: 0.83rem !important;
            border: 1.5px solid #edf0f7 !important;
            background: #fafbff !important;
            color: #2d3748 !important;
            transition: all 0.2s ease;
            box-shadow: none !important;
        }

        .ah-modal .form-select:focus,
        .ah-modal .form-control:focus {
            border-color: #6366f1 !important;
            background: #fff !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
        }

        .ah-modal .form-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 7px;
        }

        /* Modal Buttons */
        .btn-modal-reset {
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

        .btn-modal-reset:hover {
            border-color: #f43f5e;
            color: #e11d48;
            background: #fff1f2;
            text-decoration: none;
        }

        .btn-modal-cancel {
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

        .btn-modal-cancel:hover {
            background: #f8f9fc;
            border-color: #cbd5e1;
        }

        .btn-modal-apply {
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

        .btn-modal-apply:hover {
            background: #4f46e5;
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.4);
            transform: translateY(-1px);
        }

        /* Air Datepicker */
        .air-datepicker {
            width: 320px;
            border: none !important;
            border-radius: 22px !important;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .18);
            padding: 18px;
            z-index: 99999 !important;
            transform: translateX(-150px) !important;
        }

        .air-datepicker-nav {
            margin: -18px -18px 18px;
            padding: 18px;
            background: linear-gradient(135deg, #1316a6, #7060c9);
            color: #fff;
            border: none;
        }

        .air-datepicker-nav--title  { color: #fff !important; font-size: 15px; font-weight: 700; }
        .air-datepicker-nav--action { color: #fff !important; }
        .air-datepicker-body--day-name { color: #8b8fa5; font-weight: 700; }

        .air-datepicker-cell {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            transition: .2s;
        }

        .air-datepicker-cell:hover { background: #EEF2FF; }

        .air-datepicker-cell.-selected- {
            background: #6366F1 !important;
            color: #fff !important;
            box-shadow: 0 8px 20px rgba(99, 102, 241, .35);
        }

        .air-datepicker-cell.-current- { color: #6366F1 !important; font-weight: 700; }
        .air-datepicker-buttons { border: none; }
        .air-datepicker-button  { color: #6366F1; font-weight: 700; }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .ah-stats-row { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 640px) {
            .ah-stats-row { grid-template-columns: 1fr; gap: 12px; }
            .ah-stat-value { font-size: 1.75rem; }
            .ah-modal { border-radius: 18px; }
            .ah-modal-body { padding: 20px; }
            .ah-modal-head { padding: 18px 20px 16px; }
            .ah-modal-foot { padding: 14px 20px 18px; flex-direction: column; }
            .ah-modal-foot-right { width: 100%; }
            .btn-modal-apply { width: 100%; justify-content: center; }
            .ah-header-actions { width: 100%; }
            .btn-ah-date, .btn-ah-filter, .btn-ah-new { flex: 1; justify-content: center; }
            .ah-pager { padding: 14px 16px; }
            .ah-pager .pagination .page-link { min-width: 30px; height: 30px; font-size: 0.75rem; }
        }
    </style>
@endpush

@section('content')

    @php
        $auditDate    = request('audit_date', now()->toDateString());
        $isToday      = $auditDate === now()->toDateString();
        $displayDate  = \Carbon\Carbon::parse($auditDate)->format('d M Y');
        $displayLabel = $isToday ? 'Today — ' . $displayDate : $displayDate;

        $filterKeys    = ['company_id', 'zone_id', 'executive_id', 'kpi_status', 'date_from', 'date_to'];
        $activeFilters = collect($filterKeys)->filter(fn($k) => request($k))->count();
    @endphp

    <div class="ah-shell">

        {{-- ══ PAGE HEADER ══════════════════════════════════════════════ --}}
        <div class="ah-header">
            <div class="ah-header-left">
                <h1 class="ah-page-title">Daily Audit Management</h1>
                <p class="ah-page-sub">Showing records for <strong style="color:#4f46e5;">{{ $displayLabel }}</strong> — <a href="#">all companies</a></p>
            </div>
            <div class="ah-header-actions">

                {{-- Date Picker --}}
                <div class="position-relative d-inline-block" id="datePickerWrapper">
                    <button type="button" id="datePickerBtn" class="btn-ah-date {{ !$isToday ? 'has-date' : '' }}">
                        <i class="fa-regular fa-calendar"></i>
                        <span id="selectedDate">{{ $isToday ? 'Today' : $displayDate }}</span>
                        @if(!$isToday)
                            <i class="fa-solid fa-circle" style="font-size:5px;color:#6366f1;margin-left:2px;"></i>
                        @endif
                    </button>
                    <input id="auditDatePicker" type="text" readonly
                           style="position:absolute;top:0;right:0;width:1px;height:1px;opacity:0;pointer-events:none;">
                </div>

                {{-- Filters --}}
                <button class="btn-ah-filter {{ $activeFilters ? 'active' : '' }}" id="openFilterModal" type="button">
                    <i class="fa-solid fa-sliders-h"></i>
                    Filters
                    @if($activeFilters)
                        <span class="ah-filter-badge">{{ $activeFilters }}</span>
                    @endif
                </button>

                {{-- New Audit --}}
                <a href="{{ route('daily_audit.create') }}" class="btn-ah-new">
                    <i class="fa-solid fa-plus"></i>
                    New Audit
                </a>
            </div>
        </div>

        {{-- ══ ACTIVE FILTER PILLS ══════════════════════════════════════ --}}
        @if($activeFilters)
            <div class="ah-pills-row">
                <span class="ah-pills-label">Filters</span>
                @if(request('company_id'))
                    @php $cn = $companies->firstWhere('id', request('company_id'))?->name; @endphp
                    <span class="ah-pill">
                        <i class="fa-solid fa-building" style="font-size:.6rem;"></i>{{ $cn }}
                        <button class="ah-pill-x" onclick="clearFilter('company_id')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('zone_id'))
                    @php $zn = $zones->firstWhere('id', request('zone_id'))?->name; @endphp
                    <span class="ah-pill">
                        <i class="fa-solid fa-map-pin" style="font-size:.6rem;"></i>{{ $zn }}
                        <button class="ah-pill-x" onclick="clearFilter('zone_id')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('executive_id'))
                    @php $en = $executives->firstWhere('id', request('executive_id'))?->name; @endphp
                    <span class="ah-pill">
                        <i class="fa-solid fa-user" style="font-size:.6rem;"></i>{{ $en }}
                        <button class="ah-pill-x" onclick="clearFilter('executive_id')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('kpi_status'))
                    <span class="ah-pill">
                        <i class="fa-solid fa-bullseye" style="font-size:.6rem;"></i>KPI: {{ ucfirst(request('kpi_status')) }}
                        <button class="ah-pill-x" onclick="clearFilter('kpi_status')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('date_from'))
                    <span class="ah-pill">
                        <i class="fa-regular fa-calendar" style="font-size:.6rem;"></i>From {{ request('date_from') }}
                        <button class="ah-pill-x" onclick="clearFilter('date_from')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if(request('date_to'))
                    <span class="ah-pill">
                        <i class="fa-regular fa-calendar" style="font-size:.6rem;"></i>To {{ request('date_to') }}
                        <button class="ah-pill-x" onclick="clearFilter('date_to')"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                <a href="{{ route('daily_audit.index', ['audit_date' => $auditDate]) }}" class="ah-pills-clear">
                    <i class="fa-solid fa-rotate-left"></i> Clear filters
                </a>
            </div>
        @endif

        {{-- ══ SUMMARY STAT CARDS ═══════════════════════════════════════ --}}
        <div class="ah-stats-row">

            <div class="ah-stat ah-stat-audits">
                <div class="ah-stat-head">
                    <div class="ah-stat-label">{{ $isToday ? "Today's" : $displayDate }} Audits</div>
                    <div class="ah-stat-icon-wrap"><i class="fa-solid fa-clipboard-check"></i></div>
                </div>
                <div class="ah-stat-value">{{ $todaySummary['count'] ?? 0 }}</div>
                <div class="ah-stat-foot">
                    <i class="fa-regular fa-clock"></i>
                    Records for {{ $isToday ? 'today' : $displayDate }}
                </div>
            </div>

            <div class="ah-stat ah-stat-positive">
                <div class="ah-stat-head">
                    <div class="ah-stat-label">{{ $isToday ? 'Today' : 'Date' }} Positive</div>
                    <div class="ah-stat-icon-wrap"><i class="fa-solid fa-arrow-trend-up"></i></div>
                </div>
                <div class="ah-stat-value">+{{ $todaySummary['total_positive'] ?? 0 }}</div>
                <div class="ah-stat-foot">
                    <i class="fa-solid fa-circle-plus"></i>
                    Total positive points
                </div>
            </div>

            <div class="ah-stat ah-stat-negative">
                <div class="ah-stat-head">
                    <div class="ah-stat-label">{{ $isToday ? 'Today' : 'Date' }} Negative</div>
                    <div class="ah-stat-icon-wrap"><i class="fa-solid fa-arrow-trend-down"></i></div>
                </div>
                <div class="ah-stat-value">-{{ $todaySummary['total_negative'] ?? 0 }}</div>
                <div class="ah-stat-foot">
                    <i class="fa-solid fa-circle-minus"></i>
                    Total deduction points
                </div>
            </div>

            <div class="ah-stat ah-stat-net">
                <div class="ah-stat-head">
                    <div class="ah-stat-label">{{ $isToday ? 'Today' : 'Date' }} Net Score</div>
                    <div class="ah-stat-icon-wrap"><i class="fa-solid fa-scale-balanced"></i></div>
                </div>
                <div class="ah-stat-value">
                    {{ ($todaySummary['total_score'] ?? 0) >= 0 ? '+' : '' }}{{ $todaySummary['total_score'] ?? 0 }}
                </div>
                <div class="ah-stat-foot">
                    <i class="fa-solid fa-equals"></i>
                    Positive minus negative
                </div>
            </div>

        </div>

        {{-- ══ TABLE CARD ═══════════════════════════════════════════════ --}}
        <div class="ah-table-card">

            {{-- Toolbar --}}
            <div class="ah-toolbar">
                <div class="ah-toolbar-left">
                    <span class="ah-toolbar-title">Audit Records</span>
                    <span class="ah-toolbar-count">{{ $audits->total() ?? $audits->count() }} entries</span>
                </div>
                <div class="ah-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input class="ah-search-input" id="quickSearch" type="text" placeholder="Search records…">
                </div>
            </div>

            {{-- Table --}}
            <div class="ah-table-scroll">
                <table class="ah-tbl" id="auditTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Executive</th>
                            <th>Company / Zone</th>
                            <th>Date</th>
                            <th style="text-align:center;">Calls</th>
                            <th style="text-align:center;">Meetings</th>
                            <th style="text-align:center;">Positive</th>
                            <th style="text-align:center;">Negative</th>
                            <th style="text-align:center;">Recovery</th>
                            <th style="text-align:center;">Net Score</th>
                            <th style="text-align:center;">KPI</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                            @php
                                $words    = explode(' ', trim($audit->executive->name));
                                $initials = implode('', array_map(fn($w) => strtoupper(substr($w, 0, 1)), array_slice($words, 0, 2)));
                            @endphp
                            <tr>
                                <td><span class="ah-row-id">#{{ $audit->id }}</span></td>

                                <td>
                                    <div class="ah-exec">
                                        <div class="ah-avatar">{{ $initials }}</div>
                                        <div>
                                            <a href="{{ route('daily_audit.show', $audit) }}" class="ah-exec-name">{{ $audit->executive->name }}</a>
                                            <div class="ah-exec-id">{{ $audit->executive->employee_id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="ah-company">{{ $audit->executive->company->name ?? '—' }}</div>
                                    <div class="ah-zone"><i class="fa-solid fa-location-dot"></i>{{ $audit->executive->zone->name ?? '—' }}</div>
                                </td>

                                <td>
                                    <span class="ah-date"><i class="fa-regular fa-calendar"></i>{{ $audit->audit_date->format('d M Y') }}</span>
                                </td>

                                <td style="text-align:center;"><span class="ah-num">{{ $audit->connected_calls }}</span></td>
                                <td style="text-align:center;"><span class="ah-num">{{ $audit->confirmed_meetings }}</span></td>

                                <td style="text-align:center;"><span class="ah-chip ah-chip-pos"><i class="fa-solid fa-plus"></i>{{ $audit->positive_points }}</span></td>
                                <td style="text-align:center;"><span class="ah-chip ah-chip-neg"><i class="fa-solid fa-minus"></i>{{ $audit->negative_points }}</span></td>
                                <td style="text-align:center;"><span class="ah-chip ah-chip-rec"><i class="fa-solid fa-rotate-right"></i>{{ $audit->recovery_points }}</span></td>

                                <td style="text-align:center;">
                                    <span class="ah-chip {{ $audit->final_score >= 0 ? 'ah-chip-net-p' : 'ah-chip-net-n' }}">
                                        {{ $audit->final_score >= 0 ? '+' : '' }}{{ $audit->final_score }}
                                    </span>
                                </td>

                                <td style="text-align:center;">
                                    @if($audit->kpi_status === 'passed')
                                        <span class="ah-kpi ah-kpi-pass"><i class="fa-solid fa-check"></i> Pass</span>
                                    @elseif($audit->kpi_status === 'failed')
                                        <span class="ah-kpi ah-kpi-fail"><i class="fa-solid fa-xmark"></i> Fail</span>
                                    @else
                                        <span class="ah-kpi ah-kpi-na">—</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="ah-actions">
                                        <a href="{{ route('daily_audit.show', $audit) }}" class="ah-btn-act ah-btn-view" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @can('delete', $audit)
                                            <form id="daf-{{ $audit->id }}" action="{{ route('daily_audit.destroy', $audit) }}" method="POST" style="display:none;">
                                                @csrf @method('DELETE')
                                            </form>
                                            <button type="button" class="ah-btn-act ah-btn-del" title="Delete"
                                                data-confirm-delete="{{ $audit->executive->name }} ({{ $audit->audit_date->format('d M') }})"
                                                data-form-id="daf-{{ $audit->id }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" style="padding:0;border:none;">
                                    <div class="ah-empty">
                                        <div class="ah-empty-blob"><i class="fa-solid fa-clipboard-list"></i></div>
                                        <div class="ah-empty-h">No audit records for {{ $displayLabel }}</div>
                                        <p class="ah-empty-p">
                                            No entries found for this date. Try a different date, or
                                            <a href="{{ route('daily_audit.create') }}">enter a new audit</a>.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ══ PAGINATION ═══════════════════════════════════════════ --}}
            @if($audits->hasPages())
                <div class="ah-pager">
                    <span class="ah-pager-info">
                        Showing {{ $audits->firstItem() }}–{{ $audits->lastItem() }} of {{ $audits->total() }} records
                    </span>
                    {{ $audits->appends(request()->query())->links() }}
                </div>
            @endif

        </div>

    </div>{{-- /ah-shell --}}

    {{-- ══ FILTER MODAL ════════════════════════════════════════════ --}}
    <div class="ah-modal-bg" id="filterModalBg">
        <div class="ah-modal" role="dialog" aria-modal="true" aria-labelledby="filterModalTitle">

            <div class="ah-modal-head">
                <div class="ah-modal-head-left">
                    <div class="ah-modal-illo"><i class="fa-solid fa-sliders-h"></i></div>
                    <div>
                        <div class="ah-modal-title" id="filterModalTitle">Filter Audits</div>
                        <div class="ah-modal-sub">Narrow down records by any combination</div>
                    </div>
                </div>
                <button class="ah-modal-close-btn" id="closeModal" type="button" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('daily_audit.index') }}" id="filterForm">
                <input type="hidden" name="audit_date" value="{{ $auditDate }}">

                <div class="ah-modal-body">

                    <div class="ah-modal-section">
                        <div class="ah-modal-section-label"><i class="fa-solid fa-building"></i> Organisation</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-select">
                                    <option value="">All Companies</option>
                                    @foreach($companies as $c)
                                        <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Zone</label>
                                <select name="zone_id" class="form-select">
                                    <option value="">All Zones</option>
                                    @foreach($zones as $z)
                                        <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ah-modal-section">
                        <div class="ah-modal-section-label"><i class="fa-solid fa-user"></i> Executive & Performance</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Executive</label>
                                <select name="executive_id" class="form-select select2-modal">
                                    <option value="">All Executives</option>
                                    @foreach($executives as $e)
                                        <option value="{{ $e->id }}" {{ request('executive_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">KPI Status</label>
                                <select name="kpi_status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="passed" {{ request('kpi_status') === 'passed' ? 'selected' : '' }}>✓ Passed</option>
                                    <option value="failed" {{ request('kpi_status') === 'failed' ? 'selected' : '' }}>✗ Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ah-modal-section">
                        <div class="ah-modal-section-label"><i class="fa-regular fa-calendar"></i> Date Range</div>
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

                <div class="ah-modal-foot">
                    <a href="{{ route('daily_audit.index') }}" class="btn-modal-reset">
                        <i class="fa-solid fa-rotate-left"></i> Reset All
                    </a>
                    <div class="ah-modal-foot-right">
                        <button type="button" class="btn-modal-cancel" id="cancelModal">Cancel</button>
                        <button type="submit" class="btn-modal-apply"><i class="fa-solid fa-filter"></i> Apply Filters</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Filter Modal ───────────────────────────────────────────────
        const bg        = document.getElementById('filterModalBg');
        const openBtn   = document.getElementById('openFilterModal');
        const closeBtn  = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelModal');

        const openModal  = () => { bg.classList.add('open');    document.body.style.overflow = 'hidden'; };
        const closeModal = () => { bg.classList.remove('open'); document.body.style.overflow = ''; };

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        bg.addEventListener('click', e => { if (e.target === bg) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        // ── Clear single filter ────────────────────────────────────────
        function clearFilter(key) {
            const url = new URL(window.location.href);
            url.searchParams.delete(key);
            window.location.href = url.toString();
        }

        // ── Date Picker ────────────────────────────────────────────────
        const btn  = document.getElementById('datePickerBtn');
        const text = document.getElementById('selectedDate');

        const picker = new AirDatepicker('#auditDatePicker', {
            autoClose: true,
            maxDate: new Date(),
            selectedDates: ['{{ $auditDate }}'],
            dateFormat: 'yyyy-MM-dd',
            position: 'bottom left',
            locale: {
                days:        ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                daysShort:   ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
                daysMin:     ['Su','Mo','Tu','We','Th','Fr','Sa'],
                months:      ['January','February','March','April','May','June','July','August','September','October','November','December'],
                monthsShort: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                today:       'Today',
                clear:       'Clear',
                dateFormat:  'yyyy-MM-dd',
                firstDay:    0,
            },
            onSelect({ date, formattedDate }) {
                if (date) {
                    text.textContent = date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                }
                const url = new URL(window.location.href);
                url.searchParams.set('audit_date', formattedDate);
                url.searchParams.delete('page');
                window.location.href = url.toString();
            },
        });

        btn.addEventListener('click', e => { e.preventDefault(); picker.show(); });

        // ── Quick search ───────────────────────────────────────────────
        document.getElementById('quickSearch').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#auditTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        // ── Select2 ───────────────────────────────────────────────────
        $(function () {
            $('.select2-modal').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'All Executives',
                dropdownParent: $('#filterModalBg .ah-modal'),
            });
        });
    </script>
@endpush