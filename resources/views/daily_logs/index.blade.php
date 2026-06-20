@extends('layouts.app')

@section('title', 'Daily Performance Logs')
@section('page_title', 'Daily Performance Logs')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary m-0">All daily operational entries submitted by CRO</p>
    </div>
    @can('enter_daily_logs')
    <a href="{{ route('daily_logs.create') }}" class="btn btn-primary rounded-3 px-4">
        <i class="fa-solid fa-plus me-2"></i>Log Today's Performance
    </a>
    @endcan
</div>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                <tr>
                    <th>Date</th>
                    <th>Executive</th>
                    <th>Zone</th>
                    <th class="text-center">Calls</th>
                    <th class="text-center">Meetings</th>
                    <th class="text-center">KPIs Met</th>
                    <th class="text-center">Violation</th>
                    <th class="text-center">Daily Score</th>
                    <th>Logged By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        <span class="fw-semibold">{{ $log->date->format('d M Y') }}</span>
                        <div class="text-secondary" style="font-size: 0.75rem;">{{ $log->date->format('l') }}</div>
                    </td>
                    <td>
                        <a href="{{ route('executives.scorecard', $log->executive_id) }}" class="text-decoration-none">
                            <div class="fw-bold">{{ $log->executive->name }}</div>
                            <small class="text-secondary">{{ $log->executive->employee_id }}</small>
                        </a>
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary border">{{ $log->executive->zone->name ?? '—' }}</span></td>
                    <td class="text-center">
                        @php
                            $callClass = $log->connected_calls >= 65 ? 'success' : ($log->connected_calls >= 40 ? 'warning' : 'danger');
                        @endphp
                        <span class="badge bg-{{ $callClass }}-subtle text-{{ $callClass }} border border-{{ $callClass }}-subtle fw-bold font-monospace">
                            {{ $log->connected_calls }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="fw-semibold">{{ $log->meetings_attended }}</span>
                        <span class="text-secondary">/{{ $log->meetings_arranged }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $kpis = (int)$log->first_contact_within_45_min + (int)$log->all_leads_followed_up + (int)$log->crm_disposition_correct + (int)$log->warm_lead_converted;
                        @endphp
                        <div class="d-flex justify-content-center gap-1">
                            <i class="fa-solid fa-clock {{ $log->first_contact_within_45_min ? 'text-success' : 'text-secondary opacity-25' }}" title="First contact <45 min"></i>
                            <i class="fa-solid fa-phone-volume {{ $log->all_leads_followed_up ? 'text-success' : 'text-secondary opacity-25' }}" title="All leads followed up"></i>
                            <i class="fa-solid fa-database {{ $log->crm_disposition_correct ? 'text-success' : 'text-secondary opacity-25' }}" title="CRM disposition correct"></i>
                            <i class="fa-solid fa-fire {{ $log->warm_lead_converted ? 'text-success' : 'text-secondary opacity-25' }}" title="Warm lead converted"></i>
                        </div>
                        <small class="text-secondary">{{ $kpis }}/4 met</small>
                    </td>
                    <td class="text-center">
                        @if($log->conduct_violation)
                            <span class="badge bg-danger"><i class="fa-solid fa-ban me-1"></i>Yes</span>
                        @else
                            <span class="text-secondary">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="fw-bold fs-6 {{ $log->calculated_score >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $log->calculated_score >= 0 ? '+' : '' }}{{ $log->calculated_score }}
                        </span>
                    </td>
                    <td>
                        <small class="text-secondary">{{ $log->creator->name ?? '—' }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-secondary">
                        <i class="fa-solid fa-calendar-xmark fa-2x mb-3 d-block"></i>
                        No daily logs found. Start by logging today's performance.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
