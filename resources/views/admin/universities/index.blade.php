@extends('layouts.app')

@section('page_title', ' ')
@section('page_subtitle', '')

@section('content')

{{-- ===== TOP BAR ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold m-0 text-white" style="font-size: 18px;">Academy Management</h5>
        <p class="text-secondary small m-0 mt-1">Manage and configure multiple academy settings and rulesets</p>
    </div>
    <a href="{{ route('admin.universities.create') }}" class="tims-add-user-btn">
        <i class="fa-solid fa-plus"></i>
        <span>Add Academy</span>
    </a>
</div>

{{-- ===== STAT CARDS ===== --}}
@php
    $totalAcademies   = $universities->count();
    $activeAcademies  = $universities->where('status', 'active')->count();
    $totalExecutives  = $universities->sum('total_executives');
    $activeExecutives = $universities->sum('active_executives');
    $totalRules       = $universities->sum(fn($u) => $u->score_rules_count ?? $u->scoreRules()->count());
@endphp

<div class="row g-3 mb-4">

    {{-- Total Academies — cyan/teal --}}
    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-cyan">
            <div class="tims-stat-label" style="color:#22d3ee;">
                <i class="fa-solid fa-graduation-cap"></i> Total Academies
            </div>
            <div class="tims-stat-value" style="color:#22d3ee;">{{ $totalAcademies }}</div>
            <div class="tims-stat-sub">registered</div>
        </div>
    </div>

    {{-- Active Academies — green --}}
    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-green">
            <div class="tims-stat-label" style="color:#4ade80;">
                <i class="fa-regular fa-circle-check"></i> Active Academies
            </div>
            <div class="tims-stat-value" style="color:#4ade80;">{{ $activeAcademies }}</div>
            <div class="tims-stat-sub">currently active</div>
        </div>
    </div>

    {{-- Total Executives — amber --}}
    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-amber">
            <div class="tims-stat-label" style="color:#fbbf24;">
                <i class="fa-solid fa-users"></i> Total Executives
            </div>
            <div class="tims-stat-value" style="color:#fbbf24;">{{ $totalExecutives }}</div>
            <div class="tims-stat-sub">across all academies</div>
        </div>
    </div>

    {{-- Active Executives — emerald --}}
    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-emerald">
            <div class="tims-stat-label" style="color:#34d399;">
                <i class="fa-solid fa-user-check"></i> Active Executives
            </div>
            <div class="tims-stat-value" style="color:#34d399;">{{ $activeExecutives }}</div>
            <div class="tims-stat-sub">currently active</div>
        </div>
    </div>

    {{-- Total Rules — purple --}}
    <div class="col-6 col-md-4 col-xl">
        <div class="tims-stat-card tims-stat-purple">
            <div class="tims-stat-label" style="color:#a78bfa;">
                <i class="fa-solid fa-list-check"></i> Total Rules
            </div>
            <div class="tims-stat-value" style="color:#c4b5fd;">{{ $totalRules }}</div>
            <div class="tims-stat-sub">configured</div>
        </div>
    </div>

</div>

{{-- ===== SECTION BAR ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <h6 class="fw-semibold text-white m-0" style="font-size: 15px;">All Academies</h6>
        <span class="badge rounded-pill"
            style="background: rgba(99,102,241,0.15); color: #818cf8; font-size: 12px; padding: 3px 10px; border: 1px solid rgba(99,102,241,0.2);">
            {{ $universities->count() }}
        </span>
    </div>

    <div class="position-relative">
        <i class="fa-solid fa-magnifying-glass position-absolute text-secondary"
            style="top: 50%; left: 12px; transform: translateY(-50%); font-size: 12px; pointer-events: none;"></i>
        <input type="text" id="academySearch" class="form-control form-control-sm ps-4"
            placeholder="Search academies…"
            style="width: 200px; background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.08); color: #fff; font-size: 13px;">
    </div>
</div>

{{-- ===== TABLE ===== --}}
<div class="tims-roster-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="academyTable">
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
                    <tr class="academy-row">

                        {{-- Logo Avatar --}}
                        <td>
                            @if ($uni->logo_url)
                                <img src="{{ $uni->logo_url }}" alt="{{ $uni->name }}"
                                    class="rounded-circle border border-secondary border-opacity-25"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                    style="width: 40px; height: 40px; background: linear-gradient(135deg, {{ $uni->theme_color }}, #111827); border: 1px solid rgba(255,255,255,0.1); font-size: 13px;">
                                    {{ $uni->initials }}
                                </div>
                            @endif
                        </td>

                        {{-- Academy Name --}}
                        <td>
                            <a href="{{ route('admin.universities.show', $uni->id) }}"
                                class="fw-semibold text-white text-decoration-none academy-name"
                                style="font-size: 14px;">
                                {{ $uni->name }}
                            </a>
                        </td>

                        {{-- Code --}}
                        <td>
                            <span class="font-monospace badge bg-dark text-secondary border border-secondary border-opacity-10"
                                style="font-size: 11px; padding: 4px 10px;">
                                {{ $uni->code }}
                            </span>
                        </td>

                        {{-- Total Executives --}}
                        <td>
                            <span class="fw-semibold text-white" style="font-size: 14px;">
                                {{ $uni->total_executives }}
                            </span>
                        </td>

                        {{-- Active Executives --}}
                        <td>
                            <span class="badge rounded-pill"
                                style="background: rgba(34,197,94,0.12); color: #4ade80; border: 1px solid rgba(34,197,94,0.2); font-size: 12px; padding: 4px 12px;">
                                {{ $uni->active_executives }} active
                            </span>
                        </td>

                        {{-- Active Rules --}}
                        <td>
                            <span class="text-secondary small">
                                {{ $uni->score_rules_count ?? $uni->scoreRules()->count() }} rules configured
                            </span>
                        </td>

                        {{-- Status --}}
                        <td>
                            @if ($uni->status === 'active')
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
                                <button class="btn btn-link text-secondary p-0 border-0 tims-action-dots-btn"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.universities.show', $uni->id) }}">
                                            <i class="fa-regular fa-eye me-2 text-info"></i> View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.universities.dashboard', $uni->id) }}">
                                            <i class="fa-solid fa-chart-line me-2 text-success"></i> Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.universities.edit', $uni->id) }}">
                                            <i class="fa-regular fa-pen-to-square me-2 text-primary"></i> Edit Profile
                                        </a>
                                    </li>
                                    @if ($uni->code !== 'TIMS')
                                        <li>
                                            <form action="{{ route('admin.universities.destroy', $uni->id) }}"
                                                method="POST" class="m-0"
                                                onsubmit="return confirm('Are you sure you want to delete {{ $uni->name }}? This will permanently delete all associated counselors/rules and logs.');">
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
                            <span style="color: #5e6273; font-size: 13px;">No academies registered yet.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection


@section('styles')
<style>
    /* ── Stat Cards ─────────────────────────────────── */
    .tims-stat-card {
        border-radius: 12px;
        padding: .65rem 1rem;
        height: 100%;
        border: 1px solid rgba(255,255,255,0.07);
        position: relative;
        overflow: hidden;
    }

    /* Per-card gradient themes */
    .tims-stat-cyan   { background: linear-gradient(135deg, rgba(6,182,212,.18) 0%, rgba(8,145,178,.06) 100%); border-color: rgba(6,182,212,.25); }
    .tims-stat-green  { background: linear-gradient(135deg, rgba(34,197,94,.18) 0%, rgba(22,163,74,.06) 100%);  border-color: rgba(34,197,94,.25); }
    .tims-stat-amber  { background: linear-gradient(135deg, rgba(245,158,11,.18) 0%, rgba(217,119,6,.06) 100%); border-color: rgba(245,158,11,.25); }
    .tims-stat-emerald{ background: linear-gradient(135deg, rgba(52,211,153,.18) 0%, rgba(16,185,129,.06) 100%);border-color: rgba(52,211,153,.25); }
    .tims-stat-purple { background: linear-gradient(135deg, rgba(167,139,250,.18) 0%, rgba(139,92,246,.06) 100%);border-color: rgba(167,139,250,.25); }

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

    /* ── Table hover name link ──────────────────────── */
    .academy-name:hover {
        color: #818cf8 !important;
    }

    /* ── Search input placeholder ───────────────────── */
    #academySearch::placeholder {
        color: #5e6273;
    }
    #academySearch:focus {
        background: rgba(255,255,255,0.06) !important;
        border-color: rgba(99,102,241,0.4) !important;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        color: #fff;
        outline: none;
    }
</style>
@endsection


@section('scripts')
<script>
    /* Live search filter */
    document.getElementById('academySearch').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#academyTable .academy-row').forEach(row => {
            const name = row.querySelector('.academy-name')?.textContent.toLowerCase() ?? '';
            const code = row.querySelector('.font-monospace')?.textContent.toLowerCase() ?? '';
            row.style.display = (name.includes(q) || code.includes(q)) ? '' : 'none';
        });
    });
</script>
@endsection