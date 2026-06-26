@extends('layouts.app')
@section('title', 'Executives')
@section('breadcrumb')
<ol class="breadcrumb"><li class="breadcrumb-item active">Executives</li></ol>
@endsection
@section('content')
<div class="pms-page-header">
    <div>
        <h1 class="pms-page-title"><i class="fa-solid fa-users me-2" style="color:var(--pms-accent);"></i>Executives</h1>
        <p class="pms-page-subtitle">All CRO field executives across companies</p>
    </div>
    @can('manage_executives')
    <a href="{{ route('executives.create') }}" class="btn btn-pms-primary">
        <i class="fa-solid fa-plus me-2"></i>Add Executive
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="pms-filter-bar">
    <form method="GET" action="{{ route('executives.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Companies</option>
                    @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Zone</label>
                <select name="zone_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Zones</option>
                    @foreach($zones as $z)
                    <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="probation" {{ request('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or Employee ID…">
                    <button class="btn btn-pms-primary" type="submit"><i class="fa-solid fa-search"></i></button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="pms-table-wrapper">
    <table class="pms-table">
        <thead>
            <tr>
                <th>Executive</th>
                <th>Company</th>
                <th>Zone</th>
                <th>Status</th>
                <th class="text-center">Total Score</th>
                <th class="text-center">Monthly</th>
                <th class="text-center">Tier</th>
                <th class="text-center">Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($executives as $exec)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--pms-accent),#7c3aed);display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($exec->name,0,2)) }}
                        </div>
                        <div>
                            <a href="{{ route('executives.show', $exec) }}" style="font-weight:600;color:var(--pms-accent);text-decoration:none;font-size:.83rem;">{{ $exec->name }}</a>
                            <div style="font-size:.68rem;color:var(--pms-text-muted);">{{ $exec->employee_id }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:.8rem;">{{ $exec->company->name ?? '—' }}</td>
                <td style="font-size:.8rem;">{{ $exec->zone->name ?? '—' }}</td>
                <td>
                    <span class="badge" style="background:{{ $exec->status==='active' ? 'var(--pms-success-subtle)' : ($exec->status==='probation' ? 'var(--pms-warning-subtle)' : 'var(--pms-danger-subtle)') }};color:{{ $exec->status==='active' ? 'var(--pms-success)' : ($exec->status==='probation' ? 'var(--pms-warning)' : 'var(--pms-danger)') }};">
                        {{ ucfirst($exec->status) }}
                    </span>
                </td>
                <td class="text-center" style="font-weight:800;color:var(--pms-accent);font-size:.9rem;">{{ number_format($exec->current_score) }}</td>
                <td class="text-center" style="font-weight:600;font-size:.85rem;">{{ number_format($exec->monthly_score) }}</td>
                <td class="text-center"><span class="badge badge-tier-{{ $exec->current_tier }}">{{ $exec->tier_label }}</span></td>
                <td class="text-center" style="font-size:.75rem;color:var(--pms-text-muted);">{{ $exec->date_joined ? $exec->date_joined->format('M Y') : '—' }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('executives.show', $exec) }}" class="btn btn-sm" style="background:var(--pms-accent-light);color:var(--pms-accent);border:none;padding:4px 8px;border-radius:6px;font-size:.72rem;" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @can('manage_executives')
                        <a href="{{ route('executives.edit', $exec) }}" class="btn btn-sm" style="background:var(--pms-warning-light);color:var(--pms-warning);border:none;padding:4px 8px;border-radius:6px;font-size:.72rem;" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form id="del-exec-{{ $exec->id }}" action="{{ route('executives.destroy', $exec) }}" method="POST">@csrf @method('DELETE')</form>
                        <button type="button" class="btn btn-sm"
                                style="background:var(--pms-danger-light);color:var(--pms-danger);border:none;padding:4px 8px;border-radius:6px;font-size:.72rem;"
                                data-confirm-delete="{{ $exec->name }}"
                                data-form-id="del-exec-{{ $exec->id }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="9"><div class="pms-empty"><i class="fa-solid fa-users"></i><p>No executives found. <a href="{{ route('executives.create') }}" style="color:var(--pms-accent);">Add the first one</a>.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($executives->hasPages())
<div class="d-flex justify-content-center mt-4">{{ $executives->links() }}</div>
@endif
@endsection
