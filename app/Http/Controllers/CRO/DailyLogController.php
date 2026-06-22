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
use App\Services\DirectPointCalculatorService;
use App\Services\EscalationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyLogController extends Controller
{
    protected DirectPointCalculatorService $calculator;
    protected EscalationService $escalator;

    public function __construct(DirectPointCalculatorService $calculator, EscalationService $escalator)
    {
        $this->calculator = $calculator;
        $this->escalator  = $escalator;
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
            'violations'                 => ['nullable', 'array'],
        ]);

        $executive = Executive::findOrFail($validated['executive_id']);
        $log = new DailyLog($validated);
        $log->university_id = $executive->university_id;
        $log->setRelation('executive', $executive);

        $result = $this->calculator->calculate($log, $request->input('violations', []));

        return response()->json([
            'positive_points' => $result['positive'],
            'negative_points' => $result['negative'],
            'recovery_points' => $result['recovery'],
            'net_score'       => $result['net'],
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

            // ── 2. Calculate & apply points using the direct calculator ───────
            $selectedViolations = $request->input('violations', []);
            $pointsEarned = $this->calculator->calculateAndApply($log, $selectedViolations);

            // ── 3. Reload the log so we have fresh calculated values ──────────
            $log->refresh();

            // ── 4. Run escalation checks ──────────────────────────────────────
            $executive->refresh();
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
}
