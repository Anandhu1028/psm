    @extends('layouts.app')
    @section('title', 'Enter Daily Audit')
    @section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('daily_audit.index') }}">Audit History</a></li>
        <li class="breadcrumb-item active">Enter Audit</li>
    </ol>
    @endsection

    @push('styles')
    <style>
    /* ═══════════════════════════════════════════════════════════
    ENTER DAILY AUDIT — Premium Design System
    ═══════════════════════════════════════════════════════════ */



    /* ── Page Header ── */
    .ca-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 36px;
        gap: 16px;
        flex-wrap: wrap;
    }
    .ca-eyebrow {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #6366f1;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ca-eyebrow::before {
        content: '';
        display: inline-block;
        width: 16px; height: 2px;
        background: #6366f1;
        border-radius: 2px;
    }
    .ca-page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0d0f1c;
        letter-spacing: -0.04em;
        line-height: 1.1;
        margin: 0 0 5px;
    }
    .ca-page-sub { font-size: 0.82rem; color: #94a3b8; font-weight: 450; }

    .btn-ca-back {
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
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        white-space: nowrap;
    }
    .btn-ca-back:hover {
        background: #fafaff;
        border-color: #6366f1;
        color: #4f46e5;
        transform: translateY(-1px);
        text-decoration: none;
    }

    /* ── Step Cards ── */
    .ca-step-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.055), 0 1px 3px rgba(0,0,0,0.03);
        margin-bottom: 20px;
        overflow: hidden;
        transition: box-shadow 0.25s ease;
        border: 1px solid rgba(226,232,240,0.5);
    }
    .ca-step-card:focus-within {
        box-shadow: 0 4px 32px rgba(99,102,241,0.08), 0 2px 8px rgba(0,0,0,0.03);
    }

    .ca-step-head {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 28px 18px;
        border-bottom: 1px solid #f4f5fb;
    }
    .ca-step-num {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1, #7c3aed);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.78rem;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(99,102,241,0.3);
        letter-spacing: -0.02em;
    }
    .ca-step-num i {
        font-size: 0.95rem;
    }
    .ca-step-info { flex: 1; min-width: 0; }
    .ca-step-title {
        font-size: 0.96rem;
        font-weight: 800;
        color: #0d0f1c;
        letter-spacing: -0.02em;
        margin: 0 0 2px;
    }
    .ca-step-desc { font-size: 0.73rem; color: #94a3b8; font-weight: 500; }

    .ca-step-body { padding: 24px 28px; }

    /* ── Form Fields ── */
    .ca-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        color: #64748b;
        margin-bottom: 7px;
        display: block;
    }
    .ca-required { color: #f43f5e; }

    .ca-input,
    .ca-select {
        height: 41px;
        border-radius: 12px !important;
        border: 1.5px solid #edf0f7 !important;
        background: #fafbff !important;
        color: #1e1f2e !important;
        font-size: 0.86rem !important;
        font-family: inherit;
        transition: all 0.2s ease;
        box-shadow: none !important;
        padding: 0 14px;
        width: 100%;
    }
    .ca-input:focus,
    .ca-select:focus {
        border-color: #6366f1 !important;
        background: #fff !important;
        box-shadow: 0 0 0 4px rgba(99,102,241,0.1) !important;
        outline: none;
    }
    .ca-input::placeholder { color: #c4b5fd; }
    .ca-textarea {
        border-radius: 12px !important;
        border: 1.5px solid #edf0f7 !important;
        background: #fafbff !important;
        color: #1e1f2e !important;
        font-size: 0.86rem !important;
        font-family: inherit;
        padding: 12px 14px;
        resize: none;
        width: 100%;
        transition: all 0.2s ease;
        box-shadow: none !important;
    }
    .ca-textarea:focus {
        border-color: #6366f1 !important;
        background: #fff !important;
        box-shadow: 0 0 0 4px rgba(99,102,241,0.1) !important;
        outline: none;
    }

    /* Input with icon */
    .ca-input-wrap {
        position: relative;
    }
    .ca-input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.82rem;
        pointer-events: none;
        z-index: 1;
    }
    .ca-input-wrap .ca-input { padding-left: 38px; }

    .ca-field-hint {
        font-size: 0.68rem;
        color: #b0b8d1;
        font-weight: 500;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .ca-field-hint i { font-size: 0.6rem; }

    /* KPI banner */
    .ca-kpi-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 30px;
        padding: 0 12px;
        border-radius: 100px;
        font-size: 0.73rem;
        font-weight: 700;
        transition: all 0.2s;
    }
    .ca-kpi-pass { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .ca-kpi-fail { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }

    /* FOCUZ fields panel */
    .ca-focuz-panel {
        border-radius: 14px;
        background: #eff6ff;
        border: 1.5px solid #bfdbfe;
        padding: 18px 20px;
        margin-top: 18px;
    }
    .ca-focuz-panel-label {
        font-size: 0.72rem;
        font-weight: 800;
        color: #2563eb;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    /* ── Compliance Toggle Cards ── */
    .ca-compliance-grid{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:12px;
    }

    @media (max-width:1200px){
        .ca-compliance-grid{
            grid-template-columns:repeat(2,1fr);
        }
    }

    @media (max-width:768px){
        .ca-compliance-grid{
            grid-template-columns:1fr;
        }
    }
    .ca-compliance-item {
        height: 45px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1.5px solid #edf0f7;
        background: #fafbff;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
        position: relative;
        overflow: hidden;
    }
    .ca-compliance-item::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(99,102,241,0.04), rgba(124,58,237,0.02));
        opacity: 0;
        transition: opacity 0.2s;
    }
    .ca-compliance-item:hover {
        border-color: #c4b5fd;
        background: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(99,102,241,0.07);
    }
    .ca-compliance-item:hover::before { opacity: 1; }
    .ca-compliance-item.checked {
        border-color: #6366f1;
        background: #fafaff;
        box-shadow: 0 4px 20px rgba(99,102,241,0.1);
    }
    .ca-compliance-item.checked::before { opacity: 1; }

    .ca-comp-icon-wrap {
        width: 25px; height: 25px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        background: #f4f4fd;
        border: 1px solid #e8e8f8;
        transition: all 0.2s;
        position: relative;
        z-index: 1;
    }
    .ca-compliance-item.checked .ca-comp-icon-wrap {
        background: #eef2ff;
        border-color: #c4b5fd;
        box-shadow: 0 3px 8px rgba(99,102,241,0.15);
    }

    .ca-comp-text {
        flex: 1;
        min-width: 0;
        position: relative;
        z-index: 1;
    }
    .ca-comp-label {
        font-size: 0.81rem;
        font-weight: 600;
        color: #374151;
        line-height: 1.3;
        transition: color 0.2s;
    }
    .ca-compliance-item.checked .ca-comp-label { color: #3730a3; }

    .ca-comp-pts {
        font-size: 0.72rem;
        font-weight: 800;
        color: #10b981;
        white-space: nowrap;
        position: relative;
        z-index: 1;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 20px;
        padding: 2px 8px;
    }

    .ca-comp-check {
        width: 20px; height: 20px;
        border-radius: 50%;
        border: 2px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
        position: relative;
        z-index: 1;
    }
    .ca-compliance-item.checked .ca-comp-check {
        background: #6366f1;
        border-color: #6366f1;
        box-shadow: 0 2px 6px rgba(99,102,241,0.3);
    }
    .ca-comp-check i { font-size: 0.55rem; color: #fff; }

    /* ── Violation Groups ── */
    .ca-viol-group { margin-bottom: 22px; }
    .ca-viol-group:last-child { margin-bottom: 0; }
    .ca-viol-group-head {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #94a3b8;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f0f2fa;
    }
    .ca-viol-group-head::before {
        content: '';
        width: 3px; height: 14px;
        background: #e2e8f0;
        border-radius: 2px;
        display: inline-block;
        flex-shrink: 0;
    }

    .ca-viol-grid{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:8px;
    }

    @media (max-width:1200px){
        .ca-viol-grid{
            grid-template-columns:repeat(2,1fr);
        }
    }

    @media (max-width:768px){
        .ca-viol-grid{
            grid-template-columns:1fr;
        }
    }
    .ca-viol-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #f0f2fa;
        background: #fafbff;
        cursor: pointer;
        transition: all 0.18s ease;
        user-select: none;
    }
    .ca-viol-item:hover {
        border-color: #fecdd3;
        background: #fff5f5;
    }
    .ca-viol-item.checked {
        border-color: #fff1f2;
        background: #fff1f2;
    }
    .ca-viol-item input { display: none; }
    .ca-viol-text {
        font-size: 0.79rem;
        font-weight: 500;
        color: #4a5568;
        transition: color 0.18s;
        flex: 1;
        min-width: 0;
    }
    .ca-viol-item.checked .ca-viol-text { color: #e11d48; font-weight: 600; }
    .ca-viol-pts {
        font-size: 0.91rem;
        font-weight: 800;
        color: #f43f5e;
        white-space: nowrap;
        flex-shrink: 0;
        font-family: 'SF Mono', 'Consolas', monospace;
    }
    .ca-viol-dot {
        width: 16px; height: 16px;
        border-radius: 50%;
        border: 1.5px solid #e2e8f0;
        flex-shrink: 0;
        transition: all 0.18s;
        display: flex; align-items: center; justify-content: center;
    }
    .ca-viol-item.checked .ca-viol-dot {
        background: #f43f5e;
        border-color: #f43f5e;
    }
    .ca-viol-dot::after {
        content: '';
        width: 5px; height: 5px;
        border-radius: 50%;
        background: #fff;
        opacity: 0;
        transition: opacity 0.18s;
    }
    .ca-viol-item.checked .ca-viol-dot::after { opacity: 1; }

    /* ── Executive Select (Select2) ── */
    .ca-exec-opt {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ca-exec-opt-av {
        width: 26px; height: 26px;
        border-radius: 8px;
        background: linear-gradient(135deg, #6366f1, #7c3aed);
        color: #fff;
        font-size: 0.62rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .ca-exec-opt-name {
        font-size: 0.86rem;
        font-weight: 600;
        color: #1e1f2e;
    }

    #executiveSelect + .select2-container .select2-selection--single {
        height: 41px !important;
        border-radius: 12px !important;
        border: 1.5px solid #edf0f7 !important;
        background: #fafbff !important;
        display: flex;
        align-items: center;
        padding: 0 14px;
        transition: all 0.2s ease;
    }
    #executiveSelect + .select2-container--open .select2-selection--single,
    #executiveSelect + .select2-container .select2-selection--single:focus {
        border-color: #6366f1 !important;
        background: #fff !important;
        box-shadow: 0 0 0 4px rgba(99,102,241,0.1) !important;
    }
    #executiveSelect + .select2-container .select2-selection__rendered {
        padding: 0 !important;
        line-height: normal !important;
    }
    #executiveSelect + .select2-container .select2-selection__placeholder { color: #c4b5fd; }
    #executiveSelect + .select2-container .select2-selection__arrow {
        height: 44px !important;
        right: 10px !important;
    }
    .select2-dropdown {
        border-radius: 14px !important;
        border: 1.5px solid #edf0f7 !important;
        box-shadow: 0 12px 32px rgba(15,23,42,0.1) !important;
        overflow: hidden;
        padding: 6px !important;
    }
    .select2-search--dropdown {
        padding: 6px 6px 10px !important;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 10px !important;
        border: 1.5px solid #edf0f7 !important;
        background: #fafbff !important;
        padding: 8px 12px !important;
        font-size: 0.83rem !important;
        outline: none;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #6366f1 !important;
        background: #fff !important;
    }
    .select2-results__option {
        border-radius: 10px !important;
        padding: 8px 10px !important;
        margin-bottom: 2px;
    }
    .select2-results__option--highlighted {
        background: #f5f3ff !important;
        color: inherit !important;
    }
    .select2-results__option[aria-selected="true"] {
        background: #eef2ff !important;
    }

    /* ── File Upload Zone ── */
    .ca-upload-zone {
        border: 2px dashed #e0e7ff;
        border-radius: 14px;
        background: #fafaff;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    .ca-upload-zone:hover, .ca-upload-zone.drag-over {
        border-color: #6366f1;
        background: #f5f3ff;
    }
    .ca-upload-zone input[type=file] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .ca-upload-icon {
        width: 44px; height: 44px;
        margin: 0 auto 10px;
        background: #eef2ff;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        color: #6366f1;
    }
    .ca-upload-title { font-size: 0.84rem; font-weight: 700; color: #374151; margin-bottom: 3px; }
    .ca-upload-sub   { font-size: 0.72rem; color: #94a3b8; }

    /* ── Submit Bar ── */
    .ca-submit-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px 28px;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.055);
        border: 1px solid rgba(226,232,240,0.5);
        margin-top: 4px;
    }

    .btn-ca-cancel {
        display: inline-flex; align-items: center; gap: 8px;
        height: 46px; padding: 0 22px;
        background: #fff; border: 1.5px solid #e8eaf2;
        border-radius: 12px; color: #64748b;
        font-size: 0.88rem; font-weight: 600;
        cursor: pointer; text-decoration: none;
        transition: all 0.2s;
    }
    .btn-ca-cancel:hover { background: #f8f9fc; border-color: #cbd5e1; color: #374151; text-decoration: none; }

    .btn-ca-submit {
        display: inline-flex; align-items: center; gap: 9px;
        height: 46px; padding: 0 32px;
        background: linear-gradient(135deg, #00039f, #f1f1f1); border: none;
        border-radius: 12px; color: #fff;
        font-size: 0.92rem; font-weight: 700;
        cursor: pointer; letter-spacing: -0.01em;
        transition: all 0.2s;
        box-shadow: 0 4px 16px rgba(99,102,241,0.35);
    }
    .btn-ca-submit:hover {
        background: #4f46e5;
        box-shadow: 0 8px 28px rgba(99,102,241,0.45);
        transform: translateY(-2px);
    }
    .btn-ca-submit:active { transform: translateY(0); }

    /* ── RIGHT SIDEBAR ── */
    .ca-sidebar-sticky {
        position: sticky;
        top: 24px;
    }
    @media (max-width: 1199px) {
        .ca-sidebar-sticky { position: static; }
    }

    .ca-sidebar-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04),
                    inset 0 0 0 1px rgba(255, 255, 255, 0.4);
        margin-bottom: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .ca-sidebar-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.07),
                    inset 0 0 0 1px rgba(255, 255, 255, 0.6);
    }

    /* No-exec placeholder */
    .ca-no-exec {
        padding: 60px 28px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .ca-no-exec-blob {
        width: 80px; height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(124, 58, 237, 0.08) 100%);
        border-radius: 28px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem;
        color: #6366f1;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.05);
        position: relative;
        animation: blobPulse 3s infinite ease-in-out;
    }
    @keyframes blobPulse {
        0%, 100% { transform: scale(1); box-shadow: 0 8px 24px rgba(99, 102, 241, 0.05); }
        50% { transform: scale(1.06); box-shadow: 0 12px 32px rgba(99, 102, 241, 0.15); }
    }
    .ca-no-exec-title {
        font-size: 1.05rem; font-weight: 800; color: #1e1f2f; margin-bottom: 8px; letter-spacing: -0.01em;
    }
    .ca-no-exec-sub {
        font-size: 0.82rem; color: #64748b; line-height: 1.6; max-width: 260px;
    }

    /* Exec profile */
    .ca-exec-profile {
        padding: 28px 24px;
        text-align: center;
        border-bottom: 1px solid rgba(226, 232, 240, 0.4);
    }
    .ca-exec-av {
        width: 76px; height: 76px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #3b82f6 100%);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
        margin: 0 auto 16px;
        box-shadow: 0 10px 28px rgba(79, 70, 229, 0.25);
        letter-spacing: -0.02em;
        border: 3px solid #fff;
        outline: 1.5px solid rgba(99, 102, 241, 0.2);
    }
    .ca-exec-name {
        font-size: 1.15rem; font-weight: 850; color: #1e1f2f; letter-spacing: -0.02em; margin-bottom: 4px;
    }
    .ca-exec-sub {
        font-size: 0.76rem; color: #64748b; font-weight: 500;
    }

    .ca-exec-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 20px;
    }
    .ca-exec-stat {
        text-align: center;
        padding: 14px 8px;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.6);
        transition: all 0.2s ease;
    }
    .ca-exec-stat:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .ca-exec-stat-val {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }
    .ca-exec-stat-lbl {
        font-size: 0.58rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #94a3b8;
        font-weight: 700;
        margin-top: 4px;
    }

    /* Recent history */
    .ca-recent {
        padding: 24px;
    }
    .ca-recent-head {
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #94a3b8;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ca-recent-head::before {
        content: '';
        width: 6px; height: 6px;
        background: #3b82f6;
        border-radius: 50%;
    }
    .ca-history-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.4);
        margin-bottom: 8px;
        transition: all 0.2s;
    }
    .ca-history-row:last-child { margin-bottom: 0; }
    .ca-history-row:hover {
        background: #f1f5f9;
        transform: translateX(4px);
    }
    .ca-history-date {
        font-size: 0.78rem;
        color: #475569;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    /* Score Preview */
    .ca-score-card { padding: 28px 24px; }
    .ca-score-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    .ca-score-title {
        font-size: 0.92rem;
        font-weight: 800;
        color: #1e1f2f;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -0.01em;
    }
    .ca-score-title i {
        font-size: 1rem;
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        width: 28px; height: 28px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
    }
    .ca-spinner {
        width: 18px; height: 18px;
        border: 2px solid #e0e7ff;
        border-top-color: #6366f1;
        border-radius: 50%;
        animation: caSpinAnim 0.6s linear infinite;
    }
    @keyframes caSpinAnim { to { transform: rotate(360deg); } }

    /* Big score display */
    .ca-score-big {
        text-align: center;
        padding: 26px 20px;
        background: linear-gradient(135deg, rgba(245, 243, 255, 0.5) 0%, rgba(238, 242, 255, 0.5) 100%);
        border-radius: 18px;
        margin-bottom: 18px;
        border: 1.5px solid rgba(99, 102, 241, 0.15);
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.05), inset 0 0 0 1px #fff;
        position: relative;
        overflow: hidden;
    }
    .ca-score-big::before {
        content: '';
        position: absolute;
        width: 140px; height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.08), transparent 70%);
        top: -30px; right: -30px;
        pointer-events: none;
    }
    .ca-score-num {
        font-size: 3.25rem;
        font-weight: 900;
        letter-spacing: -0.04em;
        line-height: 1;
        transition: color 0.3s ease;
    }
    .ca-score-lbl {
        font-size: 0.66rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: #828fa9;
        font-weight: 700;
        margin-top: 8px;
    }

    /* Breakdown tiles */
    .ca-breakdown {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }
    .ca-breakdown-tile {
        text-align: center;
        padding: 16px 8px;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #fff;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01);
    }
    .ca-breakdown-tile:hover {
        transform: translateY(-2px);
    }
    .ca-tile-pos { border-color: rgba(16, 185, 129, 0.25); background: rgba(240, 253, 250, 0.7); }
    .ca-tile-neg { border-color: rgba(239, 68, 68, 0.25); background: rgba(254, 242, 242, 0.7); }
    .ca-tile-rec { border-color: rgba(59, 130, 246, 0.25); background: rgba(239, 246, 255, 0.7); }

    .ca-tile-pos:hover { border-color: rgba(16, 185, 129, 0.5); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08); }
    .ca-tile-neg:hover { border-color: rgba(239, 68, 68, 0.5); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08); }
    .ca-tile-rec:hover { border-color: rgba(59, 130, 246, 0.5); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08); }

    .ca-tile-val { font-size: 1.4rem; font-weight: 800; letter-spacing: -0.03em; line-height: 1.1; }
    .ca-tile-pos .ca-tile-val { color: #0f766e; }
    .ca-tile-neg .ca-tile-val { color: #be123c; }
    .ca-tile-rec .ca-tile-val { color: #1d4ed8; }
    .ca-tile-lbl { font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 700; margin-top: 6px; }

    /* KPI status in sidebar */
    .ca-kpi-result {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 16px;
        border-radius: 16px;
        font-size: 0.85rem;
        font-weight: 800;
        margin-bottom: 16px;
        letter-spacing: -0.01em;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
    }
    .ca-kpi-result.pass {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);
    }
    .ca-kpi-result.fail {
        background: #fff1f2;
        color: #b91c1c;
        border: 1px solid #f43f5e;
        box-shadow: 0 4px 12px rgba(244, 63, 94, 0.08);
    }

    .ca-score-note {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 12px;
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 500;
        border: 1px solid rgba(226, 232, 240, 0.6);
        line-height: 1.4;
    }

    /* ── Responsive ── */
    @media (max-width: 1200px) {
        .ca-compliance-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .ca-shell { padding: 16px 16px 40px; }
        .ca-step-body { padding: 18px 18px; }
        .ca-step-head { padding: 16px 18px 14px; }
        .ca-viol-grid { grid-template-columns: 1fr; }
        .ca-breakdown { grid-template-columns: repeat(3, 1fr); }
    }
    </style>
    @endpush

    @section('content')
    <div class="ca-shell">

    {{-- ══ PAGE HEADER ══════════════════════════════════════════ --}}
    <div class="ca-header">
        <div>
            <h1 class="ca-page-title">Enter Daily Audit</h1>
            <p class="ca-page-sub">Scores are calculated automatically — no manual point entry needed.</p>
        </div>
        <!-- <a href="{{ route('daily_audit.index') }}" class="btn-ca-back">
            <i class="fa-solid fa-arrow-left-long"></i> Back to History
        </a> -->
    </div>

    <form action="{{ route('daily_audit.store') }}" method="POST" enctype="multipart/form-data" id="auditForm">
    @csrf

    <div class="row g-4">

    {{-- ════════════════════════════════════════════════════════
        LEFT COLUMN
        ════════════════════════════════════════════════════════ --}}
    <div class="col-xl-8">

        {{-- ── STEP 1 ── --}}
        <div class="ca-step-card">
            <div class="ca-step-head">
                <div class="ca-step-num"><i class="fa-solid fa-user-check"></i></div>
                <div class="ca-step-info">
                    <div class="ca-step-title">Select Executive &amp; Date</div>
                    <div class="ca-step-desc">Choose who is being evaluated and for which date</div>
                </div>
            </div>
            <div class="ca-step-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="ca-label">Executive <span class="ca-required">*</span></label>
                        <select name="executive_id" id="executiveSelect" class="ca-select" required>
                            <option value=""></option>
                            @foreach($executives as $exec)
                            <option value="{{ $exec->id }}"
                                    data-strategy="{{ $exec->company->calculation_strategy }}"
                                    data-sub="{{ $exec->employee_id }} · {{ $exec->company->name }}"
                                    {{ old('executive_id') == $exec->id ? 'selected' : '' }}>
                                {{ $exec->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="ca-label">Audit Date <span class="ca-required">*</span></label>
                        <div class="ca-input-wrap">
                            <i class="ca-input-icon fa-regular fa-calendar" style="color:#c4b5fd;"></i>
                            <input type="date" name="audit_date" id="auditDate" class="ca-input"
                                value="{{ old('audit_date', now()->toDateString()) }}"
                                max="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── STEP 2 ── --}}
        <div class="ca-step-card">
            <div class="ca-step-head">
                <div class="ca-step-num"><i class="fa-solid fa-chart-line"></i></div>
                <div class="ca-step-info">
                    <div class="ca-step-title">Daily Activity Metrics</div>
                    <div class="ca-step-desc">Enter call counts, meetings and attendance figures</div>
                </div>
                <div id="kpiBanner" style="display:none;"></div>
            </div>
            <div class="ca-step-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="ca-label">Connected Calls <span class="ca-required">*</span></label>
                        <div class="ca-input-wrap">
                            <i class="ca-input-icon fa-solid fa-phone" style="color:#6366f1;"></i>
                            <input type="number" name="connected_calls" id="connectedCalls" class="ca-input"
                                min="0" max="999" value="{{ old('connected_calls', 0) }}" required>
                        </div>
                        <div class="ca-field-hint"><i class="fa-solid fa-circle-info"></i> KPI minimum: 40 calls</div>
                    </div>
                    <div class="col-md-4">
                        <label class="ca-label">Confirmed Meetings <span class="ca-required">*</span></label>
                        <div class="ca-input-wrap">
                            <i class="ca-input-icon fa-solid fa-calendar-check" style="color:#6366f1;"></i>
                            <input type="number" name="confirmed_meetings" id="confirmedMeetings" class="ca-input"
                                min="0" max="99" value="{{ old('confirmed_meetings', 0) }}" required>
                        </div>
                        <div class="ca-field-hint"><i class="fa-solid fa-circle-info"></i> KPI minimum: 1 meeting</div>
                    </div>
                    <div class="col-md-4">
                        <label class="ca-label">Meetings Attended</label>
                        <div class="ca-input-wrap">
                            <i class="ca-input-icon fa-solid fa-handshake" style="color:#10b981;"></i>
                            <input type="number" name="meetings_attended" id="meetingsAttended" class="ca-input"
                                min="0" max="99" value="{{ old('meetings_attended', 0) }}" required>
                        </div>
                        <div class="ca-field-hint"><i class="fa-solid fa-plus"></i> +4 pts per attended meeting</div>
                    </div>
                </div>

                {{-- FOCUZ Rolling Fields --}}
                <!-- <div id="focuzFields" style="display:none;">
                    <div class="ca-focuz-panel">
                        <div class="ca-focuz-panel-label">
                            <i class="fa-solid fa-rotate"></i> FOCUZ Rolling Meeting Fields
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="ca-label">Rolling Day #</label>
                                <input type="number" name="rolling_day" id="rollingDay" class="ca-input"
                                    min="1" max="365" value="{{ old('rolling_day') }}" placeholder="e.g. 3">
                                <div class="ca-field-hint">Day within the rolling window</div>
                            </div>
                            <div class="col-md-4">
                                <label class="ca-label">Window (Days)</label>
                                <input type="number" name="rolling_window_days" id="rollingWindowDays" class="ca-input"
                                    min="1" value="{{ old('rolling_window_days') }}" placeholder="e.g. 3">
                            </div>
                            <div class="col-md-4">
                                <label class="ca-label">Meetings in Window</label>
                                <input type="number" name="rolling_meeting_count" id="rollingMeetingCount" class="ca-input"
                                    min="0" value="{{ old('rolling_meeting_count') }}" placeholder="e.g. 2">
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        {{-- ── STEP 3 ── --}}
        <div class="ca-step-card">
            <div class="ca-step-head">
                <div class="ca-step-num"><i class="fa-solid fa-clipboard-check"></i></div>
                <div class="ca-step-info">
                    <div class="ca-step-title">Compliance &amp; Lead Management</div>
                    <div class="ca-step-desc">Toggle all items that were completed today</div>
                </div>
            </div>
            <div class="ca-step-body">
                @php
                $complianceItems = [
                    ['field'=>'crm_followup',               'label'=>'CRM Follow-up Completed',         'pts'=>'+3',  'icon'=>'fa-solid fa-database',       'color'=>'#6366f1'],
                    ['field'=>'crm_disposition_correct',    'label'=>'Correct CRM Disposition',          'pts'=>'+3',  'icon'=>'fa-solid fa-check-double',    'color'=>'#10b981'],
                    ['field'=>'first_contact_within_45min', 'label'=>'First Contact Within 45 Minutes',  'pts'=>'+3',  'icon'=>'fa-solid fa-stopwatch',       'color'=>'#f59e0b'],
                    ['field'=>'all_leads_followed_up',      'label'=>'100% Same-Day Follow-up',          'pts'=>'+3',  'icon'=>'fa-solid fa-list-check',      'color'=>'#0ea5e9'],
                    ['field'=>'warm_lead_converted',        'label'=>'Warm Lead Converted',              'pts'=>'+8',  'icon'=>'fa-solid fa-fire',            'color'=>'#f97316'],
                    ['field'=>'cold_lead_reactivated',      'label'=>'Freeze / Cold Lead Reactivated',   'pts'=>'+10', 'icon'=>'fa-solid fa-snowflake',       'color'=>'#06b6d4'],
                ];
                @endphp
                <div class="ca-compliance-grid">
                    @foreach($complianceItems as $item)
                    <label class="ca-compliance-item {{ old($item['field']) ? 'checked' : '' }}"
                        for="chk_{{ $item['field'] }}">
                        <input type="checkbox" name="{{ $item['field'] }}" id="chk_{{ $item['field'] }}"
                            class="ca-compliance-cb" value="1"
                            {{ old($item['field']) ? 'checked' : '' }}
                            style="display:none;">
                        <div class="ca-comp-icon-wrap">
                            <i class="{{ $item['icon'] }}" style="color:{{ $item['color'] }};"></i>
                        </div>
                        <div class="ca-comp-text">
                            <div class="ca-comp-label">{{ $item['label'] }}</div>
                        </div>
                        <span class="ca-comp-pts">{{ $item['pts'] }} pts</span>
                        <div class="ca-comp-check">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── STEP 4 ── --}}
        <div class="ca-step-card">
            <div class="ca-step-head">
                <div class="ca-step-num" style="background:linear-gradient(135deg,#f43f5e,#e11d48);"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="ca-step-info">
                    <div class="ca-step-title">Violations</div>
                    <div class="ca-step-desc">Select any applicable violations — deductions applied automatically</div>
                </div>
            </div>
            <div class="ca-step-body">

                {{-- TIMS Violations --}}
                <div id="timsViolations">
                    @php
                    $timsViolationGroups = [
                        'Call Violations' => [
                            ['code'=>'tims_neg_calls_33_39',       'label'=>'33–39 Calls',                   'pts'=>'-3'],
                            ['code'=>'tims_neg_calls_27_32',       'label'=>'27–32 Calls',                   'pts'=>'-6'],
                            ['code'=>'tims_neg_calls_15_26',       'label'=>'15–26 Calls',                   'pts'=>'-10'],
                            ['code'=>'tims_neg_calls_below_15',    'label'=>'Below 15 Calls',                'pts'=>'-15'],
                            ['code'=>'tims_neg_zero_calls',        'label'=>'Zero Calls',                    'pts'=>'-20'],
                            ['code'=>'tims_neg_less_60_attempts',  'label'=>'Less Than 60 Attempts',         'pts'=>'-8'],
                            ['code'=>'tims_neg_3_days_below_kpi',  'label'=>'3 Consecutive Days Below KPI',  'pts'=>'-15'],
                        ],
                        'Meeting Violations' => [
                            ['code'=>'tims_neg_zero_meetings',        'label'=>'Zero Meetings',                     'pts'=>'-6'],
                            ['code'=>'tims_neg_3_days_no_meeting',    'label'=>'3 Consecutive Days Without Meeting','pts'=>'-15'],
                            ['code'=>'tims_neg_meeting_not_logged',   'label'=>'Meeting Not Logged Properly',       'pts'=>'-5'],
                        ],
                        'Lead Management Violations' => [
                            ['code'=>'tims_neg_no_first_contact',     'label'=>'No First Contact',                     'pts'=>'-8'],
                            ['code'=>'tims_neg_no_followup',          'label'=>'No Follow-up',                         'pts'=>'-8/lead'],
                            ['code'=>'tims_neg_wrong_crm_disposition','label'=>'Wrong CRM Disposition',               'pts'=>'-6/lead'],
                            ['code'=>'tims_neg_warm_lead_freeze',     'label'=>'Warm Lead Marked Freeze Incorrectly',  'pts'=>'-12'],
                            ['code'=>'tims_neg_freeze_reason_missing','label'=>'Freeze Reason Missing',               'pts'=>'-6'],
                        ],
                        'Conduct & Integrity' => [
                            ['code'=>'tims_neg_false_attendance',       'label'=>'False Attendance Justification', 'pts'=>'-20'],
                            ['code'=>'tims_neg_crm_manipulation',       'label'=>'CRM Manipulation',               'pts'=>'-40'],
                            ['code'=>'tims_neg_communication_violation','label'=>'Communication Violation',        'pts'=>'-12'],
                            ['code'=>'tims_neg_customer_complaint',     'label'=>'Verified Customer Complaint',    'pts'=>'-20'],
                        ],
                        'Team Lead Accountability' => [
                            ['code'=>'tims_neg_team_calls_below_kpi',  'label'=>'Team Calls Below KPI',        'pts'=>'-12'],
                            ['code'=>'tims_neg_team_meeting_below_kpi','label'=>'Team Meeting KPI Below Target','pts'=>'-10'],
                            ['code'=>'tims_neg_failed_report_violation','label'=>'Failed to Report Violation',  'pts'=>'-20'],
                        ],
                    ];
                    @endphp
                    @foreach($timsViolationGroups as $group => $items)
                    <div class="ca-viol-group">
                        <div class="ca-viol-group-head">{{ $group }}</div>
                        <div class="ca-viol-grid">
                            @foreach($items as $v)
                            <label class="ca-viol-item {{ in_array($v['code'], old('violations', [])) ? 'checked' : '' }}">
                                <input type="checkbox" name="violations[]" value="{{ $v['code'] }}"
                                    class="ca-viol-cb"
                                    {{ in_array($v['code'], old('violations', [])) ? 'checked' : '' }}>
                                <div class="ca-viol-dot"></div>
                                <span class="ca-viol-text">{{ $v['label'] }}</span>
                                <span class="ca-viol-pts">{{ $v['pts'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- FOCUZ Violations --}}
                <div id="focuzViolations" style="display:none;">
                    @php
                    $focuzViolationGroups = [
                        'Call Violations' => [
                            ['code'=>'focuz_neg_calls_33_39',      'label'=>'33–39 Calls',                'pts'=>'-3'],
                            ['code'=>'focuz_neg_calls_27_32',      'label'=>'27–32 Calls',                'pts'=>'-6'],
                            ['code'=>'focuz_neg_calls_15_26',      'label'=>'15–26 Calls',                'pts'=>'-10'],
                            ['code'=>'focuz_neg_calls_below_15',   'label'=>'Below 15 Calls',             'pts'=>'-15'],
                            ['code'=>'focuz_neg_zero_calls',       'label'=>'Zero Calls',                 'pts'=>'-20'],
                            ['code'=>'focuz_neg_less_60_attempts', 'label'=>'Less Than 60 Attempts',      'pts'=>'-8'],
                            ['code'=>'focuz_neg_3_kpi_failures',   'label'=>'3 Consecutive KPI Failures', 'pts'=>'-15'],
                        ],
                        'Meeting / Rolling Violations' => [
                            ['code'=>'focuz_neg_rolling_checkpoint_failed','label'=>'Rolling Checkpoint Failed',       'pts'=>'-6'],
                            ['code'=>'focuz_neg_3_rolling_failures',       'label'=>'3 Consecutive Rolling Failures', 'pts'=>'-15'],
                            ['code'=>'focuz_neg_meeting_not_documented',   'label'=>'Meeting Not Documented',         'pts'=>'-5'],
                        ],
                        'Lead Management Violations' => [
                            ['code'=>'focuz_neg_no_first_contact',        'label'=>'No First Contact',                       'pts'=>'-8'],
                            ['code'=>'focuz_neg_no_followup',             'label'=>'No Follow-up',                           'pts'=>'-8/lead'],
                            ['code'=>'focuz_neg_wrong_crm',               'label'=>'Wrong CRM',                              'pts'=>'-6/lead'],
                            ['code'=>'focuz_neg_warm_lead_freeze',        'label'=>'Warm Lead Marked Freeze',                'pts'=>'-12'],
                            ['code'=>'focuz_neg_not_interested_no_reason','label'=>'Lead: Not Interested Without Reason',    'pts'=>'-6'],
                        ],
                        'Conduct & Integrity' => [
                            ['code'=>'focuz_neg_false_justification',     'label'=>'False Justification',        'pts'=>'-20'],
                            ['code'=>'focuz_neg_crm_manipulation',        'label'=>'CRM Manipulation',            'pts'=>'-40'],
                            ['code'=>'focuz_neg_communication_violation', 'label'=>'Communication Violation',    'pts'=>'-12'],
                            ['code'=>'focuz_neg_customer_complaint',      'label'=>'Verified Customer Complaint','pts'=>'-20'],
                        ],
                        'Team Lead Accountability' => [
                            ['code'=>'focuz_neg_team_calls_below_kpi',   'label'=>'Team Calls Below KPI',        'pts'=>'-12'],
                            ['code'=>'focuz_neg_team_meeting_below_kpi', 'label'=>'Team Meeting KPI Below Target','pts'=>'-10'],
                            ['code'=>'focuz_neg_failed_report_violation','label'=>'Failed to Report Violation',  'pts'=>'-20'],
                        ],
                    ];
                    @endphp
                    @foreach($focuzViolationGroups as $group => $items)
                    <div class="ca-viol-group">
                        <div class="ca-viol-group-head">{{ $group }}</div>
                        <div class="ca-viol-grid">
                            @foreach($items as $v)
                            <label class="ca-viol-item {{ in_array($v['code'], old('violations', [])) ? 'checked' : '' }}">
                                <input type="checkbox" name="violations[]" value="{{ $v['code'] }}"
                                    class="ca-viol-cb"
                                    {{ in_array($v['code'], old('violations', [])) ? 'checked' : '' }}>
                                <div class="ca-viol-dot"></div>
                                <span class="ca-viol-text">{{ $v['label'] }}</span>
                                <span class="ca-viol-pts">{{ $v['pts'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- ── STEP 5 ── --}}
        <div class="ca-step-card">
            <div class="ca-step-head">
                <div class="ca-step-num" style="background:linear-gradient(135deg,#64748b,#475569);"><i class="fa-solid fa-comment-dots"></i></div>
                <div class="ca-step-info">
                    <div class="ca-step-title">Evidence &amp; Remarks</div>
                    <div class="ca-step-desc">Optional — attach supporting files or notes</div>
                </div>
            </div>
            <div class="ca-step-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ca-label">Evidence File</label>
                        <div class="ca-upload-zone" id="uploadZone">
                            <input type="file" name="evidence" accept="image/*,.pdf,video/mp4" id="evidenceFile">
                            <div class="ca-upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <div class="ca-upload-title" id="uploadTitle">Drop file or click to browse</div>
                            <div class="ca-upload-sub">JPG, PNG, PDF, MP4 · Max 20 MB</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="ca-label">Remarks</label>
                        <textarea name="remarks" class="ca-textarea" rows="5"
                                placeholder="Any notes about today's performance…">{{ old('remarks') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SUBMIT BAR ── --}}
        <div class="ca-submit-bar">
            <a href="{{ route('daily_audit.index') }}" class="btn-ca-cancel">
                <i class="fa-solid fa-xmark"></i> Cancel
            </a>
            <button type="submit" class="btn-ca-submit" id="submitBtn">
                <i class="fa-solid fa-paper-plane"></i> Submit Audit
            </button>
        </div>

    </div>{{-- /col-xl-8 --}}

    {{-- ════════════════════════════════════════════════════════
        RIGHT SIDEBAR
        ════════════════════════════════════════════════════════ --}}
    <div class="col-xl-4">
    <div class="ca-sidebar-sticky">
        <div class="ca-sidebar-card" id="noExecCard">
            <div class="ca-no-exec">
                <div class="ca-no-exec-blob"><i class="fa-solid fa-user-magnifying-glass"></i></div>
                <div class="ca-no-exec-title">No executive selected</div>
                <p class="ca-no-exec-sub">Select an executive above to see their profile, history, and live score preview.</p>
            </div>
        </div>

        {{-- Executive Profile Card --}}
        <div class="ca-sidebar-card" id="execProfileCard" style="display:none;">
            <div class="ca-exec-profile">
                <div class="ca-exec-av" id="execAvatar">—</div>
                <div class="ca-exec-name" id="execName">—</div>
                <div class="ca-exec-sub" id="execSub">—</div>
                <div class="ca-exec-stats">
                    <div class="ca-exec-stat">
                        <div class="ca-exec-stat-val" id="execScore">—</div>
                        <div class="ca-exec-stat-lbl">Total Score</div>
                    </div>
                    <div class="ca-exec-stat">
                        <div class="ca-exec-stat-val" id="execTier">—</div>
                        <div class="ca-exec-stat-lbl">Tier</div>
                    </div>
                    <div class="ca-exec-stat">
                        <div class="ca-exec-stat-val" id="execRank">—</div>
                        <div class="ca-exec-stat-lbl">Rank</div>
                    </div>
                </div>
            </div>
            <div class="ca-recent">
                <div class="ca-recent-head">Last 7 Days</div>
                <div id="historyList"><div style="font-size:.75rem;color:#94a3b8;text-align:center;padding:10px;">Loading…</div></div>
            </div>
        </div>

        {{-- Score Preview Card --}}
        <div class="ca-sidebar-card" id="scorePreviewCard" style="display:none;">
            <div class="ca-score-card">
                <div class="ca-score-head">
                    <div class="ca-score-title">
                        <i class="fa-solid fa-calculator" style="color:#6366f1;"></i> Score Preview
                    </div>
                    <div class="ca-spinner" id="previewSpinner" style="display:none;"></div>
                </div>

                <div class="ca-score-big">
                    <div class="ca-score-num" id="previewTotal">+0</div>
                    <div class="ca-score-lbl">Estimated Net Score</div>
                </div>

                <div class="ca-breakdown">
                    <div class="ca-breakdown-tile ca-tile-pos">
                        <div class="ca-tile-val" id="previewPos">+0</div>
                        <div class="ca-tile-lbl">Positive</div>
                    </div>
                    <div class="ca-breakdown-tile ca-tile-neg">
                        <div class="ca-tile-val" id="previewNeg">-0</div>
                        <div class="ca-tile-lbl">Negative</div>
                    </div>
                    <div class="ca-breakdown-tile ca-tile-rec">
                        <div class="ca-tile-val" id="previewRec">+0</div>
                        <div class="ca-tile-lbl">Recovery</div>
                    </div>
                </div>

                <div id="previewKpi" style="display:none;"></div>

                <div class="ca-score-note">
                    <i class="fa-solid fa-circle-info" style="color:#c4b5fd;flex-shrink:0;"></i>
                    Preview only — final score calculated server-side on submit.
                </div>
            </div>
        </div>

    </div>{{-- /ca-sidebar-sticky --}}
    </div>{{-- /col-xl-4 --}}

    </div>{{-- /row --}}
    </form>

    </div>{{-- /ca-shell --}}
    @endsection

    @push('scripts')
    <style>
    @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <script>
    // ── Compliance Toggles ───────────────────────────────────────
    document.querySelectorAll('.ca-compliance-item').forEach(function(item) {
        const cb = item.querySelector('.ca-compliance-cb');
        function sync() {
            item.classList.toggle('checked', cb.checked);
        }
        sync();
        cb.addEventListener('change', function() { sync(); schedulePreview(); });
        // The label click already toggles the hidden checkbox natively
    });

    // ── Violation Toggles ────────────────────────────────────────
    document.querySelectorAll('.ca-viol-item').forEach(function(item) {
        const cb = item.querySelector('.ca-viol-cb');
        if (cb.checked) item.classList.add('checked');
        cb.addEventListener('change', function() {
            item.classList.toggle('checked', cb.checked);
            schedulePreview();
        });
        // The label click already toggles the hidden checkbox natively
    });

    // ── Select2 ──────────────────────────────────────────────────
    function execInitials(name) {
        return name.trim().split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    }

    function execOptionTemplate(state) {
        if (!state.id) return state.text;
        const $wrap = $(
            '<span class="ca-exec-opt">' +
                '<span class="ca-exec-opt-av">' + execInitials(state.text) + '</span>' +
                '<span class="ca-exec-opt-name"></span>' +
            '</span>'
        );
        $wrap.find('.ca-exec-opt-name').text(state.text);
        return $wrap;
    }

    $(document).ready(function() {
        $('#executiveSelect').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search executive…',
            templateResult: execOptionTemplate,
            templateSelection: execOptionTemplate
        });
    });

    // ── Executive Select ─────────────────────────────────────────
    let currentStrategy = 'tims';

    $('#executiveSelect').on('change', function() {
        const execId = this.value;
        if (!execId) {
            document.getElementById('noExecCard').style.display = '';
            document.getElementById('execProfileCard').style.display = 'none';
            document.getElementById('scorePreviewCard').style.display = 'none';
            return;
        }
        fetch(`/api/executives/${execId}/data`)
            .then(r => r.json())
            .then(data => {
                const e = data.executive;
                currentStrategy = e.company_strategy;

                document.getElementById('noExecCard').style.display = 'none';
                document.getElementById('execProfileCard').style.display = '';
                document.getElementById('scorePreviewCard').style.display = '';

                // Profile
                const words    = e.name.trim().split(' ');
                const initials = words.map(w => w[0]).slice(0,2).join('').toUpperCase();
                document.getElementById('execAvatar').textContent = initials;
                document.getElementById('execName').textContent   = e.name;
                document.getElementById('execSub').textContent    = `${e.employee_id} · ${e.company_name} · ${e.zone_name}`;
                document.getElementById('execScore').textContent  = e.current_score?.toLocaleString() ?? '—';
                document.getElementById('execTier').textContent   = e.tier_label ?? e.current_tier ?? '—';
                document.getElementById('execRank').textContent   = `#${e.rank}`;

                // Strategy toggle
                document.getElementById('timsViolations').style.display  = currentStrategy === 'focuz' ? 'none' : '';
                document.getElementById('focuzViolations').style.display = currentStrategy === 'focuz' ? '' : 'none';
                document.getElementById('focuzFields').style.display     = currentStrategy === 'focuz' ? '' : 'none';

                // Recent history
                const list = document.getElementById('historyList');
                list.innerHTML = '';
                if (!data.recent_audits || data.recent_audits.length === 0) {
                    list.innerHTML = '<div style="font-size:.75rem;color:#94a3b8;text-align:center;padding:14px;">No recent audits</div>';
                } else {
                    data.recent_audits.forEach(a => {
                        const score = parseInt(a.final_score);
                        const isPositive = score >= 0;
                        const displayScore = (isPositive ? '+' : '') + score;
                        const badgeStyle = isPositive 
                            ? 'background:rgba(16,185,129,0.08);color:#059669;border:1px solid rgba(16,185,129,0.15);' 
                            : 'background:rgba(239,68,68,0.08);color:#e11d48;border:1px solid rgba(239,68,68,0.15);';
                        list.innerHTML += `
                        <div class="ca-history-row">
                            <span class="ca-history-date"><i class="fa-regular fa-calendar-check" style="margin-right:8px;color:#94a3b8;font-size:0.8rem;"></i>${a.audit_date}</span>
                            <span class="ca-history-score" style="padding:3px 10px;border-radius:100px;font-size:0.78rem;font-weight:700;min-width:48px;text-align:center;${badgeStyle}">${displayScore}</span>
                        </div>`;
                    });
                }
                schedulePreview();
            })
            .catch(err => console.error(err));
    });

    // ── Live Score Preview ───────────────────────────────────────
    let previewTimer = null;
    function schedulePreview() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(fetchPreview, 600);
    }

    function collectFormData() {
        const data = {
            executive_id:          document.getElementById('executiveSelect').value,
            audit_date:            document.getElementById('auditDate').value,
            connected_calls:       document.getElementById('connectedCalls').value,
            confirmed_meetings:    document.getElementById('confirmedMeetings').value,
            meetings_attended:     document.getElementById('meetingsAttended').value,
            rolling_day:           document.getElementById('rollingDay')?.value ?? '',
            rolling_window_days:   document.getElementById('rollingWindowDays')?.value ?? '',
            rolling_meeting_count: document.getElementById('rollingMeetingCount')?.value ?? '',
            violations: [],
        };
        document.querySelectorAll('.ca-viol-cb:checked').forEach(c => data.violations.push(c.value));
        document.querySelectorAll('.ca-compliance-cb').forEach(c => { data[c.name] = c.checked ? 1 : 0; });
        return data;
    }

    function fetchPreview() {
        const execId = document.getElementById('executiveSelect').value;
        if (!execId) return;
        const spinner = document.getElementById('previewSpinner');
        spinner.style.display = '';

        fetch('/api/daily-audit/preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(collectFormData())
        })
        .then(r => r.json())
        .then(data => {
            spinner.style.display = 'none';
            if (data.error) return;

            const total = parseInt(data.final_score ?? 0);
            const totalEl = document.getElementById('previewTotal');
            totalEl.textContent  = (total >= 0 ? '+' : '') + total;
            totalEl.style.color  = total >= 0 ? '#4f46e5' : '#e11d48';

            document.getElementById('previewPos').textContent = '+' + (data.positive_points ?? 0);
            document.getElementById('previewNeg').textContent = '-' + Math.abs(data.negative_points ?? 0);
            document.getElementById('previewRec').textContent = '+' + (data.recovery_points ?? 0);

            const kpiEl = document.getElementById('previewKpi');
            kpiEl.style.display = '';
            if (data.kpi_passed) {
                kpiEl.className  = 'ca-kpi-result pass';
                kpiEl.innerHTML  = '<i class="fa-solid fa-circle-check"></i> KPI Passed';
            } else {
                kpiEl.className  = 'ca-kpi-result fail';
                kpiEl.innerHTML  = '<i class="fa-solid fa-circle-xmark"></i> KPI Failed';
            }

            // KPI banner in step 2
            const calls   = parseInt(document.getElementById('connectedCalls').value);
            const meetings = parseInt(document.getElementById('confirmedMeetings').value);
            const banner  = document.getElementById('kpiBanner');
            banner.style.display = '';
            if (calls >= 40 && meetings >= 1) {
                banner.className  = 'ca-kpi-tag ca-kpi-pass';
                banner.innerHTML  = '<i class="fa-solid fa-check"></i> KPI Met';
            } else {
                banner.className  = 'ca-kpi-tag ca-kpi-fail';
                banner.innerHTML  = '<i class="fa-solid fa-xmark"></i> KPI Not Met';
            }
        })
        .catch(() => { spinner.style.display = 'none'; });
    }

    // Trigger preview on any metric change
    ['connectedCalls','confirmedMeetings','meetingsAttended','auditDate',
    'rollingDay','rollingWindowDays','rollingMeetingCount'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', schedulePreview);
    });

    // ── File Upload Label ────────────────────────────────────────
    document.getElementById('evidenceFile')?.addEventListener('change', function() {
        const name = this.files[0]?.name ?? 'Drop file or click to browse';
        document.getElementById('uploadTitle').textContent = name;
    });

    // Drag-over styling
    const zone = document.getElementById('uploadZone');
    zone?.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone?.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone?.addEventListener('drop', () => zone.classList.remove('drag-over'));

    // ── Submit Spinner ────────────────────────────────────────────
    document.getElementById('auditForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = `<span style="width:16px;height:16px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;display:inline-block;vertical-align:middle;margin-right:8px;"></span>Saving…`;
    });
    </script>
    @endpush