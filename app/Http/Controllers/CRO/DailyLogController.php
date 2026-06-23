<?php

namespace App\Http\Controllers\CRO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyLog;
use App\Models\Executive;
use App\Models\Meeting;
use App\Models\Violation;
use App\Models\ScoreTransaction;
use App\Models\User;
use App\Models\University;
use App\Models\Zone;
use App\Services\DynamicRuleEngineService;
use App\Services\EscalationService;
use App\Services\StreakService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyLogController extends Controller
{
    protected DynamicRuleEngineService $ruleEngine;
    protected EscalationService $escalator;
    protected StreakService $streaks;

    public function __construct(
        DynamicRuleEngineService $ruleEngine,
        EscalationService $escalator,
        StreakService $streaks
    ) {
        $this->ruleEngine = $ruleEngine;
        $this->escalator  = $escalator;
        $this->streaks    = $streaks;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  INDEX  – Daily Performance Logs list with day-wise filter
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        // Default to today if no date filter is set
        $dateFrom = $request->filled('date_from') ? $request->date_from : now()->toDateString();
        $dateTo   = $request->filled('date_to')   ? $request->date_to   : now()->toDateString();

        $logs = DailyLog::with([
                'executive.university',
                'executive.zone',
                'violations',
                'creator',
                'approvedBy',
            ])
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo)
            ->when($request->filled('university_id'),
                fn ($q) => $q->where('university_id', $request->university_id))
            ->when($request->filled('executive_id'),
                fn ($q) => $q->where('executive_id', $request->executive_id))
            ->when($request->filled('executive_name'), function ($q) use ($request) {
                $q->whereHas('executive', fn ($eq) =>
                    $eq->where('name', 'like', '%' . $request->executive_name . '%'));
            })
            ->when($request->filled('kpi_status'),
                fn ($q) => $q->where('kpi_status', $request->kpi_status))
            ->when($request->filled('violation_status'),
                fn ($q) => $q->where('violation_status', $request->violation_status))
            ->when($request->filled('tier'), function ($q) use ($request) {
                $q->whereHas('executive', fn ($eq) => $eq->where('current_tier', $request->tier));
            })
            ->when($request->filled('zone_id'), function ($q) use ($request) {
                $q->whereHas('executive', fn ($eq) => $eq->where('zone_id', $request->zone_id));
            })
            ->orderByDesc('date')
            ->orderByDesc('calculated_score')
            ->paginate(20)
            ->withQueryString();

        // ── Leaderboard analytics (always today) ─────────────────────────────
        $todayLogs = DailyLog::with(['executive.zone', 'executive.university'])
            ->whereDate('date', today())
            ->get();

        $analytics = [
            'top_performer'   => $todayLogs->sortByDesc('calculated_score')->first(),
            'lowest_performer'=> $todayLogs->sortBy('calculated_score')->first(),
            'most_calls'      => $todayLogs->sortByDesc('connected_calls')->first(),
            'most_meetings'   => $todayLogs->sortByDesc('meetings_attended')->first(),
            'most_violations' => $todayLogs->sortByDesc(function ($log) {
                return $log->violations()->count();
            })->first(),
        ];

        $universities = University::where('status', 'active')->orderBy('name')->get();
        $zones        = Zone::orderBy('name')->get();
        $executives   = Executive::orderBy('name')->get();

        return view('daily_logs.index', compact(
            'logs', 'universities', 'zones', 'executives', 'analytics',
            'dateFrom', 'dateTo'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  CREATE  – show the form
    // ─────────────────────────────────────────────────────────────────────────

    public function create()
    {
        $executives = Executive::where('status', '!=', 'inactive')
            ->orderBy('name', 'asc')
            ->get();

        $auditLogs = DailyLog::with(['executive', 'creator', 'approvedBy'])
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        return view('daily_logs.create', compact('executives', 'auditLogs'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  AJAX: fetch CRM metrics for an executive + date
    // ─────────────────────────────────────────────────────────────────────────

    public function fetchCrmMetrics(Request $request)
    {
        $execId = $request->input('executive_id');
        $date   = $request->input('date', Carbon::today()->toDateString());

        if (!$execId) {
            return response()->json(['error' => 'Executive ID is required.'], 400);
        }

        $executive = Executive::findOrFail($execId);

        // Check for an existing log for this date first
        $existingLog = DailyLog::where('executive_id', $execId)
            ->whereDate('date', $date)
            ->first();

        if ($existingLog) {
            return response()->json([
                'connected_calls'            => $existingLog->connected_calls,
                'meetings_arranged'          => $existingLog->meetings_arranged,
                'meetings_attended'          => $existingLog->meetings_attended,
                'first_contact_within_45_min'=> (bool) $existingLog->first_contact_within_45_min,
                'all_leads_followed_up'      => (bool) $existingLog->all_leads_followed_up,
                'crm_disposition_correct'    => (bool) $existingLog->crm_disposition_correct,
                'warm_lead_converted'        => (bool) $existingLog->warm_lead_converted,
                'source'                     => 'saved_log',
            ]);
        }

        // Fetch actual meetings from DB if any exist
        $arranged = Meeting::where('executive_id', $execId)->whereDate('meeting_date', $date)->count();
        $attended = Meeting::where('executive_id', $execId)->whereDate('meeting_date', $date)->where('status', 'attended')->count();

        // Deterministic simulation based on executive id and date
        $seed = crc32($execId . $date);
        if ($arranged === 0) {
            $arranged = $seed % 5;
            $attended = $arranged > 0 ? ($seed % ($arranged + 1)) : 0;
        }

        $connectedCalls          = 35 + ($seed % 40);
        $firstContactCompliance  = ((15 + ($seed % 45)) <= 45);
        $leadFollowups           = ($seed % 2 === 0);
        $crmDisposition          = ($seed % 3 !== 0);
        $warmLeadConverted       = ($seed % 4 === 0);

        return response()->json([
            'connected_calls'            => $connectedCalls,
            'meetings_arranged'          => $arranged,
            'meetings_attended'          => $attended,
            'first_contact_within_45_min'=> $firstContactCompliance,
            'all_leads_followed_up'      => $leadFollowups,
            'crm_disposition_correct'    => $crmDisposition,
            'warm_lead_converted'        => $warmLeadConverted,
            'source'                     => 'simulated',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  AJAX: preview score (no DB writes)
    // ─────────────────────────────────────────────────────────────────────────

    public function previewScore(Request $request)
    {
        $validated = $request->validate([
            'date'                       => ['required', 'date'],
            'executive_id'               => ['required', 'exists:executives,id'],
            'connected_calls'            => ['required', 'integer', 'min:0'],
            'meetings_arranged'          => ['required', 'integer', 'min:0'],
            'meetings_attended'          => ['required', 'integer', 'min:0'],
            'first_contact_within_45_min'=> ['nullable', 'boolean'],
            'all_leads_followed_up'      => ['nullable', 'boolean'],
            'crm_disposition_correct'    => ['nullable', 'boolean'],
            'warm_lead_converted'        => ['nullable', 'boolean'],
            'cold_lead_reactivated'      => ['nullable', 'boolean'],
            'violations'                 => ['nullable', 'array'],
        ]);

        $executive = Executive::findOrFail($validated['executive_id']);
        $log = new DailyLog($validated);
        $log->university_id = $executive->university_id;
        $log->setRelation('executive', $executive);

        // Use the dynamic rule engine — reads rules from DB per university
        $result = $this->ruleEngine->preview($log, $request->input('violations', []));

        return response()->json([
            'positive_points' => $result['positive_points'],
            'negative_points' => $result['negative_points'],
            'recovery_points' => $result['recovery_points'],
            'net_score'       => $result['net_score'],
            'kpi_status'      => $result['kpi_status'],
            'breakdown'       => $result['breakdown'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  AJAX: executive dashboard data (for the create form sidebar)
    // ─────────────────────────────────────────────────────────────────────────

    public function getExecutiveDashboardData(Request $request)
    {
        $execId = $request->input('executive_id');
        $date   = $request->input('date', Carbon::today()->toDateString());

        if (!$execId) {
            return response()->json(['error' => 'Executive ID is required.'], 400);
        }

        $executive = Executive::with('zone')->findOrFail($execId);

        // Rank within university
        $rank = Executive::where('university_id', $executive->university_id)
            ->where('status', 'active')
            ->where('current_score', '>', $executive->current_score)
            ->count() + 1;

        // 7-day and 30-day totals
        $last7Days = ScoreTransaction::where('executive_id', $executive->id)
            ->where('transaction_date', '>=', now()->subDays(7)->toDateString())
            ->get()
            ->sum(fn ($tx) => $tx->type === 'credit' ? $tx->points : -$tx->points);

        $last30Days = ScoreTransaction::where('executive_id', $executive->id)
            ->where('transaction_date', '>=', now()->subDays(30)->toDateString())
            ->get()
            ->sum(fn ($tx) => $tx->type === 'credit' ? $tx->points : -$tx->points);

        // Past 7 logs for streaks + chart
        $pastLogs = DailyLog::where('executive_id', $executive->id)
            ->where('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        $callStreakCount    = $pastLogs->where('connected_calls', '>=', 40)->count();
        $meetingStreakCount = $pastLogs->where('meetings_arranged', '>=', 1)->count();
        $dualStreakCount    = $pastLogs->filter(fn ($l) => $l->connected_calls >= 40 && $l->meetings_arranged >= 1)->count();

        // Chart data for last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::parse($date)->subDays($i)->toDateString();
            $logForDay = $pastLogs->firstWhere('date', $d) ?? $pastLogs->firstWhere('date', Carbon::parse($d)->toDateString());
            $chartData[] = [
                'date'     => Carbon::parse($d)->format('M d'),
                'score'    => $logForDay ? $logForDay->calculated_score : 0,
                'positive' => $logForDay ? $logForDay->positive_points  : 0,
                'negative' => $logForDay ? $logForDay->negative_points  : 0,
                'recovery' => $logForDay ? $logForDay->recovery_points  : 0,
                'calls'    => $logForDay ? $logForDay->connected_calls  : 0,
            ];
        }

        // Existing log for this date (to pre-fill the form)
        $existingLog = DailyLog::where('executive_id', $execId)
            ->whereDate('date', $date)
            ->first();

        $selectedViolations = Violation::where('daily_log_id', $existingLog?->id)
            ->pluck('violation_subtype')
            ->toArray();

        // Audit trail
        $auditTrail = DailyLog::with(['executive', 'creator', 'approvedBy'])
            ->where('executive_id', $executive->id)
            ->orderBy('date', 'desc')
            ->take(15)
            ->get()
            ->map(fn ($log) => [
                'date'       => $log->date->toDateString(),
                'executive'  => $log->executive->name,
                'created_by' => $log->creator->name   ?? 'System',
                'approved_by'=> $log->approvedBy->name ?? 'Pending',
                'positive'   => $log->positive_points,
                'negative'   => $log->negative_points,
                'recovery'   => $log->recovery_points,
                'total'      => $log->calculated_score,
                'remarks'    => $log->cro_remarks ?: 'No remarks',
            ]);

        return response()->json([
            'executive' => [
                'id'           => $executive->id,
                'name'         => $executive->name,
                'employee_id'  => $executive->employee_id,
                'current_score'=> $executive->current_score,
                'current_tier' => strtoupper($executive->current_tier),
                'rank'         => '#' . $rank,
                'last_7_days'  => ($last7Days >= 0 ? '+' : '') . $last7Days,
                'last_30_days' => ($last30Days >= 0 ? '+' : '') . $last30Days,
            ],
            'streaks' => [
                'calls'    => $callStreakCount,
                'meetings' => $meetingStreakCount,
                'dual'     => $dualStreakCount,
            ],
            'charts'      => $chartData,
            'audit_trail' => $auditTrail,
            'existing_log'      => $existingLog,
            'selected_violations'=> $selectedViolations,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  STORE  – save the daily log and calculate points
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        // Convert toggle/checkbox values to booleans
        $request->merge([
            'first_contact_within_45_min' => $request->has('first_contact_within_45_min') ? 1 : 0,
            'all_leads_followed_up'       => $request->has('all_leads_followed_up')       ? 1 : 0,
            'crm_disposition_correct'     => $request->has('crm_disposition_correct')     ? 1 : 0,
            'warm_lead_converted'         => $request->has('warm_lead_converted')         ? 1 : 0,
            'cold_lead_reactivated'       => $request->has('cold_lead_reactivated')       ? 1 : 0,
            'is_random_audit'             => $request->has('is_random_audit')             ? 1 : 0,
        ]);

        $validated = $request->validate([
            'date'                       => ['required', 'date'],
            'executive_id'               => ['required', 'exists:executives,id'],
            'connected_calls'            => ['required', 'integer', 'min:0'],
            'meetings_arranged'          => ['required', 'integer', 'min:0'],
            'meetings_attended'          => ['required', 'integer', 'min:0', 'lte:meetings_arranged'],
            'first_contact_within_45_min'=> ['required', 'boolean'],
            'all_leads_followed_up'      => ['required', 'boolean'],
            'crm_disposition_correct'    => ['required', 'boolean'],
            'warm_lead_converted'        => ['required', 'boolean'],
            'cold_lead_reactivated'      => ['nullable', 'boolean'],
            'is_random_audit'            => ['nullable', 'boolean'],
            'cro_remarks'                => ['nullable', 'string'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['approved_by'] = Auth::id();

        try {
            DB::beginTransaction();

            // ── 1. Upsert the DailyLog row ───────────────────────────────────
            $executive = Executive::findOrFail($validated['executive_id']);

            $log = DailyLog::updateOrCreate(
                [
                    'date'         => $validated['date'],
                    'executive_id' => $validated['executive_id'],
                ],
                array_merge($validated, [
                    'university_id' => $executive->university_id,
                ])
            );

            // ── 2. Calculate & apply points via the DYNAMIC RULE ENGINE ───────
            //       Rules are fetched from DB per university; no hardcoded values.
            $selectedViolations = $request->input('violations', []);
            $pointsEarned = $this->ruleEngine->calculateAndApply($log, $selectedViolations);

            // ── 3. Reload the log so we have fresh calculated values ──────────
            $log->refresh();
            $executive->refresh();

            // ── 4. Update call/meeting streaks ────────────────────────────────
            $this->streaks->updateCallStreak($executive, $log);
            $this->streaks->updateMeetingStreak($executive, $log);

            // ── 5. Run escalation checks ──────────────────────────────────────
            $this->escalator->checkForEscalations($executive);

            DB::commit();

            return redirect()
                ->route('daily_logs.index')
                ->with('success', "Daily performance log saved! Score: {$pointsEarned} pts applied to {$executive->name}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error saving log: ' . $e->getMessage()]);
        }
    }

    public function destroy(DailyLog $dailyLog)
{
    try {
        DB::beginTransaction();

        // Reverse the score that was applied for this log
        $executive = $dailyLog->executive;

        // Remove associated violations
        $dailyLog->violations()->delete();

        // Reverse score transactions tied to this log
        ScoreTransaction::where('daily_log_id', $dailyLog->id)->delete();

        // Recalculate the executive's current score from remaining transactions
        $newScore = ScoreTransaction::where('executive_id', $executive->id)
            ->get()
            ->sum(fn ($tx) => $tx->type === 'credit' ? $tx->points : -$tx->points);

        $executive->update(['current_score' => $newScore]);

        $dailyLog->delete();

        DB::commit();

        return redirect()
            ->route('daily_logs.index')
            ->with('success', "Log deleted and score reversed for {$executive->name}.");

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Error deleting log: ' . $e->getMessage()]);
    }
}
}
