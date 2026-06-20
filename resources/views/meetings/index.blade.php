@extends('layouts.app')

@section('title', 'Meeting Tracker')
@section('page_title', 'Meeting Tracker')

@section('content')

{{-- Stats Row --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="glass-card p-4 text-center">
            <i class="fa-solid fa-handshake fa-2x text-primary mb-2"></i>
            <div class="stat-card-value text-primary">{{ $totalMeetings }}</div>
            <div class="stat-card-label">Total Meetings</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 text-center">
            <i class="fa-solid fa-circle-check fa-2x text-success mb-2"></i>
            <div class="stat-card-value text-success">{{ $attendedCount }}</div>
            <div class="stat-card-label">Attended</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 text-center">
            <i class="fa-solid fa-percent fa-2x text-info mb-2"></i>
            <div class="stat-card-value text-info">{{ $attendanceRate }}%</div>
            <div class="stat-card-label">Attendance Rate</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold m-0">{{ $meetings->total() }} Records</h6>
    <a href="{{ route('meetings.create') }}" class="btn btn-primary rounded-3 px-4">
        <i class="fa-solid fa-plus me-2"></i>Log New Meeting
    </a>
</div>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase font-monospace" style="font-size: 0.72rem;">
                <tr>
                    <th>Meeting Date</th>
                    <th>Executive</th>
                    <th>Lead Name</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>2-Day</th>
                    <th>3-Day</th>
                    <th>CRM Ref</th>
                </tr>
            </thead>
            <tbody>
                @forelse($meetings as $meeting)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $meeting->meeting_date->format('d M Y') }}</div>
                        <small class="text-secondary">{{ $meeting->meeting_time }}</small>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $meeting->executive->name }}</div>
                        <small class="text-secondary">{{ $meeting->executive->employee_id }}</small>
                    </td>
                    <td>{{ $meeting->lead_name }}</td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary border text-capitalize">
                            {{ str_replace('_',' ',$meeting->meeting_type) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusColors = ['scheduled'=>'info','attended'=>'success','missed'=>'danger','cancelled'=>'secondary'];
                            $color = $statusColors[$meeting->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle text-capitalize">
                            {{ $meeting->status }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($meeting->passed_two_day_checkpoint)
                            <i class="fa-solid fa-circle-check text-success" title="Within 2 days"></i>
                        @else
                            <i class="fa-solid fa-circle-xmark text-secondary opacity-50" title="Exceeded 2 days"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($meeting->passed_three_day_checkpoint)
                            <i class="fa-solid fa-circle-check text-success" title="Within 3 days"></i>
                        @else
                            <i class="fa-solid fa-circle-xmark text-secondary opacity-50" title="Exceeded 3 days"></i>
                        @endif
                    </td>
                    <td>
                        <small class="font-monospace text-secondary">{{ $meeting->crm_reference ?? '—' }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-secondary">
                        <i class="fa-solid fa-calendar-xmark fa-2x mb-3 d-block"></i>No meeting records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($meetings->hasPages())
    <div class="d-flex justify-content-center mt-4">{{ $meetings->links() }}</div>
    @endif
</div>
@endsection
