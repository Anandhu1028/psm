@extends('layouts.app')

@section('title', 'Record Audit')
@section('page_title', 'Record Weekly Audit')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="glass-card p-4 p-lg-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="fa-solid fa-clipboard-check text-warning fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0">Submit Audit Verification</h5>
                    <small class="text-secondary">Failed audits automatically deduct points from the executive's score</small>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-4">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST" action="{{ route('audits.store') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Executive <span class="text-danger">*</span></label>
                        <select name="executive_id" class="form-select @error('executive_id') is-invalid @enderror" required>
                            <option value="">Select Executive...</option>
                            @foreach($executives as $exec)
                            <option value="{{ $exec->id }}" {{ old('executive_id') == $exec->id ? 'selected' : '' }}>
                                {{ $exec->name }} ({{ $exec->employee_id }})
                            </option>
                            @endforeach
                        </select>
                        @error('executive_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lead Identifier / CRM ID <span class="text-danger">*</span></label>
                        <input type="text" name="lead_identifier" class="form-control @error('lead_identifier') is-invalid @enderror"
                               placeholder="e.g. CRM-2024-00421 or lead name" value="{{ old('lead_identifier') }}" required>
                        @error('lead_identifier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Audit Date <span class="text-danger">*</span></label>
                        <input type="date" name="audit_date" class="form-control @error('audit_date') is-invalid @enderror"
                               value="{{ old('audit_date', date('Y-m-d')) }}" required>
                        @error('audit_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Call Verification Status <span class="text-danger">*</span></label>
                        <select name="call_verification_status" class="form-select @error('call_verification_status') is-invalid @enderror" required>
                            <option value="verified" {{ old('call_verification_status','verified') === 'verified' ? 'selected' : '' }}>✅ Verified — Call confirmed</option>
                            <option value="discrepancy" {{ old('call_verification_status') === 'discrepancy' ? 'selected' : '' }}>⚠️ Discrepancy — Details mismatch</option>
                            <option value="fake_lead" {{ old('call_verification_status') === 'fake_lead' ? 'selected' : '' }}>🚫 Fake Lead — Fraudulent entry</option>
                        </select>
                        @error('call_verification_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check d-flex align-items-center gap-3 p-3 border rounded-3">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="crm_entry_verified"
                                   id="crm_entry_verified" {{ old('crm_entry_verified') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="crm_entry_verified">
                                <i class="fa-solid fa-database text-primary me-2"></i>
                                CRM Entry Verified — All fields correctly filled in the CRM system
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Audit Result <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check flex-fill p-3 border rounded-3 {{ old('audit_result') === 'pass' ? 'border-success bg-success-subtle' : '' }}">
                                <input class="form-check-input" type="radio" name="audit_result" value="pass" id="result_pass"
                                       {{ old('audit_result','pass') === 'pass' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-success" for="result_pass">
                                    <i class="fa-solid fa-check-circle me-1"></i>PASS
                                </label>
                            </div>
                            <div class="form-check flex-fill p-3 border rounded-3 {{ old('audit_result') === 'fail' ? 'border-danger bg-danger-subtle' : '' }}">
                                <input class="form-check-input" type="radio" name="audit_result" value="fail" id="result_fail"
                                       {{ old('audit_result') === 'fail' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-danger" for="result_fail">
                                    <i class="fa-solid fa-times-circle me-1"></i>FAIL
                                    <div class="text-danger small fw-normal">Points deduction applied</div>
                                </label>
                            </div>
                        </div>
                        @error('audit_result')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Violation Type <small class="text-secondary fw-normal">(if applicable)</small></label>
                        <input type="text" name="violation_type" class="form-control"
                               placeholder="e.g. Fake lead entry, Wrong CRM status..."
                               value="{{ old('violation_type') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Auditor Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3"
                                  placeholder="Optional observations or context for this audit...">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-3 mt-2">
                        <a href="{{ route('audits.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
                        <button type="submit" class="btn btn-warning rounded-3 px-5 text-white fw-semibold">
                            <i class="fa-solid fa-clipboard-check me-2"></i>Submit Audit Record
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
