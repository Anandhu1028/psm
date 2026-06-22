<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Executive;
use App\Models\ScoreTransaction;
use App\Models\Violation;
use Illuminate\Support\Facades\DB;

class DirectPointCalculatorService
{
    public const RECOVERY_CAP = 20;

    /** Points deducted for each selectable violation code (mirrors the JS getViolationPoints map) */
    public const VIOLATION_POINTS = [
        'call_calls_33_39'             => 5,
        'call_calls_27_32'             => 10,
        'call_calls_15_26'             => 15,
        'call_calls_below_15'          => 20,
        'call_zero_calls'              => 25,
        'meeting_zero_meetings'        => 10,
        'meeting_3_day_no_meeting'     => 15,
        'meeting_invalid_documentation'=> 10,
        'lead_no_first_contact'        => 5,
        'lead_no_follow_up'            => 5,
        'lead_wrong_disposition'       => 5,
        'lead_warm_incorrectly_frozen' => 10,
        'lead_invalid_remarks'         => 2,
        'conduct_data_tampering'       => 20,
        'conduct_false_justification'  => 15,
        'conduct_protocol_violation'   => 10,
        'conduct_customer_complaint'   => 15,
    ];

    /** Human-readable names for violation codes */
    public const VIOLATION_NAMES = [
        'call_calls_33_39'             => 'Connected Calls 33–39',
        'call_calls_27_32'             => 'Connected Calls 27–32',
        'call_calls_15_26'             => 'Connected Calls 15–26',
        'call_calls_below_15'          => 'Connected Calls Below 15',
        'call_zero_calls'              => 'Zero Calls',
        'meeting_zero_meetings'        => 'Zero Meetings',
        'meeting_3_day_no_meeting'     => '3-Day No Meeting Streak',
        'meeting_invalid_documentation'=> 'Invalid Meeting Documentation',
        'lead_no_first_contact'        => 'No First Contact',
        'lead_no_follow_up'            => 'No Follow-up',
        'lead_wrong_disposition'       => 'Wrong CRM Disposition',
        'lead_warm_incorrectly_frozen' => 'Warm Lead Incorrectly Frozen',
        'lead_invalid_remarks'         => 'Invalid Remarks',
        'conduct_data_tampering'       => 'Data Tampering',
        'conduct_false_justification'  => 'False Justification',
        'conduct_protocol_violation'   => 'Communication Protocol Violation',
        'conduct_customer_complaint'   => 'Verified Customer Complaint',
    ];

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Preview the score breakdown without touching the database.
     */
    public function preview(DailyLog $log, array $selectedViolations = []): array
    {
        return $this->calculate($log, $selectedViolations);
    }

    /**
     * Calculate points AND persist them to the database inside a transaction.
     *
     * @return int  The net score for this daily log.
     */
    public function calculateAndApply(DailyLog $log, array $selectedViolations = []): int
    {
        return DB::transaction(function () use ($log, $selectedViolations) {

            // 1. Lock the executive row for this transaction
            $executive = Executive::lockForUpdate()->findOrFail($log->executive_id);

            // 2. Revert any existing score transactions tied to this log
            $this->revertExisting($log, $executive);

            // 3. Remove old violations for this log entry
            Violation::where('daily_log_id', $log->id)->delete();

            // 4. Calculate fresh scores
            $result = $this->calculate($log, $selectedViolations);

            // 5. Persist score breakdown back onto the DailyLog
            $log->positive_points  = $result['positive'];
            $log->negative_points  = $result['negative'];
            $log->recovery_points  = $result['recovery'];
            $log->calculated_score = $result['net'];
            $log->kpi_status       = $result['kpi_status'];
            $log->violation_status = $result['negative'] > 0 ? 'active' : 'none';
            $log->conduct_violation = collect($selectedViolations)
                ->contains(fn ($k) => str_starts_with($k, 'conduct_'));
            $log->save();

            // 6. Apply net score to the executive cumulative score
            $newScore = (int) $executive->current_score + $result['net'];
            $oldTier  = $executive->current_tier;
            $newTier  = app(TierService::class)->determineTier($executive, $newScore);

            $executive->current_score = $newScore;
            $executive->current_tier  = $newTier;
            $executive->save();

            // 7. Record tier change if it happened
            if ($oldTier !== $newTier) {
                $executive->tierHistories()->create([
                    'old_tier'      => $oldTier,
                    'new_tier'      => $newTier,
                    'change_reason' => "Daily score applied: {$result['net']} pts. Score went from " .
                                       ((int) $executive->current_score - $result['net']) . " to {$newScore}.",
                    'changed_at'    => now(),
                ]);
            }

            // 8. Create a consolidated score transaction (one row per daily log)
            $net = $result['net'];
            $executive->scoreTransactions()->create([
                'daily_log_id'    => $log->id,
                'rule_id'         => null,
                'type'            => $net >= 0 ? 'credit' : 'debit',
                'points'          => abs($net),
                'running_total'   => $newScore,
                'description'     => "Daily Score: +{$result['positive']} pos, -{$result['negative']} viol, +{$result['recovery']} recovery",
                'transaction_date'=> $log->date->toDateString(),
            ]);

            // 9. Create individual Violation records for every selected violation
            foreach ($selectedViolations as $vKey) {
                $pts = self::VIOLATION_POINTS[$vKey] ?? 0;
                if ($pts <= 0) {
                    continue;
                }

                Violation::create([
                    'university_id'   => $executive->university_id,
                    'executive_id'    => $executive->id,
                    'daily_log_id'    => $log->id,
                    'violation_type'  => $this->getViolationType($vKey),
                    'violation_subtype' => $vKey,
                    'points_deducted' => $pts,
                    'description'     => self::VIOLATION_NAMES[$vKey] ?? $vKey,
                    'status'          => 'active',
                    'date_committed'  => $log->date,
                    'created_by'      => $log->created_by ?? auth()->id() ?? 1,
                ]);
            }

            return $result['net'];
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Pure calculation — no DB writes
    // ─────────────────────────────────────────────────────────────────────────

    public function calculate(DailyLog $log, array $selectedViolations = []): array
    {
        $calls    = (int)  $log->connected_calls;
        $arranged = (int)  $log->meetings_arranged;
        $attended = (int)  $log->meetings_attended;
        $c45      = (bool) $log->first_contact_within_45_min;
        $followup = (bool) $log->all_leads_followed_up;
        $disp     = (bool) $log->crm_disposition_correct;
        $warm     = (bool) $log->warm_lead_converted;

        // ── Positive points ──────────────────────────────────────────────────
        $callPts = match (true) {
            $calls >= 65 => 8,
            $calls >= 50 => 6,
            $calls >= 40 => 4,
            default      => 0,
        };

        $arrangedPts = match (true) {
            $arranged >= 4 => 8,
            $arranged >= 2 => 5,
            $arranged === 1 => 3,
            default        => 0,
        };

        $attendedPts  = $attended * 4;
        $c45Pts       = $c45      ? 2 : 0;
        $followupPts  = $followup ? 2 : 0;
        $dispPts      = $disp     ? 2 : 0;
        $warmPts      = $warm     ? 5 : 0;

        $positive = $callPts + $arrangedPts + $attendedPts + $c45Pts + $followupPts + $dispPts + $warmPts;

        // ── Negative points (selected violations) ────────────────────────────
        $negative = 0;
        foreach ($selectedViolations as $vKey) {
            $negative += self::VIOLATION_POINTS[$vKey] ?? 0;
        }

        // ── Recovery points (capped at RECOVERY_CAP) ─────────────────────────
        $recovery = 0;
        if ($calls >= 65)   $recovery += 6;
        if ($attended >= 2) $recovery += 6;
        if ($c45 && $followup && $disp && empty($selectedViolations)) {
            $recovery += 8;
        }
        $recovery = min($recovery, self::RECOVERY_CAP);

        // ── KPI status ───────────────────────────────────────────────────────
        $kpiMet    = (int)$c45 + (int)$followup + (int)$disp + (int)$warm;
        $kpiStatus = match (true) {
            $kpiMet === 4 => 'passed',
            $kpiMet  >  0 => 'partial',
            default       => 'failed',
        };

        return [
            'positive'    => $positive,
            'negative'    => $negative,
            'recovery'    => $recovery,
            'net'         => $positive - $negative + $recovery,
            'kpi_status'  => $kpiStatus,
            'kpi_met'     => $kpiMet,
            'breakdown'   => compact(
                'callPts', 'arrangedPts', 'attendedPts',
                'c45Pts', 'followupPts', 'dispPts', 'warmPts'
            ),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function revertExisting(DailyLog $log, Executive $executive): void
    {
        $old = ScoreTransaction::where('daily_log_id', $log->id)->get();

        foreach ($old as $tx) {
            // Undo the previously applied credit/debit
            $executive->current_score += ($tx->type === 'credit') ? -$tx->points : $tx->points;
        }

        if ($old->isNotEmpty()) {
            $executive->save();
        }

        ScoreTransaction::where('daily_log_id', $log->id)->delete();
    }

    private function getViolationType(string $key): string
    {
        $prefixes = ['call_' => 'call', 'meeting_' => 'meeting', 'lead_' => 'lead', 'conduct_' => 'conduct'];
        foreach ($prefixes as $prefix => $type) {
            if (str_starts_with($key, $prefix)) {
                return $type;
            }
        }
        return 'conduct';
    }
}
