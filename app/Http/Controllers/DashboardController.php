<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Executive;
use App\Models\DailyLog;
use App\Models\Meeting;
use App\Models\Escalation;
use App\Models\PipRecord;
use App\Models\Zone;
use App\Models\Violation;
use App\Models\University;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function crmDashboard()
    {
        return view('dashboards.crm');
    }

    public function switchActiveUniversity(Request $request)
    {
        $uniId = $request->input('university_id');
        if ($uniId) {
            $university = University::findOrFail($uniId);
            session(['active_university_id' => $university->id]);
        } else {
            session()->forget('active_university_id');
        }
        return back()->with('success', 'Active university context switched.');
    }

    public function universityDashboard(University $university)
    {
        $executives = Executive::where('university_id', $university->id)->get();
        $totalExecs = $executives->count();
        
        // Active score is average score of active executives
        $activeScore = round($executives->where('status', 'active')->avg('current_score') ?? 0, 1);
        
        $topPerformer = $executives->sortByDesc('current_score')->first();
        $lowestPerformer = $executives->sortBy('current_score')->first();
        
        $tierCounts = $executives->groupBy('current_tier');
        $tiers = [
            'platinum' => $tierCounts->get('platinum')?->count() ?? 0,
            'gold' => $tierCounts->get('gold')?->count() ?? 0,
            'silver' => $tierCounts->get('silver')?->count() ?? 0,
            'bronze' => $tierCounts->get('bronze')?->count() ?? 0,
            'review_zone' => $tierCounts->get('review_zone')?->count() ?? 0,
        ];
        
        return view('admin.universities.dashboard', compact('university', 'totalExecs', 'activeScore', 'topPerformer', 'lowestPerformer', 'tiers'));
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('CRO')) {
            return $this->croDashboard();
        } elseif ($user->hasRole('Zonal Manager') || $user->hasRole('AGM')) {
            return $this->managerDashboard();
        } elseif ($user->hasRole('GM')) {
            return $this->gmDashboard();
        } elseif ($user->hasRole('Chairman')) {
            return $this->chairmanDashboard();
        } elseif ($user->hasRole('Developer')) {
            return redirect()->route('admin.rules.index');
        }

        abort(403, 'Unauthorized role access.');
    }

    protected function croDashboard()
    {
        $today = Carbon::today()->toDateString();
        $activeUniId = session('active_university_id');

        // Cards Metrics
        $totalExecs = Executive::when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))->count();
        $activeExecs = Executive::where('status', 'active')->when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))->count();
        
        $todayCalls = DailyLog::when($activeUniId, function($q) use($activeUniId) {
            $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
        })->where('date', $today)->sum('connected_calls');
        
        $todayMeetingsArranged = DailyLog::when($activeUniId, function($q) use($activeUniId) {
            $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
        })->where('date', $today)->sum('meetings_arranged');
        
        $todayMeetingsAttended = DailyLog::when($activeUniId, function($q) use($activeUniId) {
            $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
        })->where('date', $today)->sum('meetings_attended');
        
        $reviewZoneCount = Executive::where('current_tier', 'review_zone')->when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))->count();
        
        $activePipCount = PipRecord::when($activeUniId, function($q) use($activeUniId) {
            $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
        })->where('status', 'active')->count();

        // Trends Data (Last 7 Days)
        $pastWeek = collect();
        for ($i = 6; $i >= 0; $i--) {
            $pastWeek->put(Carbon::today()->subDays($i)->toDateString(), [
                'calls' => 0,
                'arranged' => 0,
                'attended' => 0,
            ]);
        }

        $logs = DailyLog::where('date', '>=', Carbon::today()->subDays(6)->toDateString())
            ->when($activeUniId, function($q) use($activeUniId) {
                $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
            })
            ->select('date', DB::raw('SUM(connected_calls) as calls'), DB::raw('SUM(meetings_arranged) as arranged'), DB::raw('SUM(meetings_attended) as attended'))
            ->groupBy('date')
            ->get();

        foreach ($logs as $log) {
            $dateStr = $log->date->toDateString();
            if ($pastWeek->has($dateStr)) {
                $pastWeek->put($dateStr, [
                    'calls'    => (int) $log->calls,
                    'arranged' => (int) $log->arranged,
                    'attended' => (int) $log->attended,
                ]);
            }
        }

        // Tier distribution
        $tierCounts = Executive::when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))
            ->select('current_tier', DB::raw('count(*) as count'))
            ->groupBy('current_tier')
            ->pluck('count', 'current_tier')
            ->toArray();

        $tiers = [
            'platinum'    => $tierCounts['platinum']    ?? 0,
            'gold'        => $tierCounts['gold']        ?? 0,
            'silver'      => $tierCounts['silver']      ?? 0,
            'bronze'      => $tierCounts['bronze']      ?? 0,
            'review_zone' => $tierCounts['review_zone'] ?? 0,
        ];

        // Recent performance entries
        $recentLogs = DailyLog::with('executive')
            ->when($activeUniId, function($q) use($activeUniId) {
                $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
            })
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        // ── New widget data ────────────────────────────────────────────────────

        // Today's score breakdown aggregates
        $todayLogs = DailyLog::when($activeUniId, function($q) use($activeUniId) {
                $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
            })
            ->whereDate('date', $today)
            ->get();

        $scoreBreakdown = [
            'positive' => (int) $todayLogs->sum('positive_points'),
            'negative' => (int) $todayLogs->sum('negative_points'),
            'recovery' => (int) $todayLogs->sum('recovery_points'),
            'net'      => (int) $todayLogs->sum('calculated_score'),
            'logs'     => $todayLogs->count(),
        ];

        // KPI compliance % for today
        $logsWithKpi = $todayLogs->count();
        $kpiCompliance = [
            'calls_kpi_pct'    => $logsWithKpi ? round($todayLogs->where('connected_calls', '>=', 40)->count() / $logsWithKpi * 100) : 0,
            'meetings_kpi_pct' => $logsWithKpi ? round($todayLogs->where('meetings_attended', '>=', 1)->count() / $logsWithKpi * 100) : 0,
            'crm_kpi_pct'      => $logsWithKpi ? round($todayLogs->where('crm_disposition_correct', true)->count() / $logsWithKpi * 100) : 0,
            'first_contact_pct'=> $logsWithKpi ? round($todayLogs->where('first_contact_within_45_min', true)->count() / $logsWithKpi * 100) : 0,
        ];

        // Top 5 performers by current score
        $topPerformers = Executive::where('status', 'active')
            ->when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))
            ->orderByDesc('current_score')
            ->take(5)
            ->get();

        // Streak leaders (call streak)
        $streakLeaders = Executive::where('status', 'active')
            ->when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))
            ->orderByDesc('call_streak_count')
            ->take(3)
            ->get();

        // Recovery summary for today
        $recoveryToday = [
            'total_recovery'  => (int) $todayLogs->sum('recovery_points'),
            'execs_recovered' => $todayLogs->where('recovery_points', '>', 0)->count(),
        ];

        // Open escalations count
        $openEscalations = \App\Models\Escalation::where('status', 'open')
            ->when($activeUniId, function($q) use($activeUniId) {
                $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
            })->count();

        return view('dashboards.cro', compact(
            'totalExecs', 'activeExecs', 'todayCalls', 'todayMeetingsArranged',
            'todayMeetingsAttended', 'reviewZoneCount', 'activePipCount',
            'pastWeek', 'tiers', 'recentLogs',
            'scoreBreakdown', 'kpiCompliance', 'topPerformers',
            'streakLeaders', 'recoveryToday', 'openEscalations'
        ));
    }

    protected function managerDashboard()
    {
        $user = Auth::user();
        $activeUniId = session('active_university_id');

        // Fetch zones managed by this user
        $zonesQuery = Zone::query();
        if ($user->hasRole('Zonal Manager')) {
            $zonesQuery->where('manager_id', $user->id);
        }
        $zones = $zonesQuery->get();
        $zoneIds = $zones->pluck('id');

        $executivesQuery = Executive::query();
        if ($user->hasRole('Zonal Manager')) {
            $executivesQuery->whereIn('zone_id', $zoneIds);
        }
        if ($activeUniId) {
            $executivesQuery->where('university_id', $activeUniId);
        }
        $executives = $executivesQuery->get();

        $topPerformers = $executives->sortByDesc('current_score')->take(5);
        $bottomPerformers = $executives->sortBy('current_score')->take(5);

        // Zone comparisons
        $zoneStats = Zone::withAvg(['executives as executives_avg_current_score' => function($q) use($activeUniId) {
            if ($activeUniId) $q->where('university_id', $activeUniId);
        }], 'current_score')->get();

        return view('dashboards.manager', compact('topPerformers', 'bottomPerformers', 'zoneStats', 'zones'));
    }

    protected function gmDashboard()
    {
        $activeUniId = session('active_university_id');

        $topPerformers = Executive::when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))
            ->orderBy('current_score', 'desc')->take(5)->get();
            
        $bottomPerformers = Executive::when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))
            ->orderBy('current_score', 'asc')->take(5)->get();

        $zoneStats = Zone::withAvg(['executives as executives_avg_current_score' => function($q) use($activeUniId) {
            if ($activeUniId) $q->where('university_id', $activeUniId);
        }], 'current_score')->get();

        $activePips = PipRecord::with('executive')
            ->when($activeUniId, function($q) use($activeUniId) {
                $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
            })
            ->where('status', 'active')
            ->get();

        return view('dashboards.gm', compact('topPerformers', 'bottomPerformers', 'zoneStats', 'activePips'));
    }

    protected function chairmanDashboard()
    {
        $activeUniId = session('active_university_id');

        $promotionEligible = Executive::where('current_tier', 'platinum')
            ->when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))->get();
            
        $reviewZone = Executive::where('current_tier', 'review_zone')
            ->when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))->get();
            
        $escalations = Escalation::with('executive')->where('status', 'open')
            ->when($activeUniId, function($q) use($activeUniId) {
                $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
            })->get();

        // High level numbers
        $totalActive = Executive::where('status', 'active')
            ->when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))->count();
            
        $pipCount = PipRecord::where('status', 'active')
            ->when($activeUniId, function($q) use($activeUniId) {
                $q->whereHas('executive', fn($eq) => $eq->where('university_id', $activeUniId));
            })->count();

        return view('dashboards.chairman', compact('promotionEligible', 'reviewZone', 'escalations', 'totalActive', 'pipCount'));
    }

    protected function adminDashboard()
    {
        $activeUniId = session('active_university_id');

        $totalUsers = \App\Models\User::count();
        $rulesCount = \App\Models\ScoreRule::when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))->count();
        $totalExecs = Executive::when($activeUniId, fn($q) => $q->where('university_id', $activeUniId))->count();
        $recentActivities = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->take(10)->get();

        return view('dashboards.admin', compact('totalUsers', 'rulesCount', 'totalExecs', 'recentActivities'));
    }
}
