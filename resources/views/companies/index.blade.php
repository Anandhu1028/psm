@extends('layouts.app')
@section('title', 'Companies')
@section('breadcrumb')
<ol class="breadcrumb"><li class="breadcrumb-item active">Companies</li></ol>
@endsection
@section('content')
<div class="pms-page-header">
    <div>
        <h1 class="pms-page-title"><i class="fa-solid fa-building me-2" style="color:var(--pms-accent);"></i>Companies</h1>
        <p class="pms-page-subtitle">Manage TIMS & FOCUZ company configurations</p>
    </div>
    @can('manage_companies')
    <button class="btn btn-pms-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
        <i class="fa-solid fa-plus me-2"></i>Add Company
    </button>
    @endcan
</div>

<div class="row g-3">
    @forelse($companies as $company)
    <div class="col-xl-6">
        <div class="pms-card" style="border-top:4px solid {{ $company->theme_color }};">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;border-radius:12px;background:{{ $company->theme_color }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1rem;">
                        {{ substr($company->code,0,2) }}
                    </div>
                    <div>
                        <div style="font-size:1rem;font-weight:700;color:var(--pms-text-primary);">{{ $company->name }}</div>
                        <div style="font-size:.72rem;color:var(--pms-text-muted);">{{ $company->code }} · {{ ucfirst($company->calculation_strategy) }} Strategy</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('companies.show', $company) }}" class="btn btn-pms-secondary" style="padding:5px 12px;font-size:.78rem;">
                        <i class="fa-solid fa-eye me-1"></i>View
                    </a>
                    @can('manage_companies')
                    <button class="btn btn-sm" style="background:var(--pms-warning-light);color:var(--pms-warning);border:none;padding:5px 10px;border-radius:6px;"
                            onclick="editCompany({{ $company->id }}, '{{ $company->name }}', '{{ $company->theme_color }}', '{{ $company->description }}')">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    @endcan
                </div>
            </div>
            <div class="row g-2">
                @php
                $stats = [
                    ['label'=>'Executives', 'val'=>$company->executives_count, 'color'=>'var(--pms-accent)'],
                    ['label'=>'Zones',      'val'=>$company->zones_count,      'color'=>'var(--pms-success)'],
                ];
                @endphp
                @foreach($stats as $s)
                <div class="col-6">
                    <div class="text-center p-3 rounded-3" style="background:var(--pms-bg-elevated);border:1px solid var(--pms-border);">
                        <div style="font-size:1.4rem;font-weight:800;color:{{ $s['color'] }};">{{ $s['val'] }}</div>
                        <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.06em;color:var(--pms-text-muted);">{{ $s['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($company->description)
            <p style="font-size:.78rem;color:var(--pms-text-muted);margin-top:12px;margin-bottom:0;">{{ $company->description }}</p>
            @endif
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="pms-empty"><i class="fa-solid fa-building"></i><p>No companies yet.</p></div>
    </div>
    @endforelse
</div>

{{-- Add Company Modal --}}
<div class="modal fade" id="addCompanyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:1px solid var(--pms-border);">
            <div class="modal-header" style="border-color:var(--pms-border);">
                <h5 class="modal-title" style="font-weight:700;font-size:.95rem;">Add Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('companies.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Company Name *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Code * (e.g. TIMS)</label><input type="text" name="code" class="form-control" required style="text-transform:uppercase;"></div>
                    <div class="mb-3"><label class="form-label">Calculation Strategy *</label>
                        <select name="calculation_strategy" class="form-select" required>
                            <option value="tims">TIMS</option>
                            <option value="focuz">FOCUZ</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Theme Color</label><input type="color" name="theme_color" class="form-control form-control-color" value="#4f46e5"></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Status</label>
                        <select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--pms-border);">
                    <button type="button" class="btn btn-pms-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-pms-primary">Save Company</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Company Modal --}}
<div class="modal fade" id="editCompanyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:1px solid var(--pms-border);">
            <div class="modal-header" style="border-color:var(--pms-border);">
                <h5 class="modal-title" style="font-weight:700;font-size:.95rem;">Edit Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCompanyForm" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Company Name *</label><input type="text" name="name" id="editName" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Theme Color</label><input type="color" name="theme_color" id="editColor" class="form-control form-control-color"></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="editDesc" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Status</label>
                        <select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--pms-border);">
                    <button type="button" class="btn btn-pms-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-pms-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function editCompany(id, name, color, desc) {
    document.getElementById('editCompanyForm').action = `/companies/${id}`;
    document.getElementById('editName').value  = name;
    document.getElementById('editColor').value = color;
    document.getElementById('editDesc').value  = desc;
    new bootstrap.Modal(document.getElementById('editCompanyModal')).show();
}
</script>
@endpush
