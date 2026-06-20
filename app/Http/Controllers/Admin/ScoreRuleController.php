<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScoreRule;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ScoreRuleController extends Controller
{
    public function index()
    {
        $rules = ScoreRule::orderBy('rule_group', 'asc')
            ->orderBy('rule_key', 'asc')
            ->get();

        return view('admin.rules', compact('rules'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'rules' => ['required', 'array'],
            'rules.*.id' => ['required', 'exists:score_rules,id'],
            'rules.*.rule_value' => ['required', 'numeric'],
            'rules.*.is_active' => ['nullable'],
        ]);

        foreach ($request->rules as $ruleInput) {
            $rule = ScoreRule::findOrFail($ruleInput['id']);
            $oldValue = $rule->rule_value;
            $newValue = $ruleInput['rule_value'];
            
            $rule->rule_value = $newValue;
            $rule->is_active = isset($ruleInput['is_active']);
            $rule->save();

            if ($oldValue != $newValue) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'event_type' => 'score_mod',
                    'target_type' => ScoreRule::class,
                    'target_id' => $rule->id,
                    'description' => "Scoring rule coefficient for '{$rule->rule_name}' changed from {$oldValue} to {$newValue}.",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return redirect()
            ->route('admin.rules.index')
            ->with('success', 'Dynamic Point Engine coefficients updated successfully.');
    }
}
