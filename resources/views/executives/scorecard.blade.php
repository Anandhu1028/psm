@extends('layouts.app')

@section('title', $executive->name . ' — Scorecard')
@section('page_title', 'Executive Scorecard')

@section('content')

{{-- Header Profile Card --}}
<div class="glass-card p-4 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-auto">
            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 80px; height: 80px; font-size: 2rem; font-weight: 800; color: #2563eb;">
                {{ strtoupper(substr($executive->name, 0, 2)) }}
            </div>
        </div>
        <div class="col">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h3 class="fw-bold m-0">{{ $executive->name }}</h3>
                <span class="tier-badge tier-{{ $executive->current_tier }}">
                    {{ str_replace('_', ' ', ucwords($executive->current_tier)) }}
                </span>
                @if($executive->status === 'active')
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                @elseif($executive->status === 'probation')
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Probation</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border">Inactive</span>
                @endif
            </div>
            <div class="d-flex gap-4 mt-2 flex-wrap text-secondary small">
                <span><i class="fa-solid fa-id-badge me-1"></i>{{ $executive->employee_id }}</span>
                <span><i class="fa-solid fa-envelope me-1"></i>{{ $executive->email }}</span>
                <span><i class="fa-solid fa-phone me-1"></i>{{ $executive->phone }}</span>
                <span><i class="fa-solid fa-map-marker-alt me-1"></i>{{ $executive->zone->name ?? 'N/A' }}</span>
                <span><i class="fa-solid fa-calendar me-1"></i>Joined {{ $executive->date_joined->format('d M Y') }}</span>
            </div>
        </div>
        <div class="col-auto text-end">
            <div class="text-secondary small fw-semibold text-uppercase mb-1">Cumulative Score</div>
            <div class="display-5 fw-bold {{ $executive->current_score >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $executive->current_score >= 0 ? '+' : '' }}{{ $executive->current_score }}
            </div>
            <small class="text-secondary">points balance</small>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Score Transactions Ledger --}}
    <div class="col-lg-7">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="fa-solid fa-receipt me-2 text-primary"></i>Score Transaction Ledger</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.7rem;">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Points</th>
                            <th>Balance</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($executive->scoreTransactions as $tx)
                        <tr>
                            <td class="text-secondary small">{{ $tx->transaction_date->format('d M') }}</td>
                            <td>
                                <span class="badge {{ $tx->type === 'credit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border">
                                    {{ strtoupper($tx->type) }}
                                </span>
                            </td>
                            <td class="fw-bold {{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $tx->type === 'credit' ? '+' : '-' }}{{ $tx->points }}
                            </td>
                            <td class="fw-semibold font-monospace">{{ $tx->running_total }}</td>
                            <td class="text-secondary small">{{ Str::limit($tx->description, 45) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-secondary">No transactions recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right Column: Violations + Tier History --}}
    <div class="col-lg-5">
        {{-- Tier Progression --}}
        <div class="glass-card p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-arrow-up-right-dots me-2 text-warning"></i>Tier History</h5>
            @forelse($executive->tierHistories->sortByDesc('changed_at') as $th)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <span class="tier-badge tier-{{ $th->old_tier }}" style="font-size: 0.65rem;">{{ str_replace('_',' ',$th->old_tier) }}</span>
                    <i class="fa-solid fa-arrow-right text-secondary mx-2"></i>
                    <span class="tier-badge tier-{{ $th->new_tier }}" style="font-size: 0.65rem;">{{ str_replace('_',' ',$th->new_tier) }}</span>
                </div>
                <small class="text-secondary">{{ \Carbon\Carbon::parse($th->changed_at)->format('d M Y') }}</small>
            </div>
            @empty
            <p class="text-secondary small">No tier changes recorded.</p>
            @endforelse
        </div>

        {{-- Active Violations --}}
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-ban me-2 text-danger"></i>Violations</h5>
            @forelse($executive->violations->take(5) as $v)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <span class="fw-semibold text-capitalize">{{ str_replace('_',' ',$v->violation_type) }}</span>
                    <span class="badge {{ $v->status === 'active' ? 'bg-danger' : 'bg-secondary' }} ms-2">{{ $v->status }}</span>
                    <div class="text-danger small">-{{ $v->points_deducted }} pts</div>
                </div>
                <small class="text-secondary">{{ $v->date_committed->format('d M Y') }}</small>
            </div>
            @empty
            <p class="text-secondary small"><i class="fa-solid fa-circle-check text-success me-1"></i>No violations on record.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Active PIP + Escalations --}}
<div class="row g-4">
    <div class="col-lg-6">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-line me-2 text-info"></i>PIP Records</h5>
            @forelse($executive->pipRecords as $pip)
            <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                <div>
                    <div class="fw-semibold">Target: {{ $pip->target_score }} pts</div>
                    <small class="text-secondary">{{ $pip->start_date->toDateString() }} → {{ $pip->end_date->toDateString() }}</small>
                </div>
                <span class="badge {{ $pip->status === 'active' ? 'bg-warning text-dark' : ($pip->status === 'completed' ? 'bg-success' : 'bg-danger') }}">
                    {{ $pip->status }}
                </span>
            </div>
            @empty
            <p class="text-secondary small">No PIP plans initiated.</p>
            @endforelse
        </div>
    </div>
    <div class="col-lg-6">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-bell me-2 text-warning"></i>Escalations</h5>
            @forelse($executive->escalations as $esc)
            <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                <div>
                    <div class="fw-semibold text-capitalize">{{ str_replace('_',' ',$esc->type) }}</div>
                    <small class="text-secondary">{{ Str::limit($esc->trigger_reason, 55) }}</small>
                </div>
                <span class="badge {{ $esc->status === 'open' ? 'bg-danger' : ($esc->status === 'resolved' ? 'bg-success' : 'bg-warning text-dark') }}">
                    {{ $esc->status }}
                </span>
            </div>
            @empty
            <p class="text-secondary small"><i class="fa-solid fa-circle-check text-success me-1"></i>No escalations raised.</p>
            @endforelse
        </div>
    </div>
</div>


@endsection
