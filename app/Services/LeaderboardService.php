<?php

namespace App\Services;

use App\Models\DailyAudit;
use App\Models\Executive;
use App\Models\Leaderboard;
use Illuminate\Support\Facades\DB;

/**
 * Regenerates leaderboard snapshots for a company/month/year.
 */
class LeaderboardService
{
    /**
     * Refresh the leaderboard snapshot for the given company/year/month.
     * Called after every audit submission via the AuditSubmitted event.
     */
    public function refresh(int $companyId, int $year, int $month): void
    {
        // Get all executives for the company with their monthly scores
        $executives = DB::table('executives')
            ->leftJoin('monthly_scores', function ($join) use ($companyId, $year, $month) {
                $join->on('monthly_scores.executive_id', '=', 'executives.id')
                     ->where('monthly_scores.company_id', $companyId)
                     ->where('monthly_scores.year', $year)
                     ->where('monthly_scores.month', $month);
            })
            ->where('executives.company_id', $companyId)
            ->where('executives.status', '!=', 'inactive')
            ->whereNull('executives.deleted_at')
            ->select(
                'executives.id',
                'executives.current_score',
                'executives.current_tier',
                DB::raw('COALESCE(monthly_scores.net_score, 0) as monthly_score')
            )
            ->orderByDesc('monthly_score')
            ->orderByDesc('executives.current_score')
            ->get();

        $rank = 1;

        foreach ($executives as $exec) {
            // Get previous rank to determine trend
            $previous = Leaderboard::where('company_id', $companyId)
                ->where('executive_id', $exec->id)
                ->where('year', $year)
                ->where('month', $month)
                ->value('rank');

            $trend = 'stable';
            if ($previous !== null) {
                $trend = $rank < $previous ? 'up' : ($rank > $previous ? 'down' : 'stable');
            }

            Leaderboard::updateOrCreate(
                [
                    'company_id'   => $companyId,
                    'executive_id' => $exec->id,
                    'year'         => $year,
                    'month'        => $month,
                ],
                [
                    'rank'          => $rank,
                    'current_score' => $exec->current_score,
                    'monthly_score' => $exec->monthly_score,
                    'tier'          => $exec->current_tier,
                    'trend'         => $trend,
                    'previous_rank' => $previous,
                ]
            );

            $rank++;
        }
    }
}
