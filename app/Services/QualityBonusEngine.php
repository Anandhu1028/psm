<?php

namespace App\Services;

use App\Models\PointTransaction;
use Carbon\Carbon;

class QualityBonusEngine
{
    protected int $points = 15;

    /**
     * Award quality bonuses for a given month/year based on ranking results.
     * Ensures idempotency by checking existing `quality_bonus` transactions for the same month.
     *
     * @param array $rankedItems Array of items with keys: executive, admissions, target, achievement, rank
     * @param int $month
     * @param int $year
     * @return array created transactions
     */
    public function award(array $rankedItems, int $month, int $year): array
    {
        $created = [];
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        foreach ($rankedItems as $item) {
            if (($item['rank'] ?? 0) < 1 || ($item['rank'] ?? 0) > 3) {
                continue;
            }

            $exec = $item['executive'];

            // Check for existing quality bonus for this executive for this month
            $exists = PointTransaction::where('executive_id', $exec->id)
                ->where('category', 'quality_bonus')
                ->whereYear('audit_date', $year)
                ->whereMonth('audit_date', $month)
                ->exists();

            if ($exists) {
                continue;
            }

            $tx = PointTransaction::create([
                'company_id'   => $exec->company_id,
                'executive_id' => $exec->id,
                'audit_date'   => $monthEnd,
                'category'     => 'quality_bonus',
                'rule_code'    => 'monthly_conversion_top3',
                'rule_name'    => 'Top 3 Executive in Monthly Conversion Rate',
                'points'       => $this->points,
                'type'         => 'credit',
                'running_total'=> $exec->current_score + $this->points,
                'created_by'   => null,
            ]);

            // update executive scores to reflect awarded bonus
            $exec->increment('current_score', $this->points);
            $exec->increment('monthly_score', $this->points);

            $created[] = $tx;
        }

        return $created;
    }
}
