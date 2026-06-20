@extends('layouts.app')

@section('title', 'Reports Center')
@section('page_title', 'Reports & Analytics Center')

@section('content')

{{-- Report Type Tabs --}}
<div class="glass-card p-3 mb-4">
    <div class="d-flex flex-wrap gap-2">
        @foreach(['daily'=>'Daily Report','weekly'=>'Weekly Summary','zonal'=>'Zone Comparison','tier'=>'Tier Distribution','violation'=>'Violation Report','pip'=>'PIP Report'] as $key => $label)
        <a href="{{ route('reports.index', ['type' => $key]) }}"
           class="btn rounded-3 btn-sm px-4 {{ $type === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

{{-- Date Filters for relevant report types --}}
@if(in_array($type, ['daily','weekly']))
<div class="glass-card p-4 mb-4">
    <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
        <input type="hidden" name="type" value="{{ $type }}">
        @if($type === 'daily')
        <div class="col-md-4">
            <label class="form-label fw-semibold small text-secondary text-uppercase">Report Date</label>
            <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
        </div>
        @elseif($type === 'weekly')
        <div class="col-md-4">
            <label class="form-label fw-semibold small text-secondary text-uppercase">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->subDays(7)->toDateString()) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold small text-secondary text-uppercase">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->toDateString()) }}">
        </div>
        @endif
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary rounded-3 w-100">
                <i class="fa-solid fa-filter me-2"></i>Apply Filter
            </button>
        </div>
    </form>
</div>
@endif

{{-- Export Bar --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold m-0">
        {{ ['daily'=>'Daily Performance Report','weekly'=>'Weekly Summary Report','zonal'=>'Zone Comparison Report','tier'=>'Tier Distribution','violation'=>'Violation Report','pip'=>'PIP Status Report'][$type] ?? 'Report' }}
    </h6>
    <a href="{{ route('reports.export', array_merge(['type'=>$type], request()->query())) }}"
       class="btn btn-success rounded-3 px-4 btn-sm">
        <i class="fa-solid fa-file-csv me-2"></i>Export CSV
    </a>
</div>

{{-- Report Data Table --}}
<div class="glass-card p-4">
    <div class="table-responsive">
        @if($type === 'daily')
        <table class="table table-hover align-middle">
            <thead class="table-light text-uppercase font-monospace" style="font-size:0.72rem;">
                <tr><th>Executive</th><th>Emp ID</th><th>Calls</th><th>Arranged</th><th>Attended</th><th>KPIs</th><th>Daily Score</th></tr>
            </thead>
            <tbody>
                @forelse($data as $log)
                <tr>
                    <td class="fw-bold">{{ $log->executive->name ?? '—' }}</td>
                    <td class="font-monospace text-secondary">{{ $log->executive->employee_id ?? '—' }}</td>
                    <td><span class="fw-bold {{ $log->connected_calls >= 65 ? 'text-success' : ($log->connected_calls >= 40 ? 'text-warning' : 'text-danger') }}">{{ $log->connected_calls }}</span></td>
                    <td>{{ $log->meetings_arranged }}</td>
                    <td>{{ $log->meetings_attended }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <i class="fa-solid fa-clock {{ $log->first_contact_within_45_min ? 'text-success':'text-secondary opacity-25' }}" title="45-min contact"></i>
                            <i class="fa-solid fa-phone {{ $log->all_leads_followed_up ? 'text-success':'text-secondary opacity-25' }}" title="Follow-up"></i>
                            <i class="fa-solid fa-database {{ $log->crm_disposition_correct ? 'text-success':'text-secondary opacity-25' }}" title="CRM OK"></i>
                            <i class="fa-solid fa-fire {{ $log->warm_lead_converted ? 'text-success':'text-secondary opacity-25' }}" title="Conversion"></i>
                        </div>
                    </td>
                    <td><span class="fw-bold {{ $log->calculated_score >= 0 ? 'text-success':'text-danger' }}">{{ $log->calculated_score >= 0 ? '+' : '' }}{{ $log->calculated_score }}</span></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-secondary">No logs found for the selected date.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'weekly')
        <table class="table table-hover align-middle">
            <thead class="table-light text-uppercase font-monospace" style="font-size:0.72rem;">
                <tr><th>Executive</th><th>Total Calls</th><th>Meetings Arr.</th><th>Meetings Att.</th><th>Total Points</th></tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td class="fw-bold">{{ $row->executive->name ?? '—' }}</td>
                    <td class="fw-bold">{{ $row->calls }}</td>
                    <td>{{ $row->arranged }}</td>
                    <td>{{ $row->attended }}</td>
                    <td><span class="fw-bold {{ $row->total_score >= 0 ? 'text-success':'text-danger' }}">{{ $row->total_score >= 0 ? '+':'' }}{{ $row->total_score }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-secondary">No data for selected range.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'zonal')
        <table class="table table-hover align-middle">
            <thead class="table-light text-uppercase font-monospace" style="font-size:0.72rem;">
                <tr><th>Zone</th><th>Code</th><th>Executives</th><th>Average Score</th></tr>
            </thead>
            <tbody>
                @forelse($data as $zone)
                <tr>
                    <td class="fw-bold">{{ $zone->name }}</td>
                    <td class="font-monospace text-secondary">{{ $zone->code }}</td>
                    <td>{{ $zone->executives_count }}</td>
                    <td>
                        <div class="progress" style="height: 6px; width: 120px; display: inline-flex; vertical-align: middle;">
                            <div class="progress-bar bg-primary" style="width: {{ min(100, max(0, $zone->executives_avg_current_score/12)) }}%;"></div>
                        </div>
                        <span class="ms-2 fw-bold">{{ round($zone->executives_avg_current_score, 1) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-secondary">No zone data found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'tier')
        <table class="table align-middle">
            <thead class="table-light text-uppercase font-monospace" style="font-size:0.72rem;">
                <tr><th>Tier</th><th>Executive Count</th><th>Percentage</th></tr>
            </thead>
            <tbody>
                @php $total = $data->sum('count'); @endphp
                @forelse($data as $row)
                <tr>
                    <td><span class="tier-badge tier-{{ $row->current_tier }}">{{ str_replace('_',' ',ucwords($row->current_tier)) }}</span></td>
                    <td class="fw-bold fs-5">{{ $row->count }}</td>
                    <td>
                        @php $pct = $total > 0 ? round(($row->count / $total)*100,1) : 0; @endphp
                        <div class="progress" style="height:8px; width:180px; display:inline-flex; vertical-align:middle;">
                            <div class="progress-bar bg-primary" style="width:{{ $pct }}%;"></div>
                        </div>
                        <span class="ms-2 fw-semibold">{{ $pct }}%</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-4 text-secondary">No tier data available.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'violation')
        <table class="table table-hover align-middle">
            <thead class="table-light text-uppercase font-monospace" style="font-size:0.72rem;">
                <tr><th>Date</th><th>Executive</th><th>Type</th><th>Points Deducted</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($data as $v)
                <tr>
                    <td>{{ $v->date_committed->toDateString() }}</td>
                    <td class="fw-bold">{{ $v->executive->name }} <small class="text-secondary">({{ $v->executive->employee_id }})</small></td>
                    <td><span class="badge bg-danger-subtle text-danger border text-capitalize">{{ str_replace('_',' ',$v->violation_type) }}</span></td>
                    <td class="text-danger fw-bold">-{{ $v->points_deducted }} pts</td>
                    <td><span class="badge {{ $v->status === 'active' ? 'bg-danger' : 'bg-secondary' }}">{{ $v->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-secondary">No violations recorded.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'pip')
        <table class="table table-hover align-middle">
            <thead class="table-light text-uppercase font-monospace" style="font-size:0.72rem;">
                <tr><th>Executive</th><th>Start</th><th>End</th><th>Target</th><th>Current Score</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($data as $pip)
                <tr>
                    <td class="fw-bold">{{ $pip->executive->name }}</td>
                    <td>{{ $pip->start_date->toDateString() }}</td>
                    <td>{{ $pip->end_date->toDateString() }}</td>
                    <td class="fw-bold text-primary">{{ $pip->target_score }} pts</td>
                    <td class="fw-bold {{ $pip->executive->current_score >= 0 ? 'text-success':'text-danger' }}">{{ $pip->executive->current_score }}</td>
                    <td><span class="badge {{ $pip->status === 'active' ? 'bg-warning text-dark' : ($pip->status === 'completed' ? 'bg-success' : 'bg-danger') }}">{{ $pip->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-secondary">No PIP records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
