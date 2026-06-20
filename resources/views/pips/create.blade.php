@extends('layouts.app')

@section('title', 'Launch PIP')
@section('page_title', 'Launch Performance Improvement Plan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="glass-card p-4 p-lg-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="fa-solid fa-file-signature text-warning fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0">Create New PIP Plan</h5>
                    <small class="text-secondary">Executive will be placed under formal Performance Improvement Plan monitoring</small>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-4">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST" action="{{ route('pips.store') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Executive <span class="text-danger">*</span></label>
                        <select name="executive_id" id="pipExec" class="form-select @error('executive_id') is-invalid @enderror" required>
                            <option value="">Select Executive to place on PIP...</option>
                            @foreach($executives as $exec)
                            <option value="{{ $exec->id }}"
                                    data-score="{{ $exec->current_score }}"
                                    data-tier="{{ $exec->current_tier }}"
                                    {{ old('executive_id') == $exec->id ? 'selected' : '' }}>
                                {{ $exec->name }} ({{ $exec->employee_id }}) — Score: {{ $exec->current_score }} — {{ ucwords(str_replace('_',' ',$exec->current_tier)) }}
                            </option>
                            @endforeach
                        </select>
                        @error('executive_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="execAlert" class="mt-2" style="display:none;"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">PIP Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', date('Y-m-d')) }}" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">PIP End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date') }}" required>
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Target Score to Achieve <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary-subtle border-primary-subtle text-primary fw-bold">pts</span>
                            <input type="number" name="target_score" class="form-control @error('target_score') is-invalid @enderror"
                                   placeholder="e.g. 300 (minimum for Bronze tier exit)" min="1" value="{{ old('target_score', 300) }}" required>
                        </div>
                        <div class="form-text">Recommended: 300 (Bronze), 700 (Silver), 1200 (Gold)</div>
                        @error('target_score')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Remarks / Action Plan</label>
                        <textarea name="remarks" class="form-control" rows="4"
                                  placeholder="Describe the improvement targets, mentoring plan, check-in schedule...">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-warning border-0 rounded-3 d-flex gap-3 align-items-start">
                            <i class="fa-solid fa-triangle-exclamation fa-lg mt-1 flex-shrink-0"></i>
                            <div>
                                <strong>Confirmation Notice:</strong> Launching this PIP will set the executive's status to
                                <strong>Probation</strong>. All daily performance data and scoring will continue to be tracked
                                against the target score you set above.
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-3 mt-2">
                        <a href="{{ route('pips.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
                        <button type="submit" class="btn btn-warning rounded-3 px-5 text-white fw-semibold">
                            <i class="fa-solid fa-rocket me-2"></i>Launch PIP Plan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('pipExec').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const alert = document.getElementById('execAlert');
        if (opt.value) {
            const score = parseInt(opt.dataset.score);
            const tier = opt.dataset.tier.replace('_', ' ').toUpperCase();
            const cls = score < 0 ? 'danger' : (score < 300 ? 'warning' : 'info');
            alert.innerHTML = `<div class="alert alert-${cls} border-0 rounded-3 py-2 small">
                Current Score: <strong>${score} pts</strong> &bull; Current Tier: <strong>${tier}</strong>
            </div>`;
            alert.style.display = 'block';
        } else {
            alert.style.display = 'none';
        }
    });
</script>
@endsection
