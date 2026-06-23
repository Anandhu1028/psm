@extends('layouts.app')

@section('title', '')
@section('page_title', '')
@section('page_subtitle', '')

@section('content')

    {{-- ===== TOP BAR ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold m-0 text-white" style="font-size: 18px;">Meeting Tracker</h5>
            <p class="text-secondary small m-0 mt-1">Track scheduled meetings, attendance, and CRM follow-up compliance</p>
        </div>

    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="row g-3 mb-4">

        {{-- Total Meetings --}}
        <div class="col-6 col-md-4 col-xl">
            <div class="tims-stat-card tims-stat-cyan">
                <div class="tims-stat-label" style="color:#22d3ee;">
                    <i class="fa-solid fa-handshake"></i> Total Meetings
                </div>
                <div class="tims-stat-value" style="color:#22d3ee;">{{ $totalMeetings }}</div>
                <div class="tims-stat-sub">all time</div>
            </div>
        </div>

        {{-- Attended --}}
        <div class="col-6 col-md-4 col-xl">
            <div class="tims-stat-card tims-stat-green">
                <div class="tims-stat-label" style="color:#4ade80;">
                    <i class="fa-solid fa-circle-check"></i> Attended
                </div>
                <div class="tims-stat-value" style="color:#4ade80;">{{ $attendedCount }}</div>
                <div class="tims-stat-sub">meetings attended</div>
            </div>
        </div>

        {{-- Attendance Rate --}}
        <div class="col-6 col-md-4 col-xl">
            <div class="tims-stat-card tims-stat-purple">
                <div class="tims-stat-label" style="color:#a78bfa;">
                    <i class="fa-solid fa-percent"></i> Attendance Rate
                </div>
                <div class="tims-stat-value" style="color:#c4b5fd;">{{ $attendanceRate }}%</div>
                <div class="tims-stat-sub">overall rate</div>
            </div>
        </div>

        {{-- Missed --}}
        <div class="col-6 col-md-4 col-xl">
            <div class="tims-stat-card tims-stat-red">
                <div class="tims-stat-label" style="color:#f87171;">
                    <i class="fa-solid fa-circle-xmark"></i> Missed
                </div>
                <div class="tims-stat-value" style="color:#f87171;">{{ $meetings->where('status', 'missed')->count() ?? 0 }}
                </div>
                <div class="tims-stat-sub">not attended</div>
            </div>
        </div>

        {{-- Scheduled --}}
        <div class="col-6 col-md-4 col-xl">
            <div class="tims-stat-card tims-stat-amber">
                <div class="tims-stat-label" style="color:#fbbf24;">
                    <i class="fa-solid fa-calendar-days"></i> Scheduled
                </div>
                <div class="tims-stat-value" style="color:#fbbf24;">
                    {{ $meetings->where('status', 'scheduled')->count() ?? 0 }}</div>
                <div class="tims-stat-sub">upcoming</div>
            </div>
        </div>

    </div>

    {{-- ===== SECTION BAR ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h6 class="fw-semibold text-white m-0" style="font-size: 15px;">All Meetings</h6>
            <span class="badge rounded-pill"
                style="background: rgba(99,102,241,0.15); color: #818cf8; font-size: 12px; padding: 3px 10px; border: 1px solid rgba(99,102,241,0.2);">
                {{ $meetings->total() }}
            </span>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Search --}}
            <div class="position-relative">
                <i class="fa-solid fa-magnifying-glass position-absolute text-secondary"
                    style="top: 50%; left: 10px; transform: translateY(-50%); font-size: 11px; pointer-events:none;"></i>
                <input type="text" id="meetingSearch" class="form-control form-control-sm ps-4"
                    placeholder="Search lead, executive…"
                    style="width:200px; background:rgba(255,255,255,0.04); border-color:rgba(255,255,255,0.08); color:#fff; font-size:12px;">
            </div>

            {{-- Status Filter --}}
            <select id="statusFilter" class="form-select form-select-sm"
                style="width:130px; background:rgba(255,255,255,0.04); border-color:rgba(255,255,255,0.08); color:#9ca3af; font-size:12px;">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="attended">Attended</option>
                <option value="missed">Missed</option>
                <option value="cancelled">Cancelled</option>
            </select>

            {{-- Export --}}
            <button class="btn btn-sm btn-outline-primary rounded-2" style="font-size:12px;">
                <i class="fa-solid fa-download me-1"></i>Export
            </button>
            <button class="btn btn-sm btn-primary rounded-2" data-bs-toggle="modal" data-bs-target="#newMeetingModal"
                style="font-size:12px;">
                <i class="fa-solid fa-plus me-1"></i>New Meeting
            </button>

        </div>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="tims-roster-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="meetingTable">
                <thead>
                    <tr>
                        <th>Meeting Date</th>
                        <th>Executive</th>
                        <th>Lead Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-center">2-Day</th>
                        <th class="text-center">3-Day</th>
                        <th>CRM Ref</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $meeting)
                        @php
                            $statusColors = [
                                'scheduled' => ['bg' => 'rgba(34,211,238,.12)', 'color' => '#22d3ee', 'border' => 'rgba(34,211,238,.25)'],
                                'attended' => ['bg' => 'rgba(74,222,128,.12)', 'color' => '#4ade80', 'border' => 'rgba(74,222,128,.25)'],
                                'missed' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'border' => 'rgba(248,113,113,.25)'],
                                'cancelled' => ['bg' => 'rgba(156,163,175,.10)', 'color' => '#9ca3af', 'border' => 'rgba(156,163,175,.2)'],
                            ];
                            $sc = $statusColors[$meeting->status] ?? $statusColors['cancelled'];
                        @endphp
                        <tr class="meeting-row">

                            {{-- Meeting Date --}}
                            <td>
                                <div class="fw-semibold text-white" style="font-size:13px;">
                                    {{ $meeting->meeting_date->format('d M Y') }}
                                </div>
                                <small class="text-secondary" style="font-size:11px;">{{ $meeting->meeting_time }}</small>
                            </td>

                            {{-- Executive --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                        style="width:32px;height:32px;background:linear-gradient(135deg,#6d5ce7,#111827);font-size:11px;border:1px solid rgba(255,255,255,0.1);">
                                        {{ strtoupper(substr($meeting->executive->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-white" style="font-size:13px;">
                                            {{ $meeting->executive->name }}</div>
                                        <small class="text-secondary"
                                            style="font-size:11px;">{{ $meeting->executive->employee_id }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Lead Name --}}
                            <td>
                                <span class="text-white" style="font-size:13px;">{{ $meeting->lead_name }}</span>
                            </td>

                            {{-- Type --}}
                            <td>
                                <span class="badge text-capitalize"
                                    style="background:rgba(99,102,241,0.12);color:#818cf8;border:1px solid rgba(99,102,241,0.2);font-size:11px;padding:4px 10px;border-radius:20px;">
                                    {{ str_replace('_', ' ', $meeting->meeting_type) }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="badge text-capitalize"
                                    style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};font-size:11px;padding:4px 10px;border-radius:20px;">
                                    {{ $meeting->status }}
                                </span>
                            </td>

                            {{-- 2-Day Checkpoint --}}
                            <td class="text-center">
                                @if($meeting->passed_two_day_checkpoint)
                                    <i class="fa-solid fa-circle-check text-success" style="font-size:16px;"
                                        title="Within 2 days"></i>
                                @else
                                    <i class="fa-solid fa-circle-xmark text-secondary opacity-50" style="font-size:16px;"
                                        title="Exceeded 2 days"></i>
                                @endif
                            </td>

                            {{-- 3-Day Checkpoint --}}
                            <td class="text-center">
                                @if($meeting->passed_three_day_checkpoint)
                                    <i class="fa-solid fa-circle-check text-success" style="font-size:16px;"
                                        title="Within 3 days"></i>
                                @else
                                    <i class="fa-solid fa-circle-xmark text-secondary opacity-50" style="font-size:16px;"
                                        title="Exceeded 3 days"></i>
                                @endif
                            </td>

                            {{-- CRM Ref --}}
                            <td>
                                <span class="font-monospace"
                                    style="font-size:11px;color:#6b7280;">{{ $meeting->crm_reference ?? '—' }}</span>
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-link text-secondary p-0 border-0 tims-action-dots-btn" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('meetings.show', $meeting->id) }}">
                                                <i class="fa-regular fa-eye me-2 text-info"></i> View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('meetings.edit', $meeting->id) }}">
                                                <i class="fa-regular fa-pen-to-square me-2 text-primary"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('meetings.destroy', $meeting->id) }}" method="POST"
                                                class="m-0" onsubmit="return confirm('Delete this meeting record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item text-danger" type="submit">
                                                    <i class="fa-regular fa-trash-can me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fa-solid fa-calendar-xmark fa-2x mb-3 d-block text-secondary"></i>
                                <span style="color:#5e6273;font-size:13px;">No meeting records found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($meetings->hasPages())
            <div
                class="d-flex justify-content-between align-items-center px-3 py-3 border-top border-secondary border-opacity-10">
                <small class="text-secondary" style="font-size:12px;">
                    Page {{ $meetings->currentPage() }} of {{ $meetings->lastPage() }}
                </small>
                <div>{{ $meetings->links() }}</div>
            </div>
        @endif
    </div>


    {{-- ===================================================== --}}
    {{-- MODAL: Log New Meeting --}}
    {{-- ===================================================== --}}
    <div class="modal fade" id="newMeetingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content tims-modal-content">

                {{-- Modal Header --}}
                <div class="modal-header tims-modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="tims-modal-icon">
                            <i class="fa-solid fa-handshake" style="color:#818cf8;font-size:15px;"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-white m-0" style="font-size:16px;">Log New Meeting</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        style="font-size:12px;opacity:.5;"></button>
                </div>

                {{-- Modal Body --}}
                <form method="POST" action="{{ route('meetings.store') }}">
                    @csrf
                    <div class="modal-body tims-modal-body">
                        <div class="row g-3">

                            {{-- Executive --}}
                            <div class="col-md-6">
                                <label class="tims-modal-label">EXECUTIVE</label>
                                <select name="executive_id"
                                    class="tims-modal-input @error('executive_id') is-invalid @enderror" required>
                                    <option value="">Select Executive...</option>
                                    @foreach($executives ?? [] as $exec)
                                        <option value="{{ $exec->id }}" {{ old('executive_id') == $exec->id ? 'selected' : '' }}>
                                            {{ $exec->name }} ({{ $exec->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('executive_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Lead Name --}}
                            <div class="col-md-6">
                                <label class="tims-modal-label">LEAD NAME</label>
                                <input type="text" name="lead_name"
                                    class="tims-modal-input @error('lead_name') is-invalid @enderror"
                                    placeholder="e.g. John Smith" value="{{ old('lead_name') }}" required>
                                @error('lead_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Meeting Date --}}
                            <div class="col-md-6">
                                <label class="tims-modal-label">MEETING DATE</label>
                                <input type="date" name="meeting_date"
                                    class="tims-modal-input @error('meeting_date') is-invalid @enderror"
                                    value="{{ old('meeting_date') }}" required>
                                @error('meeting_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Meeting Time --}}
                            <div class="col-md-6">
                                <label class="tims-modal-label">MEETING TIME</label>
                                <input type="time" name="meeting_time"
                                    class="tims-modal-input @error('meeting_time') is-invalid @enderror"
                                    value="{{ old('meeting_time') }}">
                                @error('meeting_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Meeting Type --}}
                            <div class="col-md-6">
                                <label class="tims-modal-label">MEETING TYPE</label>
                                <select name="meeting_type"
                                    class="tims-modal-input @error('meeting_type') is-invalid @enderror" required>
                                    <option value="">Select Type...</option>
                                    <option value="in_person" {{ old('meeting_type') === 'in_person' ? 'selected' : '' }}>In
                                        Person</option>
                                    <option value="virtual" {{ old('meeting_type') === 'virtual' ? 'selected' : '' }}>Virtual
                                    </option>
                                    <option value="phone_call" {{ old('meeting_type') === 'phone_call' ? 'selected' : '' }}>
                                        Phone Call</option>
                                    <option value="follow_up" {{ old('meeting_type') === 'follow_up' ? 'selected' : '' }}>
                                        Follow Up</option>
                                </select>
                                @error('meeting_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="tims-modal-label">STATUS</label>
                                <select name="status" class="tims-modal-input @error('status') is-invalid @enderror"
                                    required>
                                    <option value="scheduled" {{ old('status', 'scheduled') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="attended" {{ old('status') === 'attended' ? 'selected' : '' }}>Attended
                                    </option>
                                    <option value="missed" {{ old('status') === 'missed' ? 'selected' : '' }}>Missed</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                                    </option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- CRM Reference --}}
                            <div class="col-md-6">
                                <label class="tims-modal-label">CRM REFERENCE <span
                                        class="text-secondary fw-normal">(Optional)</span></label>
                                <input type="text" name="crm_reference"
                                    class="tims-modal-input @error('crm_reference') is-invalid @enderror"
                                    placeholder="e.g. CRM-00123" value="{{ old('crm_reference') }}">
                                @error('crm_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-md-6">
                                <label class="tims-modal-label">NOTES <span
                                        class="text-secondary fw-normal">(Optional)</span></label>
                                <input type="text" name="notes"
                                    class="tims-modal-input @error('notes') is-invalid @enderror"
                                    placeholder="Brief notes about the meeting…" value="{{ old('notes') }}">
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Checkpoints --}}
                            <div class="col-12">
                                <div class="d-flex gap-4 pt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="passed_two_day_checkpoint"
                                            id="twoDayCheck" value="1" {{ old('passed_two_day_checkpoint') ? 'checked' : '' }}>
                                        <label class="form-check-label text-secondary small" for="twoDayCheck">
                                            Passed 2-Day Checkpoint
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="passed_three_day_checkpoint"
                                            id="threeDayCheck" value="1" {{ old('passed_three_day_checkpoint') ? 'checked' : '' }}>
                                        <label class="form-check-label text-secondary small" for="threeDayCheck">
                                            Passed 3-Day Checkpoint
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer tims-modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal"
                            style="font-size:13px;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-5 fw-semibold" style="font-size:13px;">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Save Meeting
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection


@section('styles')
    <style>
        /* ── Stat Cards ───────────────────────────── */
        .tims-stat-card {
            border-radius: 12px;
            padding: .65rem 1rem;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.07);
            position: relative;
            overflow: hidden;
        }

        .tims-stat-cyan {
            background: linear-gradient(135deg, rgba(6, 182, 212, .18) 0%, rgba(8, 145, 178, .06) 100%);
            border-color: rgba(6, 182, 212, .25);
        }

        .tims-stat-green {
            background: linear-gradient(135deg, rgba(34, 197, 94, .18) 0%, rgba(22, 163, 74, .06) 100%);
            border-color: rgba(34, 197, 94, .25);
        }

        .tims-stat-purple {
            background: linear-gradient(135deg, rgba(167, 139, 250, .18) 0%, rgba(139, 92, 246, .06) 100%);
            border-color: rgba(167, 139, 250, .25);
        }

        .tims-stat-red {
            background: linear-gradient(135deg, rgba(248, 113, 113, .18) 0%, rgba(220, 38, 38, .06) 100%);
            border-color: rgba(248, 113, 113, .25);
        }

        .tims-stat-amber {
            background: linear-gradient(135deg, rgba(245, 158, 11, .18) 0%, rgba(217, 119, 6, .06) 100%);
            border-color: rgba(245, 158, 11, .25);
        }

        .tims-stat-label {
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .07em;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
        }

        .tims-stat-value {
            font-size: 24px;
            font-weight: 700;
            line-height: 1;
        }

        .tims-stat-sub {
            font-size: 11px;
            color: #5e6273;
            margin-top: 3px;
        }

        /* ── Search / filter inputs ───────────────── */
        #meetingSearch::placeholder,
        #statusFilter {
            color: #6b7280;
        }

        #meetingSearch:focus,
        #statusFilter:focus {
            background: rgba(255, 255, 255, 0.06) !important;
            border-color: rgba(99, 102, 241, 0.4) !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            color: #fff;
            outline: none;
        }

        #statusFilter option {
            background: #0f0f17;
            color: #d1d5db;
        }

        /* ── Modal ────────────────────────────────── */
        .tims-modal-content {
            background: #0d0d18;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
        }

        .tims-modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 1.1rem 1.5rem;
        }

        .tims-modal-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tims-modal-body {
            padding: 1.4rem 1.5rem;
        }

        .tims-modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            padding: .9rem 1.5rem;
        }

        .tims-modal-label {
            display: block;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .07em;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .tims-modal-input {
            display: block;
            width: 100%;
            padding: 9px 14px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            color: #e5e7eb;
            font-size: 13px;
            transition: border-color .2s, box-shadow .2s;
            appearance: auto;
        }

        .tims-modal-input::placeholder {
            color: #4b5563;
        }

        .tims-modal-input:focus {
            outline: none;
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .tims-modal-input option {
            background: #0d0d18;
            color: #e5e7eb;
        }

        /* ── Checkbox overrides ───────────────────── */
        .form-check-input:checked {
            background-color: #6d5ce7;
            border-color: #6d5ce7;
        }
    </style>
@endsection


@section('scripts')
    <script>
        /* Live search */
        document.getElementById('meetingSearch').addEventListener('input', function () {
            filterRows();
        });
        document.getElementById('statusFilter').addEventListener('change', function () {
            filterRows();
        });

        function filterRows() {
            const q = document.getElementById('meetingSearch').value.toLowerCase();
            const status = document.getElementById('statusFilter').value.toLowerCase();

            document.querySelectorAll('#meetingTable .meeting-row').forEach(row => {
                const text = row.textContent.toLowerCase();
                const badge = row.querySelector('.badge.text-capitalize')?.textContent.trim().toLowerCase() ?? '';
                const matchQ = !q || text.includes(q);
                const matchS = !status || badge === status;
                row.style.display = (matchQ && matchS) ? '' : 'none';
            });
        }

        /* Auto-open modal if validation fails on return */
        @if($errors->any())
            var modal = new bootstrap.Modal(document.getElementById('newMeetingModal'));
            modal.show();
        @endif
    </script>
@endsection