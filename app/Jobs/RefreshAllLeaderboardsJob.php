<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\LeaderboardService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Refreshes leaderboards for all active companies.
 * Can be scheduled monthly via artisan schedule.
 */
class RefreshAllLeaderboardsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        private ?int $year  = null,
        private ?int $month = null,
    ) {
        $this->year  = $year  ?? now()->year;
        $this->month = $month ?? now()->month;
    }

    public function handle(LeaderboardService $leaderboard): void
    {
        Company::where('status', 'active')->each(function (Company $company) use ($leaderboard) {
            $leaderboard->refresh($company->id, $this->year, $this->month);
        });
    }
}
