<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\ScoreRule;
use App\Models\Executive;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PointEngineService
{
    /**
     * Evaluate daily performance log against score rules and apply transactions.
     */
    public function calculateAndApply(DailyLog $log): int
    {
        return DB::transaction(function () use ($log) {
            $executive = $log->executive;
            $rules = ScoreRule::where('is_active', true)
                ->where('university_id', $executive->university_id)
                ->get()
                ->keyBy('rule_key');

            $totalPoints = 0;
            $breakdown = [];

            // 1. Call Points
            $calls = $log->connected_calls;
            if ($calls >= 65 && isset($rules['calls_65_plus'])) {
                $points = (int) $rules['calls_65_plus']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['calls_65_plus']->id, 'points' => $points, 'desc' => 'Calls volume: 65+'];
            } elseif ($calls >= 50 && $calls <= 64 && isset($rules['calls_50_64'])) {
                $points = (int) $rules['calls_50_64']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['calls_50_64']->id, 'points' => $points, 'desc' => 'Calls volume: 50-64'];
            } elseif ($calls >= 40 && $calls <= 49 && isset($rules['calls_40_49'])) {
                $points = (int) $rules['calls_40_49']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['calls_40_49']->id, 'points' => $points, 'desc' => 'Calls volume: 40-49'];
            }

            // 2. Meeting Arranged Points
            $arranged = $log->meetings_arranged;
            if ($arranged >= 4 && isset($rules['meetings_arranged_4_plus'])) {
                $points = (int) $rules['meetings_arranged_4_plus']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['meetings_arranged_4_plus']->id, 'points' => $points, 'desc' => 'Meetings arranged: 4+'];
            } elseif ($arranged >= 2 && $arranged <= 3 && isset($rules['meetings_arranged_2_3'])) {
                $points = (int) $rules['meetings_arranged_2_3']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['meetings_arranged_2_3']->id, 'points' => $points, 'desc' => 'Meetings arranged: 2-3'];
            } elseif ($arranged == 1 && isset($rules['meetings_arranged_1'])) {
                $points = (int) $rules['meetings_arranged_1']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['meetings_arranged_1']->id, 'points' => $points, 'desc' => 'Meetings arranged: 1'];
            }

            // 3. Attendance Bonus Points
            $attended = $log->meetings_attended;
            if ($attended > 0 && isset($rules['attendance_bonus'])) {
                $unitVal = (int) $rules['attendance_bonus']->rule_value;
                $points = $attended * $unitVal;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['attendance_bonus']->id, 'points' => $points, 'desc' => "Meeting Attended Bonus ({$attended} attended)"];
            }

            // 4. Lead Management KPIs
            if ($log->first_contact_within_45_min && isset($rules['first_contact_45_min'])) {
                $points = (int) $rules['first_contact_45_min']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['first_contact_45_min']->id, 'points' => $points, 'desc' => 'First contact under 45 minutes KPI met'];
            }
            if ($log->all_leads_followed_up && isset($rules['same_day_followup'])) {
                $points = (int) $rules['same_day_followup']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['same_day_followup']->id, 'points' => $points, 'desc' => 'Same day follow up KPI met'];
            }
            if ($log->crm_disposition_correct && isset($rules['correct_crm_disposition'])) {
                $points = (int) $rules['correct_crm_disposition']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['correct_crm_disposition']->id, 'points' => $points, 'desc' => 'Correct CRM disposition logged KPI met'];
            }

            // 5. Conversion Bonus
            if ($log->warm_lead_converted && isset($rules['warm_lead_converted'])) {
                $points = (int) $rules['warm_lead_converted']->rule_value;
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['warm_lead_converted']->id, 'points' => $points, 'desc' => 'Warm lead converted KPI met'];
            }

            // 6. Conduct Violation Deductions (Immediate)
            if ($log->conduct_violation && isset($rules['conduct_violation'])) {
                $points = (int) $rules['conduct_violation']->rule_value; // negative
                $totalPoints += $points;
                $breakdown[] = ['rule_id' => $rules['conduct_violation']->id, 'points' => $points, 'desc' => 'Conduct violation penalty'];
                
                // Track direct violation
                $executive->violations()->updateOrCreate(
                    ['daily_log_id' => $log->id, 'violation_type' => 'conduct'],
                    [
                        'points_deducted' => abs($points),
                        'status' => 'active',
                        'date_committed' => $log->date,
                        'created_by' => $log->created_by,
                    ]
                );
            } else {
                // Delete if unchecked during a edit
                $executive->violations()->where('daily_log_id', $log->id)->where('violation_type', 'conduct')->delete();
            }

            // Update Log
            $log->calculated_score = $totalPoints;
            $log->save();

            // Apply points changes to Executive score & trigger ledger
            foreach ($breakdown as $item) {
                $executive->updateScoreAndTier(
                    $item['points'],
                    $item['desc'],
                    $log->id,
                    $item['rule_id']
                );
            }

            return $totalPoints;
        });
    }

    /**
     * Recalculate historical scores for rolling statistics.
     */
    public function buildMonthlySnapshots(Executive $executive, string $period)
    {
        $startDate = Carbon::parse($period . '-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($period . '-01')->endOfMonth()->toDateString();

        $monthlySum = $executive->scoreTransactions()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('points');

        // Build rolling 6 months
        $rollingStart = Carbon::parse($period . '-01')->subMonths(5)->startOfMonth()->toDateString();
        $rollingSum = $executive->scoreTransactions()
            ->whereBetween('transaction_date', [$rollingStart, $endDate])
            ->sum('points');

        $executive->scoreHistories()->updateOrCreate(
            ['period' => $period],
            [
                'daily_points_sum' => $monthlySum,
                'monthly_score' => $monthlySum,
                'rolling_6_month_score' => $rollingSum,
            ]
        );
    }
}
