<?php

namespace App\Services\Recovery;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * RecoveryHistoryService
 *
 * Single responsibility: query recovery-related transaction history.
 * Used by eligibility and orchestration to determine what has been
 * deducted and how much has already been recovered.
 *
 * RULE: Only transactions with audit_date BEFORE the given date are
 * considered — never today, never future dates.
 */
class RecoveryHistoryService
{
    public function getTotalDeducted(int $executiveId, Carbon $date): int
    {
        return (int) DB::table('point_transactions')
            ->where('executive_id', $executiveId)
            ->where('audit_date', '<', $date->toDateString())
            ->where('type', 'debit')
            ->sum('points');
    }

    /**
     * Sum of all recovery (credit) point transactions before the given date.
     * These are points already credited back in previous recovery sessions.
     */
    public function getTotalRecovered(int $executiveId, Carbon $date): int
    {
        return (int) DB::table('point_transactions')
            ->where('executive_id', $executiveId)
            ->where('audit_date', '<', $date->toDateString())
            ->where('category', 'recovery')
            ->sum('points');
    }

    /**
     * Remaining recoverable balance = total deducted minus total already recovered.
     * Cannot go below zero.
     */
    public function getRemainingBalance(int $executiveId, Carbon $date): int
    {
        $deducted  = $this->getTotalDeducted($executiveId, $date);
        $recovered = $this->getTotalRecovered($executiveId, $date);

        return max(0, $deducted - $recovered);
    }
}
