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
/* ── Header ── */
.ad-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 28px;
}
.ad-eyebrow {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.13em;
    color: #6366f1;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ad-eyebrow::before {
    content: '';
    display: inline-block;
    width: 14px; height: 2px;
    background: #6366f1;
    border-radius: 2px;
}
.ad-page-title {
    font-size: 1.55rem;
    font-weight: 800;
    color: #0d0f1c;
    letter-spacing: -0.04em;
    line-height: 1.15;
    margin: 0 0 4px;
}
.ad-page-sub { font-size: 0.8rem; color: #94a3b8; font-weight: 500; }
.ad-header-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.btn-ad {
    display: inline-flex; align-items: center; gap: 7px;
    height: 38px; padding: 0 16px;
    border-radius: 10px; font-size: 0.8rem; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: all 0.18s ease;
    border: 1.5px solid #e8eaf2; white-space: nowrap;
}
.btn-ad-secondary { background: #fff; color: #4a5568; }
.btn-ad-secondary:hover { background: #f8f9fc; border-color: #c4b5fd; color: #4f46e5; text-decoration: none; transform: translateY(-1px); }
.btn-ad-danger { background: #fff1f2; color: #e11d48; border-color: #fecdd3; }
.btn-ad-danger:hover { background: #e11d48; color: #fff; border-color: #e11d48; transform: translateY(-1px); }

/* ── Card Base ── */
.ad-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(226,232,240,0.7);
    overflow: hidden;
    margin-bottom: 16px;
}
.ad-card:last-child { margin-bottom: 0; }
.ad-card-header {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 18px;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbff;
}
.ad-card-icon {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; flex-shrink: 0;
}
.ad-card-title { font-size: 0.82rem; font-weight: 800; color: #1e1f2e; letter-spacing: -0.01em; }
.ad-card-body { padding: 16px 18px; }

/* ── Score Panel ── */
.ad-score-panel { text-align: center; padding: 22px 18px 16px; }
.ad-score-num {
    font-size: 3rem; font-weight: 900;
    letter-spacing: -0.06em; line-height: 1; margin-bottom: 4px;
}
.ad-score-label {
    font-size: 0.62rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.12em;
    color: #b0b8d1; margin-bottom: 16px;
}
.ad-score-tiles {
    display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-bottom: 14px;
}
.ad-score-tile { border-radius: 10px; padding: 10px 6px 8px; text-align: center; }
.ad-tile-val { font-size: 1.2rem; font-weight: 800; letter-spacing: -0.03em; line-height: 1; margin-bottom: 3px; }
.ad-tile-lbl { font-size: 0.58rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
.ad-tile-pos { background: #ecfdf5; }
.ad-tile-pos .ad-tile-val { color: #059669; }
.ad-tile-pos .ad-tile-lbl { color: #6ee7b7; }
.ad-tile-neg { background: #fff1f2; }
.ad-tile-neg .ad-tile-val { color: #e11d48; }
.ad-tile-neg .ad-tile-lbl { color: #fca5a5; }
.ad-tile-rec { background: #eff6ff; }
.ad-tile-rec .ad-tile-val { color: #2563eb; }
.ad-tile-rec .ad-tile-lbl { color: #93c5fd; }

.ad-kpi-row {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding-top: 14px; border-top: 1px solid #f1f5f9;
}
.ad-kpi-pass {
    display: inline-flex; align-items: center; gap: 6px;
    height: 28px; padding: 0 12px;
    background: #d1fae5; color: #065f46;
    border-radius: 8px; font-size: 0.72rem; font-weight: 700;
}
.ad-kpi-fail {
    display: inline-flex; align-items: center; gap: 6px;
    height: 28px; padding: 0 12px;
    background: #fee2e2; color: #9f1239;
    border-radius: 8px; font-size: 0.72rem; font-weight: 700;
}
.ad-status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    height: 28px; padding: 0 10px;
    border-radius: 8px; font-size: 0.7rem; font-weight: 600;
    background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;
}
.ad-status-badge::before {
    content: ''; width: 5px; height: 5px;
    border-radius: 50%; background: currentColor;
}

/* ── Executive Profile ── */
.ad-exec-profile {
    display: flex; flex-direction: column;
    align-items: center; text-align: center;
    padding: 18px 16px 16px;
}
.ad-exec-avatar {
    width: 50px; height: 50px; border-radius: 13px;
    background: linear-gradient(135deg, #00039f, #a5b4fc);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.95rem; font-weight: 800; color: #fff;
    letter-spacing: 0.03em; margin-bottom: 10px;
    box-shadow: 0 4px 14px rgba(99,102,241,0.25);
}
.ad-exec-name { font-size: 0.9rem; font-weight: 800; color: #1e1f2e; letter-spacing: -0.02em; margin-bottom: 2px; }
.ad-exec-sub { font-size: 0.65rem; color: #b0b8d1; font-weight: 500; font-family: 'SF Mono','Consolas',monospace; margin-bottom: 12px; }
.ad-exec-stats {
    display: grid; grid-template-columns: repeat(3,1fr);
    gap: 6px; width: 100%; padding-top: 12px; border-top: 1px solid #f1f5f9;
}
.ad-exec-stat { padding: 7px 4px; background: #fafbff; border-radius: 8px; border: 1px solid #f0f2fa; }
.ad-exec-stat .val { font-size: 0.95rem; font-weight: 800; color: #3730a3; letter-spacing: -0.03em; line-height: 1; margin-bottom: 2px; }
.ad-exec-stat .lbl { font-size: 0.56rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #b0b8d1; }

/* ── Remarks Box (left col) ── */
.ad-remarks-box {
    background: #fafbff; border: 1px solid #f0f2fa;
    border-radius: 10px; padding: 13px 15px;
    font-size: 0.8rem; color: #4a5568; line-height: 1.65;
    margin-bottom: 10px;
}
.btn-ad-download {
    display: inline-flex; align-items: center; gap: 7px;
    height: 34px; padding: 0 14px;
    background: #fff; border: 1.5px solid #e8eaf2;
    border-radius: 9px; color: #4a5568;
    font-size: 0.76rem; font-weight: 600;
    text-decoration: none; transition: all 0.18s;
}
.btn-ad-download:hover { background: #f5f3ff; border-color: #c4b5fd; color: #4f46e5; text-decoration: none; }

/* ── Activity Metrics — Premium ── */
.ad-metrics-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; }
.ad-metric-tile {
    border-radius: 13px; padding: 18px 12px 14px;
    text-align: center; position: relative; overflow: hidden;
    border: 1px solid transparent;
    transition: transform 0.2s, box-shadow 0.2s;
}
.ad-metric-tile:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,0,0,.07); }
.ad-metric-tile::after {
    content: ''; position: absolute;
    bottom: -20px; right: -20px;
    width: 70px; height: 70px; border-radius: 50%;
    opacity: 0.12; pointer-events: none;
}
.ad-metric-calls   { background: #eef2ff; border-color: #e0e7ff; }
.ad-metric-meetings { background: #ecfdf5; border-color: #a7f3d0; }
.ad-metric-attended { background: #fffbeb; border-color: #fde68a; }

.ad-metric-icon-wrap {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px; font-size: 0.85rem;
}
.ad-metric-calls   .ad-metric-icon-wrap { background: #fff; color: #6366f1; box-shadow: 0 2px 8px rgba(99,102,241,0.18); }
.ad-metric-meetings .ad-metric-icon-wrap { background: #fff; color: #10b981; box-shadow: 0 2px 8px rgba(16,185,129,0.18); }
.ad-metric-attended .ad-metric-icon-wrap { background: #fff; color: #f59e0b; box-shadow: 0 2px 8px rgba(245,158,11,0.18); }

.ad-metric-val { font-size: 1.8rem; font-weight: 900; letter-spacing: -0.05em; line-height: 1; margin-bottom: 4px; }
.ad-metric-calls    .ad-metric-val { color: #3730a3; }
.ad-metric-meetings .ad-metric-val { color: #059669; }
.ad-metric-attended .ad-metric-val { color: #d97706; }
.ad-metric-lbl { font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
.ad-metric-calls    .ad-metric-lbl { color: #a5b4fc; }
.ad-metric-meetings .ad-metric-lbl { color: #6ee7b7; }
.ad-metric-attended .ad-metric-lbl { color: #fcd34d; }

/* ── Compliance Flags ── */
.ad-flags-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 8px; }
.ad-flag {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 13px; border-radius: 10px;
    font-size: 0.78rem; font-weight: 500; border: 1px solid;
    transition: transform 0.15s;
}
.ad-flag-pass { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; font-weight: 600; }
.ad-flag-fail { background: #fafbff; border-color: #f0f2fa; color: #94a3b8; }
.ad-flag-icon {
    width: 26px; height: 26px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; flex-shrink: 0;
}
.ad-flag-pass .ad-flag-icon { background: #d1fae5; color: #059669; }
.ad-flag-fail .ad-flag-icon { background: #f1f5f9; color: #cbd5e1; }

/* ── Point Transactions Table ── */
.ad-tx-wrap { overflow-x: auto; }
.ad-tx-table { width: 100%; border-collapse: collapse; font-size: 0.79rem; }
.ad-tx-table thead tr { background: #fafbff; border-bottom: 1px solid #f0f2fa; }
.ad-tx-table thead th {
    padding: 10px 16px;
    font-size: 0.6rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.1em;
    color: #b0b8d1; white-space: nowrap;
}
.ad-tx-table thead th:first-child { padding-left: 20px; }
.ad-tx-table thead th:last-child  { padding-right: 20px; text-align: right; }
.ad-tx-table tbody tr { border-bottom: 1px solid #f7f8fc; transition: background 0.12s; }
.ad-tx-table tbody tr:last-child { border-bottom: none; }
.ad-tx-table tbody tr:hover { background: #fafaff; }
.ad-tx-table tbody td { padding: 11px 16px; vertical-align: middle; color: #374151; }
.ad-tx-table tbody td:first-child { padding-left: 20px; }
.ad-tx-table tbody td:last-child  { padding-right: 20px; text-align: right; }

.ad-tx-rule { font-size: 0.79rem; font-weight: 600; color: #1e1f2e; }
.ad-tx-rule-sub { font-size: 0.65rem; color: #b0b8d1; font-weight: 400; margin-top: 1px; }

.ad-cat-badge {
    display: inline-flex; align-items: center;
    height: 22px; padding: 0 9px;
    background: #f8f9fc; border: 1px solid #edf0f7;
    border-radius: 6px; font-size: 0.63rem;
    font-weight: 700; color: #64748b; text-transform: capitalize;
}
.ad-type-badge {
    display: inline-flex; align-items: center; gap: 4px;
    height: 22px; padding: 0 9px;
    border-radius: 6px; font-size: 0.63rem; font-weight: 700;
}
.ad-type-credit { background: #ecfdf5; color: #059669; }
.ad-type-debit  { background: #fff1f2; color: #e11d48; }

.ad-pts-pos {
    display: inline-flex; align-items: center;
    font-size: 0.85rem; font-weight: 800;
    color: #059669;
}
.ad-pts-neg {
    display: inline-flex; align-items: center;
    font-size: 0.85rem; font-weight: 800;
    color: #e11d48;
}

/* tx summary footer */
.ad-tx-footer {
    display: flex; align-items: center; justify-content: flex-end;
    gap: 16px; padding: 10px 20px;
    border-top: 1px solid #f0f2fa;
    background: #fafbff;
}
.ad-tx-footer-item { display: flex; align-items: center; gap: 6px; font-size: 0.72rem; color: #94a3b8; }
.ad-tx-footer-item strong { font-size: 0.82rem; font-weight: 800; }
.ad-tx-footer-item.pos strong { color: #059669; }
.ad-tx-footer-item.neg strong { color: #e11d48; }

/* ── Empty State ── */
.ad-empty { text-align: center; padding: 36px 20px; color: #b0b8d1; }
.ad-empty i { font-size: 1.4rem; margin-bottom: 8px; display: block; }
.ad-empty p { font-size: 0.78rem; margin: 0; }
</style>
@endpush

@section('content')
<div class="ah-shell">

{{-- PAGE HEADER --}}
<div class="ad-header">
    <div>
        <h1 class="ad-page-title"><i class="fa-solid fa-file-lines"></i>&nbsp;&nbsp;{{ $audit->executive->name }}</h1>
        <p class="ad-page-sub">
            {{ $audit->audit_date->format('l, d F Y') }}&nbsp;·&nbsp;{{ ucfirst($audit->audit_type) }} Strategy
        </p>
    </div>
    <div class="ad-header-actions">
        <!-- <a href="{{ route('daily_audit.index') }}" class="btn-ad btn-ad-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a> -->
        <a href="{{ route('executives.show', $audit->executive) }}" class="btn-ad btn-ad-secondary">
            <i class="fa-solid fa-user"></i> View Executive
        </a>
        @can('delete', $audit)
        <form id="delForm" action="{{ route('daily_audit.destroy', $audit) }}" method="POST" style="display:none;">
            @csrf @method('DELETE')
        </form>
        <button type="button" class="btn-ad btn-ad-danger"
                data-confirm-delete="{{ $audit->executive->name }}'s audit on {{ $audit->audit_date->format('d M Y') }}"
                data-form-id="delForm">
            <i class="fa-solid fa-trash"></i> Delete
        </button>
        @endcan
    </div>
</div>

<div class="row g-3">

    {{-- ═══ LEFT COLUMN ═══ --}}
    <div class="col-xl-4">


         {{-- Executive Profile --}}
        <div class="ad-card">
            <div class="ad-card-header">
                <div class="ad-card-icon" style="background:#eef2ff;color:#6366f1;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <span class="ad-card-title">Executive</span>
            </div>
            <div class="ad-exec-profile">
                <div class="ad-exec-avatar">
                    {{ strtoupper(substr($audit->executive->name, 0, 2)) }}
                </div>
                <div class="ad-exec-name">{{ $audit->executive->name }}</div>
                <div class="ad-exec-sub">
                    {{ $audit->executive->employee_id }}@if($audit->executive->zone) · {{ $audit->executive->zone->name }}@endif
                </div>
                <div class="ad-exec-stats">
                    <div class="ad-exec-stat">
                        <div class="val">{{ number_format($audit->executive->current_score) }}</div>
                        <div class="lbl">Total Score</div>
                    </div>
                    <div class="ad-exec-stat">
                        <div class="val">{{ $audit->executive->tier_label }}</div>
                        <div class="lbl">Tier</div>
                    </div>
                    <div class="ad-exec-stat">
                        <div class="val">{{ number_format($audit->executive->monthly_score) }}</div>
                        <div class="lbl">Monthly</div>
                    </div>
                </div>
            </div>
        </div>

        
        {{-- Score Summary --}}
        <div class="ad-card">
            <div class="ad-score-panel">
                <div class="ad-score-num" style="color:{{ $audit->final_score >= 0 ? '#3730a3' : '#e11d48' }};">
                    {{ $audit->final_score >= 0 ? '+' : '' }}{{ $audit->final_score }}
                </div>
                <div class="ad-score-label">Final Score</div>
                <div class="ad-score-tiles">
                    <div class="ad-score-tile ad-tile-pos">
                        <div class="ad-tile-val">+{{ $audit->positive_points }}</div>
                        <div class="ad-tile-lbl">Positive</div>
                    </div>
                    <div class="ad-score-tile ad-tile-neg">
                        <div class="ad-tile-val">-{{ $audit->negative_points }}</div>
                        <div class="ad-tile-lbl">Negative</div>
                    </div>
                    <div class="ad-score-tile ad-tile-rec">
                        <div class="ad-tile-val">+{{ $audit->recovery_points }}</div>
                        <div class="ad-tile-lbl">Recovery</div>
                    </div>
                </div>
                <div class="ad-kpi-row">
                    @if($audit->kpi_status === 'passed')
                        <span class="ad-kpi-pass"><i class="fa-solid fa-circle-check"></i> KPI Passed</span>
                    @elseif($audit->kpi_status === 'failed')
                        <span class="ad-kpi-fail"><i class="fa-solid fa-circle-xmark"></i> KPI Failed</span>
                    @endif
                    <span class="ad-status-badge">{{ ucfirst($audit->status) }}</span>
                </div>
            </div>
        </div>

       

        {{-- Remarks & Evidence (moved to left) --}}
        @if($audit->remarks || $audit->evidence_path)
        <div class="ad-card">
            <div class="ad-card-header">
                <div class="ad-card-icon" style="background:#f5f3ff;color:#7c3aed;">
                    <i class="fa-solid fa-paperclip"></i>
                </div>
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

    {{-- ═══ RIGHT COLUMN ═══ --}}
    <div class="col-xl-8">

        {{-- Activity Metrics — Premium --}}
        <div class="ad-card">
            <div class="ad-card-header">
                <div class="ad-card-icon" style="background:#fffbeb;color:#f59e0b;">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
                <span class="ad-card-title">Activity Metrics</span>
            </div>
            <div class="ad-card-body">
                <div class="ad-metrics-grid">
                    <div class="ad-metric-tile ad-metric-calls">
                        <div class="ad-metric-icon-wrap"><i class="fa-solid fa-phone"></i></div>
                        <div class="ad-metric-val">{{ $audit->connected_calls }}</div>
                        <div class="ad-metric-lbl">Connected Calls</div>
                    </div>
                    <div class="ad-metric-tile ad-metric-meetings">
                        <div class="ad-metric-icon-wrap"><i class="fa-solid fa-calendar-check"></i></div>
                        <div class="ad-metric-val">{{ $audit->confirmed_meetings }}</div>
                        <div class="ad-metric-lbl">Confirmed Meetings</div>
                    </div>
                    <div class="ad-metric-tile ad-metric-attended">
                        <div class="ad-metric-icon-wrap"><i class="fa-solid fa-handshake"></i></div>
                        <div class="ad-metric-val">{{ $audit->meetings_attended }}</div>
                        <div class="ad-metric-lbl">Meetings Attended</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Compliance Flags --}}
        <div class="ad-card">
            <div class="ad-card-header">
                <div class="ad-card-icon" style="background:#ecfdf5;color:#10b981;">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <span class="ad-card-title">Compliance Flags</span>
            </div>
            <div class="ad-card-body">
                @php
                $flags = [
                    ['label' => 'CRM Follow-up',              'val' => $audit->crm_followup],
                    ['label' => 'Correct CRM Disposition',    'val' => $audit->crm_disposition_correct],
                    ['label' => 'First Contact ≤ 45 min',     'val' => $audit->first_contact_within_45min],
                    ['label' => '100% Follow-up',             'val' => $audit->all_leads_followed_up],
                    ['label' => 'Warm Lead Converted',        'val' => $audit->warm_lead_converted],
                    ['label' => 'Cold Lead Reactivated',      'val' => $audit->cold_lead_reactivated],
                ];
                $passCount = collect($flags)->where('val', true)->count();
                @endphp

                {{-- pass/fail summary strip --}}
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <div style="flex:1;height:5px;border-radius:10px;background:#f1f5f9;overflow:hidden;">
                        <div style="height:100%;border-radius:10px;background:linear-gradient(90deg,#10b981,#34d399);width:{{ ($passCount/count($flags))*100 }}%;transition:width .4s;"></div>
                    </div>
                    <span style="font-size:0.72rem;font-weight:700;color:#10b981;white-space:nowrap;">{{ $passCount }}/{{ count($flags) }} passed</span>
                </div>

                <div class="ad-flags-grid">
                    @foreach($flags as $f)
                    <div class="ad-flag {{ $f['val'] ? 'ad-flag-pass' : 'ad-flag-fail' }}">
                        <div class="ad-flag-icon">
                            <i class="fa-solid {{ $f['val'] ? 'fa-check' : 'fa-xmark' }}"></i>
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
                <div class="ad-card-icon" style="background:#fffbeb;color:#f59e0b;">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <span class="ad-card-title">Point Transactions</span>
                @if($audit->pointTransactions->count())
                <span style="margin-left:auto;display:inline-flex;align-items:center;height:22px;padding:0 10px;background:#f5f3ff;border:1px solid #e0e7ff;border-radius:100px;font-size:0.65rem;font-weight:800;color:#4f46e5;">
                    {{ $audit->pointTransactions->count() }} records
                </span>
                @endif
            </div>

            @if($audit->pointTransactions->count())
            @php
                $totalCredit = $audit->pointTransactions->where('type','credit')->sum('points');
                $totalDebit  = $audit->pointTransactions->where('type','debit')->sum('points');
            @endphp
            <div class="ad-tx-wrap">
                <table class="ad-tx-table">
                    <thead>
                        <tr>
                            <th>Rule / Description</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audit->pointTransactions as $tx)
                        <tr>
                            <td>
                                <div class="ad-tx-rule">{{ $tx->description }}</div>
                            </td>
                            <td><span class="ad-cat-badge">{{ ucfirst($tx->category) }}</span></td>
                            <td>
                                <span class="ad-type-badge {{ $tx->type === 'credit' ? 'ad-type-credit' : 'ad-type-debit' }}">
                                    <i class="fa-solid {{ $tx->type === 'credit' ? 'fa-arrow-up' : 'fa-arrow-down' }}" style="font-size:.55rem;"></i>
                                    {{ ucfirst($tx->type) }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $tx->type === 'credit' ? 'ad-pts-pos' : 'ad-pts-neg' }}">
                                    {{ $tx->type === 'credit' ? '+' : '−' }}{{ $tx->points }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="ad-tx-footer">
                <div class="ad-tx-footer-item pos">
                    <i class="fa-solid fa-circle-plus" style="font-size:.7rem;color:#10b981;"></i>
                    Credits: <strong>+{{ $totalCredit }}</strong>
                </div>
                <div class="ad-tx-footer-item neg">
                    <i class="fa-solid fa-circle-minus" style="font-size:.7rem;color:#f43f5e;"></i>
                    Debits: <strong>−{{ $totalDebit }}</strong>
                </div>
                <div class="ad-tx-footer-item" style="border-left:1px solid #f0f2fa;padding-left:16px;">
                    Net: <strong style="color:{{ ($totalCredit-$totalDebit)>=0?'#059669':'#e11d48' }};font-size:.85rem;">
                        {{ ($totalCredit-$totalDebit)>=0?'+':'' }}{{ $totalCredit-$totalDebit }}
                    </strong>
                </div>
            </div>
            @else
            <div class="ad-empty">
                <i class="fa-solid fa-coins"></i>
                <p>No transactions recorded for this audit</p>
            </div>
            @endif
        </div>

    </div>
</div>

</div>{{-- /ah-shell --}}
@endsection