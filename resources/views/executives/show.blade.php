@extends('layouts.app')
@section('title', $executive->name . ' — Profile')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('executives.index') }}">Executives</a></li>
    <li class="breadcrumb-item active">{{ $executive->name }}</li>
</ol>
@endsection
@section('content')

<div class="pms-page-header">
    <div>
        <h1 class="pms-page-title"><i class="fa-solid fa-user-tie me-2" style="color:var(--pms-accent);"></i>{{ $executive->name }}</h1>
        <p class="pms-page-subtitle">{{ $executive->employee_id }} · {{ $executive->company->name }} · {{ $executive->zone->name }}</p>
    </div>
    <div class="d-flex gap-2">
        @can('manage_executives')
        <a href="{{ route('executives.edit', $executive) }}" class="btn btn-pms-secondary">
            <i class="fa-solid fa-pen me-2"></i>Edit
        </a>
        @endcan
        <a href="{{ route('daily_audit.create') }}?executive_id={{ $executive->id }}" class="btn btn-pms-primary">
            <i class="fa-solid fa-plus me-2"></i>Enter Audit
        </a>
    </div>
</div>

<div class="row g-3">

    {{-- LEFT: Profile + Score Cards --}}
    <div class="col-xl-3">

        {{-- Profile Card --}}
        <div class="pms-card mb-3 text-center">
            <div class="exec-avatar-lg">{{ strtoupper(substr($executive->name,0,2)) }}</div>
            <div class="exec-profile-name">{{ $executive->name }}</div>
            <div class="exec-profile-sub">{{ $executive->employee_id }}</div>

            <div class="my-3">
                <span class="badge badge-tier-{{ $executive->current_tier }}" style="font-size:.8rem;padding:6px 14px;">
                    {{ $executive->tier_label }}
                </span>
            </div>

            <div class="row g-2 mt-2 text-start">
                @php
                $info = [
                    ['icon'=>'fa-building',         'label'=>'Company', 'val'=>$executive->company->name],
                    ['icon'=>'fa-map-marker-alt',    'label'=>'Zone',    'val'=>$executive->zone->name],
                    ['icon'=>'fa-mobile-alt',        'label'=>'Mobile',  'val'=>$executive->mobile ?? '—'],
                    ['icon'=>'fa-envelope',          'label'=>'Email',   'val'=>$executive->email ?? '—'],
                    ['icon'=>'fa-calendar-alt',      'label'=>'Joined',  'val'=>$executive->date_joined?->format('d M Y') ?? '—'],
                ];
                @endphp
                @foreach($info as $item)
                <div class="col-12">
                    <div class="d-flex align-items-start gap-2 p-2 rounded-2" style="background:var(--pms-bg-elevated);">
                        <i class="fa-solid {{ $item['icon'] }}" style="color:var(--pms-accent);width:14px;margin-top:2px;font-size:.75rem;"></i>
                        <div>
                            <div style="font-size:.6rem;color:var(--pms-text-muted);text-transform:uppercase;letter-spacing:.06em;">{{ $item['label'] }}</div>
                            <div style="font-size:.8rem;color:var(--pms-text-primary);font-weight:500;">{{ $item['val'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="background:{{ $executive->status==='active' ? 'var(--pms-success-light)' : 'var(--pms-warning-light)' }};">
                        <i class="fa-solid fa-circle" style="color:{{ $executive->status==='active' ? 'var(--pms-success)' : 'var(--pms-warning)' }};font-size:.5rem;"></i>
                        <span style="font-size:.8rem;font-weight:600;color:{{ $executive->status==='active' ? 'var(--pms-success)' : 'var(--pms-warning)' }};">{{ ucfirst($executive->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Score Summary --}}
        <div class="pms-card">
            <div class="pms-card-title mb-3"><i class="fa-solid fa-coins"></i> Score Summary</div>
            @php
            $scoreTiles = [
                ['label'=>'Total Score',   'val'=> number_format($executive->current_score), 'color'=>'var(--pms-accent)'],
                ['label'=>'Monthly Score', 'val'=> number_format($executive->monthly_score),  'color'=>'var(--pms-success)'],
                ['label'=>'Call Streak',   'val'=> ($executive->call_streak_count ?? 0) . ' days', 'color'=>'var(--pms-warning)'],
                ['label'=>'Mtg Streak',    'val'=> ($executive->meeting_streak_count ?? 0) . ' days', 'color'=>'var(--pms-info)'],
            ];
            @endphp
            @foreach($scoreTiles as $tile)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:var(--pms-border)!important;">
                <span style="font-size:.78rem;color:var(--pms-text-secondary);">{{ $tile['label'] }}</span>
                <span style="font-size:.9rem;font-weight:800;color:{{ $tile['color'] }};">{{ $tile['val'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- RIGHT: Charts + History --}}
    <div class="col-xl-9">

        {{-- Monthly Score Chart --}}
        <div class="pms-card mb-3">
            <div class="pms-card-header">
                <div class="pms-card-title"><i class="fa-solid fa-chart-line"></i> Monthly Score History</div>
            </div>
            <div style="height:220px;">
                <canvas id="monthlyScoreChart"></canvas>
            </div>
        </div>

        <div class="row g-3 mb-3">
            {{-- Recent Audits --}}
            <div class="col-md-7">
                <div class="pms-card h-100">
                    <div class="pms-card-header">
                        <div class="pms-card-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Audits</div>
                        <a href="{{ route('daily_audit.index', ['executive_id'=>$executive->id]) }}" style="font-size:.75rem;color:var(--pms-accent);">View All</a>
                    </div>
                    <div class="pms-table-wrapper" style="box-shadow:none;border:none;">
                        <table class="pms-table">
                            <thead><tr><th>Date</th><th class="text-center">Calls</th><th class="text-center">Meetings</th><th class="text-center">Score</th><th class="text-center">KPI</th></tr></thead>
                            <tbody>
                                @forelse($executive->dailyAudits as $audit)
                                <tr>
                                    <td><a href="{{ route('daily_audit.show', $audit) }}" style="color:var(--pms-accent);font-size:.8rem;font-weight:500;text-decoration:none;">{{ $audit->audit_date->format('d M Y') }}</a></td>
                                    <td class="text-center" style="font-size:.82rem;">{{ $audit->connected_calls }}</td>
                                    <td class="text-center" style="font-size:.82rem;">{{ $audit->confirmed_meetings }}</td>
                                    <td class="text-center">
                                        <span style="font-weight:800;font-size:.85rem;color:{{ $audit->final_score>=0?'var(--pms-success)':'var(--pms-danger)' }};">
                                            {{ $audit->final_score>=0?'+':'' }}{{ $audit->final_score }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($audit->kpi_status==='passed')
                                            <span class="badge" style="background:var(--pms-success-subtle);color:var(--pms-success);">✓</span>
                                        @elseif($audit->kpi_status==='failed')
                                            <span class="badge" style="background:var(--pms-danger-subtle);color:var(--pms-danger);">✗</span>
                                        @else
                                            <span class="badge badge-status-draft">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center" style="padding:24px;color:var(--pms-text-muted);font-size:.82rem;">No audits yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tier History --}}
            <div class="col-md-5">
                <div class="pms-card h-100">
                    <div class="pms-card-header">
                        <div class="pms-card-title"><i class="fa-solid fa-medal"></i> Tier History</div>
                    </div>
                    @forelse($executive->tierHistories as $th)
                    <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded-2" style="background:var(--pms-bg-elevated);">
                        <span class="badge badge-tier-{{ $th->new_tier }}" style="min-width:60px;text-align:center;">{{ ucfirst($th->new_tier) }}</span>
                        <div class="flex-grow-1">
                            @if($th->previous_tier)
                            <span style="font-size:.7rem;color:var(--pms-text-muted);">from {{ ucfirst($th->previous_tier) }}</span>
                            @endif
                        </div>
                        <span style="font-size:.7rem;color:var(--pms-text-muted);">{{ $th->changed_at->format('d M Y') }}</span>
                    </div>
                    @empty
                    <div class="pms-empty" style="padding:30px;"><i class="fa-solid fa-medal"></i><p>No tier changes yet</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="pms-card">
            <div class="pms-card-header">
                <div class="pms-card-title"><i class="fa-solid fa-coins"></i> Recent Point Transactions</div>
                <a href="{{ route('point_history.index', ['executive_id'=>$executive->id]) }}" style="font-size:.75rem;color:var(--pms-accent);">Full History</a>
            </div>
            <div class="pms-table-wrapper" style="box-shadow:none;border:none;">
                <table class="pms-table">
                    <thead><tr><th>Date</th><th>Description</th><th>Category</th><th class="text-end">Points</th></tr></thead>
                    <tbody>
                        @forelse($executive->pointTransactions as $tx)
                        <tr>
                            <td style="font-size:.75rem;color:var(--pms-text-muted);white-space:nowrap;">{{ $tx->audit_date->format('d M Y') }}</td>
                            <td style="font-size:.8rem;">{{ $tx->description }}</td>
                            <td><span class="badge" style="background:var(--pms-bg-elevated);color:var(--pms-text-secondary);border:1px solid var(--pms-border);">{{ ucfirst($tx->category) }}</span></td>
                            <td class="text-end" style="font-weight:700;color:{{ $tx->type==='credit'?'var(--pms-success)':'var(--pms-danger)' }};">
                                {{ $tx->type==='credit'?'+':'-' }}{{ $tx->points }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center" style="padding:24px;color:var(--pms-text-muted);">No transactions yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
@push('scripts')
<script>
const monthlyScores = @json($monthlyScores);
const labels  = monthlyScores.map(s => {
    const d = new Date(s.year, s.month - 1);
    return d.toLocaleString('default', { month:'short', year:'2-digit' });
}).reverse();
const scores = monthlyScores.map(s => s.net_score).reverse();

new Chart(document.getElementById('monthlyScoreChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Net Score',
            data: scores,
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79,70,229,0.08)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#4f46e5',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor:'#fff', titleColor:'#0f172a', bodyColor:'#475569', borderColor:'#e4e8f0', borderWidth:1 }
        },
        scales: {
            x: { grid:{ display:false }, ticks:{ font:{family:'Inter',size:11}, color:'#94a3b8' } },
            y: { grid:{ color:'#f1f5f9' }, ticks:{ font:{family:'Inter',size:11}, color:'#94a3b8' } }
        }
    }
});
</script>
@endpush
