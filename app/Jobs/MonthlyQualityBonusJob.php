<?php

namespace App\Jobs;

use App\Services\MonthlyPerformanceRankingService;
use App\Services\QualityBonusEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class MonthlyQualityBonusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $month = null, public ?int $year = null, public ?int $companyId = null, public ?int $zoneId = null)
    {
        // noop
    }

    public function handle(MonthlyPerformanceRankingService $ranking, QualityBonusEngine $engine): void
    {
        $now = Carbon::now();
        $month = $this->month ?? $now->subMonth()->month;
        $year  = $this->year  ?? $now->subMonth()->year;

        $rows = $ranking->calculate($month, $year, $this->companyId, $this->zoneId)
            ->values()
            ->toArray();

        $eligible = array_values(array_filter($rows, fn($row) => $row['eligible'] ?? false));
        $top = $eligible ? array_slice($eligible, 0, 3) : array_slice($rows, 0, 3);

        $engine->award($top, $month, $year);
    }
}
