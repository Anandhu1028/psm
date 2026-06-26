<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DailyAudit;
use App\Models\Executive;
use App\Models\PointTransaction;
use App\Repositories\Contracts\DailyAuditRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private DailyAuditRepositoryInterface $audits,
    ) {}

    public function index()
    {
        $companies       = Company::withCount(['executives' => fn($q) => $q->where('status', '!=', 'inactive')])->get();
        $totalExecutives = Executive::where('status', '!=', 'inactive')->count();
        $todayAudits     = $this->audits->todayCount();
        $todayPoints     = $this->audits->todayPointsSummary();

        // Top & bottom performers today
        $todayAuditsList = DailyAudit::with(['executive.company', 'executive.zone'])
            ->whereDate('audit_date', today())
            ->get();

        $topPerformer    = $todayAuditsList->sortByDesc('final_score')->first();
        $lowestPerformer = $todayAuditsList->sortBy('final_score')->first();

        // Recent audits for the activity feed
        $recentAudits = $this->audits->getRecentForDashboard(8);

        // Monthly performance chart (last 6 months)
        $monthlyChart = $this->buildMonthlyChart();

        // Zone performance summary
        $zonePerformance = $this->buildZonePerformance();

        // Company leaderboard snapshot (current month)
        $companyLeaderboards = $this->buildCompanyLeaderboards();

        // Recent violations (negative transactions)
        $recentViolations = PointTransaction::with(['executive.company', 'executive.zone'])
            ->where('type', 'debit')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'companies', 'totalExecutives', 'todayAudits', 'todayPoints',
            'topPerformer', 'lowestPerformer', 'recentAudits',
            'monthlyChart', 'zonePerformance', 'companyLeaderboards',
            'recentViolations',
        ));
    }

    private function buildMonthlyChart(): array
    {
        $months = collect(range(5, 0))->map(function ($ago) {
            $date  = now()->subMonths($ago);
            $year  = $date->year;
            $month = $date->month;

            $data = DB::table('daily_audits')
                ->whereYear('audit_date', $year)
                ->whereMonth('audit_date', $month)
                ->selectRaw('SUM(positive_points) as pos, SUM(negative_points) as neg, SUM(recovery_points) as rec')
                ->first();

            return [
                'label'    => $date->format('M Y'),
                'positive' => (int) ($data->pos ?? 0),
                'negative' => (int) ($data->neg ?? 0),
                'recovery' => (int) ($data->rec ?? 0),
            ];
        });

        return $months->values()->all();
    }

    private function buildZonePerformance(): array
    {
        return DB::table('executives')
            ->join('zones', 'executives.zone_id', '=', 'zones.id')
            ->join('companies', 'executives.company_id', '=', 'companies.id')
            ->where('executives.status', '!=', 'inactive')
            ->whereNull('executives.deleted_at')
            ->selectRaw('
                zones.id as zone_id,
                zones.name as zone_name,
                companies.name as company_name,
                COUNT(executives.id) as executive_count,
                AVG(executives.current_score) as avg_score,
                SUM(executives.current_score) as total_score
            ')
            ->groupBy('zones.id', 'zones.name', 'companies.name')
            ->orderByDesc('avg_score')
            ->get()
            ->toArray();
    }

    private function buildCompanyLeaderboards(): array
    {
        $result = [];
        $companies = Company::where('status', 'active')->get();

        foreach ($companies as $company) {
            $result[$company->id] = [
                'company'  => $company,
                'top5'     => Executive::with('zone')
                    ->where('company_id', $company->id)
                    ->where('status', '!=', 'inactive')
                    ->whereNull('deleted_at')
                    ->orderByDesc('current_score')
                    ->limit(5)
                    ->get(),
            ];
        }

        return $result;
    }
}
