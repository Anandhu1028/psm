<?php

namespace App\Services\Recovery;

use App\Models\DailyAudit;
use App\Models\Executive;
use App\Models\PointTransaction;
use Illuminate\Support\Facades\Auth;

/**
 * RecoveryTransactionService
 *
 * Single responsibility: persist recovery point transactions with
 * full duplicate protection.
 *
 * Core guarantees:
 *  - Each (executive_id, daily_audit_id, rule_code) recovery row is unique.
 *  - If a matching transaction already exists, insertion is skipped.
 *  - Original negative/debit transactions are NEVER touched.
 *  - Recovery is always stored as category='recovery', type='credit'.
 *  - The engine is fully idempotent: running 1× or 100× = same result.
 */
class RecoveryTransactionService
{
    /**
     * Persist all recovery breakdown items for an audit.
     * Skips any item whose transaction already exists (duplicate guard).
     *
     * @param  DailyAudit  $audit
     * @param  Executive   $executive
     * @param  array       $breakdown   Items from RecoveryCalculationService
     * @param  int         $runningTotal  Running score total before recovery items
     * @return int  Running total after all persisted recovery items
     */
    public function persist(
        DailyAudit $audit,
        Executive  $executive,
        array      $breakdown,
        int        $runningTotal
    ): int {
        foreach ($breakdown as $item) {
            if (($item['points'] ?? 0) <= 0) {
                continue;
            }

            $exists = PointTransaction::where('executive_id', $executive->id)
                ->where('daily_audit_id', $audit->id)
                ->where('rule_code', $item['rule_code'])
                ->where('category', 'recovery')
                ->exists();

            if ($exists) {
                continue;
            }

            $runningTotal += (int) $item['points'];

            PointTransaction::create([
                'company_id'     => $executive->company_id,
                'executive_id'   => $executive->id,
                'daily_audit_id' => $audit->id,
                'rule_id'        => $item['rule_id'] ?? null,
                'audit_date'     => $audit->audit_date,
                'category'       => 'recovery',
                'rule_code'      => $item['rule_code'] ?? null,
                'rule_name'      => $item['rule_name'] ?? $item['message'],
                'points'         => abs((int) $item['points']),
                'type'           => 'credit',
                'running_total'  => $runningTotal,
                'created_by'     => Auth::id(),
            ]);
        }

        return $runningTotal;
    }
}
