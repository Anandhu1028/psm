<?php

namespace App\Services;

use App\Models\Executive;
use App\Models\DailyAudit;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MonthlyPerformanceRankingService
{
    /**
     * Calculate monthly admissions and achievement for all executives filtered by optional company/zone.
     * Returns a collection of items with achievement, eligibility, and rank metadata for eligible executives.
     */
    public function calculate(int $month, int $year, ?int $companyId = null, ?int $zoneId = null): Collection
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $executives = Executive::with(['company', 'zone'])
            ->where('status', 'active')
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($zoneId, fn($q) => $q->where('zone_id', $zoneId))
            ->get();

        $results = $executives->map(function (Executive $exec) use ($start, $end) {
            $admissions = DailyAudit::where('executive_id', $exec->id)
                ->whereBetween('audit_date', [$start->toDateString(), $end->toDateString()])
                ->sum('admissions_today');

            $target = (int) $exec->monthly_admission_target;
            $remaining = max(0, $target - $admissions);
            $achievement = $target > 0 ? round(($admissions / $target) * 100, 2) : 0.00;

            return [
                'executive'   => $exec,
                'admissions'  => (int) $admissions,
                'target'      => $target,
                'remaining'   => $remaining,
                'achievement' => $achievement,
                'eligible'    => $achievement >= 80.0,
                'rank'        => null,
            ];
        });

        $eligible = $results->filter(fn($r) => $r['eligible'])
            ->sortByDesc(fn($r) => $r['achievement'])
            ->values()
            ->map(function ($item, $index) {
                $item['rank'] = $index + 1;
                return $item;
            });

        $rankMap = $eligible->pluck('rank', 'executive.id')->all();

        return $results->sortByDesc(fn($r) => $r['achievement'])
            ->values()
            ->map(function ($item) use ($rankMap) {
                $item['rank'] = $item['eligible'] ? ($rankMap[$item['executive']->id] ?? null) : null;
                return $item;
            });
    }
}
