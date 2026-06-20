@extends('layouts.app')

@section('title', 'Log Daily Performance')
@section('page_title', 'Daily Performance Log Entry')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="glass-card p-4 p-lg-5">
            <div class="d-flex align-items-center mb-5">
                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="fa-solid fa-calendar-day text-primary fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0">Enter Daily Performance Data</h5>
                    <small class="text-secondary">Points are calculated automatically upon saving. One entry per executive per day.</small>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-4">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('daily_logs.store') }}" id="dailyLogForm">
                @csrf

                {{-- Date & Executive --}}
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold text-uppercase text-secondary small border-bottom pb-2">
                            <i class="fa-solid fa-calendar me-2"></i>Log Details
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="log_date" class="form-control @error('date') is-invalid @enderror"
                               value="{{ old('date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Executive <span class="text-danger">*</span></label>
                        <select name="executive_id" id="executive_id" class="form-select @error('executive_id') is-invalid @enderror" required>
                            <option value="">Select Executive...</option>
                            @foreach($executives as $exec)
                            <option value="{{ $exec->id }}" data-tier="{{ $exec->current_tier }}" data-score="{{ $exec->current_score }}"
                                {{ old('executive_id') == $exec->id ? 'selected' : '' }}>
                                {{ $exec->name }} ({{ $exec->employee_id }}) — {{ $exec->zone->name ?? 'No Zone' }}
                            </option>
                            @endforeach
                        </select>
                        @error('executive_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Executive quick info banner (populated by JS) --}}
                    <div class="col-12" id="execInfoBanner" style="display: none;">
                        <div class="alert alert-info border-0 rounded-3 py-2 px-3 d-flex align-items-center gap-3">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Current Score: <strong id="execScore">—</strong> &bull;
                            Current Tier: <strong id="execTier">—</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Call & Meeting Metrics --}}
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold text-uppercase text-secondary small border-bottom pb-2">
                            <i class="fa-solid fa-phone-volume me-2"></i>Call & Meeting Metrics
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Connected Calls <span class="text-danger">*</span></label>
                        <input type="number" name="connected_calls" id="connected_calls"
                               class="form-control @error('connected_calls') is-invalid @enderror"
                               value="{{ old('connected_calls', 0) }}" min="0" max="999" required>
                        <div class="form-text" id="callPointHint">Enter calls to see point preview</div>
                        @error('connected_calls')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Meetings Arranged <span class="text-danger">*</span></label>
                        <input type="number" name="meetings_arranged" id="meetings_arranged"
                               class="form-control @error('meetings_arranged') is-invalid @enderror"
                               value="{{ old('meetings_arranged', 0) }}" min="0" max="99" required>
                        @error('meetings_arranged')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Meetings Attended <span class="text-danger">*</span></label>
                        <input type="number" name="meetings_attended" id="meetings_attended"
                               class="form-control @error('meetings_attended') is-invalid @enderror"
                               value="{{ old('meetings_attended', 0) }}" min="0" max="99" required>
                        @error('meetings_attended')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- KPI Checklist --}}
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold text-uppercase text-secondary small border-bottom pb-2">
                            <i class="fa-solid fa-list-check me-2"></i>KPI Compliance Checklist
                        </h6>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check d-flex align-items-center gap-3 p-3 border rounded-3 {{ old('first_contact_within_45_min') ? 'border-success bg-success-subtle' : '' }}" id="check_card_45min">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="first_contact_within_45_min"
                                   id="first_contact_within_45_min" value="1" {{ old('first_contact_within_45_min') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="first_contact_within_45_min">
                                <i class="fa-solid fa-clock text-primary me-2"></i>
                                First Contact Within 45 Minutes
                                <div class="text-success small fw-normal">+2 pts when checked</div>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check d-flex align-items-center gap-3 p-3 border rounded-3 {{ old('all_leads_followed_up') ? 'border-success bg-success-subtle' : '' }}">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="all_leads_followed_up"
                                   id="all_leads_followed_up" value="1" {{ old('all_leads_followed_up') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="all_leads_followed_up">
                                <i class="fa-solid fa-phone-volume text-primary me-2"></i>
                                All Leads Followed Up (Same Day)
                                <div class="text-success small fw-normal">+2 pts when checked</div>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check d-flex align-items-center gap-3 p-3 border rounded-3 {{ old('crm_disposition_correct') ? 'border-success bg-success-subtle' : '' }}">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="crm_disposition_correct"
                                   id="crm_disposition_correct" value="1" {{ old('crm_disposition_correct') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="crm_disposition_correct">
                                <i class="fa-solid fa-database text-primary me-2"></i>
                                CRM Disposition Correctly Updated
                                <div class="text-success small fw-normal">+2 pts when checked</div>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check d-flex align-items-center gap-3 p-3 border rounded-3 {{ old('warm_lead_converted') ? 'border-success bg-success-subtle' : '' }}">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="warm_lead_converted"
                                   id="warm_lead_converted" value="1" {{ old('warm_lead_converted') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="warm_lead_converted">
                                <i class="fa-solid fa-fire text-warning me-2"></i>
                                Warm Lead Converted
                                <div class="text-success small fw-normal">+5 pts when checked</div>
                            </label>
                        </div>
                    </div>

                    {{-- Conduct Violation --}}
                    <div class="col-12">
                        <div class="form-check d-flex align-items-center gap-3 p-3 border border-danger rounded-3 {{ old('conduct_violation') ? 'bg-danger-subtle' : '' }}">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="conduct_violation"
                                   id="conduct_violation" value="1" {{ old('conduct_violation') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-danger" for="conduct_violation">
                                <i class="fa-solid fa-ban me-2"></i>
                                Conduct Violation Committed
                                <div class="text-danger small fw-normal">-15 pts penalty deducted automatically</div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-uppercase text-secondary small border-bottom pb-2">
                        <i class="fa-solid fa-comment me-2"></i>CRO Remarks
                    </h6>
                    <textarea name="cro_remarks" id="cro_remarks" class="form-control mt-3" rows="3"
                              placeholder="Optional: Add any relevant observations, context, or notes about today's performance...">{{ old('cro_remarks') }}</textarea>
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('daily_logs.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary rounded-3 px-5 fw-semibold">
                        <i class="fa-solid fa-calculator me-2"></i>Calculate & Save Daily Record
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Show executive quick info on selection
    const execSelect = document.getElementById('executive_id');
    const banner = document.getElementById('execInfoBanner');

    execSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            document.getElementById('execScore').textContent = opt.dataset.score;
            document.getElementById('execTier').textContent = opt.dataset.tier.replace('_', ' ').toUpperCase();
            banner.style.display = 'block';
        } else {
            banner.style.display = 'none';
        }
    });

    // Live call point preview
    document.getElementById('connected_calls').addEventListener('input', function() {
        const calls = parseInt(this.value) || 0;
        const hint = document.getElementById('callPointHint');
        if (calls >= 65) hint.textContent = '✅ 65+ calls → +8 points (Maximum tier)';
        else if (calls >= 50) hint.textContent = '✅ 50-64 calls → +6 points';
        else if (calls >= 40) hint.textContent = '⚠️ 40-49 calls → +4 points';
        else if (calls > 0) hint.textContent = '❌ Below 40 — no call points earned';
        else hint.textContent = 'Enter calls to see point preview';
    });

    // Validate meetings_attended <= meetings_arranged
    const form = document.getElementById('dailyLogForm');
    form.addEventListener('submit', function(e) {
        const arr = parseInt(document.getElementById('meetings_arranged').value) || 0;
        const att = parseInt(document.getElementById('meetings_attended').value) || 0;
        if (att > arr) {
            e.preventDefault();
            alert('Meetings Attended cannot exceed Meetings Arranged.');
            document.getElementById('meetings_attended').focus();
        }
    });
</script>
@endsection
