@extends('layouts.app')

@section('title', 'Executives Roster')
@section('page_title', 'Executive Roster')
@section('page_subtitle', 'Manage and monitor all CRO executives')

@section('page_actions')
<div class="d-flex align-items-center gap-2">
    @can('manage_executives')
    <button class="tims-add-user-btn" type="button" data-bs-toggle="modal" data-bs-target="#addExecutiveModal">
        <i class="fa-solid fa-plus"></i>
        <span>Add Executive</span>
    </button>
    @endcan
</div>
@endsection

@section('content')

{{-- ── Leaderboard Spotlight Cards ── --}}
@php
    $topScorer     = $executives->sortByDesc('current_score')->first();
    $needsAttention= $executives->sortBy('current_score')->first();
    $mostActive    = $executives->sortByDesc('current_score')->first(); // placeholder — swap for your metric
    $totalActive   = $executives->where('status','active')->count();
    $totalProbation= $executives->where('status','probation')->count();
    $totalReview   = $executives->where('current_tier','review_zone')->count();
@endphp

<div class="exec-spotlight-row mb-4">

    {{-- Top Performer --}}
    <div class="exec-spotlight-card spotlight-green">
        <div class="spotlight-eyebrow"><i class="fa-solid fa-crown me-1"></i> Top Performer</div>
        @if($topScorer)
        <div class="d-flex align-items-center gap-3 mt-2">
            <div class="spotlight-avatar">{{ strtoupper(substr($topScorer->name,0,2)) }}</div>
            <div>
                <div class="spotlight-name">{{ $topScorer->name }}</div>
                <div class="spotlight-meta">{{ $topScorer->zone->name ?? '—' }}</div>
            </div>
        </div>
        <div class="spotlight-stat text-success mt-2">+{{ $topScorer->current_score }} pts</div>
        @else
        <div class="text-secondary mt-3" style="font-size:12px;">No data yet</div>
        @endif
    </div>

    {{-- Needs Attention --}}
    <div class="exec-spotlight-card spotlight-red">
        <div class="spotlight-eyebrow"><i class="fa-solid fa-triangle-exclamation me-1"></i> Needs Attention</div>
        @if($needsAttention && $needsAttention->current_score < 0)
        <div class="d-flex align-items-center gap-3 mt-2">
            <div class="spotlight-avatar" style="background: linear-gradient(135deg,#ef4444,#7f1d1d);">{{ strtoupper(substr($needsAttention->name,0,2)) }}</div>
            <div>
                <div class="spotlight-name">{{ $needsAttention->name }}</div>
                <div class="spotlight-meta">{{ $needsAttention->zone->name ?? '—' }}</div>
            </div>
        </div>
        <div class="spotlight-stat text-danger mt-2">{{ $needsAttention->current_score }} pts</div>
        @else
        <div class="text-secondary mt-3" style="font-size:12px;">All performing well</div>
        @endif
    </div>

    {{-- Active Count --}}
    <div class="exec-spotlight-card spotlight-cyan">
        <div class="spotlight-eyebrow"><i class="fa-regular fa-circle-check me-1"></i> Active Executives</div>
        <div class="spotlight-big-num text-info mt-2">{{ $totalActive }}</div>
        <div class="spotlight-meta mt-1">out of {{ $executives->total() }} total</div>
    </div>

    {{-- Probation Count --}}
    <div class="exec-spotlight-card spotlight-yellow">
        <div class="spotlight-eyebrow"><i class="fa-regular fa-clock me-1"></i> On Probation</div>
        <div class="spotlight-big-num text-warning mt-2">{{ $totalProbation }}</div>
        <div class="spotlight-meta mt-1">under monitoring</div>
    </div>

    {{-- Review Zone --}}
    <div class="exec-spotlight-card spotlight-purple">
        <div class="spotlight-eyebrow"><i class="fa-solid fa-flag me-1"></i> Review Zone</div>
        <div class="spotlight-big-num mt-2" style="color:#a78bfa;">{{ $totalReview }}</div>
        <div class="spotlight-meta mt-1">require action</div>
    </div>

</div>

{{-- ── Table Controls Row ── --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold m-0 text-white" style="font-size: 17px;">
            All Executives
            <span class="exec-count-chip">{{ $executives->total() }}</span>
        </h5>
    </div>
    <div class="d-flex align-items-center gap-2">

        <!-- University Switcher -->
        <form action="{{ route('active_university.switch') }}" method="POST" id="global-univ-switcher" class="m-0">
            @csrf
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-dark border-secondary border-opacity-10 text-secondary" style="font-size: 11px;">
                    <i class="fa-solid fa-graduation-cap"></i>
                </span>
                <select name="university_id"
                        class="form-select bg-dark text-white border-secondary border-opacity-10"
                        style="font-size: 11.5px; border-radius: 0 10px 10px 0;"
                        onchange="document.getElementById('global-univ-switcher').submit();">
                    <option value="">All Universities (TIMS)</option>
                    @foreach($allUniversities as $uni)
                        <option value="{{ $uni->id }}" {{ $activeUniversity && $activeUniversity->id == $uni->id ? 'selected' : '' }}>
                            {{ $uni->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <!-- Search inline -->
        <form method="GET" action="{{ route('executives.index') }}" class="m-0">
            <div class="exec-search-wrap">
                <i class="fa-solid fa-magnifying-glass exec-search-icon"></i>
                <input type="text" name="search" class="exec-search-input"
                       value="{{ request('search') }}"
                       placeholder="Search name, ID, email…">
            </div>
        </form>

        <!-- Filter Toggle -->
        <button class="tims-table-control-btn" type="button"
                data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fa-solid fa-sliders"></i>
            <span>Filters</span>
        </button>

        <!-- Export Button -->
        <a href="#" class="tims-table-control-btn" title="Export Data"
           onclick="alert('Exporting data as CSV...'); return false;">
            <i class="fa-solid fa-download"></i>
            <span>Export</span>
        </a>
    </div>
</div>

{{-- ── Roster Table ── --}}
<div class="tims-roster-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 exec-table" id="executivesTable">
            <thead>
                <tr>
                    <th>EMP ID</th>
                    <th class="text-center">EXECUTIVE</th>
                    <th class="text-center">EMAIL</th>
                    <th class="text-center">ZONE</th>
                    <th class="text-center">ACADEMY</th>
                    <th class="text-center">STATUS</th>
                    <th class="text-center">TIER</th>
                    <th>SCORE</th>
                    <th class="text-center">PROBATION END</th>
                    <th class="text-end">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($executives as $exec)
                <tr class="exec-table-row">

                    {{-- EMP ID --}}
                    <td>
                        <a href="{{ route('executives.scorecard', $exec->id) }}" class="exec-emp-id-link">
                            {{ $exec->employee_id }}
                        </a>
                    </td>

                    {{-- Executive --}}
                    <td class="text-center">
                        <div class="text-center d-flex align-items-center gap-2">
                            <div class="text-center exec-table-avatar">
                                {{ strtoupper(substr($exec->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="exec-name">{{ $exec->name }}</div>
                                <div class="exec-sub">{{ $exec->department->name ?? 'No Dept' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Email --}}
                    <td class="text-center">
                        <span class= 'text-center exec-email'>{{ $exec->email }}</span>
                    </td>

                    {{-- Zone --}}
                    <td class="text-center">
                        <span class="exec-zone-badge">
                            <i class="fa-solid fa-location-dot me-1" style="font-size:9px;"></i>
                            {{ $exec->zone->name ?? '—' }}
                        </span>
                    </td>

                    {{-- University / Academy --}}
                    <td class="text-center">
                        <div class="text-center d-flex align-items-center gap-2">
                            @if($exec->university)
                                @if($exec->university->logo_url)
                                    <img src="{{ $exec->university->logo_url }}"
                                         alt="{{ $exec->university->name }}"
                                         class="rounded-circle border border-secondary border-opacity-10"
                                         style="width: 22px; height: 22px; object-fit: cover;">
                                @else
                                    <div class="exec-uni-dot"
                                         style="background: linear-gradient(135deg, {{ $exec->university->theme_color }}, #111827);">
                                        {{ $exec->university->initials }}
                                    </div>
                                @endif
                                <span class="exec-uni-name">{{ $exec->university->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="text-center">
                        @if($exec->status === 'active')
                            <span class="exec-status-pill status-active">
                                <i class="fa-regular fa-circle-check"></i> Active
                            </span>
                        @elseif($exec->status === 'probation')
                            <span class="exec-status-pill status-probation">
                                <i class="fa-regular fa-clock"></i> Probation
                            </span>
                        @else
                            <span class="exec-status-pill status-inactive">
                                <i class="fa-regular fa-circle-xmark"></i> Inactive
                            </span>
                        @endif
                    </td>

                    {{-- Tier --}}
                    <td class="text-center">
                        @php
                            $tierIcons = [
                                'platinum'    => ['icon'=>'fa-gem',     'color'=>'#e2e8f0'],
                                'gold'        => ['icon'=>'fa-trophy',  'color'=>'#facc15'],
                                'silver'      => ['icon'=>'fa-medal',   'color'=>'#94a3b8'],
                                'bronze'      => ['icon'=>'fa-award',   'color'=>'#d97706'],
                                'review_zone' => ['icon'=>'fa-flag',    'color'=>'#f87171'],
                            ];
                            $ti = $tierIcons[$exec->current_tier] ?? ['icon'=>'fa-circle','color'=>'#6b7280'];
                        @endphp
                        <span class="exec-tier-badge tier-{{ $exec->current_tier }}">
                            <i class="fa-solid {{ $ti['icon'] }}" style="color:{{ $ti['color'] }};font-size:10px;"></i>
                            {{ ucwords(str_replace('_', ' ', $exec->current_tier)) }}
                        </span>
                    </td>

                    {{-- Score --}}
                    <td>
                        @php
                            $score = $exec->current_score;
                            $absScore = abs($score);
                            $pct = min(100, max(0, $absScore));
                            $radius = 22;
                            $circ = round(2 * M_PI * $radius, 2);
                            $dash = round($circ * $pct / 100, 2);
                            $gap  = round($circ - $dash, 2);
                            $color = $score < 0 ? '#ef4444' : ($score > 0 ? '#10b981' : '#4b5563');
                            $trackColor = $score < 0 ? 'rgba(239,68,68,0.12)' : 'rgba(16,185,129,0.12)';
                        @endphp
                        <div class="exec-score-ring-wrap">
                            <svg width="56" height="56" viewBox="0 0 56 56" style="transform:rotate(-90deg);">
                                <circle cx="28" cy="28" r="{{ $radius }}"
                                        fill="none"
                                        stroke="{{ $trackColor }}"
                                        stroke-width="4"/>
                                <circle cx="28" cy="28" r="{{ $radius }}"
                                        fill="none"
                                        stroke="{{ $color }}"
                                        stroke-width="4"
                                        stroke-linecap="round"
                                        stroke-dasharray="{{ $dash }} {{ $gap }}"
                                        stroke-dashoffset="0"/>
                            </svg>
                            <div class="exec-score-ring-label">
                                <span style="color:{{ $color }}; font-size:13px; font-weight:800; letter-spacing:-0.03em; line-height:1;">
                                    {{ $score > 0 ? '+' : '' }}{{ $score }}
                                </span>
                                <span style="font-size:8px; color:#4b5563; font-weight:700; letter-spacing:.06em; text-transform:uppercase; margin-top:1px;">pts</span>
                            </div>
                        </div>
                    </td>

                    {{-- Probation End --}}
                    <td class="text-center">
                        @if($exec->probation_end_date)
                            <div>
                                <span class="{{ $exec->probation_end_date->isPast() ? 'text-danger fw-semibold' : 'text-white' }}"
                                      style="font-size:12.5px;">
                                    {{ $exec->probation_end_date->toDateString() }}
                                </span>
                                @if($exec->probation_end_date->isPast())
                                    <span class="exec-expired-chip">EXPIRED</span>
                                @endif
                                <div style="font-size:10.5px; color:#6b7280; margin-top:2px;">
                                    {{ $exec->probation_end_date->diffForHumans() }}
                                </div>
                            </div>
                        @else
                            <span class="text-secondary">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="exec-action-btn" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow exec-dropdown">
                                <li>
                                    <button class="dropdown-item" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewExecutiveModal{{ $exec->id }}">
                                        <i class="fa-regular fa-eye me-2 text-info"></i> View Profile
                                    </button>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('executives.scorecard', $exec->id) }}">
                                        <i class="fa-regular fa-id-card me-2 text-primary"></i> View Points
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editExecutiveModal{{ $exec->id }}">
                                        <i class="fa-regular fa-pen-to-square me-2 text-warning"></i> Edit Profile
                                    </button>
                                </li>
                                @can('manage_executives')
                                <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.06);"></li>
                                <li>
                                    <form action="{{ route('executives.destroy', $exec->id) }}" method="POST"
                                          class="m-0"
                                          onsubmit="return confirm('Delete {{ $exec->name }}? This will permanently remove all logs, scorecard history, and PIP records.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="fa-regular fa-trash-can me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <i class="fa-solid fa-users-slash fa-2x mb-3 d-block" style="color:#3e3f56;"></i>
                        <span style="color:#5e6273; font-size:13px;">No executives found matching your criteria.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Footer --}}
    <div class="tims-table-footer">
        <div>
            @if($executives->hasPages())
                {{ $executives->withQueryString()->links('pagination::bootstrap-5') }}
            @else
                <nav>
                    <ul class="pagination">
                        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                        <li class="page-item active"><span class="page-link">1</span></li>
                        <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                    </ul>
                </nav>
            @endif
        </div>
        <div class="tims-pagination-info">
            Page {{ $executives->currentPage() }} of {{ $executives->lastPage() }}
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════
     SECTION STYLES  (scoped to this page)
══════════════════════════════════════════════ --}}
<style>
/* ── Spotlight row ── */
.exec-spotlight-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
}
@media(max-width:1100px){ .exec-spotlight-row{ grid-template-columns: repeat(3,1fr); } }
@media(max-width:640px) { .exec-spotlight-row{ grid-template-columns: 1fr 1fr; } }

.exec-spotlight-card {
    background: rgba(255,255,255,0.025);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    padding: 16px 18px 18px;
    position: relative;
    overflow: hidden;
    transition: transform .18s, box-shadow .18s;
}
.exec-spotlight-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.4);
}
.exec-spotlight-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 14px;
    opacity: .08;
    pointer-events: none;
}
.spotlight-green::before { background: linear-gradient(135deg,#10b981,transparent); border-top: 2px solid #10b981; }
.spotlight-red::before   { background: linear-gradient(135deg,#ef4444,transparent); border-top: 2px solid #ef4444; }
.spotlight-cyan::before  { background: linear-gradient(135deg,#22d3ee,transparent); border-top: 2px solid #22d3ee; }
.spotlight-yellow::before{ background: linear-gradient(135deg,#fbbf24,transparent); border-top: 2px solid #fbbf24; }
.spotlight-purple::before{ background: linear-gradient(135deg,#8b5cf6,transparent); border-top: 2px solid #8b5cf6; }

.spotlight-eyebrow {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #6b7280;
}
.spotlight-green  .spotlight-eyebrow { color: #34d399; }
.spotlight-red    .spotlight-eyebrow { color: #f87171; }
.spotlight-cyan   .spotlight-eyebrow { color: #22d3ee; }
.spotlight-yellow .spotlight-eyebrow { color: #fbbf24; }
.spotlight-purple .spotlight-eyebrow { color: #a78bfa; }

.spotlight-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg,#7c5cfc,#4f46e5);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: #fff;
    flex-shrink: 0;
}
.spotlight-name { font-weight: 700; color: #fff; font-size: 13.5px; line-height: 1.2; }
.spotlight-meta { font-size: 11px; color: #6b7280; margin-top: 1px; }
.spotlight-stat  { font-size: 22px; font-weight: 800; letter-spacing: -.02em; }
.spotlight-big-num { font-size: 36px; font-weight: 800; letter-spacing: -.04em; color: #fff; }

/* ── Count chip ── */
.exec-count-chip {
    display: inline-block;
    background: rgba(124,92,252,0.18);
    color: #a78bfa;
    border: 1px solid rgba(124,92,252,0.3);
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    padding: 1px 10px;
    vertical-align: middle;
    margin-left: 6px;
}

/* ── Inline search ── */
.exec-search-wrap {
    position: relative;
}
.exec-search-icon {
    position: absolute; left: 10px; top: 50%;
    transform: translateY(-50%);
    color: #6b7280; font-size: 11px;
    pointer-events: none;
}
.exec-search-input {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    color: #fff;
    font-size: 12px;
    padding: 6px 12px 6px 28px;
    width: 200px;
    transition: border-color .2s, background .2s;
    outline: none;
}
.exec-search-input::placeholder { color: #4b5563; }
.exec-search-input:focus {
    border-color: rgba(124,92,252,0.5);
    background: rgba(124,92,252,0.07);
}

/* ── Table ── */
.exec-table thead th {
    background: rgba(255,255,255,0.025);
    color: #4b5563;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .07em;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    padding: 11px 14px;
    white-space: nowrap;
}
.exec-table tbody td {
    padding: 13px 14px;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    vertical-align: middle;
}
.exec-table-row:hover td {
    background: rgba(255,255,255,0.025);
}
.exec-table-row:last-child td { border-bottom: none; }

/* EMP ID link */
.exec-emp-id-link {
    font-family: 'Fira Mono', 'Courier New', monospace;
    font-size: 12px;
    font-weight: 600;
    color: #7c5cfc;
    text-decoration: none;
    background: rgba(124,92,252,0.1);
    border: 1px solid rgba(124,92,252,0.2);
    padding: 2px 9px;
    border-radius: 6px;
    transition: background .15s;
}
.exec-emp-id-link:hover { background: rgba(124,92,252,0.22); color: #a78bfa; }

/* Avatar */
.exec-table-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg,#7c5cfc,#4338ca);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; color: #fff;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(124,92,252,0.3);
}
.exec-name { font-weight: 700; color: #f1f5f9; font-size: 13.5px; }
.exec-sub  { font-size: 11px; color: #4b5563; margin-top: 1px; }

/* Email */
.exec-email { font-size: 12.5px; color: #6b7280; }

/* Zone badge */
.exec-zone-badge {
    display: inline-flex; align-items: center;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
    padding: 3px 10px;
    white-space: nowrap;
}

/* Uni */
.exec-uni-dot {
    width: 22px; height: 22px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 9px; font-weight: 700; color: #fff;
    flex-shrink: 0;
    border: 1px solid rgba(255,255,255,0.1);
}
.exec-uni-name { font-size: 12px; color: #94a3b8; }

/* Status pills */
.exec-status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 600;
    padding: 4px 11px;
    border-radius: 20px;
    white-space: nowrap;
}
.status-active   { background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.25); }
.status-probation{ background: rgba(251,191,36,0.12);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.25); }
.status-inactive { background: rgba(248,113,113,0.12); color: #f87171; border: 1px solid rgba(248,113,113,0.25); }

/* Tier badges */
.exec-tier-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700;
    padding: 4px 11px; border-radius: 20px;
    white-space: nowrap;
    text-transform: uppercase; letter-spacing: .04em;
}
.tier-platinum { background: rgba(226,232,240,0.08); color: #e2e8f0; border: 1px solid rgba(226,232,240,0.2); }
.tier-gold     { background: rgba(250,204,21,0.10);  color: #facc15; border: 1px solid rgba(250,204,21,0.25); }
.tier-silver   { background: rgba(148,163,184,0.10); color: #94a3b8; border: 1px solid rgba(148,163,184,0.25); }
.tier-bronze   { background: rgba(217,119,6,0.10);   color: #f59e0b; border: 1px solid rgba(217,119,6,0.25); }
.tier-review_zone { background: rgba(248,113,113,0.12); color: #f87171; border: 1px solid rgba(248,113,113,0.3); }

/* Score ring */
.exec-score-ring-wrap {
    position: relative;
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.exec-score-ring-wrap svg {
    position: absolute;
    inset: 0;
}
.exec-score-ring-label {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

/* Expired chip */
.exec-expired-chip {
    display: inline-block;
    background: rgba(248,113,113,0.15);
    color: #f87171;
    border: 1px solid rgba(248,113,113,0.3);
    border-radius: 4px;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .06em;
    padding: 1px 6px;
    margin-left: 5px;
    vertical-align: middle;
}

/* Action button */
.exec-action-btn {
    background: none; border: none;
    color: #4b5563;
    padding: 4px 8px;
    border-radius: 6px;
    transition: background .15s, color .15s;
    cursor: pointer;
}
.exec-action-btn:hover { background: rgba(255,255,255,0.07); color: #e2e8f0; }

/* Dropdown */
.exec-dropdown {
    background: #0f1322;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    min-width: 170px;
    padding: 6px;
}
.exec-dropdown .dropdown-item {
    color: #94a3b8; font-size: 13px;
    border-radius: 8px; padding: 8px 12px;
    transition: background .15s, color .15s;
}
.exec-dropdown .dropdown-item:hover { background: rgba(255,255,255,0.06); color: #f1f5f9; }
.exec-dropdown .dropdown-item.text-danger:hover { background: rgba(248,113,113,0.1); color: #f87171; }

 
/* ══════════════════════════════════════════════
   MODAL STYLES — dark, vivid, high-contrast
══════════════════════════════════════════════ */
 
/* Backdrop darker */
.modal-backdrop { background:#000 !important; }
.modal-backdrop.show { opacity:.75 !important; }
 
/* Shared modal shell */
.exec-modal-content {
    background: #0b0e1a;
    border: 1px solid rgba(255,255,255,0.10);
    border-radius: 18px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.9), 0 0 0 1px rgba(124,92,252,0.08);
    overflow: hidden;
}
 
/* Modal header */
.exec-modal-header {
    background: linear-gradient(135deg, rgba(124,92,252,0.12), rgba(15,19,34,0));
    border-bottom: 1px solid rgba(255,255,255,0.07) !important;
    padding: 20px 24px !important;
}
.exec-modal-header .modal-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #f1f5f9 !important;
    letter-spacing: -0.01em;
}
 
/* Modal body */
.exec-modal-body { padding: 24px !important; }
 
/* Modal footer */
.exec-modal-footer {
    background: rgba(255,255,255,0.02);
    border-top: 1px solid rgba(255,255,255,0.06) !important;
    padding: 16px 24px !important;
}
 
/* Info grid cards inside modals */
.exec-info-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 14px 16px;
    height: 100%;
    transition: border-color .15s;
}
.exec-info-card:hover { border-color: rgba(255,255,255,0.14); }
.exec-info-card-label {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .09em;
    color: #4b5563;
    margin-bottom: 7px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.exec-info-card-label i { font-size: 11px; }
.exec-info-card-value {
    font-size: 13px;
    font-weight: 600;
    color: #e2e8f0;
    line-height: 1.3;
}
 
/* Avatar in view modal */
.exec-modal-avatar {
    width: 68px; height: 68px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c5cfc, #4338ca);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; font-weight: 800; color: #fff;
    margin: 0 auto;
    box-shadow: 0 0 0 4px rgba(124,92,252,0.2), 0 8px 24px rgba(124,92,252,0.3);
}
 
/* Form inputs inside modals */
.exec-modal-content .form-control,
.exec-modal-content .form-select {
    background: rgba(255,255,255,0.04) !important;
    border: 1px solid rgba(255,255,255,0.10) !important;
    color: #e2e8f0 !important;
    border-radius: 10px !important;
    font-size: 13px !important;
    padding: 9px 13px !important;
    transition: border-color .2s, background .2s;
}
.exec-modal-content .form-control:focus,
.exec-modal-content .form-select:focus {
    background: rgba(124,92,252,0.07) !important;
    border-color: rgba(124,92,252,0.5) !important;
    box-shadow: 0 0 0 3px rgba(124,92,252,0.12) !important;
    color: #f1f5f9 !important;
}
.exec-modal-content .form-control::placeholder { color: #374151 !important; }
.exec-modal-content .form-select option { background: #0f1322; color: #e2e8f0; }
.exec-modal-content .form-label {
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #6b7280 !important;
    margin-bottom: 6px !important;
}
</style>


{{-- ══════════════════════════════════════════════
     VIEW PROFILE MODALS
══════════════════════════════════════════════ --}}
@foreach($executives as $exec)
<div class="modal fade" id="viewExecutiveModal{{ $exec->id }}" tabindex="-1"
     aria-labelledby="viewExecutiveModalLabel{{ $exec->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 640px;">
        <div class="modal-content" style="background: #0f1322; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; box-shadow: 0 15px 50px rgba(0,0,0,0.8);">
            <div class="modal-header border-bottom border-secondary border-opacity-10 px-4 py-3">
                <h5 class="modal-title fw-bold text-white" id="viewExecutiveModalLabel{{ $exec->id }}">
                    <i class="fa-regular fa-user text-primary me-2"></i>Executive Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3"
                         style="width:64px;height:64px;font-size:1.5rem;border-radius:50%;
                                background:linear-gradient(135deg,var(--primary),var(--primary-2));
                                display:flex;align-items:center;justify-content:center;
                                color:white;font-weight:700;box-shadow:0 5px 15px rgba(139,92,246,0.3);">
                        {{ strtoupper(substr($exec->name, 0, 2)) }}
                    </div>
                    <h4 class="fw-bold text-white mb-1">{{ $exec->name }}</h4>
                    <span class="badge bg-secondary-subtle mb-3">{{ $exec->employee_id }}</span>
                </div>

                <div class="row g-3">
                    @php
                    $fields = [
                        ['icon'=>'fa-envelope','color'=>'#7C5CFC','label'=>'Email','val'=>$exec->email,'truncate'=>true],
                        ['icon'=>'fa-phone','color'=>'#22d3ee','label'=>'Phone','val'=>$exec->phone],
                        ['icon'=>'fa-map-pin','color'=>'#fbbf24','label'=>'Zone','val'=>$exec->zone->name ?? '—'],
                        ['icon'=>'fa-building','color'=>'#7C5CFC','label'=>'Department','val'=>$exec->department->name ?? '—'],
                        ['icon'=>'fa-graduation-cap','color'=>'#22d3ee','label'=>'University','val'=>$exec->university->name ?? '—'],
                        ['icon'=>'fa-calendar','color'=>'#7C5CFC','label'=>'Date Joined','val'=> $exec->date_joined ? $exec->date_joined->toDateString() : '—'],
                    ];
                    @endphp
                    @foreach($fields as $f)
                    <div class="col-4">
                        <div class="p-3 rounded-3 h-100"
                             style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-regular {{ $f['icon'] }}" style="color:{{ $f['color'] }};font-size:13px;"></i>
                                <small class="text-secondary"
                                       style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">
                                    {{ $f['label'] }}
                                </small>
                            </div>
                            <span class="text-white d-block {{ isset($f['truncate']) ? 'text-truncate' : '' }}"
                                  style="font-size:12.5px;" {{ isset($f['truncate']) ? 'title="'.$f['val'].'"' : '' }}>
                                {{ $f['val'] }}
                            </span>
                        </div>
                    </div>
                    @endforeach

                    {{-- Status --}}
                    <div class="col-4">
                        <div class="p-3 rounded-3 h-100"
                             style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-regular fa-circle-dot" style="color:#fbbf24;font-size:13px;"></i>
                                <small class="text-secondary"
                                       style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Status</small>
                            </div>
                            <div class="mt-1">
                                @if($exec->status==='active')
                                    <span class="exec-status-pill status-active" style="font-size:11px;padding:3px 10px;"><i class="fa-regular fa-circle-check"></i> Active</span>
                                @elseif($exec->status==='probation')
                                    <span class="exec-status-pill status-probation" style="font-size:11px;padding:3px 10px;"><i class="fa-regular fa-clock"></i> Probation</span>
                                @else
                                    <span class="exec-status-pill status-inactive" style="font-size:11px;padding:3px 10px;"><i class="fa-regular fa-circle-xmark"></i> Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Tier --}}
                    <div class="col-4">
                        <div class="p-3 rounded-3 h-100"
                             style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-regular fa-medal" style="color:#facc15;font-size:13px;"></i>
                                <small class="text-secondary"
                                       style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Tier</small>
                            </div>
                            <div class="mt-1">
                                <span class="exec-tier-badge tier-{{ $exec->current_tier }}" style="font-size:11px;">
                                    {{ ucwords(str_replace('_',' ',$exec->current_tier)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Score --}}
                    <div class="col-4">
                        <div class="p-3 rounded-3 h-100"
                             style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-regular fa-chart-bar" style="color:#10b981;font-size:13px;"></i>
                                <small class="text-secondary"
                                       style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Score</small>
                            </div>
                            <span class="fw-bold d-block mt-1"
                                  style="font-size:15px;{{ $exec->current_score>=0?'color:#10b981;':'color:#f87171;' }}">
                                {{ $exec->current_score>=0?'+':'' }}{{ $exec->current_score }} pts
                            </span>
                        </div>
                    </div>

                    {{-- Probation End --}}
                    @if($exec->probation_end_date)
                    <div class="col-6">
                        <div class="p-3 rounded-3 h-100"
                             style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-regular fa-hourglass-half" style="color:#fbbf24;font-size:13px;"></i>
                                <small class="text-secondary"
                                       style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Probation End</small>
                            </div>
                            <span class="{{ $exec->probation_end_date->isPast()?'text-danger fw-bold':'text-white' }}"
                                  style="font-size:12.5px;">
                                {{ $exec->probation_end_date->toDateString() }}
                            </span>
                            <small class="d-block mt-1 {{ $exec->probation_end_date->isPast()?'text-danger':'text-secondary' }}"
                                   style="font-size:11px;">
                                {{ $exec->probation_end_date->isPast()?'Expired':'Active' }} · {{ $exec->probation_end_date->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    @endif

                    {{-- Reporting Manager --}}
                    @if($exec->reportingManager)
                    <div class="col-6">
                        <div class="p-3 rounded-3 h-100"
                             style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-regular fa-user-tie" style="color:#22d3ee;font-size:13px;"></i>
                                <small class="text-secondary"
                                       style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Reporting Manager</small>
                            </div>
                            <span class="text-white d-block" style="font-size:12.5px;">{{ $exec->reportingManager->name }}</span>
                            <small class="text-secondary d-block mt-1" style="font-size:11px;">{{ $exec->reportingManager->email }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-top border-secondary border-opacity-10 px-4 py-3">
                <a href="{{ route('executives.scorecard', $exec->id) }}" class="btn btn-primary rounded-3 px-4 fw-semibold">
                    <i class="fa-regular fa-id-card me-2"></i>Full Scorecard
                </a>
                <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach


{{-- ══════════════════════════════════════════════
     ADD EXECUTIVE MODAL
══════════════════════════════════════════════ --}}

{{-- ══════════════════════════════════════════════
     ADD EXECUTIVE MODAL
══════════════════════════════════════════════ --}}
@can('manage_executives')
<div class="modal fade" id="addExecutiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content exec-modal-content">
            <div class="modal-header exec-modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-user-plus me-2" style="color:#10b981;"></i>Add New Executive
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('executives.store') }}" method="POST">
                @csrf
                <div class="modal-body exec-modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">University Assignment</label>
                            <select name="university_id" class="form-select" required>
                                <option value="">Select University...</option>
                                @foreach($universities as $uni)
                                <option value="{{ $uni->id }}" {{ session('active_university_id')==$uni->id?'selected':'' }}>
                                    {{ $uni->name }} ({{ $uni->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" placeholder="e.g. EMP005" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. John Smith" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. john@tims.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. +919988776655" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Zone</label>
                            <select name="zone_id" class="form-select" required>
                                <option value="">Select Zone...</option>
                                @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">Select Department (Optional)...</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date Joined</label>
                            <input type="date" name="date_joined" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Probation End Date</label>
                            <input type="date" name="probation_end_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reporting Manager</label>
                            <select name="reporting_manager_id" class="form-select">
                                <option value="">Select Manager (Optional)...</option>
                                @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->roles->first()?->name ?? 'User' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="probation">Probation</option>
                                <option value="active">Active</option>
                                <option value="inactive">Expired (Inactive)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer exec-modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal" style="font-size:13px;">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-5 fw-semibold" style="font-size:13px;">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Save Executive
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan


{{-- ══════════════════════════════════════════════
     EDIT EXECUTIVE MODALS
══════════════════════════════════════════════ --}}
@foreach($executives as $exec)
<div class="modal fade" id="editExecutiveModal{{ $exec->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content exec-modal-content">
            <div class="modal-header exec-modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-user-pen me-2" style="color:#fbbf24;"></i>Edit Executive
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('executives.update', $exec->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body exec-modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">University</label>
                            <select name="university_id" class="form-select">
                                @foreach($universities as $uni)
                                <option value="{{ $uni->id }}" {{ $exec->university_id==$uni->id?'selected':'' }}>{{ $uni->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" value="{{ $exec->employee_id }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $exec->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $exec->email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $exec->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Zone</label>
                            <select name="zone_id" class="form-select">
                                @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ $exec->zone_id==$zone->id?'selected':'' }}>{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">Select</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $exec->department_id==$dept->id?'selected':'' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date Joined</label>
                            <input type="date" name="date_joined" class="form-control" value="{{ optional($exec->date_joined)->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Probation End Date</label>
                            <input type="date" name="probation_end_date" class="form-control" value="{{ optional($exec->probation_end_date)->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reporting Manager</label>
                            <select name="reporting_manager_id" class="form-select">
                                <option value="">Select Manager</option>
                                @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ $exec->reporting_manager_id==$manager->id?'selected':'' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"    {{ $exec->status=='active'   ?'selected':'' }}>Active</option>
                                <option value="probation" {{ $exec->status=='probation'?'selected':'' }}>Probation</option>
                                <option value="inactive"  {{ $exec->status=='inactive' ?'selected':'' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tier</label>
                            <select name="current_tier" class="form-select">
                                <option value="bronze"      {{ $exec->current_tier=='bronze'     ?'selected':'' }}>Bronze</option>
                                <option value="silver"      {{ $exec->current_tier=='silver'     ?'selected':'' }}>Silver</option>
                                <option value="gold"        {{ $exec->current_tier=='gold'       ?'selected':'' }}>Gold</option>
                                <option value="platinum"    {{ $exec->current_tier=='platinum'   ?'selected':'' }}>Platinum</option>
                                <option value="review_zone" {{ $exec->current_tier=='review_zone'?'selected':'' }}>Review Zone</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer exec-modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal" style="font-size:13px;">Cancel</button>
                    <button type="submit" class="btn btn-primary  rounded-3 fw-semibold px-4" style="font-size:13px;">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Update Executive
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
 
 
{{-- ══════════════════════════════════════════════
     FILTER MODAL
══════════════════════════════════════════════ --}}
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content exec-modal-content">
            <div class="modal-header exec-modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-sliders me-2" style="color:#22d3ee;"></i>Advanced Filters
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('executives.index') }}">
                <div class="modal-body exec-modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, Email, Employee ID">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Zone</label>
                            <select name="zone_id" class="form-select">
                                <option value="">All Zones</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ request('zone_id')==$zone->id?'selected':'' }}>{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">University</label>
                            <select name="university_id" class="form-select">
                                <option value="">All Universities</option>
                                @foreach($universities as $uni)
                                    <option value="{{ $uni->id }}" {{ request('university_id')==$uni->id?'selected':'' }}>{{ $uni->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tier</label>
                            <select name="tier" class="form-select">
                                <option value="">All Tiers</option>
                                <option value="platinum">Platinum</option>
                                <option value="gold">Gold</option>
                                <option value="silver">Silver</option>
                                <option value="bronze">Bronze</option>
                                <option value="review_zone">Review Zone</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="probation">Probation</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer exec-modal-footer">
                    <a href="{{ route('executives.index') }}" class="btn btn-outline-secondary rounded-3" style="font-size:13px;">Reset</a>
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold px-4" style="font-size:13px;">
                        <i class="fa-solid fa-magnifying-glass me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection