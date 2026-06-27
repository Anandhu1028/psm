@extends('layouts.app')
@section('title', 'Executives')
@section('breadcrumb')
<ol class="breadcrumb"><li class="breadcrumb-item active">Executives</li></ol>
@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   EXECUTIVES INDEX
   ═══════════════════════════════════════════════════════════ */

/* ── Page Header ── */
.exec-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 28px;
}
.exec-page-title {
    font-size: 1.55rem;
    font-weight: 900;
    color: #0d0f1c;
    letter-spacing: -0.03em;
    margin: 0 0 4px;
    line-height: 1.2;
}
.exec-page-subtitle { font-size: .82rem; color: #64748b; margin: 0; }
.exec-page-subtitle strong { color: #6366f1; font-weight: 700; }

/* ── Top-right toolbar ── */
.exec-toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.btn-exec-chip {
    display: inline-flex; align-items: center; gap: 7px;
    height: 40px; padding: 0 16px;
    background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: .82rem; font-weight: 600; color: #374151;
    cursor: pointer; transition: all .2s; text-decoration: none;
}
.btn-exec-chip:hover { border-color: #6366f1; color: #6366f1; background: #fafaff; }
.btn-exec-chip i { font-size: .78rem; color: #6366f1; }

.btn-exec-primary {
    display: inline-flex; align-items: center; gap: 8px;
    height: 40px; padding: 0 20px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #00039f, #a5b4fc);
    color: #fff; font-size: .84rem; font-weight: 700;
    text-decoration: none; transition: all .2s;
    box-shadow: 0 4px 14px -2px rgba(99,102,241,.40);
}
.btn-exec-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 22px -4px rgba(99,102,241,.55);
    color: #fff; text-decoration: none;
}

/* ── Stat Cards ── */
.exec-stats-row {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 16px; margin-bottom: 26px;
}
@media (max-width: 992px) { .exec-stats-row { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px)  { .exec-stats-row { grid-template-columns: 1fr; } }

.exec-stat-card {
    background: #fff; border-radius: 18px; border: 1px solid #eef0f8;
    padding: 22px 22px 18px; position: relative; overflow: hidden;
    box-shadow: 0 1px 6px rgba(15,23,42,.04); transition: box-shadow .2s, transform .2s;
}
.exec-stat-card:hover { box-shadow: 0 6px 24px rgba(15,23,42,.08); transform: translateY(-2px); }
.exec-stat-label { font-size: .67rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: #94a3b8; margin-bottom: 10px; }
.exec-stat-value { font-size: 2.1rem; font-weight: 900; letter-spacing: -0.04em; line-height: 1; margin-bottom: 6px; }
.exec-stat-value.blue  { color: #1e1f2e; }
.exec-stat-value.green { color: #10b981; }
.exec-stat-value.red   { color: #ef4444; }
.exec-stat-value.gold  { color: #f59e0b; }
.exec-stat-note { font-size: .72rem; color: #94a3b8; display: flex; align-items: center; gap: 6px; }
.exec-stat-icon {
    position: absolute; top: 18px; right: 18px;
    width: 38px; height: 38px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
}
.exec-stat-icon.blue  { background: rgba(99,102,241,.10); color: #6366f1; }
.exec-stat-icon.green { background: rgba(16,185,129,.10);  color: #10b981; }
.exec-stat-icon.red   { background: rgba(239,68,68,.10);   color: #ef4444; }
.exec-stat-icon.gold  { background: rgba(245,158,11,.10);  color: #f59e0b; }

/* ── Table Card ── */
.exec-table-card {
    background: #fff; border-radius: 20px; border: 1px solid #eef0f8;
    box-shadow: 0 1px 8px rgba(15,23,42,.04); overflow: hidden;
}
.exec-table-card-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; padding: 18px 22px 16px;
    border-bottom: 1px solid #f1f3fb;
}
.exec-table-title { font-size: .92rem; font-weight: 800; color: #0d0f1c; display: flex; align-items: center; gap: 10px; }
.exec-count-pill {
    display: inline-flex; align-items: center;
    padding: 3px 11px; border-radius: 100px;
    background: #f1f0ff; color: #6366f1; font-size: .72rem; font-weight: 800;
}
.exec-search-box {
    position: relative; display: flex; align-items: center;
    width: 240px; height: 38px; background: #f8f9fc;
    border: 1.5px solid #edf0f7; border-radius: 10px;
    padding: 0 8px 0 12px; transition: all .2s;
}
.exec-search-box:focus-within { background: #fff; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.10); }
.exec-search-box i { font-size: .75rem; color: #b0b8d1; margin-right: 8px; flex-shrink: 0; }
.exec-search-box input { border: none; outline: none; flex: 1; background: transparent; font-size: .82rem; color: #1e1f2e; }
.exec-search-box input::placeholder { color: #b0b8d1; }
.exec-search-clear {
    display: flex; align-items: center; justify-content: center;
    width: 20px; height: 20px; border-radius: 50%; background: #edf0f7;
    color: #94a3b8; font-size: .58rem; flex-shrink: 0; text-decoration: none; transition: all .15s;
}
.exec-search-clear:hover { background: #e2e8f0; color: #475569; }

/* ── Table ── */
.exec-table { width: 100%; border-collapse: collapse; }
.exec-table thead th {
    font-size: .65rem; text-transform: uppercase; letter-spacing: .09em;
    color: #a0a8c0; font-weight: 800; padding: 11px 16px;
    background: #fbfbfe; border-bottom: 1px solid #f0f2fa; white-space: nowrap;
}
.exec-table thead th:first-child { padding-left: 22px; }
.exec-table thead th:last-child  { padding-right: 22px; }
.exec-table tbody tr { transition: background .12s; }
.exec-table tbody tr:hover { background: rgba(99,102,241,.025); }
.exec-table tbody td {
    padding: 13px 16px; border-bottom: 1px solid #f5f6fc;
    vertical-align: middle; font-size: .82rem; color: #374151;
}
.exec-table tbody td:first-child { padding-left: 22px; }
.exec-table tbody td:last-child  { padding-right: 22px; }
.exec-table tbody tr:last-child td { border-bottom: none; }

.exec-row-num { font-size: .7rem; color: #cbd5e1; font-weight: 700; }
.exec-avatar {
    width: 36px; height: 36px; border-radius: 11px;
    background: linear-gradient(135deg, #00039f, #a5b4fc);
    display: flex; align-items: center; justify-content: center;
    font-size: .68rem; font-weight: 900; color: #fff; flex-shrink: 0;
    box-shadow: 0 4px 12px -4px rgba(99,102,241,.50); letter-spacing: -0.02em;
}
.exec-name-link {
    font-weight: 700; color: #1e1f2e; text-decoration: none;
    font-size: .84rem; line-height: 1.2; display: block; transition: color .15s;
}
.exec-name-link:hover { color: #6366f1; }
.exec-emp-id { font-size: .67rem; color: #94a3b8; margin-top: 1px; }

.exec-net-score {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 54px; padding: 4px 10px; border-radius: 8px;
    font-size: .78rem; font-weight: 900;
}
.exec-net-positive { background: rgba(16,185,129,.10); color: #10b981; border: 1px solid rgba(16,185,129,.18); }
.exec-net-negative { background: rgba(239,68,68,.08);  color: #ef4444; border: 1px solid rgba(239,68,68,.15); }

.kpi-pass {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 11px; border-radius: 8px;
    background: rgba(16,185,129,.10); color: #10b981;
    font-size: .72rem; font-weight: 800; border: 1px solid rgba(16,185,129,.18);
}
.kpi-fail {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 11px; border-radius: 8px;
    background: rgba(239,68,68,.08); color: #ef4444;
    font-size: .72rem; font-weight: 800; border: 1px solid rgba(239,68,68,.15);
}
.exec-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 8px; border: none;
    font-size: .72rem; cursor: pointer; transition: all .15s; text-decoration: none;
}
.exec-action-btn:hover { transform: translateY(-1px); filter: brightness(.93); }
.exec-action-view   { background: rgba(99,102,241,.10); color: #6366f1; }
.exec-action-edit   { background: rgba(245,158,11,.12); color: #f59e0b; }
.exec-action-delete { background: rgba(239,68,68,.10);  color: #ef4444; }

.exec-empty { text-align: center; padding: 60px 20px; color: #94a3b8; }
.exec-empty i { font-size: 2.5rem; margin-bottom: 14px; opacity: .3; display: block; }
.exec-empty p { font-size: .88rem; margin: 0; }

/* ── Filter Modal ── */
.pms-modal-content {
    border-radius: 20px; border: none; overflow: hidden;
    box-shadow: 0 28px 64px -12px rgba(15,23,42,.28);
}
.pms-modal-content .modal-header {
    border-bottom: 1px solid #f0f2fa; padding: 20px 24px;
    background: linear-gradient(135deg, rgba(99,102,241,.07), rgba(124,58,237,.03));
}
.pms-modal-content .modal-title {
    font-weight: 800; font-size: 1rem; color: #0d0f1c;
    letter-spacing: -0.02em; display: flex; align-items: center; gap: 9px;
}
.pms-modal-content .modal-title i { color: #6366f1; }
.pms-modal-content .modal-body { padding: 22px 24px 6px; }
.pms-modal-content .modal-body .form-label {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: #64748b; margin-bottom: 7px;
}
.pms-modal-content .modal-body .form-select {
    height: 43px; border-radius: 12px;
    border: 1.5px solid #edf0f7; background: #fafbff; font-size: .85rem; transition: all .2s;
}
.pms-modal-content .modal-body .form-select:focus {
    border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99,102,241,.10); background: #fff;
}
.pms-modal-content .modal-footer { border-top: 1px solid #f0f2fa; padding: 16px 24px; gap: 10px; }

.btn-pms-ghost {
    display: inline-flex; align-items: center; gap: 7px;
    height: 42px; padding: 0 18px;
    background: #fff; border: 1.5px solid #e8eaf2; border-radius: 12px;
    color: #64748b; font-weight: 600; font-size: .84rem; text-decoration: none; transition: all .2s;
}
.btn-pms-ghost:hover { background: #f8f9fc; border-color: #cbd5e1; color: #374151; text-decoration: none; }
.btn-pms-apply {
    display: inline-flex; align-items: center; gap: 8px;
    height: 42px; padding: 0 22px; border: none; border-radius: 12px;
   background: linear-gradient(135deg, #00039f, #a5b4fc); color: #fff;
    font-weight: 700; font-size: .85rem;
    box-shadow: 0 6px 18px -4px rgba(99,102,241,.40); transition: all .2s;
}
.btn-pms-apply:hover { transform: translateY(-1px); box-shadow: 0 10px 26px -4px rgba(99,102,241,.5); color: #fff; }

.exec-filter-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px; padding: 0 5px; border-radius: 100px;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    color: #fff; font-size: .62rem; font-weight: 800;
}

/* ── Pagination ── */
.exec-pagination-bar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 14px; margin-top: 20px; padding: 16px 22px;
    background: #fff; border-radius: 16px; border: 1px solid #eef0f8;
    box-shadow: 0 1px 6px rgba(15,23,42,.04);
}
.exec-pagination-info { font-size: .78rem; color: #94a3b8; }
.exec-pagination-info strong { color: #374151; font-weight: 700; }
.exec-pagination-controls { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }

/* ── Modal fields ── */
.exec-modal-avatar {
    width: 72px; height: 72px; border-radius: 14px;
    background: linear-gradient(135deg, #00039f, #a5b4fc);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; color: #fff; flex-shrink: 0; overflow: hidden;
    box-shadow: 0 6px 18px -4px rgba(99,102,241,.45); cursor: pointer; transition: opacity .2s;
}
.exec-modal-avatar:hover { opacity: .85; }
.exec-photo-label {
    display: flex; align-items: center; justify-content: center; gap: 5px;
    font-size: .68rem; font-weight: 700; color: #6366f1; cursor: pointer; text-align: center;
    background: rgba(99,102,241,.08); border-radius: 8px; padding: 4px 10px; transition: background .15s;
}
.exec-photo-label:hover { background: rgba(99,102,241,.15); }
.exec-modal-section-label {
    font-size: .68rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .09em; color: #6366f1;
    display: flex; align-items: center; gap: 7px;
    margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #f0f2fa;
}
.exec-modal-label {
    display: block; font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: #64748b; margin-bottom: 6px;
}
.exec-modal-input {
    display: block; width: 100%; height: 42px; padding: 0 14px;
    background: #f8f9fc; border: 1.5px solid #edf0f7; border-radius: 11px;
    font-size: .85rem; color: #1e1f2e; transition: all .2s; outline: none;
}
textarea.exec-modal-input { height: auto; padding: 10px 14px; }
.exec-modal-input:focus { background: #fff; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.10); }
.exec-modal-input::placeholder { color: #b0b8d1; }
.exec-modal-select {
    display: block; width: 100%; height: 42px; padding: 0 14px;
    background: #f8f9fc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%236366f1' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") no-repeat right 12px center;
    border: 1.5px solid #edf0f7; border-radius: 11px;
    font-size: .85rem; color: #1e1f2e; appearance: none; transition: all .2s; outline: none; cursor: pointer;
}
.exec-modal-select:focus { background-color: #fff; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.10); }
.exec-field-error { font-size: .7rem; color: #ef4444; margin-top: 4px; font-weight: 600; }

/* ── Date Input with Calendar Icon ── */
.exec-date-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.exec-date-wrap .exec-modal-input {
    padding-right: 42px; /* leave room for the icon button */
}
.exec-date-wrap .exec-cal-btn {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    border-left: 1.5px solid #edf0f7;
    border-radius: 0 11px 11px 0;
    color: #6366f1;
    font-size: .78rem;
    cursor: pointer;
    transition: background .15s, color .15s;
    z-index: 2;
}
.exec-date-wrap .exec-cal-btn:hover {
    background: rgba(99,102,241,.08);
    color: #4f46e5;
}
/* When the wrapping input is focused, style the icon border too */
.exec-date-wrap:focus-within .exec-cal-btn {
    border-left-color: #6366f1;
    background: rgba(99,102,241,.05);
}
/* Flatpickr alt-input override inside date-wrap */
.exec-date-wrap .flatpickr-input[readonly] {
    display: none !important; /* hide the hidden real input */
}

/* ══════════════════════════════════════════════════════════
   FLATPICKR — compact, clean, themed to match design
   ══════════════════════════════════════════════════════════ */
.flatpickr-input { cursor: pointer !important; }

/* The visible altInput should look like exec-modal-input */
.flatpickr-input.form-control,
input.flatpickr-input[readonly] {
    background: #f8f9fc !important;
    border: 1.5px solid #edf0f7 !important;
    border-radius: 11px !important;
    height: 42px !important;
    padding: 0 14px !important;
    font-size: .85rem !important;
    color: #1e1f2e !important;
    width: 100% !important;
    cursor: pointer !important;
    outline: none !important;
    font-family: 'Inter', sans-serif !important;
    display: block !important;
}
input.flatpickr-input[readonly]:focus {
    background: #fff !important;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99,102,241,.10) !important;
}

/* Calendar popup */
.flatpickr-calendar {
    border-radius: 16px !important;
    border: 1.5px solid #e2e8f0 !important;
    box-shadow: 0 12px 36px -6px rgba(15,23,42,.16) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: .78rem !important;
    width: 256px !important;
    padding: 10px 10px 12px !important;
}

/* Header */
.flatpickr-months { padding: 0 0 4px !important; align-items: center !important; }
.flatpickr-months .flatpickr-month { background: transparent !important; height: 30px !important; }
.flatpickr-current-month {
    font-size: .82rem !important; font-weight: 800 !important; color: #0d0f1c !important;
    padding: 0 !important; display: flex !important; align-items: center !important;
    justify-content: center !important; gap: 4px !important; line-height: 30px !important;
}

/* Month dropdown — compact, no giant list */
.flatpickr-current-month .flatpickr-monthDropdown-months {
    font-size: .82rem !important; font-weight: 800 !important; color: #0d0f1c !important;
    background: #f1f0ff !important; border: none !important; border-radius: 8px !important;
    padding: 3px 8px !important; cursor: pointer !important;
    max-width: 96px !important; appearance: auto !important;
}
.flatpickr-current-month .flatpickr-monthDropdown-months:focus {
    outline: none !important; box-shadow: 0 0 0 2px rgba(99,102,241,.25) !important;
}

/* Year input */
.flatpickr-current-month input.cur-year {
    font-size: .82rem !important; font-weight: 800 !important; color: #0d0f1c !important;
    width: 52px !important; padding: 3px 6px !important;
    background: #f1f0ff !important; border-radius: 8px !important; border: none !important;
}

/* Nav arrows */
.flatpickr-prev-month,
.flatpickr-next-month {
    padding: 5px 8px !important; color: #6366f1 !important; top: 8px !important;
}
.flatpickr-prev-month:hover,
.flatpickr-next-month:hover { background: rgba(99,102,241,.08) !important; border-radius: 8px !important; }
.flatpickr-prev-month svg,
.flatpickr-next-month svg { width: 11px !important; height: 11px !important; fill: #6366f1 !important; }

/* Weekday row */
.flatpickr-weekdays { margin-bottom: 2px !important; }
.flatpickr-weekday {
    font-size: .63rem !important; font-weight: 800 !important; color: #94a3b8 !important;
    background: transparent !important; letter-spacing: .04em !important;
}

/* Day cells */
.flatpickr-day {
    border-radius: 9px !important; font-size: .75rem !important; font-weight: 600 !important;
    height: 30px !important; line-height: 30px !important; max-width: 30px !important;
    color: #374151 !important; border: none !important; transition: all .12s !important;
}
.flatpickr-day:hover { background: rgba(99,102,241,.10) !important; color: #6366f1 !important; }
.flatpickr-day.today {
    border: 1.5px solid #a5b4fc !important; color: #6366f1 !important;
    font-weight: 800 !important; background: transparent !important;
}
.flatpickr-day.today:hover { background: rgba(99,102,241,.10) !important; }
.flatpickr-day.selected,
.flatpickr-day.selected:hover {
    background: linear-gradient(135deg, #6366f1, #7c3aed) !important;
    border-color: transparent !important; color: #fff !important; font-weight: 800 !important;
    box-shadow: 0 3px 10px rgba(99,102,241,.35) !important;
}
.flatpickr-day.flatpickr-disabled,
.flatpickr-day.prevMonthDay,
.flatpickr-day.nextMonthDay { color: #cbd5e1 !important; opacity: .45 !important; }

/* Inner containers sizing */
.dayContainer   { min-width: unset !important; max-width: unset !important; width: 100% !important; }
.flatpickr-days { width: 100% !important; }
.flatpickr-innerContainer { width: 100% !important; }

/* ── Modal backdrop blur ── */
.modal-backdrop {
    backdrop-filter: blur(6px) !important;
    -webkit-backdrop-filter: blur(6px) !important;
    background-color: rgba(15,23,42,.45) !important;
    opacity: 1 !important;
}

@media (max-width: 768px) {
    .exec-page-header    { flex-direction: column; }
    .exec-toolbar-right  { width: 100%; justify-content: flex-end; }
    .exec-search-box     { width: 100%; }
    .exec-pagination-bar { flex-direction: column; align-items: center; text-align: center; }
}
</style>
@endpush

@section('content')

@php
    $activeFilterCount = collect([request('company_id'), request('zone_id'), request('status')])
        ->filter(fn($v) => !is_null($v) && $v !== '')->count();
    $totalScore = $executives->getCollection()->sum('current_score');
    $avgScore   = $executives->total()
        ? round($totalScore / max($executives->getCollection()->count(), 1))
        : 0;
@endphp

{{-- ══ PAGE HEADER ══════════════════════════════════════════ --}}
<div class="exec-page-header">
    <div>
        <h1 class="exec-page-title">Executives</h1>
        <p class="exec-page-subtitle">
            Showing <strong>{{ $executives->total() }} executives</strong>
            @if(request('company_id') && isset($companies))
                — <strong>{{ $companies->firstWhere('id', request('company_id'))->name ?? 'filtered' }}</strong>
            @else
                — <strong>all companies</strong>
            @endif
        </p>
    </div>

    <form method="GET" action="{{ route('executives.index') }}" id="execFilterForm">
        <div class="exec-toolbar-right">
            <button type="button" class="btn-exec-chip {{ $activeFilterCount ? 'has-active' : '' }}"
                    data-bs-toggle="modal" data-bs-target="#executiveFilterModal">
                <i class="fa-solid fa-sliders"></i> Filters
                @if($activeFilterCount)
                <span class="exec-filter-badge">{{ $activeFilterCount }}</span>
                @endif
            </button>

            @can('manage_executives')
            <button type="button" class="btn-exec-primary" data-bs-toggle="modal" data-bs-target="#addExecutiveModal">
                <i class="fa-solid fa-plus"></i> Add Executive
            </button>
            @endcan
        </div>

        {{-- ── FILTER MODAL ── --}}
        <div class="modal fade" id="executiveFilterModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content pms-modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid fa-sliders"></i> Filter Executives</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <select name="company_id" class="form-select">
                                <option value="">All Companies</option>
                                @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Zone</label>
                            <select name="zone_id" class="form-select">
                                <option value="">All Zones</option>
                                @foreach($zones as $z)
                                <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                                <option value="probation" {{ request('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                                <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ request()->fullUrlWithQuery(['company_id' => null, 'zone_id' => null, 'status' => null]) }}" class="btn-pms-ghost">
                            <i class="fa-solid fa-rotate-left"></i> Clear All
                        </a>
                        <button type="submit" class="btn-pms-apply">
                            <i class="fa-solid fa-check"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ══ STAT CARDS ══════════════════════════════════════════ --}}
<div class="exec-stats-row">
    <div class="exec-stat-card">
        <div class="exec-stat-icon blue"><i class="fa-solid fa-users"></i></div>
        <div class="exec-stat-label">Total Executives</div>
        <div class="exec-stat-value blue">{{ $executives->total() }}</div>
        <div class="exec-stat-note"><i class="fa-solid fa-circle-info" style="font-size:.6rem;"></i> All field executives</div>
    </div>
    <div class="exec-stat-card">
        <div class="exec-stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="exec-stat-label">Active</div>
        <div class="exec-stat-value green">{{ $executives->getCollection()->where('status','active')->count() }}</div>
        <div class="exec-stat-note"><i class="fa-solid fa-circle" style="font-size:.45rem;color:#10b981;"></i> Currently active</div>
    </div>
    <div class="exec-stat-card">
        <div class="exec-stat-icon gold"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="exec-stat-label">On Probation</div>
        <div class="exec-stat-value gold">{{ $executives->getCollection()->where('status','probation')->count() }}</div>
        <div class="exec-stat-note"><i class="fa-solid fa-circle" style="font-size:.45rem;color:#f59e0b;"></i> Under review</div>
    </div>
    <div class="exec-stat-card">
        <div class="exec-stat-icon red"><i class="fa-solid fa-chart-bar"></i></div>
        <div class="exec-stat-label">Avg Score (Page)</div>
        <div class="exec-stat-value {{ $avgScore >= 0 ? 'green' : 'red' }}">{{ $avgScore >= 0 ? '+' : '' }}{{ number_format($avgScore) }}</div>
        <div class="exec-stat-note"><i class="fa-solid fa-equals" style="font-size:.6rem;"></i> Average net score</div>
    </div>
</div>

{{-- ══ TABLE CARD ══════════════════════════════════════════ --}}
<div class="exec-table-card">

    <div class="exec-table-card-header">
        <div class="exec-table-title">
            Executives
            <span class="exec-count-pill">{{ $executives->total() }} entries</span>
        </div>
        <form method="GET" action="{{ route('executives.index') }}">
            @foreach(request()->except('search') as $key => $val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
            <div class="exec-search-box">
                <i class="fa-solid fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search records…" autocomplete="off">
                @if(request('search'))
                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="exec-search-clear" title="Clear">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="exec-table-scroll">
        <table class="exec-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Executive</th>
                    <th>Company / Zone</th>
                    <th>Status</th>
                    <th class="text-center">Total Score</th>
                    <th class="text-center">Monthly</th>
                    <th class="text-center">Recovery</th>
                    <th class="text-center">Net Score</th>
                    <th class="text-center">Tier / KPI</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($executives as $i => $exec)
                <tr>
                    <td><span class="exec-row-num">#{{ $executives->firstItem() + $i }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="exec-avatar">{{ strtoupper(substr($exec->name, 0, 2)) }}</div>
                            <div>
                                <a href="{{ route('executives.show', $exec) }}" class="exec-name-link">{{ $exec->name }}</a>
                                <div class="exec-emp-id">{{ $exec->employee_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="exec-zone-text">
                            <i class="fa-solid fa-location-dot" style="color:#a5b4fc;font-size:0.75rem;"></i>
                            {{ $exec->zone->name ?? '—' }}
                        </div>
                        <div class="exec-company-text">{{ $exec->company->name ?? '—' }}</div>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $exec->status }}">
                            {{ ucfirst($exec->status) }}
                        </span>
                    </td>
                    <td class="text-center"><strong>{{ number_format($exec->current_score) }}</strong></td>
                    <td class="text-center">{{ number_format($exec->monthly_score) }}</td>
                    <td class="text-center" style="color:#0ea5e9;">+{{ number_format($exec->recovery_score ?? 0) }}</td>
                    @php $net = ($exec->current_score ?? 0) - abs($exec->deduction_score ?? 0); @endphp
                    <td class="text-center">
                        <span class="{{ $net >= 0 ? 'exec-net-positive' : 'exec-net-negative' }} exec-net-score">
                            {{ $net >= 0 ? '+' : '' }}{{ number_format($net) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @php $pass = ($exec->kpi_pass ?? true); @endphp
                        @if($pass)
                        <span class="kpi-pass"><i class="fa-solid fa-check"></i> Pass</span>
                        @else
                        <span class="kpi-fail"><i class="fa-solid fa-xmark"></i> Fail</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('executives.show', $exec) }}"
                               class="exec-action-btn exec-action-view" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @can('manage_executives')
                            <button type="button" class="exec-action-btn exec-action-edit"
                                    data-id="{{ $exec->id }}"
                                    data-name="{{ addslashes($exec->name) }}"
                                    data-employee-id="{{ $exec->employee_id }}"
                                    data-mobile="{{ $exec->mobile ?? '' }}"
                                    data-email="{{ $exec->email ?? '' }}"
                                    data-company-id="{{ $exec->company_id }}"
                                    data-zone-id="{{ $exec->zone_id }}"
                                    data-status="{{ $exec->status }}"
                                    data-date-joined="{{ $exec->date_joined ? $exec->date_joined->format('Y-m-d') : '' }}"
                                    data-probation-end-date="{{ $exec->probation_end_date ? $exec->probation_end_date->format('Y-m-d') : '' }}"
                                    data-notes="{{ addslashes($exec->notes ?? '') }}"
                                    data-photo="{{ $exec->photo ? asset('storage/' . $exec->photo) : '' }}"
                                    onclick="editExecutive(this)"
                                    title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            @endcan
                            @can('manage_executives')
                            <form id="del-exec-{{ $exec->id }}" action="{{ route('executives.destroy', $exec) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button"
                                    class="exec-action-btn exec-action-delete"
                                    data-confirm-delete="{{ $exec->name }}"
                                    data-form-id="del-exec-{{ $exec->id }}"
                                    title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="exec-empty">
                            <i class="fa-solid fa-users"></i>
                            <p>No executives found.
                                @can('manage_executives')
                                <a href="#" data-bs-toggle="modal" data-bs-target="#addExecutiveModal"
                                   style="color:#6366f1;font-weight:700;">Add the first one →</a>
                                @endcan
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($executives->hasPages())
    <div class="exec-pagination-bar">
        <div class="exec-pagination-info">
            Showing <strong>{{ $executives->firstItem() }}</strong>–<strong>{{ $executives->lastItem() }}</strong>
            of <strong>{{ $executives->total() }}</strong> executives
        </div>
        <div class="exec-pagination-controls">
            {{ $executives->links() }}
        </div>
    </div>
    @endif

</div>{{-- /exec-table-card --}}


{{-- ══ ADD EXECUTIVE MODAL ═══════════════════════════════════ --}}
@can('manage_executives')
<div class="modal fade" id="addExecutiveModal" tabindex="-1" aria-labelledby="addExecutiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content pms-modal-content" style="border-radius:22px;">
            <div class="modal-header" style="padding:22px 28px;">
                <h5 class="modal-title" id="addExecutiveModalLabel">
                    <i class="fa-solid fa-user-plus"></i> Add New Executive
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('executives.store') }}" method="POST" enctype="multipart/form-data" id="addExecForm">
                @csrf
                <div class="modal-body" style="padding:24px 28px;">

                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div>
                            <div id="addExecPhotoPreview" class="exec-modal-avatar" style="background: linear-gradient(135deg, rgb(99, 102, 241), rgb(124, 58, 237));">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <label for="addExecPhotoInput" class="exec-photo-label mt-2">
                                <i class="fa-solid fa-camera"></i> Photo
                            </label>
                            <input type="file" name="photo" id="addExecPhotoInput" accept="image/*" style="display:none;">
                        </div>
                        <div class="flex-grow-1">
                            <label class="exec-modal-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="exec-modal-input"
                                   value="{{ old('name') }}" required placeholder="e.g. Arjun Mehta">
                            @error('name')<div class="exec-field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="exec-modal-section-label">
                        <i class="fa-solid fa-id-card"></i> Personal Information
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="exec-modal-label">Employee ID <span class="text-danger">*</span></label>
                            <input type="text" name="employee_id" class="exec-modal-input"
                                   value="{{ old('employee_id') }}" required placeholder="e.g. TIMS001">
                            @error('employee_id')<div class="exec-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="exec-modal-label">Mobile</label>
                            <input type="text" name="mobile" class="exec-modal-input"
                                   value="{{ old('mobile') }}" placeholder="+91 9876543210">
                        </div>
                        <div class="col-md-4">
                            <label class="exec-modal-label">Email</label>
                            <input type="email" name="email" class="exec-modal-input"
                                   value="{{ old('email') }}" placeholder="exec@company.com">
                        </div>
                    </div>

                    <div class="exec-modal-section-label">
                        <i class="fa-solid fa-building"></i> Assignment
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="exec-modal-label">Company <span class="text-danger">*</span></label>
                            <select name="company_id" id="addExecCompanySelect" class="exec-modal-select" required>
                                <option value="">— Select Company —</option>
                                @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')<div class="exec-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="exec-modal-label">Zone <span class="text-danger">*</span></label>
                            <select name="zone_id" id="addExecZoneSelect" class="exec-modal-select" required>
                                <option value="">— Select Zone —</option>
                                @foreach($zones as $z)
                                <option value="{{ $z->id }}" {{ old('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                                @endforeach
                            </select>
                            @error('zone_id')<div class="exec-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="exec-modal-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="exec-modal-select" required>
                                <option value="probation" {{ old('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                                <option value="active"    {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive"  {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        {{-- ── Date Joined with calendar icon ── --}}
                        <div class="col-md-4">
                            <label class="exec-modal-label">Date Joined</label>
                            <div class="exec-date-wrap">
                                <input type="text" name="date_joined" id="addExecDateJoinedInput"
                                       class="exec-modal-input"
                                       value="{{ old('date_joined') ? \Carbon\Carbon::parse(old('date_joined'))->format('d/m/Y') : '' }}"
                                       placeholder="DD/MM/YYYY" autocomplete="off">
                                <button type="button" class="exec-cal-btn" id="addExecDateJoinedTrigger" tabindex="-1" title="Pick date">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ── Probation End Date with calendar icon ── --}}
                        <div class="col-md-4">
                            <label class="exec-modal-label">Probation End Date</label>
                            <div class="exec-date-wrap">
                                <input type="text" name="probation_end_date" id="addExecProbationEndInput"
                                       class="exec-modal-input"
                                       value="{{ old('probation_end_date') ? \Carbon\Carbon::parse(old('probation_end_date'))->format('d/m/Y') : '' }}"
                                       placeholder="DD/MM/YYYY" autocomplete="off">
                                <button type="button" class="exec-cal-btn" id="addExecProbationEndTrigger" tabindex="-1" title="Pick date">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="exec-modal-section-label">
                        <i class="fa-solid fa-note-sticky"></i> Notes
                    </div>
                    <textarea name="notes" class="exec-modal-input" rows="3"
                              placeholder="Any notes about this executive…"
                              style="height:auto;resize:vertical;">{{ old('notes') }}</textarea>

                </div>
                <div class="modal-footer" style="padding:18px 28px;gap:10px;border-top:1px solid #f0f2fa;">
                    <button type="button" class="btn-pms-ghost" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </button>
                    <button type="submit" class="btn-pms-apply">
                        <i class="fa-solid fa-save"></i> Save Executive
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan


{{-- ══ EDIT EXECUTIVE MODAL ═══════════════════════════════════ --}}
@can('manage_executives')
<div class="modal fade" id="editExecutiveModal" tabindex="-1" aria-labelledby="editExecutiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content pms-modal-content" style="border-radius:22px;">
            <div class="modal-header" style="padding:22px 28px;">
                <h5 class="modal-title" id="editExecutiveModalLabel">
                    <i class="fa-solid fa-user-pen"></i> Edit Executive Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="editExecForm">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding:24px 28px;">

                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div>
                            <div id="editExecPhotoPreview" class="exec-modal-avatar">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <label for="editExecPhotoInput" class="exec-photo-label mt-2" style="cursor:pointer;">
                                <i class="fa-solid fa-camera"></i> Photo
                            </label>
                            <input type="file" name="photo" id="editExecPhotoInput" accept="image/*" style="display:none;">
                        </div>
                        <div class="flex-grow-1">
                            <label class="exec-modal-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="exec-modal-input"
                                   required placeholder="e.g. Arjun Mehta" id="editExecNameInput">
                        </div>
                    </div>

                    <div class="exec-modal-section-label">
                        <i class="fa-solid fa-id-card"></i> Personal Information
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="exec-modal-label">Employee ID <span class="text-danger">*</span></label>
                            <input type="text" name="employee_id" class="exec-modal-input"
                                   required placeholder="e.g. TIMS001" id="editExecEmployeeIdInput">
                        </div>
                        <div class="col-md-4">
                            <label class="exec-modal-label">Mobile</label>
                            <input type="text" name="mobile" class="exec-modal-input"
                                   placeholder="+91 9876543210" id="editExecMobileInput">
                        </div>
                        <div class="col-md-4">
                            <label class="exec-modal-label">Email</label>
                            <input type="email" name="email" class="exec-modal-input"
                                   placeholder="exec@company.com" id="editExecEmailInput">
                        </div>
                    </div>

                    <div class="exec-modal-section-label">
                        <i class="fa-solid fa-building"></i> Assignment
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="exec-modal-label">Company <span class="text-danger">*</span></label>
                            <select name="company_id" id="editExecCompanySelect" class="exec-modal-select" required>
                                <option value="">— Select Company —</option>
                                @foreach($companies as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="exec-modal-label">Zone <span class="text-anchor">*</span></label>
                            <select name="zone_id" id="editExecZoneSelect" class="exec-modal-select" required>
                                <option value="">— Select Zone —</option>
                                @foreach($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="exec-modal-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="exec-modal-select" required id="editExecStatusSelect">
                                <option value="probation">Probation</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        {{-- ── Date Joined with calendar icon (Edit) ── --}}
                        <div class="col-md-4">
                            <label class="exec-modal-label">Date Joined</label>
                            <div class="exec-date-wrap">
                                <input type="text" name="date_joined" id="editExecDateJoinedInput"
                                       class="exec-modal-input"
                                       placeholder="DD/MM/YYYY" autocomplete="off">
                                <button type="button" class="exec-cal-btn" id="editExecDateJoinedTrigger" tabindex="-1" title="Pick date">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ── Probation End Date with calendar icon (Edit) ── --}}
                        <div class="col-md-4">
                            <label class="exec-modal-label">Probation End Date</label>
                            <div class="exec-date-wrap">
                                <input type="text" name="probation_end_date" id="editExecProbationEndInput"
                                       class="exec-modal-input"
                                       placeholder="DD/MM/YYYY" autocomplete="off">
                                <button type="button" class="exec-cal-btn" id="editExecProbationEndTrigger" tabindex="-1" title="Pick date">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="exec-modal-section-label">
                        <i class="fa-solid fa-note-sticky"></i> Notes
                    </div>
                    <textarea name="notes" class="exec-modal-input" rows="3"
                              placeholder="Any notes about this executive…"
                              style="height:auto;resize:vertical;" id="editExecNotesInput"></textarea>

                </div>
                <div class="modal-footer" style="padding:18px 28px;gap:10px;border-top:1px solid #f0f2fa;">
                    <button type="button" class="btn-pms-ghost" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </button>
                    <button type="submit" class="btn-pms-apply">
                        <i class="fa-solid fa-save"></i> Update Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════
// Flatpickr — dd/mm/yyyy display, Y-m-d submitted to server
// The input is now editable; the calendar icon button opens the picker.
// ══════════════════════════════════════════════════════════════
function initFlatpickr(inputEl, triggerEl) {
    if (!inputEl) return null;

    const fp = flatpickr(inputEl, {
        dateFormat   : 'd/m/Y',   // shown to user in the input
        allowInput   : true,       // user can type directly
        disableMobile: true,
        parseDate: function(dateStr) {
            // Support manual typing in dd/mm/yyyy
            const parts = dateStr.split('/');
            if (parts.length === 3) {
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }
            return null;
        },
        onReady: function(_, __, fp) {
            // We don't use altInput — the real input is the visible one.
            // flatpickr will set the value as d/m/Y automatically.
            inputEl.style.paddingRight = '42px'; // already handled by CSS
        },
        onClose: function(selectedDates, dateStr) {
            // After picking, move focus back so the border focus-ring clears cleanly
            if (triggerEl) triggerEl.blur();
        }
    });

    // Calendar icon button opens / toggles the picker
    if (triggerEl) {
        triggerEl.addEventListener('click', function (e) {
            e.preventDefault();
            fp.toggle();
        });
    }

    return fp;
}

// ── Add modal pickers ──
const addDjPicker  = initFlatpickr(
    document.getElementById('addExecDateJoinedInput'),
    document.getElementById('addExecDateJoinedTrigger')
);
const addPedPicker = initFlatpickr(
    document.getElementById('addExecProbationEndInput'),
    document.getElementById('addExecProbationEndTrigger')
);

// ── Edit modal pickers ──
const editDjPicker  = initFlatpickr(
    document.getElementById('editExecDateJoinedInput'),
    document.getElementById('editExecDateJoinedTrigger')
);
const editPedPicker = initFlatpickr(
    document.getElementById('editExecProbationEndInput'),
    document.getElementById('editExecProbationEndTrigger')
);

// ── Zone cascade (Add modal) ──
document.getElementById('addExecCompanySelect')?.addEventListener('change', function () {
    const companyId = this.value;
    const zoneSelect = document.getElementById('addExecZoneSelect');
    zoneSelect.innerHTML = '<option value="">Loading zones…</option>';
    if (!companyId) { zoneSelect.innerHTML = '<option value="">— Select Zone —</option>'; return; }
    fetch(`/api/companies/${companyId}/zones`)
        .then(r => r.json())
        .then(zones => {
            zoneSelect.innerHTML = '<option value="">— Select Zone —</option>';
            zones.forEach(z => zoneSelect.innerHTML += `<option value="${z.id}">${z.name}</option>`);
        });
});

// ── Photo preview (Add modal) ──
document.getElementById('addExecPhotoInput')?.addEventListener('change', function () {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const p = document.getElementById('addExecPhotoPreview');
            p.style.background = 'none';
            p.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:14px;">`;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
document.querySelector('label[for="addExecPhotoInput"]')?.addEventListener('click', function () {
    document.getElementById('addExecPhotoInput').click();
});

// ══════════════════════════════════════════════════════════════
// Edit modal — populate all fields
// ══════════════════════════════════════════════════════════════
function editExecutive(btn) {
    const id               = btn.dataset.id;
    const name             = btn.dataset.name;
    const employeeId       = btn.dataset.employeeId;
    const mobile           = btn.dataset.mobile;
    const email            = btn.dataset.email;
    const companyId        = btn.dataset.companyId;
    const zoneId           = btn.dataset.zoneId;
    const status           = btn.dataset.status;
    const dateJoined       = btn.dataset.dateJoined;        // Y-m-d from PHP
    const probationEndDate = btn.dataset.probationEndDate;  // Y-m-d from PHP
    const notes            = btn.dataset.notes;
    const photo            = btn.dataset.photo;

    // Form action
    document.getElementById('editExecForm').action = `/executives/${id}`;

    // Text fields
    document.getElementById('editExecNameInput').value       = name;
    document.getElementById('editExecEmployeeIdInput').value = employeeId;
    document.getElementById('editExecMobileInput').value     = mobile;
    document.getElementById('editExecEmailInput').value      = email;
    document.getElementById('editExecCompanySelect').value   = companyId;
    document.getElementById('editExecStatusSelect').value    = status;
    document.getElementById('editExecNotesInput').value      = notes;

    // Dates — parse Y-m-d from server, display as dd/mm/yyyy in the input
    if (editDjPicker)  { dateJoined       ? editDjPicker.setDate(dateJoined, true, 'Y-m-d')       : editDjPicker.clear(); }
    if (editPedPicker) { probationEndDate ? editPedPicker.setDate(probationEndDate, true, 'Y-m-d') : editPedPicker.clear(); }

    // Photo
    const preview = document.getElementById('editExecPhotoPreview');
    if (photo) {
        preview.style.background = 'none';
        preview.innerHTML = `<img src="${photo}" style="width:100%;height:100%;object-fit:cover;border-radius:14px;">`;
    } else {
        preview.style.background = 'linear-gradient(135deg,#6366f1,#7c3aed)';
        preview.innerHTML = `<i class="fa-solid fa-user"></i>`;
    }

    // Zone cascade
    const zoneSelect = document.getElementById('editExecZoneSelect');
    zoneSelect.innerHTML = '<option value="">Loading zones…</option>';
    fetch(`/api/companies/${companyId}/zones`)
        .then(r => r.json())
        .then(zones => {
            zoneSelect.innerHTML = '<option value="">— Select Zone —</option>';
            zones.forEach(z => {
                const sel = z.id == zoneId ? 'selected' : '';
                zoneSelect.innerHTML += `<option value="${z.id}" ${sel}>${z.name}</option>`;
            });
        });

    new bootstrap.Modal(document.getElementById('editExecutiveModal')).show();
}

// ── Zone cascade (Edit modal company change) ──
document.getElementById('editExecCompanySelect')?.addEventListener('change', function () {
    const companyId = this.value;
    const zoneSelect = document.getElementById('editExecZoneSelect');
    zoneSelect.innerHTML = '<option value="">Loading zones…</option>';
    if (!companyId) { zoneSelect.innerHTML = '<option value="">— Select Zone —</option>'; return; }
    fetch(`/api/companies/${companyId}/zones`)
        .then(r => r.json())
        .then(zones => {
            zoneSelect.innerHTML = '<option value="">— Select Zone —</option>';
            zones.forEach(z => zoneSelect.innerHTML += `<option value="${z.id}">${z.name}</option>`);
        });
});

// ── Photo preview (Edit modal) ──
document.getElementById('editExecPhotoInput')?.addEventListener('change', function () {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const p = document.getElementById('editExecPhotoPreview');
            p.style.background = 'none';
            p.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:14px;">`;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
document.querySelector('label[for="editExecPhotoInput"]')?.addEventListener('click', function () {
    document.getElementById('editExecPhotoInput').click();
});

// ── Re-open Add modal on validation errors ──
@if($errors->any())
var addModal = new bootstrap.Modal(document.getElementById('addExecutiveModal'));
addModal.show();
@endif
</script>
@endpush