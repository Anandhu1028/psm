@extends('layouts.app')

@section('title', 'Universities Master')
@section('page_title', 'University Master')
@section('page_subtitle', 'Manage and configure multiple university settings and rulesets')

@section('page_actions')
<a href="{{ route('admin.universities.create') }}" class="tims-add-user-btn">
    <i class="fa-solid fa-plus"></i>
    <span>Add Academies</span>
</a>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold m-0 text-white" style="font-size: 18px;">All Academies ({{ $universities->count() }})</h5>
    </div>
</div>

<div class="tims-roster-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Academy Name</th>
                    <th>Code</th>
                    <th>Total Executives</th>
                    <th>Active Executives</th>
                    <th>Active Rules</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($universities as $uni)
                <tr>
                    {{-- Logo Avatar --}}
                    <td>
                        @if($uni->logo_url)
                            <img src="{{ $uni->logo_url }}" 
                                 alt="{{ $uni->name }}" 
                                 class="rounded-circle border border-secondary border-opacity-25" 
                                 style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" 
                                 style="width: 40px; height: 40px; background: linear-gradient(135deg, {{ $uni->theme_color }}, #111827); border: 1px solid rgba(255,255,255,0.1);">
                                {{ $uni->initials }}
                            </div>
                        @endif
                    </td>

                    {{-- Company Name --}}
                    <td>
                        <a href="{{ route('admin.universities.show', $uni->id) }}" class="fw-bold text-white fs-14.5">
                            {{ $uni->name }}
                        </a>
                      
                    </td>

                    {{-- Code --}}
                    <td>
                        <span class="font-monospace badge bg-dark text-secondary border border-secondary border-opacity-10 py-1.5 px-2.5">
                            {{ $uni->code }}
                        </span>
                    </td>

                    {{-- Total Executives --}}
                    <td>
                        <span class="fw-semibold text-white fs-14">{{ $uni->total_executives }}</span>
                    </td>

                    {{-- Active Executives --}}
                    <td>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                            {{ $uni->active_executives }} active
                        </span>
                    </td>

                    {{-- Active Rules Count --}}
                    <td>
                        <span class="text-secondary small">{{ $uni->score_rules_count ?? $uni->scoreRules()->count() }} rules configured</span>
                    </td>

                    {{-- Status --}}
                    <td>
                        @if($uni->status === 'active')
                            <span class="tims-status-badge active">
                                <i class="fa-regular fa-circle-check"></i> Active
                            </span>
                        @else
                            <span class="tims-status-badge inactive">
                                <i class="fa-regular fa-circle-xmark"></i> Inactive
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-link text-secondary p-0 border-0 tims-action-dots-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.universities.show', $uni->id) }}">
                                        <i class="fa-regular fa-eye me-2 text-info"></i> View Details
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.universities.dashboard', $uni->id) }}">
                                        <i class="fa-solid fa-chart-line me-2 text-success"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.universities.edit', $uni->id) }}">
                                        <i class="fa-regular fa-pen-to-square me-2 text-primary"></i> Edit Profile
                                    </a>
                                </li>
                                @if($uni->code !== 'TIMS')
                                <!-- <li><hr class="dropdown-divider"></li> -->
                                <li>
                                    <form action="{{ route('admin.universities.destroy', $uni->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete university {{ $uni->name }}? This will permanently delete all associated counselors/rules and logs.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="fa-regular fa-trash-can me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fa-solid fa-graduation-cap fa-2x mb-3 d-block text-secondary"></i>
                        <span style="color:#5e6273; font-size:13px;">No universities registered yet.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
