<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\RuleSet;
use App\Models\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UniversityRuleController extends Controller
{
    /**
     * Show rule management UI for selected university.
     */
    public function index(Request $request)
    {
        $universities = University::orderBy('name')->get();

        $selectedUniversity = null;
        if ($request->filled('university_id')) {
            $selectedUniversity = University::find($request->university_id);
        }

        // default to first active university
        $selectedUniversity = $selectedUniversity ?? University::where('status', 'active')->first() ?? $universities->first();

        $ruleSets = $selectedUniversity ? $selectedUniversity->ruleSets()->orderByDesc('version')->get() : collect();
        $activeRuleSet = $ruleSets->firstWhere('status', 'active') ?? $ruleSets->first();
        $rules = $activeRuleSet ? $activeRuleSet->rules()->orderBy('sort_order')->get() : collect();

        return view('admin.university_rules.index', compact('universities', 'selectedUniversity', 'ruleSets', 'activeRuleSet', 'rules'));
    }

    /**
     * Show rule edit form
     */
    public function edit(Request $request, Rule $rule)
    {
        $ruleSet = $rule->ruleSet;
        $university = $rule->university;

        return view('admin.university_rules.edit', compact('rule', 'ruleSet', 'university'));
    }

    public function store(Request $request, University $university, RuleSet $ruleSet)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'code' => 'required|string',
            'name' => 'required|string',
            'points' => 'nullable|numeric',
            'calculation_type' => 'required|string',
            'input_metric' => 'nullable|string',
            'operator' => 'nullable|string',
            'threshold_value' => 'nullable|numeric',
            'threshold_to' => 'nullable|numeric',
            'condition_json' => 'nullable',
            'action_json' => 'nullable',
        ]);

        // Normalize JSON inputs: accept either JSON string or array
        $condition = $validated['condition_json'] ?? $request->input('condition_json');
        if (is_string($condition) && trim($condition) !== '') {
            $decoded = json_decode($condition, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->withInput()->withErrors(['condition_json' => 'Invalid JSON for condition_json']);
            }
            $validated['condition_json'] = $decoded;
        } else {
            $validated['condition_json'] = is_array($condition) ? $condition : [];
        }

        $action = $validated['action_json'] ?? $request->input('action_json');
        if (is_string($action) && trim($action) !== '') {
            $decodedA = json_decode($action, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->withInput()->withErrors(['action_json' => 'Invalid JSON for action_json']);
            }
            $validated['action_json'] = $decodedA;
        } else {
            $validated['action_json'] = is_array($action) ? $action : [];
        }

        $validated['university_id'] = $university->id;
        $validated['rule_set_id'] = $ruleSet->id;
        $validated['is_active'] = $request->has('is_active');

        Rule::create($validated);

        return redirect()->back()->with('success', 'Rule created.');
    }

    public function update(Request $request, Rule $rule)
    {

        $validated = $request->validate([
            'category' => 'required|string',
            'code' => 'required|string',
            'name' => 'required|string',
            'points' => 'nullable|numeric',
            'calculation_type' => 'required|string',
            'input_metric' => 'nullable|string',
            'operator' => 'nullable|string',
            'threshold_value' => 'nullable|numeric',
            'threshold_to' => 'nullable|numeric',
            'condition_json' => 'nullable',
            'action_json' => 'nullable',
        ]);

        $condition = $validated['condition_json'] ?? $request->input('condition_json');
        if (is_string($condition) && trim($condition) !== '') {
            $decoded = json_decode($condition, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->withInput()->withErrors(['condition_json' => 'Invalid JSON for condition_json']);
            }
            $validated['condition_json'] = $decoded;
        } else {
            $validated['condition_json'] = is_array($condition) ? $condition : [];
        }

        $action = $validated['action_json'] ?? $request->input('action_json');
        if (is_string($action) && trim($action) !== '') {
            $decodedA = json_decode($action, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->withInput()->withErrors(['action_json' => 'Invalid JSON for action_json']);
            }
            $validated['action_json'] = $decodedA;
        } else {
            $validated['action_json'] = is_array($action) ? $action : [];
        }

        $validated['is_active'] = $request->has('is_active');
        $rule->update($validated);

        return redirect()->back()->with('success', 'Rule updated.');
    }

    public function destroy(Rule $rule)
    {
        $rule->delete();
        return redirect()->back()->with('success', 'Rule deleted.');
    }

    public function toggleRule(Rule $rule)
    {
        $rule->is_active = !$rule->is_active;
        $rule->save();
        return redirect()->back()->with('success', 'Rule toggled.');
    }

    public function activateRuleSet(Request $request, University $university, RuleSet $ruleSet)
    {
        DB::transaction(function () use ($university, $ruleSet) {
            RuleSet::where('university_id', $university->id)->update(['status' => 'inactive']);
            $ruleSet->status = 'active';
            $ruleSet->save();
        });

        return redirect()->back()->with('success', 'Rule set activated for university.');
    }

    public function cloneToDraft(Request $request, University $university)
    {
        $active = $university->ruleSets()->where('status', 'active')->first();
        if (!$active) {
            return redirect()->back()->withErrors(['error' => 'No active rule set to clone.']);
        }

        $draft = DB::transaction(function () use ($active) {
            $new = $active->replicate();
            $new->version = $active->version + 1;
            $new->status = 'draft';
            $new->cloned_from_rule_set_id = $active->id;
            $new->save();

            foreach ($active->rules as $rule) {
                $copy = $rule->replicate();
                $copy->rule_set_id = $new->id;
                $copy->is_active = false;
                $copy->save();
            }

            return $new;
        });

        return redirect()->back()->with('success', 'Draft rule set created.');
    }
}

