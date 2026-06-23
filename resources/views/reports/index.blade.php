@extends('layouts.app')

@section('title', 'Reports Center')
@section('page_title', 'Reports & Analytics')
@section('page_subtitle', 'Run performance reports and export analytics across CRO activity')

@section('page_actions')
<a href="{{ route('reports.export', array_merge(['type' => $type], request()->query())) }}" class="btn btn-success rounded-3 px-4">
    <i class="fa-solid fa-file-csv me-2"></i>Export
</a>
@endsection

@section('styles')
<style>
    .report-nav-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.05);
        color: #aaa;
        text-decoration: none !important;
    }
    .report-nav-btn:hover {
        background: rgba(59, 123, 255, 0.15);
        border-color: rgba(59, 123, 255, 0.3);
        color: #3B7BFF;
        text-decoration: none !important;
    }
    .report-nav-btn.active {
        background: #3B7BFF;
        color: white;
        border-color: #3B7BFF;
        text-decoration: none !important;
    }
    .report-filter-compact {
        display: flex;
        gap: 8px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .report-filter-compact input,
    .report-filter-compact select {
        max-width: 140px !important;
        font-size: 0.85rem !important;
        padding: 6px 10px !important;
    }
    .report-filter-compact button {
        padding: 6px 12px !important;
        font-size: 0.85rem !important;
    }
</style>
@endsection

@section('content')

{{-- Report Navigation Header --}}
<div style=" padding: 16px; margin-bottom: 24px; backdrop-filter: blur(16px); background: #0f1322;">
    <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
        @foreach(['daily'=>'Daily Report','weekly'=>'Weekly Summary','monthly'=>'Monthly Summary','zonal'=>'Zone Comparison','tier'=>'Tier Distribution','violation'=>'Violation Report','pip'=>'PIP Report'] as $key => $label)
        <a href="{{ route('reports.index', ['type' => $key]) }}" class="report-nav-btn {{ $type === $key ? 'active' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

{{-- Report Data Table --}}
<div style="    background: linear-gradient(135deg, rgb(15 19 34) 0%, rgb(15 19 34) 100%), rgba(255,255,255,0.012) 100%); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 24px; backdrop-filter: blur(16px);">
    
    {{-- Filter Bar (Top Right, Compact) --}}
    @if(in_array($type, ['daily','weekly','monthly']))
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; gap: 16px;">
        <h6 style="font-weight: 700; margin: 0; color: #F0F4FF;">
            {{ ['daily'=>'Daily Performance Report','weekly'=>'Weekly Summary Report','monthly'=>'Monthly Executive Report','zonal'=>'Zone Comparison Report','tier'=>'Tier Distribution','violation'=>'Violation Report','pip'=>'PIP Status Report'][$type] ?? 'Report' }}
        </h6>
        <form method="GET" action="{{ route('reports.index') }}" class="report-filter-compact">
            <input type="hidden" name="type" value="{{ $type }}">
            @if($type === 'daily')
            <div>
                <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
            </div>
            @elseif($type === 'weekly')
            <div>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->subDays(7)->toDateString()) }}">
            </div>
            <div>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->toDateString()) }}">
            </div>
            @elseif($type === 'monthly')
            <div>
                <input type="month" name="month" class="form-control" value="{{ request('month', now()->toDateString()) }}">
            </div>
            @endif
            <button type="submit" class="btn btn-primary rounded-2" style="padding: 6px 14px; font-size: 0.85rem;">
                <i class="fa-solid fa-filter me-1"></i>Apply
            </button>
        </form>
    </div>
    @else
    <h6 style="font-weight: 700; margin-bottom: 20px; color: #F0F4FF;">
        {{ ['daily'=>'Daily Performance Report','weekly'=>'Weekly Summary Report','monthly'=>'Monthly Executive Report','zonal'=>'Zone Comparison Report','tier'=>'Tier Distribution','violation'=>'Violation Report','pip'=>'PIP Status Report'][$type] ?? 'Report' }}
    </h6>
    @endif
    
    <div class="table-responsive">
        @if($type === 'daily')
        <table class="table table-hover align-middle" style="color: #F0F4FF;">
            <thead style="background: rgba(59,123,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1);">
                <tr style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; font-weight: 600; letter-spacing: 0.5px;">
                    <th style="padding: 12px; border: none;">Executive</th>
                    <th style="padding: 12px; border: none;">Emp ID</th>
                    <th style="padding: 12px; border: none;">Calls</th>
                    <th style="padding: 12px; border: none;">Arranged</th>
                    <th style="padding: 12px; border: none;">Attended</th>
                    <th style="padding: 12px; border: none;">KPIs</th>
                    <th style="padding: 12px; border: none;">Daily Score</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid rgba(255,255,255,0.07);">
                @forelse($data as $log)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">
                    <td style="padding: 12px; font-weight: 600;">{{ $log->executive->name ?? '—' }}</td>
                    <td style="padding: 12px; color: #aaa; font-family: monospace; font-size: 0.85rem;">{{ $log->executive->employee_id ?? '—' }}</td>
                    <td style="padding: 12px;"><span style="color: {{ $log->connected_calls >= 65 ? '#10B981' : ($log->connected_calls >= 40 ? '#F59E0B' : '#F43F5E') }}; font-weight: 600;">{{ $log->connected_calls }}</span></td>
                    <td style="padding: 12px;">{{ $log->meetings_arranged }}</td>
                    <td style="padding: 12px;">{{ $log->meetings_attended }}</td>
                    <td style="padding: 12px;">
                        <div style="display: flex; gap: 6px;">
                            <i class="fa-solid fa-clock" style="color: {{ $log->first_contact_within_45_min ? '#10B981' : 'rgba(255,255,255,0.2)' }};"></i>
                            <i class="fa-solid fa-phone" style="color: {{ $log->all_leads_followed_up ? '#10B981' : 'rgba(255,255,255,0.2)' }};"></i>
                            <i class="fa-solid fa-database" style="color: {{ $log->crm_disposition_correct ? '#10B981' : 'rgba(255,255,255,0.2)' }};"></i>
                            <i class="fa-solid fa-fire" style="color: {{ $log->warm_lead_converted ? '#10B981' : 'rgba(255,255,255,0.2)' }};"></i>
                        </div>
                    </td>
                    <td style="padding: 12px; font-weight: 600; color: {{ $log->calculated_score >= 0 ? '#10B981' : '#F43F5E' }};">{{ $log->calculated_score >= 0 ? '+' : '' }}{{ $log->calculated_score }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 24px; text-align: center; color: #aaa;">No logs found for the selected date.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'weekly')
        <table class="table table-hover align-middle" style="color: #F0F4FF;">
            <thead style="background: rgba(59,123,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1);">
                <tr style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; font-weight: 600; letter-spacing: 0.5px;">
                    <th style="padding: 12px; border: none;">Executive</th>
                    <th style="padding: 12px; border: none;">Total Calls</th>
                    <th style="padding: 12px; border: none;">Meetings Arr.</th>
                    <th style="padding: 12px; border: none;">Meetings Att.</th>
                    <th style="padding: 12px; border: none;">Total Points</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid rgba(255,255,255,0.07);">
                @forelse($data as $row)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">
                    <td style="padding: 12px; font-weight: 600;">{{ $row->executive->name ?? '—' }}</td>
                    <td style="padding: 12px; font-weight: 600;">{{ $row->calls }}</td>
                    <td style="padding: 12px;">{{ $row->arranged }}</td>
                    <td style="padding: 12px;">{{ $row->attended }}</td>
                    <td style="padding: 12px; font-weight: 600; color: {{ $row->total_score >= 0 ? '#10B981' : '#F43F5E' }};">{{ $row->total_score >= 0 ? '+' : '' }}{{ $row->total_score }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 24px; text-align: center; color: #aaa;">No data for selected range.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'monthly')
        <div style="overflow-x: auto;">
        <table class="table table-sm align-middle" style="border-collapse: collapse; min-width: 1200px; color: #F0F4FF;">
            <thead style="background: rgba(59,123,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1);">
                <tr style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; font-weight: 600; letter-spacing: 0.5px;">
                    <th style="padding: 12px; text-align: left; border: none;">Executive</th>
                    <th style="padding: 12px; border: none; background: rgba(124,58,237,0.1);">Current Tier</th>
                    <th style="padding: 12px; border: none;">6M Score</th>
                    <th style="padding: 12px; border: none;">Prev Month</th>
                    <th style="padding: 12px; border: none; background: rgba(124,58,237,0.1);">Change</th>
                    <th style="padding: 12px; border: none;">Conv. Target</th>
                    <th style="padding: 12px; border: none;">Conversions</th>
                    <th style="padding: 12px; border: none;">Meetings Arr.</th>
                    <th style="padding: 12px; border: none;">Meetings Att.</th>
                    <th style="padding: 12px; border: none; text-align: left;">CRO Notes</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid rgba(255,255,255,0.07);">
                @forelse($monthlyData ?? collect() as $exec)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">
                    <td style="padding: 12px; text-align: left; font-weight: 600;">{{ $exec->name }}</td>
                    <td style="padding: 12px; text-align: center; font-weight: 700; color: #fff; background: {{ $exec->tierColor ?? '#95a5a6' }}; border-radius: 4px;">
                        {{ strtoupper(str_replace('_',' ', $exec->current_tier)) }}
                    </td>
                    <td style="padding: 12px; text-align: center; font-weight: 600;">{{ $exec->current_score ?? 0 }}</td>
                    <td style="padding: 12px; text-align: center;">{{ $exec->prev_score ?? 0 }}</td>
                    <td style="padding: 12px; text-align: center; font-weight: 600; color: {{ ($exec->score_change ?? 0) >= 0 ? '#10B981' : '#F43F5E' }};">
                        {{ ($exec->score_change ?? 0) >= 0 ? '+' : '' }}{{ $exec->score_change ?? 0 }}
                    </td>
                    <td style="padding: 12px; text-align: center;">{{ $exec->conversion_target ?? 10 }}</td>
                    <td style="padding: 12px; text-align: center; font-weight: 600;">{{ $exec->conversions ?? 0 }}</td>
                    <td style="padding: 12px; text-align: center;">{{ $exec->meetings_arranged ?? 0 }}</td>
                    <td style="padding: 12px; text-align: center;">{{ $exec->meetings_attended ?? 0 }}</td>
                    <td style="padding: 12px; text-align: left; font-size: 0.85rem; color: #aaa;">
                        {{ $exec->cro_notes ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="padding: 24px; text-align: center; color: #aaa;">No monthly data available. Please select a valid month.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Summary Metrics (Monthly) --}}
        @if(($monthlyData ?? collect())->count() > 0)
        <div style="margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.07);">
            <h6 style="font-weight: 700; margin-bottom: 16px; color: #F0F4FF; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">SUMMARY METRICS</h6>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px;">
                <div style="background: rgba(59,123,255,0.1); border: 1px solid rgba(59,123,255,0.2); border-radius: 8px; padding: 16px; text-align: center;">
                    <div style="font-weight: 700; font-size: 24px; color: #3B7BFF;">{{ $monthlyData->count() }}</div>
                    <small style="color: #aaa; font-size: 0.75rem; text-transform: uppercase;">Total Executives</small>
                </div>
                <div style="background: rgba(244,63,94,0.1); border: 1px solid rgba(244,63,94,0.2); border-radius: 8px; padding: 16px; text-align: center;">
                    <div style="font-weight: 700; font-size: 24px; color: #F43F5E;">{{ $monthlyData->where('current_tier', 'review_zone')->count() }}</div>
                    <small style="color: #aaa; font-size: 0.75rem; text-transform: uppercase;">In Review Zone</small>
                </div>
                <div style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2); border-radius: 8px; padding: 16px; text-align: center;">
                    <div style="font-weight: 700; font-size: 24px; color: #F59E0B;">{{ $monthlyData->whereIn('current_tier', ['gold', 'platinum'])->count() }}</div>
                    <small style="color: #aaa; font-size: 0.75rem; text-transform: uppercase;">Gold/Platinum</small>
                </div>
                <div style="background: rgba(59,123,255,0.1); border: 1px solid rgba(59,123,255,0.2); border-radius: 8px; padding: 16px; text-align: center;">
                    <div style="font-weight: 700; font-size: 24px; color: #3B7BFF;">{{ round($monthlyData->avg('current_score'), 0) }}</div>
                    <small style="color: #aaa; font-size: 0.75rem; text-transform: uppercase;">Avg 6M Score</small>
                </div>
                <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); border-radius: 8px; padding: 16px; text-align: center;">
                    <div style="font-weight: 700; font-size: 24px; color: #10B981;">{{ $monthlyData->max('current_score') }}</div>
                    <small style="color: #aaa; font-size: 0.75rem; text-transform: uppercase;">Highest</small>
                </div>
                <div style="background: rgba(244,63,94,0.1); border: 1px solid rgba(244,63,94,0.2); border-radius: 8px; padding: 16px; text-align: center;">
                    <div style="font-weight: 700; font-size: 24px; color: #F43F5E;">{{ $monthlyData->min('current_score') }}</div>
                    <small style="color: #aaa; font-size: 0.75rem; text-transform: uppercase;">Lowest</small>
                </div>
            </div>
        </div>
        @endif

        @elseif($type === 'zonal')
        <table class="table table-hover align-middle" style="color: #F0F4FF;">
            <thead style="background: rgba(59,123,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1);">
                <tr style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; font-weight: 600; letter-spacing: 0.5px;">
                    <th style="padding: 12px; border: none;">Zone</th>
                    <th style="padding: 12px; border: none;">Code</th>
                    <th style="padding: 12px; border: none;">Executives</th>
                    <th style="padding: 12px; border: none;">Average Score</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid rgba(255,255,255,0.07);">
                @forelse($data as $zone)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">
                    <td style="padding: 12px; font-weight: 600;">{{ $zone->name }}</td>
                    <td style="padding: 12px; color: #aaa; font-family: monospace; font-size: 0.85rem;">{{ $zone->code }}</td>
                    <td style="padding: 12px;">{{ $zone->executives_count }}</td>
                    <td style="padding: 12px;">
                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                            <div style="background: linear-gradient(90deg, #3B7BFF 0%, rgba(59,123,255,0.2) 100%); height: 6px; width: 120px; border-radius: 3px; display: inline-block; vertical-align: middle;"></div>
                            <span style="font-weight: 600;">{{ round($zone->executives_avg_current_score, 1) }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding: 24px; text-align: center; color: #aaa;">No zone data found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'tier')
        <table class="table table-hover align-middle" style="color: #F0F4FF;">
            <thead style="background: rgba(59,123,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1);">
                <tr style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; font-weight: 600; letter-spacing: 0.5px;">
                    <th style="padding: 12px; border: none;">Tier</th>
                    <th style="padding: 12px; border: none;">Executive Count</th>
                    <th style="padding: 12px; border: none;">Percentage</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid rgba(255,255,255,0.07);">
                @php $total = $data->sum('count'); @endphp
                @forelse($data as $row)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">
                    <td style="padding: 12px;">
                        <span style="padding: 4px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; background: 
                            {{ $row->current_tier === 'platinum' ? 'rgba(155,89,182,0.2)' : 
                               ($row->current_tier === 'gold' ? 'rgba(245,158,11,0.2)' : 
                               ($row->current_tier === 'silver' ? 'rgba(149,165,166,0.2)' : 
                               ($row->current_tier === 'bronze' ? 'rgba(211,84,0,0.2)' : 'rgba(244,63,94,0.2)')))}}; 
                            color: 
                            {{ $row->current_tier === 'platinum' ? '#9b59b6' : 
                               ($row->current_tier === 'gold' ? '#F59E0B' : 
                               ($row->current_tier === 'silver' ? '#95a5a6' : 
                               ($row->current_tier === 'bronze' ? '#d35400' : '#F43F5E')))}};">
                            {{ str_replace('_',' ',ucwords($row->current_tier)) }}
                        </span>
                    </td>
                    <td style="padding: 12px; font-weight: 600; font-size: 1.1rem;">{{ $row->count }}</td>
                    <td style="padding: 12px;">
                        @php $pct = $total > 0 ? round(($row->count / $total)*100,1) : 0; @endphp
                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                            <div style="background: linear-gradient(90deg, #3B7BFF 0%, rgba(59,123,255,0.2) 100%); height: 6px; width: 120px; border-radius: 3px; display: inline-block; vertical-align: middle;"></div>
                            <span style="font-weight: 600;">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding: 24px; text-align: center; color: #aaa;">No tier data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'violation')
        <table class="table table-hover align-middle" style="color: #F0F4FF;">
            <thead style="background: rgba(59,123,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1);">
                <tr style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; font-weight: 600; letter-spacing: 0.5px;">
                    <th style="padding: 12px; border: none;">Date</th>
                    <th style="padding: 12px; border: none;">Executive</th>
                    <th style="padding: 12px; border: none;">Type</th>
                    <th style="padding: 12px; border: none;">Points Deducted</th>
                    <th style="padding: 12px; border: none;">Status</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid rgba(255,255,255,0.07);">
                @forelse($data as $v)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">
                    <td style="padding: 12px;">{{ $v->date_committed->toDateString() }}</td>
                    <td style="padding: 12px; font-weight: 600;">{{ $v->executive->name }} <small style="color: #aaa;">({{ $v->executive->employee_id }})</small></td>
                    <td style="padding: 12px;"><span style="padding: 4px 8px; border-radius: 4px; background: rgba(244,63,94,0.2); color: #F43F5E; font-size: 0.85rem; font-weight: 600;">{{ str_replace('_',' ',$v->violation_type) }}</span></td>
                    <td style="padding: 12px; color: #F43F5E; font-weight: 600;">-{{ $v->points_deducted }} pts</td>
                    <td style="padding: 12px;"><span style="padding: 4px 8px; border-radius: 4px; background: {{ $v->status === 'active' ? 'rgba(244,63,94,0.2)' : 'rgba(127,127,127,0.2)' }}; color: {{ $v->status === 'active' ? '#F43F5E' : '#aaa' }}; font-size: 0.85rem; font-weight: 600;">{{ $v->status }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 24px; text-align: center; color: #aaa;">No violations recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type === 'pip')
        <table class="table table-hover align-middle" style="color: #F0F4FF;">
            <thead style="background: rgba(59,123,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1);">
                <tr style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; font-weight: 600; letter-spacing: 0.5px;">
                    <th style="padding: 12px; border: none;">Executive</th>
                    <th style="padding: 12px; border: none;">Start</th>
                    <th style="padding: 12px; border: none;">End</th>
                    <th style="padding: 12px; border: none;">Target</th>
                    <th style="padding: 12px; border: none;">Current Score</th>
                    <th style="padding: 12px; border: none;">Status</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid rgba(255,255,255,0.07);">
                @forelse($data as $pip)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">
                    <td style="padding: 12px; font-weight: 600;">{{ $pip->executive->name }}</td>
                    <td style="padding: 12px;">{{ $pip->start_date->toDateString() }}</td>
                    <td style="padding: 12px;">{{ $pip->end_date->toDateString() }}</td>
                    <td style="padding: 12px; font-weight: 600; color: #3B7BFF;">{{ $pip->target_score }} pts</td>
                    <td style="padding: 12px; font-weight: 600; color: {{ $pip->executive->current_score >= 0 ? '#10B981' : '#F43F5E' }};">{{ $pip->executive->current_score }}</td>
                    <td style="padding: 12px;"><span style="padding: 4px 8px; border-radius: 4px; background: 
                        {{ $pip->status === 'active' ? 'rgba(245,158,11,0.2)' : 
                           ($pip->status === 'completed' ? 'rgba(16,185,129,0.2)' : 'rgba(244,63,94,0.2)')}}; 
                        color: {{ $pip->status === 'active' ? '#F59E0B' : 
                           ($pip->status === 'completed' ? '#10B981' : '#F43F5E')}}; 
                        font-size: 0.85rem; font-weight: 600;">{{ $pip->status }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 24px; text-align: center; color: #aaa;">No PIP records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
