<?php

namespace App\Services;

use App\Models\DailyAudit;
use App\Models\Executive;
use App\Models\MonthlyScore;
use Illuminate\Support\Facades\DB;

/**
 * Manages current_score and monthly_score on executives.
 * Score is ALWAYS the sum of all transactions — never manually edited.
 */
class ScoreEngineService
{
    /**
     * Recalculate and update the executive's cumulative current_score
     * from the sum of all point_transactions.
     */
    public function recalculateCurrentScore(Executive $executive): int
    {
        $score = DB::table('point_transactions')
            ->where('executive_id', $executive->id)
            ->selectRaw("SUM(CASE WHEN type = 'credit' THEN points ELSE -points END) as total")
            ->value('total') ?? 0;

        $executive->current_score = (int) $score;
        $executive->save();

        return (int) $score;
    }

    /**
     * Update the monthly_score aggregate for the executive for a given month/year.
     */
    public function updateMonthlyScore(Executive $executive, DailyAudit $audit): void
    {
        $year  = $audit->audit_date->year;
        $month = $audit->audit_date->month;

        $aggregates = DB::table('daily_audits')
            ->where('executive_id', $executive->id)
            ->whereYear('audit_date', $year)
            ->whereMonth('audit_date', $month)
            ->selectRaw('
                SUM(positive_points) as positive,
                SUM(negative_points) as negative,
                SUM(recovery_points) as recovery,
                SUM(final_score)     as net,
                COUNT(*)             as audit_count
            ')
            ->first();

        MonthlyScore::updateOrCreate(
            [
                'executive_id' => $executive->id,
                'company_id'   => $executive->company_id,
                'year'         => $year,
                'month'        => $month,
            ],
            [
                'positive_points' => (int) ($aggregates->positive    ?? 0),
                'negative_points' => (int) ($aggregates->negative    ?? 0),
                'recovery_points' => (int) ($aggregates->recovery    ?? 0),
                'net_score'       => (int) ($aggregates->net         ?? 0),
                'audit_count'     => (int) ($aggregates->audit_count ?? 0),
            ]
        );

        // Sync monthly_score on the executive for quick access
        $executive->monthly_score = (int) ($aggregates->net ?? 0);
        $executive->save();
    }
}
