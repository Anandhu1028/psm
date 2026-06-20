<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\ScoreRule;
use App\Models\Executive;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::withCount([
            'executives as total_executives',
            'executives as active_executives' => function ($query) {
                $query->where('status', 'active');
            }
        ])->get();

        return view('admin.universities.index', compact('universities'));
    }

    public function create()
    {
        return view('admin.universities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:universities,code'],
            'logo' => ['nullable', 'image', 'max:2048'], // Max 2MB
            'description' => ['nullable', 'string'],
            'theme_color' => ['required', 'string', 'max:10'],
            'status' => ['required', 'in:active,inactive'],
            'tier_colors' => ['required', 'array'],
            'tier_colors.platinum' => ['required', 'string', 'max:10'],
            'tier_colors.gold' => ['required', 'string', 'max:10'],
            'tier_colors.silver' => ['required', 'string', 'max:10'],
            'tier_colors.bronze' => ['required', 'string', 'max:10'],
            'tier_colors.review_zone' => ['required', 'string', 'max:10'],
        ]);

        $validated['created_by'] = Auth::id();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('universities', 'public');
            $validated['logo'] = basename($path);
        }

        return DB::transaction(function () use ($validated, $request) {
            $university = University::create($validated);

            // Clone rules from default TIMS university if it exists, otherwise seed defaults
            $tims = University::where('code', 'TIMS')->first();
            if ($tims && $tims->id !== $university->id) {
                $rules = ScoreRule::where('university_id', $tims->id)->get();
                foreach ($rules as $rule) {
                    $newRule = $rule->replicate();
                    $newRule->university_id = $university->id;
                    $newRule->save();
                }
            } else {
                // Seeding a basic fallback default rule template
                $defaultSeeder = new \Database\Seeders\ScoreRuleSeeder();
                // Since seeder handles firstOrCreate for TIMS, we can also manually clone after it
                // But since TIMS is seeded in migrations, the above cloning will run.
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'event_type' => 'university_create',
                'target_type' => University::class,
                'target_id' => $university->id,
                'description' => "University '{$university->name}' created successfully.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()
                ->route('admin.universities.index')
                ->with('success', "University '{$university->name}' created successfully.");
        });
    }

    public function show(University $university)
    {
        $university->loadCount([
            'executives as total_executives',
            'executives as active_executives' => function ($query) {
                $query->where('status', 'active');
            },
            'scoreRules as total_rules'
        ]);

        // Get tier structure from rules
        $tierRules = ScoreRule::where('university_id', $university->id)
            ->whereIn('rule_key', ['tier_platinum_min', 'tier_gold_min', 'tier_silver_min', 'tier_bronze_min'])
            ->get()
            ->keyBy('rule_key');

        $tierStructure = [
            'platinum' => isset($tierRules['tier_platinum_min']) ? (int) $tierRules['tier_platinum_min']->rule_value : 1200,
            'gold' => isset($tierRules['tier_gold_min']) ? (int) $tierRules['tier_gold_min']->rule_value : 700,
            'silver' => isset($tierRules['tier_silver_min']) ? (int) $tierRules['tier_silver_min']->rule_value : 300,
            'bronze' => isset($tierRules['tier_bronze_min']) ? (int) $tierRules['tier_bronze_min']->rule_value : 0,
        ];

        // Monthly average performance
        $executiveIds = Executive::where('university_id', $university->id)->pluck('id');
        $monthlyPerformance = DB::table('daily_logs')
            ->whereIn('executive_id', $executiveIds)
            ->where('date', '>=', now()->startOfMonth())
            ->avg('calculated_score');

        // Charts data
        // 1. Executive distribution by Zone
        $zonesDistribution = Executive::where('university_id', $university->id)
            ->join('zones', 'executives.zone_id', '=', 'zones.id')
            ->select('zones.name as zone_name', DB::raw('count(*) as count'))
            ->groupBy('zones.name')
            ->get();

        // 2. Tier distribution
        $tierDistribution = Executive::where('university_id', $university->id)
            ->select('current_tier', DB::raw('count(*) as count'))
            ->groupBy('current_tier')
            ->get();

        // 3. Performance Trend (last 6 months)
        $performanceTrend = DB::table('daily_logs')
            ->join('executives', 'daily_logs.executive_id', '=', 'executives.id')
            ->where('executives.university_id', $university->id)
            ->where('date', '>=', now()->subMonths(5)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(date, '%Y-%m') as month"), DB::raw('AVG(calculated_score) as avg_score'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Load rules
        $rules = ScoreRule::where('university_id', $university->id)
            ->orderBy('rule_group', 'asc')
            ->get();

        return view('admin.universities.show', compact(
            'university', 'tierStructure', 'monthlyPerformance',
            'zonesDistribution', 'tierDistribution', 'performanceTrend', 'rules'
        ));
    }

    public function edit(University $university)
    {
        return view('admin.universities.edit', compact('university'));
    }

    public function update(Request $request, University $university)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', "unique:universities,code,{$university->id}"],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
            'theme_color' => ['required', 'string', 'max:10'],
            'status' => ['required', 'in:active,inactive'],
            'tier_colors' => ['required', 'array'],
            'tier_colors.platinum' => ['required', 'string', 'max:10'],
            'tier_colors.gold' => ['required', 'string', 'max:10'],
            'tier_colors.silver' => ['required', 'string', 'max:10'],
            'tier_colors.bronze' => ['required', 'string', 'max:10'],
            'tier_colors.review_zone' => ['required', 'string', 'max:10'],
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($university->logo) {
                Storage::disk('public')->delete('universities/' . $university->logo);
            }
            $path = $request->file('logo')->store('universities', 'public');
            $validated['logo'] = basename($path);
        }

        $university->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'event_type' => 'university_update',
            'target_type' => University::class,
            'target_id' => $university->id,
            'description' => "University '{$university->name}' details updated.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('admin.universities.show', $university->id)
            ->with('success', "University details updated successfully.");
    }

    public function destroy(University $university)
    {
        $name = $university->name;

        // Delete logo if exists
        if ($university->logo) {
            Storage::disk('public')->delete('universities/' . $university->logo);
        }

        $university->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'event_type' => 'university_delete',
            'target_type' => University::class,
            'target_id' => $university->id,
            'description' => "University '{$name}' deleted.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.universities.index')
            ->with('success', "University '{$name}' has been deleted successfully.");
    }

    public function replaceLogo(Request $request, University $university)
    {
        $request->validate([
            'logo' => ['required', 'image', 'max:2048'],
        ]);

        if ($university->logo) {
            Storage::disk('public')->delete('universities/' . $university->logo);
        }

        $path = $request->file('logo')->store('universities', 'public');
        $university->logo = basename($path);
        $university->save();

        return back()->with('success', 'Logo replaced successfully.');
    }

    public function removeLogo(University $university)
    {
        if ($university->logo) {
            Storage::disk('public')->delete('universities/' . $university->logo);
            $university->logo = null;
            $university->save();
        }

        return back()->with('success', 'Logo removed successfully.');
    }

    public function updateRules(Request $request, University $university)
    {
        $request->validate([
            'rules' => ['required', 'array'],
            'rules.*.id' => ['required', 'exists:score_rules,id'],
            'rules.*.rule_value' => ['required', 'numeric'],
            'rules.*.is_active' => ['nullable'],
        ]);

        foreach ($request->rules as $ruleInput) {
            $rule = ScoreRule::where('university_id', $university->id)->findOrFail($ruleInput['id']);
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
                    'description' => "Rule '{$rule->rule_name}' for '{$university->name}' changed from {$oldValue} to {$newValue}.",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return back()->with('success', 'University point engine configurations updated successfully.');
    }
}
