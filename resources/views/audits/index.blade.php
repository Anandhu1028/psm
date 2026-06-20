@extends('layouts.app')

@section('title', 'Weekly Audits')
@section('page_title', 'Weekly CRM Audit Log')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-secondary m-0">Verify CRM entries and call records. Failed audits trigger automatic point deductions.</p>
    @can('approve_audits')
    <a href="{{ route('audits.create') }}" class="btn btn-primary rounded-3 px-4">
        <i class="fa-solid fa-clipboard-check me-2"></i>Record New Audit
    </a>
    @endcan
</div>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase font-monospace" style="font-size: 0.72rem;">
                <tr>
                    <th>Audit Date</th>
                    <th>Executive</th>
                    <th>Lead ID</th>
                    <th>CRM Verified</th>
                    <th>Call Status</th>
                    <th>Result</th>
                    <th>Audited By</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $audit)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $audit->audit_date->format('d M Y') }}</div>
                        <small class="text-secondary">{{ $audit->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        <a href="{{ route('executives.scorecard', $audit->executive_id) }}" class="text-decoration-none fw-bold">
                            {{ $audit->executive->name }}
                        </a>
                        <div class="text-secondary" style="font-size: 0.75rem;">{{ $audit->executive->employee_id }}</div>
                    </td>
                    <td><span class="font-monospace text-secondary">{{ $audit->lead_identifier }}</span></td>
                    <td class="text-center">
                        @if($audit->crm_entry_verified)
                            <i class="fa-solid fa-circle-check text-success fa-lg" title="CRM Verified"></i>
                        @else
                            <i class="fa-solid fa-circle-xmark text-danger fa-lg" title="Not Verified"></i>
                        @endif
                    </td>
                    <td>
                        @php
                            $callColors = ['verified'=>'success','discrepancy'=>'warning','fake_lead'=>'danger'];
                            $callColor = $callColors[$audit->call_verification_status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $callColor }}-subtle text-{{ $callColor }} border border-{{ $callColor }}-subtle text-capitalize">
                            {{ str_replace('_',' ',$audit->call_verification_status) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge fw-bold {{ $audit->audit_result === 'pass' ? 'bg-success' : 'bg-danger' }}">
                            <i class="fa-solid {{ $audit->audit_result === 'pass' ? 'fa-check' : 'fa-xmark' }} me-1"></i>
                            {{ strtoupper($audit->audit_result) }}
                        </span>
                    </td>
                    <td>
                        <small class="fw-semibold">{{ $audit->auditor->name ?? '—' }}</small>
                    </td>
                    <td>
                        <small class="text-secondary">{{ $audit->remarks ? Str::limit($audit->remarks, 40) : '—' }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-secondary">
                        <i class="fa-solid fa-clipboard fa-2x mb-3 d-block text-muted"></i>
                        No audit records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($audits->hasPages())
    <div class="d-flex justify-content-center mt-4">{{ $audits->links() }}</div>
    @endif
</div>
@endsection
