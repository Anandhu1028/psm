@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'System Administration')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="glass-card p-4 text-center">
            <i class="fa-solid fa-users-gear fa-2x text-primary mb-3"></i>
            <div class="stat-card-value text-primary">{{ $totalUsers }}</div>
            <div class="stat-card-label">System Users</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 text-center">
            <i class="fa-solid fa-sliders fa-2x text-success mb-3"></i>
            <div class="stat-card-value text-success">{{ $rulesCount }}</div>
            <div class="stat-card-label">Active Score Rules</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 text-center">
            <i class="fa-solid fa-user-tie fa-2x text-info mb-3"></i>
            <div class="stat-card-value text-info">{{ $totalExecs }}</div>
            <div class="stat-card-label">Total Executives</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Recent Activity Log</h5>
                <span class="badge bg-primary">Last 10 actions</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.72rem;">
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Event</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivities as $log)
                        <tr>
                            <td class="text-secondary small">{{ $log->created_at->format('d M, H:i') }}</td>
                            <td class="fw-semibold">{{ $log->user?->name ?? 'System' }}</td>
                            <td>
                                <span class="badge rounded-3
                                    {{ $log->event_type === 'login' ? 'bg-success' :
                                       ($log->event_type === 'logout' ? 'bg-secondary' :
                                       ($log->event_type === 'score_mod' ? 'bg-danger' : 'bg-info')) }}">
                                    {{ strtoupper($log->event_type) }}
                                </span>
                            </td>
                            <td class="text-secondary small">{{ Str::limit($log->description, 60) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-secondary">No activity recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-4"><i class="fa-solid fa-toolbox me-2 text-warning"></i>Quick Admin Actions</h5>
            <div class="d-grid gap-3">
                <a href="{{ route('admin.rules.index') }}" class="btn btn-outline-primary rounded-3 text-start px-3">
                    <i class="fa-solid fa-sliders me-2"></i>Configure Point Engine Rules
                </a>
                <a href="{{ route('executives.create') }}" class="btn btn-outline-success rounded-3 text-start px-3">
                    <i class="fa-solid fa-user-plus me-2"></i>Add New Executive
                </a>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-info rounded-3 text-start px-3">
                    <i class="fa-solid fa-file-chart-column me-2"></i>Generate Reports
                </a>
                <a href="{{ route('pips.create') }}" class="btn btn-outline-warning rounded-3 text-start px-3">
                    <i class="fa-solid fa-file-signature me-2"></i>Launch PIP Plan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
