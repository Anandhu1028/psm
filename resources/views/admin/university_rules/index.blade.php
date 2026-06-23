@extends('layouts.app')

@section('content')
<div class="container">
    <h3>University Rule Management</h3>

    <form method="GET" class="mb-3">
        <label for="university_id">Select University</label>
        <select id="university_id" name="university_id" onchange="this.form.submit()" class="form-control">
            @foreach($universities as $uni)
                <option value="{{ $uni->id }}" @if(optional($selectedUniversity)->id===$uni->id) selected @endif>{{ $uni->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="mb-3">
        <strong>Selected:</strong> {{ $selectedUniversity->name ?? '—' }}
        <div>Active Rule Set: {{ $activeRuleSet->name ?? 'None' }} <small>({{ $activeRuleSet->version ?? '' }})</small></div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h5>Rules</h5>
            <table class="table table-sm">
                <thead>
                    <tr><th>Code</th><th>Name</th><th>Category</th><th>Points</th><th>Active</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                        <tr>
                            <td>{{ $rule->code }}</td>
                            <td>{{ $rule->name }}</td>
                            <td>{{ $rule->category }}</td>
                            <td>{{ $rule->points }}</td>
                            <td>{{ $rule->is_active ? 'Yes' : 'No' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.university_rules.rules.update', ['rule' => $rule->id]) }}" class="d-inline">
                                    @method('PUT') @csrf
                                    <button class="btn btn-sm btn-secondary">Edit</button>
                                </form>
                                <form method="POST" action="{{ route('admin.university_rules.rules.store', ['university' => $selectedUniversity->id, 'ruleSet' => $activeRuleSet->id]) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="code" value="{{ $rule->code }}">
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No rules defined for this rule set.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="col-md-4">
            <h5>Create Rule (Quick)</h5>
            <form method="POST" action="{{ route('admin.university_rules.rules.store', ['university' => $selectedUniversity->id, 'ruleSet' => $activeRuleSet->id]) }}">
                @csrf
                <div class="form-group">
                    <label>Name</label>
                    <input name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Code</label>
                    <input name="code" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" class="form-control">
                        <option value="positive">positive</option>
                        <option value="negative">negative</option>
                        <option value="recovery">recovery</option>
                        <option value="kpi">kpi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Points</label>
                    <input name="points" class="form-control" type="number" step="0.1">
                </div>
                <div class="form-group">
                    <label>Calculation Type</label>
                    <select name="calculation_type" class="form-control">
                        <option value="fixed">fixed</option>
                        <option value="per_unit">per_unit</option>
                        <option value="streak">streak</option>
                        <option value="selected_violation">selected_violation</option>
                    </select>
                </div>
                <button class="btn btn-primary mt-2">Create</button>
            </form>

            <hr>
            <form method="POST" action="{{ route('admin.university_rules.rule_sets.clone', ['university' => $selectedUniversity->id]) }}">
                @csrf
                <button class="btn btn-outline-secondary">Clone Active to Draft</button>
            </form>

            @if($activeRuleSet)
            <form method="POST" action="{{ route('admin.university_rules.rule_sets.activate', ['university' => $selectedUniversity->id, 'ruleSet' => $activeRuleSet->id]) }}" class="mt-2">
                @csrf
                <button class="btn btn-success">Activate This Rule Set</button>
            </form>
            <form method="POST" action="{{ route('admin.university_rules.rule_sets.publish', ['university' => $selectedUniversity->id, 'ruleSet' => $activeRuleSet->id]) }}" class="mt-2">
                @csrf
                <input type="hidden" name="notes" value="Published via admin UI">
                <button class="btn btn-primary">Publish & Activate</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')
@section('title', 'University Rule Engine')
@section('page_title', 'Rule Engine')
@section('page_subtitle', 'Manage scoring rules independently per university')

@section('page_actions')
    <div class="d-flex align-items-center gap-2">
        {{-- University Switcher --}}
        <form method="GET" action="{{ route('admin.university_rules.index') }}" id="uniSwitchForm">
            <select name="university_id" id="university_id" class="form-select form-select-sm"
                style="background:#1e1e2a; border:1px solid rgba(255,255,255,0.1); color:#fff; min-width:160px; border-radius:8px;"
                onchange="document.getElementById('uniSwitchForm').submit()">
                @foreach($universities as $uni)
                    <option value="{{ $uni->id }}" @selected($uni->id == $university->id)>{{ $uni->name }}</option>
                @endforeach
            </select>
            @if(request('rule_set_id'))
                <input type="hidden" name="rule_set_id" value="{{ request('rule_set_id') }}">
            @endif
        </form>

        {{-- Rule Set Switcher --}}
        @if($ruleSets->count() > 1)
        <form method="GET" action="{{ route('admin.university_rules.index') }}" id="ruleSetSwitchForm">
            <input type="hidden" name="university_id" value="{{ $university->id }}">
            <select name="rule_set_id" class="form-select form-select-sm"
                style="background:#1e1e2a; border:1px solid rgba(255,255,255,0.1); color:#fff; min-width:180px; border-radius:8px;"
                onchange="document.getElementById('ruleSetSwitchForm').submit()">
                @foreach($ruleSets as $rs)
                    <option value="{{ $rs->id }}" @selected($rs->id == $currentRuleSet?->id)>
                        {{ $rs->name }}
                        @if($rs->status === 'active') ✓ @endif
                    </option>
                @endforeach
            </select>
        </form>
        @endif

        {{-- Clone to Draft --}}
        <form method="POST" action="{{ route('admin.university_rules.rule_sets.clone', $university) }}">
            @csrf
            <button type="submit" class="tims-header-control-pill border-0 cursor-pointer"
                onclick="return confirm('Clone active rule set to a new draft?')"
                title="Clone active rule set to a new draft version">
                <i class="fa-solid fa-copy me-1"></i> Clone Draft
            </button>
        </form>

        {{-- Activate --}}
        @if($currentRuleSet && $currentRuleSet->status !== 'active')
        <form method="POST" action="{{ route('admin.university_rules.rule_sets.activate', [$university, $currentRuleSet]) }}">
            @csrf
            <button type="submit" class="btn btn-sm px-3 py-2 rounded-3 fw-semibold"
                style="background:linear-gradient(135deg,#10b981,#059669); color:#fff; font-size:13px; border:none;"
                onclick="return confirm('Activate this rule set? It will replace the current active set.')">
                <i class="fa-solid fa-check me-1"></i> Activate
            </button>
        </form>
        @endif
    </div>
@endsection

@section('content')

{{-- Status bar --}}
<div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3"
    style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06);">
    @if($university->logo_url)
        <img src="{{ $university->logo_url }}" alt="{{ $university->name }}" style="height:36px; width:36px; object-fit:contain; border-radius:8px;">
    @else
        <div class="rounded-2 d-flex align-items-center justify-content-center fw-bold"
            style="width:36px; height:36px; background:{{ $university->theme_color ?? '#6366f1' }}22; color:{{ $university->theme_color ?? '#6366f1' }}; font-size:13px;">
            {{ $university->initials }}
        </div>
    @endif
    <div>
        <div class="fw-bold text-white" style="font-size:15px;">{{ $university->name }}</div>
        <div style="font-size:12px; color:#64748b;">{{ $university->code }}</div>
    </div>
    <div class="ms-3 vr" style="border-color:rgba(255,255,255,0.06); height:32px;"></div>
    <div class="ms-2">
        @if($currentRuleSet)
            <span class="fw-semibold text-white" style="font-size:13px;">{{ $currentRuleSet->name }}</span>
            <span class="ms-2 badge text-uppercase px-2 py-1"
                style="font-size:10px; letter-spacing:0.05em;
                @if($currentRuleSet->status === 'active') background:rgba(16,185,129,0.15); color:#10b981;
                @else background:rgba(250,204,21,0.15); color:#facc15; @endif">
                {{ $currentRuleSet->status }}
            </span>
            <span class="text-secondary ms-2" style="font-size:11px;">v{{ $currentRuleSet->version }}</span>
        @else
            <span class="text-secondary" style="font-size:13px;">No rule set found</span>
        @endif
    </div>
    <div class="ms-auto">
        <span class="text-secondary" style="font-size:12px;">
            <i class="fa-solid fa-scale-balanced me-1"></i>
            {{ $rules->count() }} rules total
        </span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success border-0 rounded-3 mb-4"
    style="background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3) !important; color:#10b981;">
    <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="alert alert-danger border-0 rounded-3 mb-4"
    style="background:rgba(248,113,113,0.1); border:1px solid rgba(248,113,113,0.3) !important; color:#f87171;">
    @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
</div>
@endif

@if(!$currentRuleSet)
<div class="text-center py-5">
    <i class="fa-solid fa-scale-balanced text-secondary mb-3" style="font-size:48px;"></i>
    <div class="text-secondary mb-3">No rule set found for {{ $university->name }}.</div>
    <small class="text-secondary">Run <code>php artisan db:seed --class=DynamicRuleEngineSeeder</code> to create default rule sets.</small>
</div>
@else

{{-- Category Tabs --}}
@php
    $allCategories = ['kpi', 'positive', 'attendance', 'lead_management', 'negative', 'recovery', 'tier'];
    $categoryColors = [
        'kpi'             => ['bg' => 'rgba(56,189,248,0.12)', 'border' => '#38bdf8', 'text' => '#38bdf8', 'icon' => 'fa-bullseye'],
        'positive'        => ['bg' => 'rgba(16,185,129,0.12)', 'border' => '#10b981', 'text' => '#10b981', 'icon' => 'fa-circle-plus'],
        'attendance'      => ['bg' => 'rgba(99,102,241,0.12)', 'border' => '#6366f1', 'text' => '#6366f1', 'icon' => 'fa-calendar-check'],
        'lead_management' => ['bg' => 'rgba(251,191,36,0.12)', 'border' => '#fbbf24', 'text' => '#fbbf24', 'icon' => 'fa-user-tag'],
        'negative'        => ['bg' => 'rgba(248,113,113,0.12)', 'border' => '#f87171', 'text' => '#f87171', 'icon' => 'fa-circle-minus'],
        'recovery'        => ['bg' => 'rgba(250,204,21,0.12)', 'border' => '#facc15', 'text' => '#facc15', 'icon' => 'fa-rotate'],
        'tier'            => ['bg' => 'rgba(139,92,246,0.12)', 'border' => '#8b5cf6', 'text' => '#8b5cf6', 'icon' => 'fa-layer-group'],
    ];
    $existingCats = $rules->pluck('category')->unique()->values()->toArray();
    $allCats = array_unique(array_merge($allCategories, $existingCats));
@endphp

<ul class="nav gap-2 mb-4 flex-wrap" id="catTabs">
    <li class="nav-item">
        <a class="nav-link active px-3 py-2 rounded-3 fw-semibold" data-cat="all" href="#"
            style="font-size:12px; background:rgba(255,255,255,0.08); color:#fff; text-decoration:none; border:1px solid rgba(255,255,255,0.1);">
            All ({{ $rules->count() }})
        </a>
    </li>
    @foreach($allCats as $cat)
        @php $cc = $categoryColors[$cat] ?? ['bg'=>'rgba(255,255,255,0.05)','border'=>'rgba(255,255,255,0.1)','text'=>'#94a3b8','icon'=>'fa-circle']; @endphp
        <li class="nav-item">
            <a class="nav-link px-3 py-2 rounded-3 fw-semibold cat-tab" data-cat="{{ $cat }}" href="#"
                style="font-size:12px; background:{{ $cc['bg'] }}; color:{{ $cc['text'] }}; text-decoration:none; border:1px solid {{ $cc['border'] }}44;">
                <i class="fa-solid {{ $cc['icon'] }} me-1"></i>
                {{ ucwords(str_replace('_', ' ', $cat)) }}
                ({{ $rules->where('category', $cat)->count() }})
            </a>
        </li>
    @endforeach
</ul>

{{-- Rules Table --}}
@foreach($allCats as $cat)
    @php $catRules = $rules->where('category', $cat); $cc = $categoryColors[$cat] ?? ['bg'=>'rgba(255,255,255,0.03)','border'=>'rgba(255,255,255,0.08)','text'=>'#94a3b8','icon'=>'fa-circle']; @endphp
    @if($catRules->count() > 0)
    <div class="rule-section mb-4" data-section="{{ $cat }}">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold m-0" style="color:{{ $cc['text'] }};">
                <i class="fa-solid {{ $cc['icon'] }} me-2"></i>
                {{ ucwords(str_replace('_', ' ', $cat)) }} Rules
            </h6>
            <button class="btn btn-sm px-3 py-1 rounded-3"
                style="background:{{ $cc['bg'] }}; color:{{ $cc['text'] }}; border:1px solid {{ $cc['border'] }}33; font-size:12px;"
                onclick="openAddRule('{{ $cat }}')">
                <i class="fa-solid fa-plus me-1"></i> Add Rule
            </button>
        </div>
        <div class="tims-filter-card p-0 overflow-hidden">
            <table class="table table-dark mb-0" style="border-color:rgba(255,255,255,0.04);">
                <thead style="background:rgba(255,255,255,0.03);">
                    <tr style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:#64748b; border-bottom:1px solid rgba(255,255,255,0.06);">
                        <th class="px-4 py-3">Rule Name / Code</th>
                        <th class="px-3 py-3">Type</th>
                        <th class="px-3 py-3">Metric</th>
                        <th class="px-3 py-3">Condition</th>
                        <th class="px-3 py-3 text-end">Points</th>
                        <th class="px-3 py-3 text-center">Active</th>
                        <th class="px-3 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($catRules->sortBy('sort_order') as $rule)
                    <tr class="rule-row" style="border-bottom:1px solid rgba(255,255,255,0.04);">
                        <td class="px-4 py-3">
                            <div class="fw-semibold text-white" style="font-size:13px;">{{ $rule->name }}</div>
                            <code style="font-size:10px; color:#6366f1; background:rgba(99,102,241,0.1); padding:1px 6px; border-radius:4px;">{{ $rule->code }}</code>
                        </td>
                        <td class="px-3 py-3">
                            <span class="badge text-uppercase" style="font-size:10px; background:rgba(255,255,255,0.06); color:#94a3b8; letter-spacing:0.04em;">
                                {{ str_replace('_', ' ', $rule->calculation_type) }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-secondary" style="font-size:12px;">{{ $rule->input_metric ?? '—' }}</td>
                        <td class="px-3 py-3" style="font-size:12px; color:#94a3b8;">
                            @if($rule->operator && $rule->threshold_value !== null)
                                <code style="color:#facc15;">{{ $rule->operator }} {{ $rule->threshold_value }}{{ $rule->threshold_to ? ' – ' . $rule->threshold_to : '' }}</code>
                            @else
                                <span class="text-secondary">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-end">
                            <span class="fw-bold" style="font-size:15px; color:{{ $rule->points >= 0 ? '#10b981' : '#f87171' }};">
                                {{ $rule->points >= 0 ? '+' : '' }}{{ $rule->points }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <form method="POST" action="{{ route('admin.university_rules.rules.toggle', $rule) }}">
                                @csrf
                                <button type="submit" class="border-0 bg-transparent p-0"
                                    style="cursor:pointer; color:{{ $rule->is_active ? '#10b981' : '#475569' }}; font-size:18px;"
                                    title="{{ $rule->is_active ? 'Click to disable' : 'Click to enable' }}">
                                    <i class="fa-solid {{ $rule->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                </button>
                            </form>
                        </td>
                        <td class="px-3 py-3 text-end">
                            <button class="btn btn-sm px-2 py-1 me-1 rounded-2"
                                style="background:rgba(99,102,241,0.1); color:#6366f1; border:1px solid rgba(99,102,241,0.2); font-size:11px;"
                                onclick="openEditRule({{ $rule->id }}, {{ $rule->toJson() }})">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.university_rules.rules.destroy', $rule) }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm px-2 py-1 rounded-2"
                                    style="background:rgba(248,113,113,0.1); color:#f87171; border:1px solid rgba(248,113,113,0.2); font-size:11px;"
                                    onclick="return confirm('Delete rule \'{{ addslashes($rule->name) }}\'?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endforeach

{{-- Empty state --}}
@if($rules->count() === 0)
<div class="text-center py-5">
    <i class="fa-solid fa-scale-balanced text-secondary mb-3" style="font-size:48px;"></i>
    <div class="text-secondary">No rules yet. Add your first rule using the button above.</div>
</div>
@endif

@endif {{-- end if currentRuleSet --}}

{{-- ─────────────── Add/Edit Rule Modal ─────────────────────────────── --}}
<div class="modal fade" id="ruleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4"
            style="background:#1a1a28; border:1px solid rgba(255,255,255,0.08) !important;">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold text-white" id="ruleModalTitle">Add Rule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="ruleForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label text-secondary" style="font-size:12px;">Rule Name *</label>
                            <input type="text" name="name" id="rule_name" class="form-control" required
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-secondary" style="font-size:12px;">Code * (alpha_dash, unique)</label>
                            <input type="text" name="code" id="rule_code" class="form-control" required
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary" style="font-size:12px;">Category *</label>
                            <select name="category" id="rule_category" class="form-select"
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                                <option value="kpi">KPI</option>
                                <option value="positive">Positive</option>
                                <option value="attendance">Attendance</option>
                                <option value="lead_management">Lead Management</option>
                                <option value="negative">Negative</option>
                                <option value="recovery">Recovery</option>
                                <option value="tier">Tier</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary" style="font-size:12px;">Calculation Type *</label>
                            <select name="calculation_type" id="rule_calc_type" class="form-select"
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                                <option value="fixed">Fixed</option>
                                <option value="range">Range (between)</option>
                                <option value="per_unit">Per Unit</option>
                                <option value="boolean">Boolean (flag)</option>
                                <option value="selected_violation">Selected Violation</option>
                                <option value="rolling_window">Rolling Window (FOCUZ)</option>
                                <option value="recovery_cap">Recovery Cap</option>
                                <option value="streak">Streak Bonus</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary" style="font-size:12px;">Input Metric</label>
                            <input type="text" name="input_metric" id="rule_input_metric" class="form-control"
                                placeholder="e.g. connected_calls"
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary" style="font-size:12px;">Operator</label>
                            <select name="operator" id="rule_operator" class="form-select"
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                                <option value="">—</option>
                                <option value=">=">≥ (gte)</option>
                                <option value=">"> > (gt)</option>
                                <option value="<=">≤ (lte)</option>
                                <option value="<"> < (lt)</option>
                                <option value="=">= (eq)</option>
                                <option value="between">Between</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary" style="font-size:12px;">Threshold From</label>
                            <input type="number" name="threshold_value" id="rule_threshold" class="form-control" step="0.01"
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary" style="font-size:12px;">Threshold To (range)</label>
                            <input type="number" name="threshold_to" id="rule_threshold_to" class="form-control" step="0.01"
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary" style="font-size:12px;">Points *</label>
                            <input type="number" name="points" id="rule_points" class="form-control" step="0.5" required
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-secondary" style="font-size:12px;">Sort Order</label>
                            <input type="number" name="sort_order" id="rule_sort_order" class="form-control"
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:8px;">
                        </div>
                        <div class="col-md-10 d-flex align-items-end gap-2 pb-1">
                            <div class="form-check form-switch ms-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="rule_is_active" value="1" checked>
                                <label class="form-check-label text-secondary" for="rule_is_active" style="font-size:13px;">Active</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary" style="font-size:12px;">condition_json <span style="color:#64748b;">(optional JSON)</span></label>
                            <textarea name="condition_json" id="rule_condition_json" class="form-control" rows="3"
                                placeholder='e.g. {"all_true":["crm_disposition_correct","all_leads_followed_up"]}'
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#a78bfa; border-radius:8px; font-family:monospace; font-size:12px;"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary" style="font-size:12px;">action_json <span style="color:#64748b;">(optional JSON)</span></label>
                            <textarea name="action_json" id="rule_action_json" class="form-control" rows="3"
                                placeholder='e.g. {"create_violation":true,"violation_type":"call"}'
                                style="background:#121217; border:1px solid rgba(255,255,255,0.08); color:#facc15; border-radius:8px; font-family:monospace; font-size:12px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-sm px-4 py-2 rounded-3 text-secondary" data-bs-dismiss="modal"
                        style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);">Cancel</button>
                    <button type="submit" class="btn btn-sm px-4 py-2 rounded-3 fw-semibold"
                        style="background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border:none;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ── Category tab filter ────────────────────────────────────────────────
    document.querySelectorAll('.cat-tab, [data-cat="all"]').forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();
            document.querySelectorAll('#catTabs .nav-link').forEach(t => {
                t.style.background = 'rgba(255,255,255,0.04)';
                t.style.color = '#64748b';
                t.classList.remove('active');
            });
            tab.style.background = 'rgba(255,255,255,0.1)';
            tab.style.color = '#fff';
            tab.classList.add('active');

            const cat = tab.dataset.cat;
            document.querySelectorAll('.rule-section').forEach(sec => {
                sec.style.display = (cat === 'all' || sec.dataset.section === cat) ? '' : 'none';
            });
        });
    });

    // ── Modal helpers ─────────────────────────────────────────────────────
    const addStoreUrl = '{{ route('admin.university_rules.rules.store', [$university, $currentRuleSet ?? 0]) }}';

    function openAddRule(cat) {
        document.getElementById('ruleModalTitle').textContent = 'Add Rule';
        document.getElementById('ruleForm').action = addStoreUrl;
        document.getElementById('methodField').innerHTML = '';

        // Reset
        document.getElementById('ruleForm').reset();
        document.getElementById('rule_is_active').checked = true;
        if (cat) document.getElementById('rule_category').value = cat;

        new bootstrap.Modal(document.getElementById('ruleModal')).show();
    }

    function openEditRule(id, data) {
        document.getElementById('ruleModalTitle').textContent = 'Edit Rule';
        const base = '{{ route('admin.university_rules.rules.update', ':id') }}'.replace(':id', id);
        document.getElementById('ruleForm').action = base;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('rule_name').value = data.name || '';
        document.getElementById('rule_code').value = data.code || '';
        document.getElementById('rule_category').value = data.category || 'positive';
        document.getElementById('rule_calc_type').value = data.calculation_type || 'fixed';
        document.getElementById('rule_input_metric').value = data.input_metric || '';
        document.getElementById('rule_operator').value = data.operator || '';
        document.getElementById('rule_threshold').value = data.threshold_value ?? '';
        document.getElementById('rule_threshold_to').value = data.threshold_to ?? '';
        document.getElementById('rule_points').value = data.points || 0;
        document.getElementById('rule_sort_order').value = data.sort_order ?? '';
        document.getElementById('rule_is_active').checked = !!data.is_active;
        document.getElementById('rule_condition_json').value = data.condition_json ? JSON.stringify(data.condition_json, null, 2) : '';
        document.getElementById('rule_action_json').value = data.action_json ? JSON.stringify(data.action_json, null, 2) : '';

        new bootstrap.Modal(document.getElementById('ruleModal')).show();
    }
</script>
@endsection
