@extends('layouts.app')
@section('title', 'Audit Detail — ' . $audit->executive->name)
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('daily_audit.index') }}">Audit History</a></li>
        <li class="breadcrumb-item active">{{ $audit->executive->name }} · {{ $audit->audit_date->format('d M Y') }}</li>
    </ol>
@endsection

@push('styles')
    <style>
        /* ══════════════════════════════════════════════════════════
       BASE CARD SYSTEM
       ══════════════════════════════════════════════════════════ */
        .ad-shell * {
            box-sizing: border-box;
        }

        .ad-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.7);
            overflow: hidden;
            margin-bottom: 16px;
        }

        .ad-card:last-child {
            margin-bottom: 0;
        }

        .ad-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 18px;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbff;
        }

        .ad-card-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .ad-card-title {
            font-size: 0.82rem;
            font-weight: 800;
            color: #1e1f2e;
            letter-spacing: -0.01em;
        }

        .ad-card-header-meta {
            margin-left: auto;
            font-size: 0.72rem;
            font-weight: 700;
            color: #10b981;
            white-space: nowrap;
        }

        .ad-card-header-pill {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            height: 22px;
            padding: 0 10px;
            background: #f5f3ff;
            border: 1px solid #e0e7ff;
            border-radius: 100px;
            font-size: 0.65rem;
            font-weight: 800;
            color: #4f46e5;
            white-space: nowrap;
        }

        .ad-card-body {
            padding: 16px 18px;
        }

        /* ══════════════════════════════════════════════════════════
       TOP ROW — Executive / Scorecard / Actions
       ══════════════════════════════════════════════════════════ */
        .ad-top-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            align-items: stretch;
            flex-wrap: wrap;
        }

        .ad-top-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.7);
            display: flex;
            align-items: center;
        }

        /* Executive mini card */
        .ad-exec-mini {
            flex: 0 0 230px;
            gap: 12px;
            padding: 14px 18px;
        }

        .ad-exec-mini-avatar {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            flex-shrink: 0;
            background: linear-gradient(135deg, #4338ca, #a5b4fc);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.03em;
            box-shadow: 0 4px 14px rgba(67, 56, 202, 0.25);
        }

        .ad-exec-mini-info {
            min-width: 0;
        }

        .ad-exec-mini-name {
            font-size: 0.92rem;
            font-weight: 800;
            color: #1e1f2e;
            letter-spacing: -0.02em;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ad-exec-mini-sub {
            font-size: 0.66rem;
            color: #94a3b8;
            font-weight: 600;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ad-exec-mini-date {
            font-size: 0.66rem;
            color: #b0b8d1;
            font-weight: 500;
            margin-top: 1px;
            white-space: nowrap;
        }

        /* Performance scorecard strip */
        .ad-score-strip {
            flex: 1 1 480px;
            padding: 0 6px;
        }

        .ad-score-strip-label {
            flex: 0 0 auto;
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            line-height: 1.5;
            padding: 0 18px;
            border-right: 1px solid #f1f5f9;
        }

        .ad-score-strip-item {
            flex: 1;
            text-align: center;
            padding: 14px 10px;
        }

        .ad-score-strip-item+.ad-score-strip-item {
            border-left: 1px solid #f1f5f9;
        }

        .ad-score-strip-val {
            font-size: 1.55rem;
            font-weight: 900;
            letter-spacing: -0.05em;
            line-height: 1;
        }

        .ad-score-strip-lbl {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b0b8d1;
            margin-top: 5px;
        }

        /* Actions card */
        .ad-actions-card {
            flex: 0 0 auto;
            gap: 8px;
            padding: 12px 16px;
            flex-wrap: wrap;
        }

        .ad-kpi-pass {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 30px;
            padding: 0 12px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 8px;
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .ad-kpi-fail {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 30px;
            padding: 0 12px;
            background: #fee2e2;
            color: #9f1239;
            border-radius: 8px;
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .ad-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            height: 30px;
            padding: 0 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .ad-status-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .ad-icon-btn {
            width: 36px;
            height: 30px;
            border-radius: 9px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1.5px solid #e8eaf2;
            color: #4a5568;
            transition: all 0.18s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .ad-icon-btn:hover {
            background: #f8f9fc;
            border-color: #c4b5fd;
            color: #4f46e5;
            transform: translateY(-1px);
        }

        .ad-icon-btn-danger {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }

        .ad-icon-btn-danger:hover {
            background: #e11d48;
            color: #fff;
            border-color: #e11d48;
        }

        .ad-tier-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 30px;
            padding: 0 13px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.18s ease;
            border: 1.5px solid transparent;
        }

        .ad-tier-pill:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .ad-tier-pill-warn {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }

        .ad-tier-pill-warn:hover {
            background: #e11d48;
            color: #fff;
        }

        .ad-tier-pill-ok {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .ad-tier-pill-ok:hover {
            background: #2563eb;
            color: #fff;
        }

        .ad-tier-pill-sm {
            height: 24px;
            padding: 0 10px;
            font-size: 0.62rem;
        }

        /* ══════════════════════════════════════════════════════════
       MAIN 3-COLUMN GRID
       ══════════════════════════════════════════════════════════ */
        .ad-main-grid {
            display: grid;
            grid-template-columns: minmax(230px, 270px) minmax(0, 1fr) minmax(250px, 300px);
            gap: 16px;
            align-items: start;
        }

        .ad-col-left,
        .ad-col-mid,
        .ad-col-right {
            min-width: 0;
        }

        /* ── Activity metrics (mini tiles) ── */
        .ad-mini-metric-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .ad-mini-metric {
            border-radius: 11px;
            padding: 10px 6px;
            text-align: center;
        }

        .ad-mini-metric-blue {
            background: #eef2ff;
        }

        .ad-mini-metric-green {
            background: #ecfdf5;
        }

        .ad-mini-metric-amber {
            background: #fffbeb;
        }

        .ad-mini-metric-icon {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            margin: 0 auto 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            background: #fff;
        }

        .ad-mini-metric-blue .ad-mini-metric-icon {
            color: #6366f1;
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.18);
        }

        .ad-mini-metric-green .ad-mini-metric-icon {
            color: #10b981;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.18);
        }

        .ad-mini-metric-amber .ad-mini-metric-icon {
            color: #f59e0b;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.18);
        }

        .ad-mini-metric-val {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1;
        }

        .ad-mini-metric-blue .ad-mini-metric-val {
            color: #3730a3;
        }

        .ad-mini-metric-green .ad-mini-metric-val {
            color: #059669;
        }

        .ad-mini-metric-amber .ad-mini-metric-val {
            color: #d97706;
        }

        .ad-mini-metric-lbl {
            font-size: 0.56rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            margin-top: 3px;
        }

        .ad-plain-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .ad-plain-stat {
            background: #fafbff;
            border: 1px solid #f0f2fa;
            border-radius: 9px;
            padding: 9px 4px;
            text-align: center;
        }

        .ad-plain-stat-val {
            font-size: 0.84rem;
            font-weight: 800;
            color: #1e1f2e;
            letter-spacing: -0.02em;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ad-plain-stat-lbl {
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #b0b8d1;
            margin-top: 3px;
        }

        /* ── Audit info list ── */
        .ad-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8.5px 0;
            border-bottom: 1px solid #f7f8fc;
            font-size: 0.78rem;
        }

        .ad-info-row:last-child {
            border-bottom: none;
        }

        .ad-info-lbl {
            color: #94a3b8;
            font-weight: 500;
        }

        .ad-info-val {
            color: #1e1f2e;
            font-weight: 700;
            text-align: right;
        }

        /* ── Remarks ── */
        .ad-remarks-box {
            background: #fafbff;
            border: 1px solid #f0f2fa;
            border-radius: 10px;
            padding: 13px 15px;
            font-size: 0.8rem;
            color: #4a5568;
            line-height: 1.65;
            margin-bottom: 10px;
        }

        .btn-ad-download {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 34px;
            padding: 0 14px;
            background: #fff;
            border: 1.5px solid #e8eaf2;
            border-radius: 9px;
            color: #4a5568;
            font-size: 0.76rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.18s;
        }

        .btn-ad-download:hover {
            background: #f5f3ff;
            border-color: #c4b5fd;
            color: #4f46e5;
            text-decoration: none;
        }

        /* ── Compliance progress ── */
        .ad-progress-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .ad-progress-track {
            flex: 1;
            height: 5px;
            border-radius: 10px;
            background: #f1f5f9;
            overflow: hidden;
        }

        .ad-progress-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #10b981, #34d399);
            transition: width .4s;
        }

        .ad-progress-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #10b981;
            white-space: nowrap;
        }

        /* ── Compliance flags grid ── */
        .ad-flags-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .ad-flag {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 13px;
            border-radius: 10px;
            font-size: 0.76rem;
            font-weight: 500;
            border: 1px solid;
            transition: transform 0.15s;
        }

        .ad-flag-pass {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
            font-weight: 600;
        }

        .ad-flag-fail {
            background: #fafbff;
            border-color: #f0f2fa;
            color: #94a3b8;
        }

        .ad-flag-icon {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            flex-shrink: 0;
        }

        .ad-flag-pass .ad-flag-icon {
            background: #d1fae5;
            color: #059669;
        }

        .ad-flag-fail .ad-flag-icon {
            background: #f1f5f9;
            color: #cbd5e1;
        }

        /* ── Point transactions ── */
        .ad-tx-summary-band {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border-bottom: 1px solid #f1f5f9;
            background: #fafbff;
        }

        .ad-tx-summary-item {
            padding: 14px 10px;
            text-align: center;
            border-right: 1px solid #f1f5f9;
        }

        .ad-tx-summary-item:last-child {
            border-right: none;
        }

        .ad-tx-summary-val {
            font-size: 1.3rem;
            font-weight: 900;
            letter-spacing: -0.04em;
            line-height: 1;
        }

        .ad-tx-summary-lbl {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b0b8d1;
            margin-top: 4px;
        }

        .ad-tx-wrap {
            overflow-x: auto;
        }

        .ad-tx-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.79rem;
        }

        .ad-tx-table thead tr {
            background: #fafbff;
            border-bottom: 1px solid #f0f2fa;
        }

        .ad-tx-table thead th {
            padding: 10px 16px;
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b0b8d1;
            white-space: nowrap;
        }

        .ad-tx-table thead th:first-child {
            padding-left: 20px;
        }

        .ad-tx-table thead th:last-child {
            padding-right: 20px;
            text-align: right;
        }

        .ad-tx-table tbody tr {
            border-bottom: 1px solid #f7f8fc;
            transition: background 0.12s;
        }

        .ad-tx-table tbody tr:last-child {
            border-bottom: none;
        }

        .ad-tx-table tbody tr:hover {
            background: #fafaff;
        }

        .ad-tx-table tbody td {
            padding: 11px 16px;
            vertical-align: middle;
            color: #374151;
        }

        .ad-tx-table tbody td:first-child {
            padding-left: 20px;
        }

        .ad-tx-table tbody td:last-child {
            padding-right: 20px;
            text-align: right;
        }

        .ad-tx-rule {
            font-size: 0.79rem;
            font-weight: 600;
            color: #1e1f2e;
        }

        .ad-cat-badge {
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

        .ad-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            height: 22px;
            padding: 0 9px;
            border-radius: 6px;
            font-size: 0.63rem;
            font-weight: 700;
        }

        .ad-type-credit {
            background: #ecfdf5;
            color: #059669;
        }

        .ad-type-debit {
            background: #fff1f2;
            color: #e11d48;
        }

        .ad-pts-pos {
            font-size: 0.85rem;
            font-weight: 800;
            color: #059669;
        }

        .ad-pts-neg {
            font-size: 0.85rem;
            font-weight: 800;
            color: #e11d48;
        }

        .ad-pts-cell {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .ad-pts-bar-track {
            width: 46px;
            height: 3px;
            border-radius: 2px;
            background: #f1f5f9;
            overflow: hidden;
            flex-shrink: 0;
        }

        .ad-pts-bar-fill {
            display: block;
            height: 100%;
            border-radius: 2px;
        }

        /* ── Empty state ── */
        .ad-empty {
            text-align: center;
            padding: 36px 20px;
            color: #b0b8d1;
        }

        .ad-empty i {
            font-size: 1.4rem;
            margin-bottom: 8px;
            display: block;
        }

        .ad-empty p {
            font-size: 0.78rem;
            margin: 0;
        }

        /* ── Goal tracking & trend (gauge) ── */
        .ad-goal-head-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .ad-goal-target-label {
            font-size: 0.62rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
        }

        .ad-gauge-wrap {
            position: relative;
            width: 100%;
            max-width: 230px;
            margin: 0 auto;
        }

        .ad-gauge-wrap svg {
            width: 100%;
            height: auto;
            display: block;
        }

        .ad-gauge-score {
            position: absolute;
            left: 50%;
            top: 64%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 100%;
        }

        .ad-gauge-score-val {
            font-size: 1.7rem;
            font-weight: 900;
            letter-spacing: -0.05em;
            line-height: 1;
        }

        .ad-gauge-score-lbl {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b0b8d1;
            margin-top: 2px;
        }

        .ad-gauge-minmax {
            display: flex;
            justify-content: space-between;
            font-size: 0.62rem;
            color: #cbd5e1;
            font-weight: 700;
            max-width: 230px;
            margin: -4px auto 0;
            padding: 0 6px;
        }

        .ad-goal-msg {
            font-size: 0.74rem;
            line-height: 1.5;
            border-radius: 10px;
            padding: 10px 12px;
            margin: 16px 0;
            border: 1px solid;
        }

        .ad-goal-msg-warn {
            background: #fff1f2;
            color: #9f1239;
            border-color: #fecdd3;
        }

        .ad-goal-msg-ok {
            background: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .ad-goal-tiles {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .ad-goal-tile {
            background: #fafbff;
            border: 1px solid #f0f2fa;
            border-radius: 10px;
            padding: 10px 8px;
            text-align: center;
        }

        .ad-goal-tile-val {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1e1f2e;
            letter-spacing: -0.02em;
        }

        .ad-goal-tile-lbl {
            font-size: 0.56rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #b0b8d1;
            margin-top: 3px;
        }

        .ad-goal-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        /* ══════════════════════════════════════════════════════════
       RESPONSIVE
       ══════════════════════════════════════════════════════════ */
        @media (max-width: 1399px) {
            .ad-main-grid {
                grid-template-columns: 1fr 1fr;
            }

            .ad-col-right {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 991px) {
            .ad-main-grid {
                grid-template-columns: 1fr;
            }

            .ad-score-strip {
                flex-wrap: wrap;
            }

            .ad-score-strip-label {
                border-right: none;
                padding-bottom: 8px;
            }
        }

        @media (max-width: 767px) {
            .ad-top-row {
                flex-direction: column;
            }

            .ad-exec-mini,
            .ad-score-strip,
            .ad-actions-card {
                flex: 1 1 100%;
            }

            .ad-flags-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .ad-tx-summary-band {
                grid-template-columns: 1fr;
            }

            .ad-tx-summary-item {
                border-right: none;
                border-bottom: 1px solid #f1f5f9;
            }

            .ad-tx-summary-item:last-child {
                border-bottom: none;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $tierLabel = $audit->executive->tier_label;
        $isReviewZone = $tierLabel && (stripos($tierLabel, 'review') !== false);

        $finalScore = $audit->final_score;
        $gaugeMag = max(0, min(100, abs($finalScore)));
        $gaugeIsBad = $finalScore < 0 || $audit->kpi_status === 'failed';
        $gaugeColor = $gaugeIsBad ? '#e11d48' : '#10b981';

        $flags = [
            ['label' => 'CRM Follow-up', 'val' => $audit->crm_followup],
            ['label' => 'Correct CRM Disposition', 'val' => $audit->crm_disposition_correct],
            ['label' => 'First Contact ≤ 45 min', 'val' => $audit->first_contact_within_45min],
            ['label' => '100% Follow-up', 'val' => $audit->all_leads_followed_up],
            ['label' => 'Warm Lead Converted', 'val' => $audit->warm_lead_converted],
            ['label' => 'Cold Lead Reactivated', 'val' => $audit->cold_lead_reactivated],
        ];
        $passCount = collect($flags)->where('val', true)->count();
    @endphp

    <div class="ah-shell ad-shell">

        {{-- ═══ TOP ROW ═══ --}}
        <div class="ad-top-row">

            {{-- Executive mini card --}}
            <div class="ad-top-card ad-exec-mini">
                <div class="ad-exec-mini-avatar">{{ strtoupper(substr($audit->executive->name, 0, 2)) }}</div>
                <div class="ad-exec-mini-info">
                    <div class="ad-exec-mini-name">{{ $audit->executive->name }}</div>
                    <div class="ad-exec-mini-sub">
                        {{ $audit->executive->employee_id }}@if($audit->executive->zone) ·
                        {{ $audit->executive->zone->name }}@endif
                    </div>
                    <div class="ad-exec-mini-date">{{ $audit->audit_date->format('d M Y') }} ·
                        {{ ucfirst($audit->audit_type) }}</div>
                </div>
            </div>

            {{-- Performance scorecard strip --}}
            <div class="ad-top-card ad-score-strip">
                <div class="ad-score-strip-label">Performance<br>Scorecard</div>
                <div class="ad-score-strip-item">
                    <div class="ad-score-strip-val" style="color:{{ $finalScore >= 0 ? '#3730a3' : '#e11d48' }};">
                        {{ $finalScore >= 0 ? '+' : '' }}{{ $finalScore }}</div>
                    <div class="ad-score-strip-lbl">Final Score</div>
                </div>
                <div class="ad-score-strip-item">
                    <div class="ad-score-strip-val" style="color:#059669;">+{{ $audit->positive_points }}</div>
                    <div class="ad-score-strip-lbl">Positive</div>
                </div>
                <div class="ad-score-strip-item">
                    <div class="ad-score-strip-val" style="color:#e11d48;">-{{ $audit->negative_points }}</div>
                    <div class="ad-score-strip-lbl">Negative</div>
                </div>
                <div class="ad-score-strip-item">
                    <div class="ad-score-strip-val" style="color:#2563eb;">+{{ $audit->recovery_points }}</div>
                    <div class="ad-score-strip-lbl">Recovery</div>
                </div>
            </div>

            {{-- Actions card --}}
            <div class="ad-top-card ad-actions-card">
                @if($audit->kpi_status === 'passed')
                    <span class="ad-kpi-pass"><i class="fa-solid fa-circle-check"></i> KPI Passed</span>
                @elseif($audit->kpi_status === 'failed')
                    <span class="ad-kpi-fail"><i class="fa-solid fa-circle-xmark"></i> KPI Failed</span>
                @endif
                <span class="ad-status-badge">{{ ucfirst($audit->status) }}</span>

                <a href="{{ route('executives.show', $audit->executive) }}" class="ad-icon-btn" title="View Executive">
                    <i class="fa-solid fa-user"></i>
                </a>

                @can('delete', $audit)
                    <form id="delForm" action="{{ route('daily_audit.destroy', $audit) }}" method="POST" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button" class="ad-icon-btn ad-icon-btn-danger" title="Delete"
                        data-confirm-delete="{{ $audit->executive->name }}'s audit on {{ $audit->audit_date->format('d M Y') }}"
                        data-form-id="delForm">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                @endcan

                @if($tierLabel)
                    <a href="{{ route('executives.show', $audit->executive) }}"
                        class="ad-tier-pill {{ $isReviewZone ? 'ad-tier-pill-warn' : 'ad-tier-pill-ok' }}">
                        <i class="fa-solid {{ $isReviewZone ? 'fa-triangle-exclamation' : 'fa-star' }}"></i>
                        {{ strtoupper($tierLabel) }}
                    </a>
                @endif
            </div>

        </div>

        {{-- ═══ MAIN GRID ═══ --}}
        <div class="ad-main-grid">

            {{-- ───── LEFT COLUMN ───── --}}
            <div class="ad-col-left">

                {{-- Activity Metrics --}}
                <div class="ad-card">
                    <div class="ad-card-header">
                        <div class="ad-card-icon" style="background:#fffbeb;color:#f59e0b;"><i
                                class="fa-solid fa-chart-simple"></i></div>
                        <span class="ad-card-title">Activity Metrics</span>
                    </div>
                    <div class="ad-card-body">
                        <div class="ad-mini-metric-grid">
                            <div class="ad-mini-metric ad-mini-metric-blue">
                                <div class="ad-mini-metric-icon"><i class="fa-solid fa-phone"></i></div>
                                <div class="ad-mini-metric-val">{{ $audit->connected_calls }}</div>
                                <div class="ad-mini-metric-lbl">Calls</div>
                            </div>
                            <div class="ad-mini-metric ad-mini-metric-green">
                                <div class="ad-mini-metric-icon"><i class="fa-solid fa-calendar-check"></i></div>
                                <div class="ad-mini-metric-val">{{ $audit->confirmed_meetings }}</div>
                                <div class="ad-mini-metric-lbl">Confirmed</div>
                            </div>
                            <div class="ad-mini-metric ad-mini-metric-amber">
                                <div class="ad-mini-metric-icon"><i class="fa-solid fa-handshake"></i></div>
                                <div class="ad-mini-metric-val">{{ $audit->meetings_attended }}</div>
                                <div class="ad-mini-metric-lbl">Attended</div>
                            </div>
                        </div>
                        <!-- <div class="ad-plain-stat-grid">
                            <div class="ad-plain-stat">
                                <div class="ad-plain-stat-val" style="{{ $audit->executive->current_score < 0 ? 'color:#e11d48;' : '' }}">{{ number_format($audit->executive->current_score) }}</div>
                                <div class="ad-plain-stat-lbl">Total Score</div>
                            </div>
                            <div class="ad-plain-stat">
                                <div class="ad-plain-stat-val">{{ $tierLabel }}</div>
                                <div class="ad-plain-stat-lbl">Tier</div>
                            </div>
                            <div class="ad-plain-stat">
                                <div class="ad-plain-stat-val" style="{{ $audit->executive->monthly_score < 0 ? 'color:#e11d48;' : '' }}">{{ number_format($audit->executive->monthly_score) }}</div>
                                <div class="ad-plain-stat-lbl">Monthly</div>
                            </div>
                        </div> -->
                    </div>
                </div>

                {{-- Audit Info --}}
                <div class="ad-card">
                    <div class="ad-card-header">
                        <div class="ad-card-icon" style="background:#eef2ff;color:#6366f1;"><i
                                class="fa-solid fa-table-list"></i></div>
                        <span class="ad-card-title">Audit Info</span>
                    </div>
                    <div class="ad-card-body">
                        <div class="ad-info-row"><span class="ad-info-lbl">Date</span><span
                                class="ad-info-val">{{ $audit->audit_date->format('d M Y') }}</span></div>
                        <div class="ad-info-row"><span class="ad-info-lbl">Day</span><span
                                class="ad-info-val">{{ $audit->audit_date->format('l') }}</span></div>
                        <div class="ad-info-row"><span class="ad-info-lbl">Strategy</span><span
                                class="ad-info-val">{{ ucfirst($audit->audit_type) }}</span></div>
                        <div class="ad-info-row"><span class="ad-info-lbl">Status</span><span
                                class="ad-info-val">{{ ucfirst($audit->status) }}</span></div>
                        <div class="ad-info-row"><span class="ad-info-lbl">Transactions</span><span
                                class="ad-info-val">{{ $audit->pointTransactions->count() }}</span></div>
                        <div class="ad-info-row"><span class="ad-info-lbl">Compliance</span><span
                                class="ad-info-val">{{ $passCount }}/{{ count($flags) }} passed</span></div>
                    </div>
                </div>

                {{-- Remarks & Evidence --}}
                @if($audit->remarks || $audit->evidence_path)
                    <div class="ad-card">
                        <div class="ad-card-header">
                            <div class="ad-card-icon" style="background:#f5f3ff;color:#7c3aed;"><i
                                    class="fa-solid fa-paperclip"></i></div>
                            <span class="ad-card-title">Remarks & Evidence</span>
                        </div>
                        <div class="ad-card-body">
                            @if($audit->remarks)
                                <div class="ad-remarks-box">{{ $audit->remarks }}</div>
                            @endif
                            @if($audit->evidence_path)
                                <a href="{{ asset('storage/' . $audit->evidence_path) }}" target="_blank" class="btn-ad-download">
                                    <i class="fa-solid fa-file-arrow-down"></i> Download Evidence
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            {{-- ───── MIDDLE COLUMN ───── --}}
            <div class="ad-col-mid">

                {{-- Compliance Flags --}}
                <div class="ad-card">
                    <div class="ad-card-header">
                        <div class="ad-card-icon" style="background:#ecfdf5;color:#10b981;"><i
                                class="fa-solid fa-shield-check"></i></div>
                        <span class="ad-card-title">Compliance Flags</span>
                        <span class="ad-card-header-meta">{{ $passCount }}/{{ count($flags) }} passed</span>
                    </div>
                    <div class="ad-card-body">
                        <div class="ad-progress-row">
                            <div class="ad-progress-track">
                                <div class="ad-progress-fill" style="width:{{ ($passCount / count($flags)) * 100 }}%;"></div>
                            </div>
                            <span class="ad-progress-label">{{ $passCount }}/{{ count($flags) }}</span>
                        </div>

                        <div class="ad-flags-grid">
                            @foreach($flags as $f)
                                <div class="ad-flag {{ $f['val'] ? 'ad-flag-pass' : 'ad-flag-fail' }}">
                                    <div class="ad-flag-icon"><i class="fa-solid {{ $f['val'] ? 'fa-check' : 'fa-xmark' }}"></i>
                                    </div>
                                    <span>{{ $f['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Point Transactions --}}
                <div class="ad-card">
                    <div class="ad-card-header">
                        <div class="ad-card-icon" style="background:#fffbeb;color:#f59e0b;"><i
                                class="fa-solid fa-coins"></i></div>
                        <span class="ad-card-title">Point Transactions</span>
                        @if($audit->pointTransactions->count())
                            <span class="ad-card-header-pill">{{ $audit->pointTransactions->count() }} records</span>
                        @endif
                    </div>

                    @if($audit->pointTransactions->count())
                        @php
                            $totalCredit = $audit->pointTransactions->where('type', 'credit')->sum('points');
                            $totalDebit = $audit->pointTransactions->where('type', 'debit')->sum('points');
                            $maxPts = $audit->pointTransactions->max('points') ?: 1;
                        @endphp
                        <div class="ad-tx-summary-band">
                            <div class="ad-tx-summary-item">
                                <div class="ad-tx-summary-val" style="color:#059669;">+{{ $totalCredit }}</div>
                                <div class="ad-tx-summary-lbl">Credits</div>
                            </div>
                            <div class="ad-tx-summary-item">
                                <div class="ad-tx-summary-val" style="color:#e11d48;">-{{ $totalDebit }}</div>
                                <div class="ad-tx-summary-lbl">Debits</div>
                            </div>
                            <div class="ad-tx-summary-item">
                                <div class="ad-tx-summary-val"
                                    style="color:{{ ($totalCredit - $totalDebit) >= 0 ? '#059669' : '#e11d48' }};">
                                    {{ ($totalCredit - $totalDebit) >= 0 ? '+' : '' }}{{ $totalCredit - $totalDebit }}</div>
                                <div class="ad-tx-summary-lbl">Net</div>
                            </div>
                        </div>
                        <div class="ad-tx-wrap">
                            <table class="ad-tx-table">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($audit->pointTransactions as $tx)
                                        <tr>
                                            <td><span class="ad-cat-badge">{{ ucfirst($tx->category) }}</span></td>
                                            <td>
                                                <span
                                                    class="ad-type-badge {{ $tx->type === 'credit' ? 'ad-type-credit' : 'ad-type-debit' }}">
                                                    <i class="fa-solid {{ $tx->type === 'credit' ? 'fa-arrow-up' : 'fa-arrow-down' }}"
                                                        style="font-size:.55rem;"></i>
                                                    {{ ucfirst($tx->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="ad-pts-cell">
                                                    <span class="ad-pts-bar-track">
                                                        <span class="ad-pts-bar-fill"
                                                            style="width:{{ min(100, ($tx->points / $maxPts) * 100) }}%;background:{{ $tx->type === 'credit' ? '#10b981' : '#e11d48' }};"></span>
                                                    </span>
                                                    <span class="{{ $tx->type === 'credit' ? 'ad-pts-pos' : 'ad-pts-neg' }}">
                                                        {{ $tx->type === 'credit' ? '+' : '−' }}{{ $tx->points }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="ad-empty">
                            <i class="fa-solid fa-coins"></i>
                            <p>No transactions recorded for this audit</p>
                        </div>
                    @endif
                </div>

            </div>

            {{-- ───── RIGHT COLUMN ───── --}}
            <div class="ad-col-right">

                {{-- Goal Tracking & Trend --}}
                <div class="ad-card">
                    <div class="ad-card-header">
                        <div class="ad-card-icon" style="background:#eef6ff;color:#2563eb;"><i
                                class="fa-solid fa-gauge-high"></i></div>
                        <span class="ad-card-title">Goal Tracking &amp; Trend</span>
                    </div>
                    <div class="ad-card-body">



                        <div class="ad-gauge-wrap">
                            <svg viewBox="0 0 200 110">
                                <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#eef1f8" stroke-width="12"
                                    stroke-linecap="round" pathLength="100" />
                                <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="{{ $gaugeColor }}"
                                    stroke-width="12" stroke-linecap="round" stroke-dasharray="{{ $gaugeMag }} 100"
                                    pathLength="100" />
                            </svg>
                            <div class="ad-gauge-score">
                                <div class="ad-gauge-score-val" style="color:{{ $gaugeColor }};">
                                    {{ $finalScore >= 0 ? '+' : '' }}{{ $finalScore }}</div>
                                <div class="ad-gauge-score-lbl">Score</div>
                            </div>
                        </div>
                        <div class="ad-gauge-minmax"><span>0</span><span>100</span></div>

                        <div class="ad-goal-msg {{ $isReviewZone ? 'ad-goal-msg-warn' : 'ad-goal-msg-ok' }}">
                            @if($isReviewZone)
                                {{ $tierLabel }} — this executive must meet performance requirements to exit the KPI review
                                tier.
                            @else
                                {{ $tierLabel ?? 'On track' }} — performance is currently within the expected range.
                            @endif
                        </div>

                        <!-- <div class="ad-goal-tiles">
                            <div class="ad-goal-tile">
                                <div class="ad-goal-tile-val" style="{{ $audit->executive->monthly_score < 0 ? 'color:#e11d48;' : '' }}">{{ number_format($audit->executive->monthly_score) }}</div>
                                <div class="ad-goal-tile-lbl">Monthly</div>
                            </div>
                            <div class="ad-goal-tile">
                                <div class="ad-goal-tile-val" style="{{ $audit->executive->current_score < 0 ? 'color:#e11d48;' : '' }}">{{ number_format($audit->executive->current_score) }}</div>
                                <div class="ad-goal-tile-lbl">Total</div>
                            </div>
                        </div> -->

                        <div class="ad-goal-badges">
                            @if($audit->kpi_status === 'passed')
                                <span class="ad-kpi-pass"><i class="fa-solid fa-circle-check"></i> KPI Passed</span>
                            @elseif($audit->kpi_status === 'failed')
                                <span class="ad-kpi-fail"><i class="fa-solid fa-circle-xmark"></i> KPI Failed</span>
                            @endif
                            <span class="ad-status-badge">{{ ucfirst($audit->status) }}</span>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>{{-- /ah-shell --}}
@endsection