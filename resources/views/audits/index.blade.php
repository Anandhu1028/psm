@extends('layouts.app')

@section('title', '')
@section('page_title', '')
@section('page_subtitle', '')

@section('content')

{{-- ===== TOP BAR ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold m-0 text-white" style="font-size: 18px;">Weekly  Audit Log</h5>
        <p class="text-secondary small m-0 mt-1">Verify CRM entries and call records. Failed audits trigger automatic point deductions.</p>
    </div>
    
</div>

{{-- ===== STAT CARDS ===== --}}
@php
    $totalAudits   = $audits->total();
    $passCount     = $audits->getCollection()->where('audit_result','pass')->count();
    $failCount     = $audits->getCollection()->where('audit_result','fail')->count();
    $verifiedCrm   = $audits->getCollection()->where('crm_entry_verified', true)->count();
    $passRate      = $totalAudits > 0 ? round(($passCount / $audits->getCollection()->count()) * 100) : 0;
@endphp

<div class="row g-3 mb-4">

    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-cyan">
            <div class="tims-stat-label" style="color:#22d3ee;">
                <i class="fa-solid fa-clipboard-list"></i> Total Audits
            </div>
            <div class="tims-stat-value" style="color:#22d3ee;">{{ $totalAudits }}</div>
            <div class="tims-stat-sub">all records</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-green">
            <div class="tims-stat-label" style="color:#4ade80;">
                <i class="fa-solid fa-circle-check"></i> Passed
            </div>
            <div class="tims-stat-value" style="color:#4ade80;">{{ $passCount }}</div>
            <div class="tims-stat-sub">audits passed</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-red">
            <div class="tims-stat-label" style="color:#f87171;">
                <i class="fa-solid fa-circle-xmark"></i> Failed
            </div>
            <div class="tims-stat-value" style="color:#f87171;">{{ $failCount }}</div>
            <div class="tims-stat-sub">points deducted</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-purple">
            <div class="tims-stat-label" style="color:#a78bfa;">
                <i class="fa-solid fa-percent"></i> Pass Rate
            </div>
            <div class="tims-stat-value" style="color:#c4b5fd;">{{ $passRate }}%</div>
            <div class="tims-stat-sub">this page</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-amber">
            <div class="tims-stat-label" style="color:#fbbf24;">
                <i class="fa-solid fa-database"></i> CRM Verified
            </div>
            <div class="tims-stat-value" style="color:#fbbf24;">{{ $verifiedCrm }}</div>
            <div class="tims-stat-sub">entries confirmed</div>
        </div>
    </div>

</div>

{{-- ===== SECTION BAR ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <h6 class="fw-semibold text-white m-0" style="font-size:15px;">All Audit Records</h6>
        <span class="badge rounded-pill"
            style="background:rgba(99,102,241,0.15);color:#818cf8;font-size:12px;padding:3px 10px;border:1px solid rgba(99,102,241,0.2);">
            {{ $audits->total() }}
        </span>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="position-relative">
            <i class="fa-solid fa-magnifying-glass position-absolute text-secondary"
                style="top:50%;left:10px;transform:translateY(-50%);font-size:11px;pointer-events:none;"></i>
            <input type="text" id="auditSearch" class="form-control form-control-sm ps-4"
                placeholder="Search executive, lead…"
                style="width:200px;background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);color:#fff;font-size:12px;">
        </div>

        <select id="resultFilter" class="form-select form-select-sm"
            style="width:120px;background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);color:#9ca3af;font-size:12px;">
            <option value="">All Results</option>
            <option value="pass">Pass</option>
            <option value="fail">Fail</option>
        </select>

      
        <button class="btn btn-sm btn-primary rounded-2" id="openAuditModal" data-bs-toggle="modal" data-bs-target="#newAuditModal" style="font-size:12px;">
            <i class="fa-solid fa-download me-1"></i>Record New Audit
        </button> 
    </div>
</div>

{{-- ===== TABLE ===== --}} 
<div class="tims-roster-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="auditTable">
            <thead>
                <tr>
                    <th>Audit Date</th>
                    <th>Executive</th>
                    <th>Lead ID</th>
                    <th class="text-center">CRM Verified</th>
                    <th>Call Status</th>
                    <th>Result</th>
                    <th>Audited By</th>
                    <th>Remarks</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $audit)
                @php
                    $callColors = [
                        'verified'     => ['bg'=>'rgba(74,222,128,.12)',  'color'=>'#4ade80',  'border'=>'rgba(74,222,128,.25)'],
                        'discrepancy'  => ['bg'=>'rgba(251,191,36,.12)',  'color'=>'#fbbf24',  'border'=>'rgba(251,191,36,.25)'],
                        'fake_lead'    => ['bg'=>'rgba(248,113,113,.12)', 'color'=>'#f87171',  'border'=>'rgba(248,113,113,.25)'],
                    ];
                    $cc = $callColors[$audit->call_verification_status] ?? ['bg'=>'rgba(156,163,175,.1)','color'=>'#9ca3af','border'=>'rgba(156,163,175,.2)'];
                @endphp
                <tr class="audit-row">

                    {{-- Audit Date --}}
                    <td>
                        <div class="fw-semibold text-white" style="font-size:13px;">
                            {{ $audit->audit_date->format('d M Y') }}
                        </div>
                        <small class="text-secondary" style="font-size:11px;">{{ $audit->created_at->diffForHumans() }}</small>
                    </td>

                    {{-- Executive --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                style="width:32px;height:32px;background:linear-gradient(135deg,#6d5ce7,#111827);font-size:11px;border:1px solid rgba(255,255,255,0.1);">
                                {{ strtoupper(substr($audit->executive->name, 0, 2)) }}
                            </div>
                            <div>
                                <a href="{{ route('executives.scorecard', $audit->executive_id) }}"
                                    class="fw-semibold text-white text-decoration-none audit-exec-link" style="font-size:13px;">
                                    {{ $audit->executive->name }}
                                </a>
                                <div class="text-secondary" style="font-size:11px;">{{ $audit->executive->employee_id }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Lead ID --}}
                    <td>
                        <span class="font-monospace"
                            style="font-size:11px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:4px;padding:3px 8px;color:#9ca3af;">
                            {{ $audit->lead_identifier }}
                        </span>
                    </td>

                    {{-- CRM Verified --}}
                    <td class="text-center">
                        @if($audit->crm_entry_verified)
                            <i class="fa-solid fa-circle-check text-success" style="font-size:16px;" title="CRM Verified"></i>
                        @else
                            <i class="fa-solid fa-circle-xmark text-danger" style="font-size:16px;opacity:.6;" title="Not Verified"></i>
                        @endif
                    </td>

                    {{-- Call Status --}}
                    <td>
                        <span class="badge text-capitalize"
                            style="background:{{ $cc['bg'] }};color:{{ $cc['color'] }};border:1px solid {{ $cc['border'] }};font-size:11px;padding:4px 10px;border-radius:20px;">
                            {{ str_replace('_', ' ', $audit->call_verification_status) }}
                        </span>
                    </td>

                    {{-- Result --}}
                    <td>
                        @if($audit->audit_result === 'pass')
                            <span class="badge fw-bold"
                                style="background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.3);font-size:11px;padding:4px 12px;border-radius:20px;">
                                <i class="fa-solid fa-check me-1"></i>PASS
                            </span>
                        @else
                            <span class="badge fw-bold"
                                style="background:rgba(248,113,113,.15);color:#f87171;border:1px solid rgba(248,113,113,.3);font-size:11px;padding:4px 12px;border-radius:20px;">
                                <i class="fa-solid fa-xmark me-1"></i>FAIL
                            </span>
                        @endif
                    </td>

                    {{-- Audited By --}}
                    <td>
                        <span class="text-white" style="font-size:13px;">{{ $audit->auditor->name ?? '—' }}</span>
                    </td>

                    {{-- Remarks --}}
                    <td>
                        <span class="text-secondary" style="font-size:12px;">
                            {{ $audit->remarks ? Str::limit($audit->remarks, 40) : '—' }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-link text-secondary p-0 border-0 tims-action-dots-btn"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('audits.show', $audit->id) }}">
                                        <i class="fa-regular fa-eye me-2 text-info"></i> View Details
                                    </a>
                                </li>
                                @can('approve_audits')
                                <li>
                                    <a class="dropdown-item" href="{{ route('audits.edit', $audit->id) }}">
                                        <i class="fa-regular fa-pen-to-square me-2 text-primary"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('audits.destroy', $audit->id) }}" method="POST" class="m-0"
                                        onsubmit="return confirm('Delete this audit record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="fa-regular fa-trash-can me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="fa-solid fa-clipboard fa-2x mb-3 d-block text-secondary"></i>
                        <span style="color:#5e6273;font-size:13px;">No audit records found.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($audits->hasPages())
    <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top border-secondary border-opacity-10">
        <small class="text-secondary" style="font-size:12px;">
            Page {{ $audits->currentPage() }} of {{ $audits->lastPage() }}
        </small>
        <div>{{ $audits->links() }}</div>
    </div>
    @endif
</div>


{{-- ===================================================== --}}
{{-- MODAL: Record New Audit                                --}}
{{-- ===================================================== --}}
<div class="modal fade" id="newAuditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content tims-modal-content">

            {{-- Header --}}
            <div class="modal-header tims-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="tims-modal-icon" style="background:rgba(251,191,36,0.15);border-color:rgba(251,191,36,0.3);">
                        <i class="fa-solid fa-clipboard-check" style="color:#fbbf24;font-size:15px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white m-0" style="font-size:16px;">Submit Audit Verification</h5>
                        <p class="text-secondary m-0" style="font-size:12px;">Failed audits automatically deduct points from the executive's score</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close" style="font-size:12px;opacity:.5;"></button>
            </div>

            {{-- Body --}}
            <form method="POST" action="{{ route('audits.store') }}">
                @csrf
                <div class="modal-body tims-modal-body">
                    <div class="row g-3">

                        {{-- Executive --}}
                        <div class="col-md-6">
                            <label class="tims-modal-label">Executive <span class="text-danger">*</span></label>
                            <select name="executive_id" class="tims-modal-input @error('executive_id') is-invalid @enderror" required>
                                <option value="">Select Executive...</option>
                                @foreach($executives ?? [] as $exec)
                                    <option value="{{ $exec->id }}" {{ old('executive_id') == $exec->id ? 'selected' : '' }}>
                                        {{ $exec->name }} ({{ $exec->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('executive_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Lead Identifier --}}
                        <div class="col-md-6">
                            <label class="tims-modal-label">Lead Identifier / CRM ID <span class="text-danger">*</span></label>
                            <input type="text" name="lead_identifier"
                                class="tims-modal-input @error('lead_identifier') is-invalid @enderror"
                                placeholder="e.g. CRM-00123"
                                value="{{ old('lead_identifier') }}" required>
                            @error('lead_identifier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Audit Date --}}
                        <div class="col-md-6">
                            <label class="tims-modal-label">Audit Date <span class="text-danger">*</span></label>
                            <input type="date" name="audit_date"
                                class="tims-modal-input @error('audit_date') is-invalid @enderror"
                                value="{{ old('audit_date', now()->format('Y-m-d')) }}" required>
                            @error('audit_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Call Verification Status --}}
                        <div class="col-md-6">
                            <label class="tims-modal-label">Call Verification Status <span class="text-danger">*</span></label>
                            <select name="call_verification_status"
                                class="tims-modal-input @error('call_verification_status') is-invalid @enderror" required>
                                <option value="verified"    {{ old('call_verification_status','verified') === 'verified'    ? 'selected' : '' }}>
                                    ✅  Verified — Call confirmed
                                </option>
                                <option value="discrepancy" {{ old('call_verification_status') === 'discrepancy' ? 'selected' : '' }}>
                                    ⚠️  Discrepancy — Mismatch found
                                </option>
                                <option value="fake_lead"   {{ old('call_verification_status') === 'fake_lead'   ? 'selected' : '' }}>
                                    ❌  Fake Lead — Invalid entry
                                </option>
                            </select>
                            @error('call_verification_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- CRM Entry Verified Checkbox --}}
                        <div class="col-12">
                            <div class="tims-crm-check-row">
                                <input class="form-check-input m-0" type="checkbox" name="crm_entry_verified"
                                    id="crmVerified" value="1" {{ old('crm_entry_verified') ? 'checked' : '' }}>
                                <label class="d-flex align-items-center gap-2 m-0" for="crmVerified" style="cursor:pointer;">
                                    <i class="fa-solid fa-database" style="color:#22d3ee;font-size:13px;"></i>
                                    <span class="text-white" style="font-size:13px;">
                                        CRM Entry Verified — All fields correctly filled in the CRM system
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Audit Result --}}
                        <div class="col-md-6">
                            <label class="tims-modal-label">Audit Result <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2 mt-1">
                                {{-- PASS --}}
                                <label class="tims-result-option tims-result-pass flex-fill" id="passOption">
                                    <input type="radio" name="audit_result" value="pass"
                                        {{ old('audit_result','pass') === 'pass' ? 'checked' : '' }}
                                        onchange="updateResultUI()" style="display:none;">
                                    <i class="fa-solid fa-circle-check" style="font-size:14px;"></i>
                                    <span class="fw-bold">PASS</span>
                                </label>
                                {{-- FAIL --}}
                                <label class="tims-result-option tims-result-fail flex-fill" id="failOption">
                                    <input type="radio" name="audit_result" value="fail"
                                        {{ old('audit_result') === 'fail' ? 'checked' : '' }}
                                        onchange="updateResultUI()" style="display:none;">
                                    <i class="fa-solid fa-circle-xmark" style="font-size:14px;"></i>
                                    <div>
                                        <div class="fw-bold">FAIL</div>
                                        <div style="font-size:10px;opacity:.8;">Points deduction applied</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Violation Type --}}
                        <div class="col-md-6">
                            <label class="tims-modal-label">Violation Type <span class="text-secondary fw-normal">(if applicable)</span></label>
                            <input type="text" name="violation_type"
                                class="tims-modal-input @error('violation_type') is-invalid @enderror"
                                placeholder="e.g. Missing CRM entry"
                                value="{{ old('violation_type') }}">
                            @error('violation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Remarks --}}
                        <div class="col-12">
                            <label class="tims-modal-label">Auditor Remarks</label>
                            <textarea name="remarks" rows="3"
                                class="tims-modal-input @error('remarks') is-invalid @enderror"
                                placeholder="Add any notes or observations about this audit…"
                                style="resize:vertical;">{{ old('remarks') }}</textarea>
                            @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer tims-modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4"
                        data-bs-dismiss="modal" style="font-size:13px;">Cancel</button>
                    <button type="submit" class="btn rounded-3 px-5 fw-semibold"
                        style="background:#f59e0b;color:#000;font-size:13px;border:none;">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Submit Audit Record
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection


@section('styles')
<style>
    /* ── Stat Cards ───────────────────────── */
    .tims-stat-card {
        border-radius: 12px;
        padding: .65rem 1rem;
        height: 100%;
        border: 1px solid rgba(255,255,255,0.07);
        overflow: hidden;
    }
    .tims-stat-cyan    { background: linear-gradient(135deg, rgba(6,182,212,.18) 0%, rgba(8,145,178,.06) 100%);   border-color: rgba(6,182,212,.25); }
    .tims-stat-green   { background: linear-gradient(135deg, rgba(34,197,94,.18) 0%, rgba(22,163,74,.06) 100%);   border-color: rgba(34,197,94,.25); }
    .tims-stat-red     { background: linear-gradient(135deg, rgba(248,113,113,.18) 0%, rgba(220,38,38,.06) 100%); border-color: rgba(248,113,113,.25); }
    .tims-stat-purple  { background: linear-gradient(135deg, rgba(167,139,250,.18) 0%, rgba(139,92,246,.06) 100%); border-color: rgba(167,139,250,.25); }
    .tims-stat-amber   { background: linear-gradient(135deg, rgba(245,158,11,.18) 0%, rgba(217,119,6,.06) 100%);  border-color: rgba(245,158,11,.25); }

    .tims-stat-label { font-size:10.5px; font-weight:600; text-transform:uppercase; letter-spacing:.07em; display:flex; align-items:center; gap:5px; margin-bottom:5px; }
    .tims-stat-value { font-size:24px; font-weight:700; line-height:1; }
    .tims-stat-sub   { font-size:11px; color:#5e6273; margin-top:3px; }

    /* ── Search/Filter ────────────────────── */
    #auditSearch::placeholder { color:#6b7280; }
    #auditSearch:focus, #resultFilter:focus {
        background: rgba(255,255,255,0.06) !important;
        border-color: rgba(99,102,241,0.4) !important;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        color: #fff; outline: none;
    }
    #resultFilter option { background:#0f0f17; color:#d1d5db; }

    /* ── Executive link hover ─────────────── */
    .audit-exec-link:hover { color:#818cf8 !important; }

    /* ── Modal ────────────────────────────── */
    .tims-modal-content {
        background: #0d0d18;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
    }
    .tims-modal-header {
        border-bottom: 1px solid rgba(255,255,255,0.06);
        padding: 1.1rem 1.5rem;
    }
    .tims-modal-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        border: 1px solid rgba(99,102,241,0.25);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .tims-modal-body   { padding: 1.4rem 1.5rem; }
    .tims-modal-footer { border-top: 1px solid rgba(255,255,255,0.06); padding: .9rem 1.5rem; }

    .tims-modal-label {
        display: block;
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: .07em;
        color: #6b7280;
        margin-bottom: 6px;
        text-transform: uppercase;
    }
    .tims-modal-input {
        display: block; width: 100%;
        padding: 9px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 8px;
        color: #e5e7eb;
        font-size: 13px;
        transition: border-color .2s, box-shadow .2s;
    }
    .tims-modal-input::placeholder { color: #4b5563; }
    .tims-modal-input:focus {
        outline: none;
        border-color: rgba(99,102,241,0.5);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        background: rgba(255,255,255,0.06);
        color: #fff;
    }
    .tims-modal-input option { background: #0d0d18; color: #e5e7eb; }

    /* ── CRM Checkbox Row ─────────────────── */
    .tims-crm-check-row {
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 8px;
        padding: 10px 14px;
    }
    .form-check-input:checked { background-color:#6d5ce7; border-color:#6d5ce7; }

    /* ── Audit Result Options ─────────────── */
    .tims-result-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(255,255,255,0.03);
        cursor: pointer;
        transition: border-color .15s, background .15s;
        font-size: 13px;
    }
    .tims-result-pass { color: #6b7280; }
    .tims-result-fail { color: #6b7280; }

    .tims-result-pass.selected {
        background: rgba(74,222,128,.12);
        border-color: rgba(74,222,128,.35);
        color: #4ade80;
    }
    .tims-result-fail.selected {
        background: rgba(248,113,113,.12);
        border-color: rgba(248,113,113,.35);
        color: #f87171;
    }
</style>
@endsection


@section('scripts')
<script>
    /* ── Live search + filter ─────────── */
    document.getElementById('auditSearch').addEventListener('input', filterAudits);
    document.getElementById('resultFilter').addEventListener('change', filterAudits);

    function filterAudits() {
        const q      = document.getElementById('auditSearch').value.toLowerCase();
        const result = document.getElementById('resultFilter').value.toLowerCase();
        document.querySelectorAll('#auditTable .audit-row').forEach(row => {
            const text    = row.textContent.toLowerCase();
            const badges  = [...row.querySelectorAll('.badge')].map(b => b.textContent.trim().toLowerCase());
            const matchQ  = !q      || text.includes(q);
            const matchR  = !result || badges.some(b => b.includes(result));
            row.style.display = (matchQ && matchR) ? '' : 'none';
        });
    }

    /* ── Audit result radio UI ────────── */
    function updateResultUI() {
        const passRadio = document.querySelector('input[name="audit_result"][value="pass"]');
        const failRadio = document.querySelector('input[name="audit_result"][value="fail"]');
        document.getElementById('passOption').classList.toggle('selected', passRadio.checked);
        document.getElementById('failOption').classList.toggle('selected', failRadio.checked);
    }
    /* Init on load */
    updateResultUI();

    /* ── Re-open modal on validation error ── */
    @if($errors->any())
        var auditModal = new bootstrap.Modal(document.getElementById('newAuditModal'));
        auditModal.show();
    @endif
</script>
@endsection 