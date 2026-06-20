@extends('layouts.app')

@section('title', 'Point Engine Configuration')
@section('page_title', 'Point Engine Configuration')

@section('content')

<div class="alert alert-info border-0 rounded-3 d-flex gap-3 align-items-start mb-4">
    <i class="fa-solid fa-sliders fa-lg mt-1 flex-shrink-0"></i>
    <div>
        <strong>Dynamic Point Engine:</strong> All scoring coefficients below are applied live to every daily log entry.
        Changes take effect immediately on the next log submission — no redeployment required.
        All modifications are tracked in the system activity log.
    </div>
</div>

<form method="POST" action="{{ route('admin.rules.update') }}" id="rulesForm">
    @csrf

    @if(session('success'))
    <div class="alert alert-success border-0 rounded-3 mb-4">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
    </div>
    @endif

    @php
        $groups = $rules->groupBy('rule_group');
        $groupLabels = [
            'calls'     => ['label' => 'Call Volume Points', 'icon' => 'fa-phone-volume', 'color' => 'primary'],
            'meetings'  => ['label' => 'Meeting Points & Bonuses', 'icon' => 'fa-handshake', 'color' => 'success'],
            'lead_mgmt' => ['label' => 'Lead Management KPIs', 'icon' => 'fa-list-check', 'color' => 'info'],
            'conversion'=> ['label' => 'Conversion Bonuses', 'icon' => 'fa-fire', 'color' => 'warning'],
            'recovery'  => ['label' => 'Recovery Bonuses', 'icon' => 'fa-rotate-right', 'color' => 'secondary'],
            'violation' => ['label' => 'Violation Penalties (Negative)', 'icon' => 'fa-ban', 'color' => 'danger'],
        ];
    @endphp

    @foreach($groups as $group => $groupRules)
    @php $meta = $groupLabels[$group] ?? ['label' => ucwords($group), 'icon' => 'fa-gear', 'color' => 'secondary']; @endphp
    <div class="glass-card p-4 mb-4">
        <h5 class="fw-bold mb-4">
            <i class="fa-solid {{ $meta['icon'] }} text-{{ $meta['color'] }} me-2"></i>{{ $meta['label'] }}
        </h5>
        <div class="row g-3">
            @foreach($groupRules as $rule)
            <input type="hidden" name="rules[{{ $loop->parent->index * 100 + $loop->index }}][id]" value="{{ $rule->id }}">
            <div class="col-lg-6">
                <div class="p-3 border rounded-3 {{ !$rule->is_active ? 'opacity-50' : '' }}" style="background: rgba(0,0,0,0.02);">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="fw-semibold">{{ $rule->rule_name }}</div>
                            <small class="font-monospace text-secondary">{{ $rule->rule_key }}</small>
                            @if($rule->description)
                            <div class="text-secondary small mt-1">{{ $rule->description }}</div>
                            @endif
                        </div>
                        <div class="form-check form-switch ms-3 flex-shrink-0">
                            <input class="form-check-input" type="checkbox"
                                   name="rules[{{ $loop->parent->index * 100 + $loop->index }}][is_active]"
                                   {{ $rule->is_active ? 'checked' : '' }}>
                            <label class="form-check-label small text-secondary">Active</label>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text fw-bold {{ $rule->rule_value >= 0 ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }}">
                            {{ $rule->rule_value >= 0 ? '+' : '' }} pts
                        </span>
                        <input type="number"
                               name="rules[{{ $loop->parent->index * 100 + $loop->index }}][rule_value]"
                               class="form-control fw-bold font-monospace"
                               value="{{ $rule->rule_value }}"
                               step="0.5"
                               required>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="d-flex justify-content-end gap-3 mt-2 mb-5">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
        <button type="submit" class="btn btn-primary rounded-3 px-5 fw-semibold">
            <i class="fa-solid fa-floppy-disk me-2"></i>Save All Rule Changes
        </button>
    </div>
</form>
@endsection
