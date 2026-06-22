<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Executive;
use Carbon\Carbon;

class PointEngineService
{
    public function __construct(protected DynamicRuleEngineService $engine)
    {
    }

    public function calculateAndApply(DailyLog $log, array $selectedViolations = []): int
    {
        if (!$log->university_id && $log->executive) {
            $log->university_id = $log->executive->university_id;
            $log->save();
        }

        return $this->engine->calculateAndApply($log, $selectedViolations);
    }

    public function preview(DailyLog $log, array $selectedViolations = []): array
    {
        if (!$log->university_id && $log->executive) {
            $log->university_id = $log->executive->university_id;
        }

        return $this->engine->preview($log, $selectedViolations);
    }

    public function buildMonthlySnapshots(Executive $executive, string $period)
    {
        $startDate = Carbon::parse($period . '-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($period . '-01')->endOfMonth()->toDateString();

        $monthlySum = $executive->scoreTransactions()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum(\Illuminate\Support\Facades\DB::raw("CASE WHEN type = 'credit' THEN points ELSE -points END"));

        $rollingStart = Carbon::parse($period . '-01')->subMonths(5)->startOfMonth()->toDateString();
        $rollingSum = $executive->scoreTransactions()
            ->whereBetween('transaction_date', [$rollingStart, $endDate])
            ->sum(\Illuminate\Support\Facades\DB::raw("CASE WHEN type = 'credit' THEN points ELSE -points END"));

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
