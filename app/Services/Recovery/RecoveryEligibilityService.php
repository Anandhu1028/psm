<?php

namespace App\Services\Recovery;

use App\Models\DailyAudit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * RecoveryEligibilityService
 *
 * Single responsibility: determine whether an executive is eligible
 * to receive recovery points on a given date per the official SOP.
 *
 * canReceiveRecoveryPoints($executiveId, $date) verifies:
 *  1. Executive has at least one previous working day's eligible negative deduction.
 *  2. The deduction still has remaining recoverable balance.
 *  3. Today has not already received recovery.
 *  4. Executive is active.
 *  5. Date is a valid working day.
 */
class RecoveryEligibilityService
{
    public function __construct(
        private RecoveryHistoryService $history,
    ) {}

    /**
     * Primary eligibility gate.
     *
     * Returns true ONLY when ALL 5 SOP conditions are satisfied.
     * The executive must have a prior unrecovered debit before the current date,
     * while the current audit date itself must be a valid working day.
     */
    public function canReceiveRecoveryPoints(int $executiveId, $date, ?int $excludeAuditId = null): bool
    {
        $dateCarbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $dateStr = $dateCarbon->toDateString();

        // 1. Date is a valid working day (exclude Sundays)
        if ($dateCarbon->isSunday()) {
            return false;
        }

        // 2. Executive is active
        $executive = DB::table('executives')->where('id', $executiveId)->first();
        if (!$executive || $executive->status !== 'active') {
            return false;
        }

        // 3. Executive has at least one previous working day's eligible negative deduction
        $totalDeducted = $this->history->getTotalDeducted($executiveId, $dateCarbon);
        if ($totalDeducted <= 0) {
            return false;
        }

        // 4. The deduction still has remaining recoverable balance
        $remaining = $this->history->getRemainingBalance($executiveId, $dateCarbon);
        if ($remaining <= 0) {
            return false;
        }

        // 5. Today has not already received recovery
        $query = DB::table('point_transactions')
            ->where('executive_id', $executiveId)
            ->where('audit_date', $dateStr)
            ->where('category', 'recovery');

        if ($excludeAuditId !== null) {
            $query->where('daily_audit_id', '!=', $excludeAuditId);
        }

        $alreadyReceived = $query->exists();
        if ($alreadyReceived) {
            return false;
        }

        return true;
    }

    /**
     * Validate today's mandatory KPI requirements (40 calls + 1 meeting).
     * Used ONLY by Perfect Compliance recovery rule, not globally.
     */
    public function mandatoryKpiPassed(DailyAudit $audit): bool
    {
        return $audit->connected_calls >= 40
            && $audit->confirmed_meetings >= 1;
    }
}
