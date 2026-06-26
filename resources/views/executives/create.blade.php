@extends('layouts.app')
@section('title', 'Add Executive')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('executives.index') }}">Executives</a></li>
    <li class="breadcrumb-item active">Add New</li>
</ol>
@endsection
@section('content')
<div class="pms-page-header">
    <div>
        <h1 class="pms-page-title"><i class="fa-solid fa-user-plus me-2" style="color:var(--pms-accent);"></i>Add Executive</h1>
        <p class="pms-page-subtitle">Register a new CRO field executive</p>
    </div>
    <a href="{{ route('executives.index') }}" class="btn btn-pms-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Back</a>
</div>
<form action="{{ route('executives.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-3">
    <div class="col-xl-8">
        <div class="pms-card mb-3">
            <div class="pms-card-header"><div class="pms-card-title"><i class="fa-solid fa-id-card"></i> Personal Information</div></div>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Arjun Mehta">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                    <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}" required placeholder="e.g. TIMS001">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" placeholder="+91 9876543210">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="executive@company.com">
                </div>
            </div>
        </div>

        <div class="pms-card mb-3">
            <div class="pms-card-header"><div class="pms-card-title"><i class="fa-solid fa-building"></i> Assignment</div></div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Company <span class="text-danger">*</span></label>
                    <select name="company_id" id="companySelect" class="form-select" required>
                        <option value="">— Select Company —</option>
                        @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Zone <span class="text-danger">*</span></label>
                    <select name="zone_id" id="zoneSelect" class="form-select" required>
                        <option value="">— Select Zone —</option>
                        @foreach($zones as $z)
                        <option value="{{ $z->id }}" {{ old('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date Joined</label>
                    <input type="date" name="date_joined" class="form-control" value="{{ old('date_joined') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Probation End Date</label>
                    <input type="date" name="probation_end_date" class="form-control" value="{{ old('probation_end_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="probation" {{ old('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                        <option value="active"    {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive"  {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="pms-card mb-3">
            <div class="pms-card-header"><div class="pms-card-title"><i class="fa-solid fa-note-sticky"></i> Notes</div></div>
            <textarea name="notes" class="form-control" rows="3" placeholder="Any notes about this executive…">{{ old('notes') }}</textarea>
        </div>

        <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('executives.index') }}" class="btn btn-pms-secondary" style="padding:11px 24px;">Cancel</a>
            <button type="submit" class="btn btn-pms-primary" style="padding:11px 32px;">
                <i class="fa-solid fa-save me-2"></i>Save Executive
            </button>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="pms-card">
            <div class="pms-card-header"><div class="pms-card-title"><i class="fa-solid fa-camera"></i> Photo</div></div>
            <div class="text-center mb-3">
                <div id="photoPreview" style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,var(--pms-accent),#7c3aed);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:2rem;color:#fff;overflow:hidden;">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
            <input type="file" name="photo" id="photoInput" class="form-control" accept="image/*" onchange="previewPhoto(this)">
            <div style="font-size:.68rem;color:var(--pms-text-muted);margin-top:4px;">JPG, PNG · Max 2MB</div>
        </div>
    </div>
</div>
</form>
@endsection
@push('scripts')
<script>
// Zone cascade on company change
document.getElementById('companySelect').addEventListener('change', function() {
    const companyId = this.value;
    const zoneSelect = document.getElementById('zoneSelect');
    zoneSelect.innerHTML = '<option value="">Loading zones…</option>';
    if (!companyId) { zoneSelect.innerHTML = '<option value="">— Select Zone —</option>'; return; }
    fetch(`/api/companies/${companyId}/zones`)
        .then(r => r.json())
        .then(zones => {
            zoneSelect.innerHTML = '<option value="">— Select Zone —</option>';
            zones.forEach(z => zoneSelect.innerHTML += `<option value="${z.id}">${z.name}</option>`);
        });
});

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const p = document.getElementById('photoPreview');
            p.style.background = 'none';
            p.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
