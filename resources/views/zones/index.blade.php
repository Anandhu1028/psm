@extends('layouts.app')
@section('title', 'Zones')
@section('breadcrumb')
<ol class="breadcrumb"><li class="breadcrumb-item active">Zones</li></ol>
@endsection
@section('content')
<div class="pms-page-header">
    <div>
        <h1 class="pms-page-title"><i class="fa-solid fa-map-location-dot me-2" style="color:var(--pms-accent);"></i>Zones</h1>
        <p class="pms-page-subtitle">Geographic zones for executive assignment</p>
    </div>
    <button class="btn btn-pms-primary" data-bs-toggle="modal" data-bs-target="#addZoneModal">
        <i class="fa-solid fa-plus me-2"></i>Add Zone
    </button>
</div>

<div class="pms-table-wrapper">
    <table class="pms-table">
        <thead>
            <tr>
                <th>Zone Name</th>
                <th>Code</th>
                <th>Company</th>
                <th class="text-center">Executives</th>
                <th class="text-center">Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($zones as $zone)
            <tr>
                <td style="font-weight:600;">{{ $zone->name }}</td>
                <td><span class="mono badge" style="background:var(--pms-accent-light);color:var(--pms-accent);font-size:.75rem;">{{ $zone->code ?? '—' }}</span></td>
                <td style="font-size:.82rem;">{{ $zone->company->name ?? '—' }}</td>
                <td class="text-center">
                    <span style="font-weight:700;color:var(--pms-accent);">{{ $zone->executives_count ?? 0 }}</span>
                </td>
                <td class="text-center">
                    <span class="badge" style="background:{{ $zone->status==='active'?'var(--pms-success-subtle)':'var(--pms-danger-subtle)' }};color:{{ $zone->status==='active'?'var(--pms-success)':'var(--pms-danger)' }};">
                        {{ ucfirst($zone->status) }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm" style="background:var(--pms-warning-light);color:var(--pms-warning);border:none;padding:4px 8px;border-radius:6px;font-size:.72rem;"
                                onclick="editZone({{ $zone->id }}, '{{ $zone->name }}', '{{ $zone->status }}')">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <form id="del-zone-{{ $zone->id }}" action="{{ route('zones.destroy', $zone) }}" method="POST">@csrf @method('DELETE')</form>
                        <button type="button" class="btn btn-sm"
                                style="background:var(--pms-danger-light);color:var(--pms-danger);border:none;padding:4px 8px;border-radius:6px;font-size:.72rem;"
                                data-confirm-delete="{{ $zone->name }}"
                                data-form-id="del-zone-{{ $zone->id }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="pms-empty"><i class="fa-solid fa-map-location-dot"></i><p>No zones yet.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($zones->hasPages())
<div class="d-flex justify-content-center mt-4">{{ $zones->links() }}</div>
@endif

{{-- Add Zone Modal --}}
<div class="modal fade" id="addZoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius-lg);">
            <div class="modal-header" style="border-color:var(--pms-border);">
                <h5 class="modal-title" style="font-weight:700;font-size:.95rem;">Add Zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('zones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Company *</label>
                        <select name="company_id" class="form-select" required>
                            <option value="">— Select —</option>
                            @foreach($companies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Zone Name *</label><input type="text" name="name" class="form-control" required placeholder="e.g. North Zone"></div>
                    <div class="mb-3"><label class="form-label">Code</label><input type="text" name="code" class="form-control" placeholder="e.g. NZ"></div>
                    <div class="mb-3"><label class="form-label">Status</label>
                        <select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--pms-border);">
                    <button type="button" class="btn btn-pms-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-pms-primary">Save Zone</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Zone Modal --}}
<div class="modal fade" id="editZoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius-lg);">
            <div class="modal-header" style="border-color:var(--pms-border);">
                <h5 class="modal-title" style="font-weight:700;font-size:.95rem;">Edit Zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editZoneForm" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Zone Name *</label><input type="text" name="name" id="editZoneName" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Status</label>
                        <select name="status" id="editZoneStatus" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
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
function editZone(id, name, status) {
    document.getElementById('editZoneForm').action = `/zones/${id}`;
    document.getElementById('editZoneName').value = name;
    document.getElementById('editZoneStatus').value = status;
    new bootstrap.Modal(document.getElementById('editZoneModal')).show();
}
</script>
@endpush
