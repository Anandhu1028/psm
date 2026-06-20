@extends('layouts.app')

@section('title', 'Log Meeting')
@section('page_title', 'Log New Meeting')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="glass-card p-4 p-lg-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="fa-solid fa-handshake text-info fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0">Record New Meeting</h5>
                    <small class="text-secondary">Log lead meetings — system tracks 2-day and 3-day checkpoints automatically</small>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-4">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST" action="{{ route('meetings.store') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Executive <span class="text-danger">*</span></label>
                        <select name="executive_id" class="form-select @error('executive_id') is-invalid @enderror" required>
                            <option value="">Select Executive...</option>
                            @foreach($executives as $exec)
                            <option value="{{ $exec->id }}" {{ old('executive_id') == $exec->id ? 'selected' : '' }}>
                                {{ $exec->name }} ({{ $exec->employee_id }})
                            </option>
                            @endforeach
                        </select>
                        @error('executive_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lead Name <span class="text-danger">*</span></label>
                        <input type="text" name="lead_name" class="form-control @error('lead_name') is-invalid @enderror"
                               placeholder="Lead's full name" value="{{ old('lead_name') }}" required>
                        @error('lead_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date Arranged <span class="text-danger">*</span></label>
                        <input type="date" name="arranged_date" class="form-control @error('arranged_date') is-invalid @enderror"
                               value="{{ old('arranged_date', date('Y-m-d')) }}" required>
                        @error('arranged_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Meeting Date <span class="text-danger">*</span></label>
                        <input type="date" name="meeting_date" class="form-control @error('meeting_date') is-invalid @enderror"
                               value="{{ old('meeting_date') }}" required>
                        @error('meeting_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Meeting Time <span class="text-danger">*</span></label>
                        <input type="time" name="meeting_time" class="form-control @error('meeting_time') is-invalid @enderror"
                               value="{{ old('meeting_time') }}" required>
                        @error('meeting_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Meeting Type <span class="text-danger">*</span></label>
                        <select name="meeting_type" class="form-select @error('meeting_type') is-invalid @enderror" required>
                            <option value="zoom" {{ old('meeting_type','zoom') === 'zoom' ? 'selected' : '' }}>Zoom / Video Call</option>
                            <option value="phone" {{ old('meeting_type') === 'phone' ? 'selected' : '' }}>Phone Call</option>
                            <option value="in_person" {{ old('meeting_type') === 'in_person' ? 'selected' : '' }}>In Person</option>
                        </select>
                        @error('meeting_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="scheduled" {{ old('status','scheduled') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="attended" {{ old('status') === 'attended' ? 'selected' : '' }}>Attended</option>
                            <option value="missed" {{ old('status') === 'missed' ? 'selected' : '' }}>Missed</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">CRM Reference <small class="text-secondary fw-normal">(optional)</small></label>
                        <input type="text" name="crm_reference" class="form-control" placeholder="e.g. CRM-2024-00421"
                               value="{{ old('crm_reference') }}">
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-3 mt-2">
                        <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-3 px-5">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Save Meeting Record
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
