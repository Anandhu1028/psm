@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Rule — {{ $rule->code }}</h3>

    <form method="POST" action="{{ route('admin.university_rules.rules.update', ['rule' => $rule->id]) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Name</label>
            <input name="name" value="{{ old('name', $rule->name) }}" class="form-control" required>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Code</label>
                <input name="code" value="{{ old('code', $rule->code) }}" class="form-control" required>
            </div>
            <div class="form-group col-md-4">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option value="positive" @if($rule->category=='positive') selected @endif>positive</option>
                    <option value="negative" @if($rule->category=='negative') selected @endif>negative</option>
                    <option value="recovery" @if($rule->category=='recovery') selected @endif>recovery</option>
                    <option value="kpi" @if($rule->category=='kpi') selected @endif>kpi</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>Points</label>
                <input name="points" type="number" step="0.1" value="{{ old('points', $rule->points) }}" class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Calculation Type</label>
                <input name="calculation_type" value="{{ old('calculation_type', $rule->calculation_type) }}" class="form-control">
            </div>
            <div class="form-group col-md-4">
                <label>Input Metric</label>
                <input name="input_metric" value="{{ old('input_metric', $rule->input_metric) }}" class="form-control">
            </div>
            <div class="form-group col-md-4">
                <label>Operator</label>
                <input name="operator" value="{{ old('operator', $rule->operator) }}" class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Threshold Value</label>
                <input name="threshold_value" type="number" step="0.01" value="{{ old('threshold_value', $rule->threshold_value) }}" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label>Threshold To</label>
                <input name="threshold_to" type="number" step="0.01" value="{{ old('threshold_to', $rule->threshold_to) }}" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>Condition JSON</label>
            <textarea name="condition_json" class="form-control" rows="5">{{ old('condition_json', json_encode($rule->condition_json)) }}</textarea>
        </div>

        <div class="form-group">
            <label>Action JSON</label>
            <textarea name="action_json" class="form-control" rows="5">{{ old('action_json', json_encode($rule->action_json)) }}</textarea>
        </div>

        <div class="form-group">
            <label>Sort Order</label>
            <input name="sort_order" type="number" value="{{ old('sort_order', $rule->sort_order) }}" class="form-control">
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" @if($rule->is_active) checked @endif>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.university_rules.index', ['university_id' => $university->id]) }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection

    @section('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function tryPrettyPrint(selector) {
            const el = document.querySelector(selector);
            if (!el) return;
            try {
                const txt = el.value.trim();
                if (!txt) return;
                const parsed = JSON.parse(txt);
                el.value = JSON.stringify(parsed, null, 2);
            } catch (e) {
                // ignore - leave user input as-is
            }
        }

        tryPrettyPrint('textarea[name="condition_json"]');
        tryPrettyPrint('textarea[name="action_json"]');

        const form = document.querySelector('form');
        form.addEventListener('submit', function (ev) {
            const fields = ['condition_json', 'action_json'];
            for (const name of fields) {
                const ta = document.querySelector('textarea[name="' + name + '"]');
                if (!ta) continue;
                const v = ta.value.trim();
                if (!v) continue;
                try {
                    JSON.parse(v);
                } catch (err) {
                    ev.preventDefault();
                    alert('Invalid JSON in ' + name + ': ' + err.message);
                    ta.focus();
                    return false;
                }
            }
        });
    });
    </script>
    @endsection
