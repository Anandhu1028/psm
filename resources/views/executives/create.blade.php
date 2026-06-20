@extends('layouts.app')

@section('title', 'Add Executive')
@section('page_title', 'Add New Executive')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="glass-card p-4 p-lg-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="fa-solid fa-user-plus text-primary fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0">Register New Executive</h5>
                    <small class="text-secondary">Executives do not have system login access</small>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-4">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('executives.store') }}">
                @csrf

                <div class="row g-4">
                    {{-- Identity --}}
                    <div class="col-12">
                        <h6 class="fw-bold text-uppercase text-secondary small border-bottom pb-2">
                            <i class="fa-solid fa-id-badge me-2"></i>Identity Information
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Employee ID <span class="text-danger">*</span></label>
                        <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                            placeholder="e.g. EMP0042" value="{{ old('employee_id') }}" required>
                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="Executive's full name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            placeholder="+91 99887 76655" value="{{ old('phone') }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="executive@tims.com" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Assignment --}}
                    <div class="col-12 mt-2">
                        <h6 class="fw-bold text-uppercase text-secondary small border-bottom pb-2">
                            <i class="fa-solid fa-sitemap me-2"></i>Zone & Assignment
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Zone <span class="text-danger">*</span></label>
                        <select name="zone_id" class="form-select @error('zone_id') is-invalid @enderror" required>
                            <option value="">Select Zone...</option>
                            @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                            @endforeach
                        </select>
                        @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Department</label>
                        <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                            <option value="">Select Department...</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Reporting Manager</label>
                        <select name="reporting_manager_id" class="form-select">
                            <option value="">Select Manager...</option>
                            @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}" {{ old('reporting_manager_id') == $mgr->id ? 'selected' : '' }}>{{ $mgr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dates & Status --}}
                    <div class="col-12 mt-2">
                        <h6 class="fw-bold text-uppercase text-secondary small border-bottom pb-2">
                            <i class="fa-solid fa-calendar me-2"></i>Dates & Status
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date Joined <span class="text-danger">*</span></label>
                        <input type="date" name="date_joined" class="form-control @error('date_joined') is-invalid @enderror"
                            value="{{ old('date_joined') }}" required>
                        @error('date_joined')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Probation End Date <span class="text-danger">*</span></label>
                        <input type="date" name="probation_end_date" class="form-control @error('probation_end_date') is-invalid @enderror"
                            value="{{ old('probation_end_date') }}" required>
                        @error('probation_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="probation" {{ old('status','probation') === 'probation' ? 'selected' : '' }}>Probation</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Submit --}}
                    <div class="col-12 d-flex justify-content-end gap-3 mt-2">
                        <a href="{{ route('executives.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-3 px-5">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Register Executive
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
