@extends('layouts.app')

@section('title', 'CRO Executive Performance Entry')
@section('page_title', 'Daily Performance Entry')

@section('styles')
    <style>
        :root {
            --primary: #2563EB;
            --secondary: #1E40AF;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #06B6D4;
            --bg-main: #0e0c16;
            --card-bg: #0f1322;
            --card-border: rgba(255, 255, 255, 0.06);
            --input-bg: #161b30;

            /* Score panel tokens */
            --dl-bg-elevated:   #111829;
            --dl-bg-overlay:    #151D30;
            --dl-blue:          #3B7BFF;
            --dl-blue-dim:      rgba(59,123,255,0.12);
            --dl-violet:        #7C3AED;
            --dl-cyan:          #06B6D4;
            --dl-amber:         #F59E0B;
            --dl-emerald:       #10B981;
            --dl-emerald-dim:   rgba(16,185,129,0.12);
            --dl-rose:          #F43F5E;
            --dl-rose-dim:      rgba(244,63,94,0.12);
            --dl-border:        rgba(255,255,255,0.07);
            --dl-border-bright: rgba(255,255,255,0.13);
            --dl-text-primary:  #F0F4FF;
            --dl-text-secondary:rgba(240,244,255,0.52);
            --dl-text-muted:    rgba(240,244,255,0.32);
            --dl-radius-sm:     8px;
            --dl-radius-md:     12px;
            --dl-radius-lg:     16px;
        }

        body {
            background-color: var(--bg-main) !important;
        }

        .custom-card {
            background: #0f1322 !important;
            border: 1px solid var(--card-border) !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 24px;
            margin-bottom: 20px;
            transition: border-color 0.2s ease;
        }

        .custom-card:hover {
            border-color: rgba(37, 99, 235, 0.2) !important;
        }

        .form-control,
        .form-select {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--card-border) !important;
            color: #fff !important;
            font-size: 13px !important;
            border-radius: 8px !important;
            padding: 9px 12px !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            outline: none;
        }

        .input-group-text {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--card-border) !important;
            border-right: none !important;
            color: rgba(255, 255, 255, 0.4) !important;
            border-radius: 8px 0 0 8px !important;
            font-size: 12px;
        }

        .input-group .form-control {
            border-radius: 0 8px 8px 0 !important;
            border-left: none !important;
        }

        /* ── PAGE TOOLBAR ── */
        .page-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-title {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
        }

        .toolbar-pill {
            height: 40px;
            min-width: 160px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 14px;
            background: #111827;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 12px;
            transition: .25s;
        }

        .toolbar-pill:hover {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        .toolbar-pill i {
            color: #8B5CF6;
            font-size: 13px;
        }

        .toolbar-select,
        .toolbar-date {
            width: 100%;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 13px;
            outline: none;
        }

        .toolbar-select option {
            background: #111827;
        }

        /* ── SECTION LABELS ── */
        .sec-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #E5E7EB;
            margin-bottom: 16px;
        }

        .fl {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.45);
            margin-bottom: 6px;
        }

        .form-hint {
            font-size: 10px;
            color: var(--success);
            margin-top: 5px;
        }

        /* ── 3-COL METRICS GRID ── */
        .row3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        @media (max-width: 575.98px) {
            .row3 { grid-template-columns: 1fr; }
        }

        /* ── CRM COMPLIANCE GRID ── */
        .compliance-grid2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        @media (max-width: 575.98px) {
            .compliance-grid2 { grid-template-columns: 1fr; }
        }

        .titem {
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 7px;
            padding: 10px 11px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .titem-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        .titem strong {
            font-size: 11px;
            color: #fff;
            font-weight: 600;
            line-height: 1.3;
            flex: 1;
        }

        /* ── CUSTOM TOGGLE SWITCH ── */
        .switch {
            position: relative;
            display: inline-block;
            width: 32px;
            height: 18px;
            flex-shrink: 0;
        }

        .switch input { opacity: 0; width: 0; height: 0; }

        .sld {
            position: absolute;
            inset: 0;
            background: #252d45;
            border-radius: 18px;
            cursor: pointer;
            transition: .2s;
        }

        .sld:before {
            content: '';
            position: absolute;
            width: 13px;
            height: 13px;
            left: 3px;
            top: 2.5px;
            background: #fff;
            border-radius: 50%;
            transition: .2s;
        }

        .switch input:checked + .sld { background: var(--primary); }
        .switch input:checked + .sld:before { transform: translateX(14px); }

        /* ── VIOLATION GROUPS ── */
        .vgrid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        @media (max-width: 575.98px) {
            .vgrid { grid-template-columns: 1fr; }
        }

        .violation-group {
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 12px 13px;
        }

        .vg-title {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--danger);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-check {
            display: flex;
            align-items: flex-start;
            gap: 7px;
            padding: 3px 0;
            margin: 0;
        }

        .form-check-input {
            background-color: var(--input-bg) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            width: 13px !important;
            height: 13px !important;
            margin: 2px 0 0 0 !important;
            flex-shrink: 0;
            cursor: pointer;
            border-radius: 3px !important;
        }

        .form-check-input:checked {
            background-color: var(--danger) !important;
            border-color: var(--danger) !important;
        }

        .form-check-label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.35;
            cursor: pointer;
        }

        .form-check:hover .form-check-label {
            color: rgba(255, 255, 255, 0.8);
        }

        /* ── STICKY SUMMARY PANEL ── */
        .sticky-summary {
            position: sticky;
            top: 14px;
            z-index: 10;
        }

        /* ── LOADING OVERLAY ── */
        #loading_spinner {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        #loading_spinner.show { display: flex !important; }

        .spinner-box {
            background: #0f1322;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 28px 36px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
        }

        .spinner-box p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            margin: 0;
        }

        /* ════════════════════════════════════════
           LIVE SCORE PREVIEW PANEL (right side)
        ════════════════════════════════════════ */
        .sp-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.045) 0%, rgba(255,255,255,0.012) 100%);
            border: 1px solid var(--dl-border);
            border-radius: var(--dl-radius-lg);
            padding: 22px;
            box-shadow: 0 4px 24px rgba(0,0,0,.38), 0 1px 3px rgba(0,0,0,.22);
        }

        /* panel header */
        .sp-hdr {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--dl-border);
        }

        .sp-hdr-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: var(--dl-blue-dim);
            border: 1px solid rgba(59,123,255,.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--dl-blue);
            flex-shrink: 0;
        }

        .sp-hdr-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--dl-text-primary);
            letter-spacing: .01em;
        }

        .sp-hdr-sub {
            font-size: 10.5px;
            color: var(--dl-text-muted);
            margin-top: 1px;
        }

        /* breakdown section label */
        .sp-section-lbl {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--dl-text-muted);
            margin-bottom: 8px;
        }

        /* each breakdown row */
        .sp-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid var(--dl-border);
        }

        .sp-row:last-child { border-bottom: none; }

        .sp-row-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: var(--dl-text-muted);
        }

        .sp-row-label i { width: 12px; text-align: center; font-size: 11px; }

        .sp-row-val {
            font-size: 12.5px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        /* totals inner box */
        .sp-totals-box {
            background: var(--dl-bg-overlay);
            border: 1px solid var(--dl-border);
            border-radius: var(--dl-radius-md);
            padding: 13px 15px;
            margin: 14px 0;
        }

        .sp-totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }

        .sp-totals-row + .sp-totals-row { border-top: 1px solid var(--dl-border); }

        .sp-totals-label { font-size: 12px; color: var(--dl-text-secondary); }
        .sp-totals-val   { font-size: 13px; font-weight: 700; font-variant-numeric: tabular-nums; }

        /* recovery progress */
        .sp-prog-meta {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            color: var(--dl-text-muted);
            margin-bottom: 5px;
        }

        .sp-prog-track {
            height: 5px;
            border-radius: 3px;
            background: rgba(255,255,255,.06);
            overflow: hidden;
            margin-bottom: 14px;
        }

        .sp-prog-fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--dl-cyan), var(--dl-blue));
            transition: width .35s ease;
        }

        /* final score block */
        .sp-final {
            background: linear-gradient(135deg, rgba(59,123,255,.1) 0%, rgba(124,58,237,.07) 100%);
            border: 1px solid rgba(59,123,255,.22);
            border-radius: var(--dl-radius-md);
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sp-final-lbl {
            font-size: 9.5px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--dl-text-muted);
            margin-bottom: 2px;
        }

        .sp-final-sub {
            font-size: 10.5px;
            color: var(--dl-text-muted);
        }

        .sp-final-score {
            font-size: 44px;
            font-weight: 800;
            letter-spacing: -.04em;
            font-variant-numeric: tabular-nums;
            line-height: 1;
            transition: color .3s;
        }

        .sp-final-score.pos { color: var(--dl-blue); }
        .sp-final-score.neg { color: var(--dl-rose); }
    </style>
@endsection

@section('content')

    {{-- Loading Spinner Overlay --}}
    <div id="loading_spinner">
        <div class="spinner-box">
            <div class="spinner-border text-primary" role="status" style="width:28px;height:28px;border-width:3px;"></div>
            <p>Loading CRM data…</p>
        </div>
    </div>

    {{-- Page Toolbar --}}
    <div class="page-toolbar">
        <div class="toolbar-actions">
            <div class="toolbar-pill">
                <i class="fa-solid fa-user"></i>
                <select id="select_executive_id" class="toolbar-select">
                    <option value="">Select Executive</option>
                    @foreach($executives as $exec)
                        <option value="{{ $exec->id }}">{{ $exec->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="toolbar-pill">
                <i class="fa-regular fa-calendar"></i>
                <input type="date" id="select_log_date" class="toolbar-date">
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── LEFT: DATA ENTRY FORM ── --}}
        <div class="col-xl-8 col-lg-7">

            <form method="POST" action="{{ route('daily_logs.store') }}" id="mainLogEntryForm">
                @csrf
                <input type="hidden" name="executive_id" id="hidden_exec_id">
                <input type="hidden" name="date" id="hidden_log_date">

                {{-- METRICS + COMPLIANCE (side by side) --}}
                <div class="row g-3 mb-0">

                    {{-- Performance Metrics --}}
                    <div class="col-lg-7">
                        <div class="custom-card h-100">
                            <div class="sec-label">
                                <i class="fa-solid fa-pen-to-square fa-xs text-primary"></i>
                                Performance entry inputs
                            </div>

                            <div class="row3">
                                <div>
                                    <div class="fl">Connected calls</div>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-phone fa-xs"></i></span>
                                        <input type="number" name="connected_calls" id="in_connected_calls"
                                            class="form-control" value="0" min="0" required>
                                    </div>
                                    <div class="form-hint"><i class="fa-solid fa-check fa-xs"></i> Auto Updated</div>
                                </div>
                                <div>
                                    <div class="fl">Meetings arranged</div>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-regular fa-calendar-check fa-xs"></i></span>
                                        <input type="number" name="meetings_arranged" id="in_meetings_arranged"
                                            class="form-control" value="0" min="0" required>
                                    </div>
                                    <div class="form-hint"><i class="fa-solid fa-check fa-xs"></i> Auto Updated</div>
                                </div>
                                <div>
                                    <div class="fl">Meetings attended</div>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-regular fa-handshake fa-xs"></i></span>
                                        <input type="number" name="meetings_attended" id="in_meetings_attended"
                                            class="form-control" value="0" min="0" required>
                                    </div>
                                    <div class="form-hint"><i class="fa-solid fa-check fa-xs"></i> Auto Updated</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CRM Compliance --}}
                    <div class="col-lg-5">
                        <div class="custom-card h-100">
                            <div class="sec-label">
                                <i class="fa-solid fa-shield-halved fa-xs text-primary"></i>
                                CRM compliance verification
                            </div>

                            <div class="compliance-grid2">
                                <div class="titem">
                                    <div class="titem-top">
                                        <strong>First contact ≤ 45 min</strong>
                                        <label class="switch">
                                            <input type="checkbox" name="first_contact_within_45_min" id="chk_45m" value="1">
                                            <span class="sld"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="titem">
                                    <div class="titem-top">
                                        <strong>All leads followed up</strong>
                                        <label class="switch">
                                            <input type="checkbox" name="all_leads_followed_up" id="chk_followup" value="1">
                                            <span class="sld"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="titem">
                                    <div class="titem-top">
                                        <strong>CRM disposition accurate</strong>
                                        <label class="switch">
                                            <input type="checkbox" name="crm_disposition_correct" id="chk_disposition" value="1">
                                            <span class="sld"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="titem">
                                    <div class="titem-top">
                                        <strong>Warm lead converted</strong>
                                        <label class="switch">
                                            <input type="checkbox" name="warm_lead_converted" id="chk_conversion" value="1">
                                            <span class="sld"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- VIOLATIONS --}}
                <div class="custom-card mt-3">
                    <div class="sec-label" style="color:var(--danger)">
                        <i class="fa-solid fa-triangle-exclamation fa-xs"></i>
                        Violation code & deduction register
                    </div>

                    <div class="vgrid">
                        {{-- Call Violations --}}
                        <div class="violation-group">
                            <div class="vg-title"><i class="fa-solid fa-phone-slash fa-xs"></i> Call violations</div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="call_calls_33_39" id="v_c_1">
                                <label class="form-check-label" for="v_c_1">Connected calls 33–39 (-5 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="call_calls_27_32" id="v_c_2">
                                <label class="form-check-label" for="v_c_2">Connected calls 27–32 (-10 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="call_calls_15_26" id="v_c_3">
                                <label class="form-check-label" for="v_c_3">Connected calls 15–26 (-15 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="call_calls_below_15" id="v_c_4">
                                <label class="form-check-label" for="v_c_4">Connected calls below 15 (-20 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="call_zero_calls" id="v_c_5">
                                <label class="form-check-label" for="v_c_5">Zero calls (-25 pts)</label>
                            </div>
                        </div>

                        {{-- Meeting Violations --}}
                        <div class="violation-group">
                            <div class="vg-title"><i class="fa-solid fa-handshake-slash fa-xs"></i> Meeting violations</div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="meeting_zero_meetings" id="v_m_1">
                                <label class="form-check-label" for="v_m_1">Zero meetings (-10 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="meeting_3_day_no_meeting" id="v_m_2">
                                <label class="form-check-label" for="v_m_2">3-day no meeting streak (-15 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="meeting_invalid_documentation" id="v_m_3">
                                <label class="form-check-label" for="v_m_3">Invalid meeting documentation (-10 pts)</label>
                            </div>
                        </div>

                        {{-- Lead Violations --}}
                        <div class="violation-group">
                            <div class="vg-title"><i class="fa-solid fa-list-check fa-xs"></i> Lead violations</div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="lead_no_first_contact" id="v_l_1">
                                <label class="form-check-label" for="v_l_1">No first contact (-5 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="lead_no_follow_up" id="v_l_2">
                                <label class="form-check-label" for="v_l_2">No follow-up (-5 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="lead_wrong_disposition" id="v_l_3">
                                <label class="form-check-label" for="v_l_3">Wrong CRM disposition (-5 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="lead_warm_incorrectly_frozen" id="v_l_4">
                                <label class="form-check-label" for="v_l_4">Warm lead incorrectly frozen (-10 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="lead_invalid_remarks" id="v_l_5">
                                <label class="form-check-label" for="v_l_5">Invalid remarks (-2 pts)</label>
                            </div>
                        </div>

                        {{-- Conduct Violations --}}
                        <div class="violation-group">
                            <div class="vg-title"><i class="fa-solid fa-gavel fa-xs"></i> Conduct violations</div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="conduct_data_tampering" id="v_d_1">
                                <label class="form-check-label" for="v_d_1">Data tampering (-20 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="conduct_false_justification" id="v_d_2">
                                <label class="form-check-label" for="v_d_2">False justification (-15 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="conduct_protocol_violation" id="v_d_3">
                                <label class="form-check-label" for="v_d_3">Communication protocol violation (-10 pts)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input v-chk" type="checkbox" name="violations[]" value="conduct_customer_complaint" id="v_d_4">
                                <label class="form-check-label" for="v_d_4">Verified customer complaint (-15 pts)</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- REMARKS --}}
                <div class="custom-card">
                    <div class="sec-label">
                        <i class="fa-solid fa-note-sticky fa-xs text-primary"></i>
                        Remarks
                    </div>
                    <textarea name="cro_remarks" id="in_remarks" class="form-control" rows="3" style="resize:none;"
                        placeholder="Add observations, validation justifications, and notes here…"></textarea>
                </div>

                {{-- ACTIONS --}}
                <div class="d-flex gap-2 justify-content-end mb-5">
                    <a href="{{ route('daily_logs.index') }}" class="btn btn-outline-secondary btn-sm px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save daily record
                    </button>
                </div>
            </form>

        </div>

        {{-- ── RIGHT: LIVE SCORE PREVIEW PANEL ── --}}
        <div class="col-xl-4 col-lg-5">
            <div class="sticky-summary">
                <div class="sp-card">

                    {{-- Header --}}
                    <div class="sp-hdr">
                        <div class="sp-hdr-icon">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <div>
                            <div class="sp-hdr-title">Point Summary</div>
                            <div class="sp-hdr-sub">Updates as you fill in the form</div>
                        </div>
                    </div>

                    {{-- Breakdown --}}
                    <div class="sp-section-lbl">Point Breakdown</div>

                    <div style="margin-bottom: 4px;">
                        <div class="sp-row">
                            <span class="sp-row-label">
                                <i class="fa-solid fa-phone" style="color:var(--dl-cyan);"></i>
                                Connected Calls
                            </span>
                            <span class="sp-row-val" id="calc_calls_pts" style="color:var(--dl-cyan);">+0</span>
                        </div>
                        <div class="sp-row">
                            <span class="sp-row-label">
                                <i class="fa-regular fa-calendar-check" style="color:var(--dl-violet);"></i>
                                Meetings Arranged
                            </span>
                            <span class="sp-row-val" id="calc_arranged_pts" style="color:var(--dl-violet);">+0</span>
                        </div>
                        <div class="sp-row">
                            <span class="sp-row-label">
                                <i class="fa-regular fa-handshake" style="color:var(--dl-emerald);"></i>
                                Meetings Attended
                            </span>
                            <span class="sp-row-val" id="calc_attended_pts" style="color:var(--dl-emerald);">+0</span>
                        </div>
                        <div class="sp-row">
                            <span class="sp-row-label">
                                <i class="fa-solid fa-clock" style="color:var(--dl-amber);"></i>
                                First Contact ≤45m
                            </span>
                            <span class="sp-row-val" id="calc_45m_pts" style="color:var(--dl-amber);">+0</span>
                        </div>
                        <div class="sp-row">
                            <span class="sp-row-label">
                                <i class="fa-solid fa-phone-volume" style="color:var(--dl-amber);"></i>
                                Lead Follow-up
                            </span>
                            <span class="sp-row-val" id="calc_followup_pts" style="color:var(--dl-amber);">+0</span>
                        </div>
                        <div class="sp-row">
                            <span class="sp-row-label">
                                <i class="fa-solid fa-database" style="color:var(--dl-amber);"></i>
                                CRM Disposition
                            </span>
                            <span class="sp-row-val" id="calc_disposition_pts" style="color:var(--dl-amber);">+0</span>
                        </div>
                        <div class="sp-row">
                            <span class="sp-row-label">
                                <i class="fa-solid fa-fire" style="color:var(--dl-rose);"></i>
                                Warm Conversion
                            </span>
                            <span class="sp-row-val" id="calc_conversion_pts" style="color:var(--dl-rose);">+0</span>
                        </div>
                    </div>

                    {{-- Totals box --}}
                    <div class="sp-totals-box">
                        <div class="sp-totals-row">
                            <span class="sp-totals-label">Positive Points</span>
                            <span class="sp-totals-val" id="calc_total_positive" style="color:var(--dl-emerald);">+0</span>
                        </div>
                        <div class="sp-totals-row">
                            <span class="sp-totals-label">Violations Deduction</span>
                            <span class="sp-totals-val" id="calc_total_negative" style="color:var(--dl-rose);">-0</span>
                        </div>
                        <div class="sp-totals-row">
                            <span class="sp-totals-label">Recovery Points</span>
                            <span class="sp-totals-val" id="calc_total_recovery" style="color:var(--dl-cyan);">+0</span>
                        </div>
                    </div>

                    {{-- Recovery cap progress --}}
                    <div class="sp-prog-meta">
                        <span>Recovery Cap Used</span>
                        <span id="txt_recovery_cap">0 / 20</span>
                    </div>
                    <div class="sp-prog-track">
                        <div class="sp-prog-fill" id="progress_recovery_cap" style="width:0%;"></div>
                    </div>

                    {{-- Final Score --}}
                    <div class="sp-final">
                        <div>
                            <div class="sp-final-lbl">Daily Final Score</div>
                            <div class="sp-final-sub">Positive − Violations + Recovery</div>
                        </div>
                        <div class="sp-final-score pos" id="calc_final_score">+0</div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const selectExec = document.getElementById('select_executive_id');
            const selectDate = document.getElementById('select_log_date');
            const hiddenExec = document.getElementById('hidden_exec_id');
            const hiddenDate = document.getElementById('hidden_log_date');

            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            selectDate.value = today;

            // Reload dashboard when executive or date changes
            selectExec.addEventListener('change', loadExecutiveDashboard);
            selectDate.addEventListener('change', loadExecutiveDashboard);

            // Recalculate score live on number inputs
            ['in_connected_calls', 'in_meetings_arranged', 'in_meetings_attended'].forEach(id => {
                document.getElementById(id).addEventListener('input', recalculateScore);
            });

            // Recalculate score live on toggle switches
            ['chk_45m', 'chk_followup', 'chk_disposition', 'chk_conversion'].forEach(id => {
                document.getElementById(id).addEventListener('change', recalculateScore);
            });

            // Recalculate score live on violation checkboxes
            document.querySelectorAll('.v-chk').forEach(chk => {
                chk.addEventListener('change', recalculateScore);
            });

            // Form submit — sync hidden fields + validate
            document.getElementById('mainLogEntryForm').addEventListener('submit', function (e) {
                if (!selectExec.value) {
                    e.preventDefault();
                    alert('Please select an executive before saving.');
                    return;
                }

                hiddenExec.value = selectExec.value;
                hiddenDate.value = selectDate.value;

                const arranged = parseInt(document.getElementById('in_meetings_arranged').value) || 0;
                const attended = parseInt(document.getElementById('in_meetings_attended').value) || 0;
                if (attended > arranged) {
                    e.preventDefault();
                    alert('Meetings Attended cannot exceed Meetings Arranged.');
                    document.getElementById('in_meetings_attended').focus();
                }
            });

            recalculateScore();
        });

        // ── AJAX: load existing log for selected exec + date ──────────────────
        function loadExecutiveDashboard() {
            const execId = document.getElementById('select_executive_id').value;
            const date   = document.getElementById('select_log_date').value;

            if (!execId) { resetDashboardUI(); return; }

            showSpinner(true);

            fetch(`{{ route('daily_logs.executive_dashboard') }}?executive_id=${execId}&date=${date}`)
                .then(res => res.json())
                .then(data => {
                    showSpinner(false);

                    if (data.existing_log) {
                        document.getElementById('in_connected_calls').value   = data.existing_log.connected_calls   ?? 0;
                        document.getElementById('in_meetings_arranged').value = data.existing_log.meetings_arranged ?? 0;
                        document.getElementById('in_meetings_attended').value = data.existing_log.meetings_attended ?? 0;
                        document.getElementById('chk_45m').checked        = !!data.existing_log.first_contact_within_45_min;
                        document.getElementById('chk_followup').checked   = !!data.existing_log.all_leads_followed_up;
                        document.getElementById('chk_disposition').checked = !!data.existing_log.crm_disposition_correct;
                        document.getElementById('chk_conversion').checked  = !!data.existing_log.warm_lead_converted;
                        document.getElementById('in_remarks').value        = data.existing_log.cro_remarks ?? '';
                    } else {
                        refreshCrmMetrics();
                        return;
                    }

                    document.querySelectorAll('.v-chk').forEach(chk => chk.checked = false);
                    if (data.selected_violations && data.selected_violations.length) {
                        data.selected_violations.forEach(key => {
                            const el = document.querySelector(`.v-chk[value="${key}"]`);
                            if (el) el.checked = true;
                        });
                    }

                    recalculateScore();
                })
                .catch(err => { showSpinner(false); console.error('Dashboard load error:', err); });
        }

        // ── AJAX: pull live CRM metrics ───────────────────────────────────────
        function refreshCrmMetrics() {
            const execId = document.getElementById('select_executive_id').value;
            const date   = document.getElementById('select_log_date').value;

            if (!execId) return;

            showSpinner(true);

            fetch(`{{ route('daily_logs.crm_metrics') }}?executive_id=${execId}&date=${date}`)
                .then(res => res.json())
                .then(data => {
                    showSpinner(false);

                    document.getElementById('in_connected_calls').value   = data.connected_calls   ?? 0;
                    document.getElementById('in_meetings_arranged').value = data.meetings_arranged ?? 0;
                    document.getElementById('in_meetings_attended').value = data.meetings_attended ?? 0;

                    document.getElementById('chk_45m').checked        = !!data.first_contact_within_45_min;
                    document.getElementById('chk_followup').checked   = !!data.all_leads_followed_up;
                    document.getElementById('chk_disposition').checked = !!data.crm_disposition_correct;
                    document.getElementById('chk_conversion').checked  = !!data.warm_lead_converted;

                    recalculateScore();
                })
                .catch(err => { showSpinner(false); console.error('CRM metrics error:', err); });
        }

        // ── Live score calculator ─────────────────────────────────────────────
        function recalculateScore() {
            const calls    = parseInt(document.getElementById('in_connected_calls').value)   || 0;
            const arranged = parseInt(document.getElementById('in_meetings_arranged').value) || 0;
            const attended = parseInt(document.getElementById('in_meetings_attended').value) || 0;

            const is45m         = document.getElementById('chk_45m').checked;
            const isFollowup    = document.getElementById('chk_followup').checked;
            const isDisposition = document.getElementById('chk_disposition').checked;
            const isConversion  = document.getElementById('chk_conversion').checked;

            let callPts = 0;
            if (calls >= 65) callPts = 8;
            else if (calls >= 50) callPts = 6;
            else if (calls >= 40) callPts = 4;

            let arrangedPts = 0;
            if (arranged >= 4) arrangedPts = 8;
            else if (arranged >= 2) arrangedPts = 5;
            else if (arranged === 1) arrangedPts = 3;

            const attendedPts    = attended * 4;
            const p45mPts        = is45m        ? 2 : 0;
            const followupPts    = isFollowup   ? 2 : 0;
            const dispositionPts = isDisposition? 2 : 0;
            const conversionPts  = isConversion ? 5 : 0;

            const totalPositive = callPts + arrangedPts + attendedPts + p45mPts + followupPts + dispositionPts + conversionPts;

            let totalNegative = 0;
            document.querySelectorAll('.v-chk:checked').forEach(chk => {
                totalNegative += getViolationPoints(chk.value);
            });

            let recovery = 0;
            if (calls >= 65) recovery += 6;
            if (attended >= 2) recovery += 6;
            if (is45m && isFollowup && isDisposition && totalNegative === 0) recovery += 8;
            if (recovery > 20) recovery = 20;

            const finalScore = totalPositive - totalNegative + recovery;

            setText('calc_calls_pts',       `+${callPts}`);
            setText('calc_arranged_pts',    `+${arrangedPts}`);
            setText('calc_attended_pts',    `+${attendedPts}`);
            setText('calc_45m_pts',         `+${p45mPts}`);
            setText('calc_followup_pts',    `+${followupPts}`);
            setText('calc_disposition_pts', `+${dispositionPts}`);
            setText('calc_conversion_pts',  `+${conversionPts}`);
            setText('calc_total_positive',  `+${totalPositive}`);
            setText('calc_total_negative',  `-${totalNegative}`);
            setText('calc_total_recovery',  `+${recovery}`);
            setText('txt_recovery_cap',     `${recovery} / 20`);

            document.getElementById('progress_recovery_cap').style.width = `${(recovery / 20) * 100}%`;

            const scoreEl = document.getElementById('calc_final_score');
            scoreEl.textContent = (finalScore >= 0 ? '+' : '') + finalScore;
            scoreEl.className   = 'sp-final-score ' + (finalScore >= 0 ? 'pos' : 'neg');
        }

        // ── Violation point map ───────────────────────────────────────────────
        function getViolationPoints(key) {
            const rules = {
                'call_calls_33_39': 5, 'call_calls_27_32': 10, 'call_calls_15_26': 15,
                'call_calls_below_15': 20, 'call_zero_calls': 25,
                'meeting_zero_meetings': 10, 'meeting_3_day_no_meeting': 15, 'meeting_invalid_documentation': 10,
                'lead_no_first_contact': 5, 'lead_no_follow_up': 5, 'lead_wrong_disposition': 5,
                'lead_warm_incorrectly_frozen': 10, 'lead_invalid_remarks': 2,
                'conduct_data_tampering': 20, 'conduct_false_justification': 15,
                'conduct_protocol_violation': 10, 'conduct_customer_complaint': 15,
            };
            return rules[key] || 0;
        }

        // ── Reset form to blank state ─────────────────────────────────────────
        function resetDashboardUI() {
            ['in_connected_calls', 'in_meetings_arranged', 'in_meetings_attended'].forEach(id => {
                document.getElementById(id).value = 0;
            });
            ['chk_45m', 'chk_followup', 'chk_disposition', 'chk_conversion'].forEach(id => {
                document.getElementById(id).checked = false;
            });
            document.getElementById('in_remarks').value = '';
            document.querySelectorAll('.v-chk').forEach(chk => chk.checked = false);
            recalculateScore();
        }

        function showSpinner(show) {
            const el = document.getElementById('loading_spinner');
            if (show) el.classList.add('show');
            else el.classList.remove('show');
        }

        function setText(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        }
    </script>
@endsection