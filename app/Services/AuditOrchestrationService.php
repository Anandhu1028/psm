<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\DailyAudit;
use App\Models\Executive;
use App\Models\PointTransaction;
use App\Models\TierHistory;
use App\Repositories\Contracts\RuleRepositoryInterface;
use App\Services\Recovery\RecoveryCalculationService;
use App\Services\Recovery\RecoveryEligibilityService;
use App\Services\Recovery\RecoveryHistoryService;
use App\Services\Recovery\RecoveryTransactionService;
use App\Events\AuditSubmitted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * AuditOrchestrationService
 *
 * Single entry point for the entire audit workflow.
 * Controllers call this — never any other service directly.
 */
class AuditOrchestrationService
{
    public function __construct(
        private RuleRepositoryInterface    $rules,
        private StrategyResolver           $strategyResolver,
        private TierEngineService          $tierEngine,
        private ScoreEngineService         $scoreEngine,
        private LeaderboardService         $leaderboard,
        // ── Recovery engine services ───────────────────────────────────────
        private RecoveryEligibilityService  $recoveryEligibility,
        private RecoveryCalculationService  $recoveryCalculation,
        private RecoveryTransactionService  $recoveryTransaction,
        private RecoveryHistoryService      $recoveryHistory,
    ) {}

    // ── Public API ─────────────────────────────────────────────────────────────

    /**
     * Preview score without writing to DB.
     * Used for the live score preview on the create form.
     */
    public function preview(DailyAudit $audit, array $selectedViolations = []): array
    {
        $company  = $audit->executive->company;
        $strategy = $this->strategyResolver->resolve($company);
        $context  = $strategy->buildContext($audit);
        $context['selected_violations'] = $selectedViolations;

        $kpiRules      = $this->rules->kpiRules($company->id);
        $positiveRules = $this->rules->positiveRules($company->id);
        $negativeRules = $this->rules->negativeRules($company->id);
        $recoveryRules = $this->rules->recoveryRules($company->id);

        // Stage 1 – Positive
        $kpiResult      = $strategy->validateKpi($context, $kpiRules);
        $positiveResult = $strategy->calculatePositive($context, $positiveRules);

        // Stage 2 – Negative
        $negativeResult = $strategy->calculateNegative($context, $negativeRules, $selectedViolations);

        $auditDate   = \Carbon\Carbon::parse($audit->audit_date);
        $startOfWeek = $auditDate->clone()->startOfWeek(\Carbon\Carbon::MONDAY);

        // Rule 5 checks previous days of the current week only (not today, not future)
        $weekRangeEnd = $auditDate->clone()->subDay();
        $pastDebitsThisWeek = false;
        if ($weekRangeEnd->gte($startOfWeek)) {
            $query = DB::table('point_transactions')
                ->where('executive_id', $audit->executive_id)
                ->where('type', 'debit')
                ->whereBetween('audit_date', [$startOfWeek->toDateString(), $weekRangeEnd->toDateString()]);

            if ($audit->id) {
                $query->where('daily_audit_id', '!=', $audit->id);
            }

            $pastDebitsThisWeek = $query->exists();
        }
        $context['has_week_deductions'] = $pastDebitsThisWeek || ($negativeResult['total'] > 0);

        // Stage 3 & 4 – Recovery (gated by eligibility)
        $recoveryResult = $this->runRecoveryEngine($audit, $context, $recoveryRules);

        $finalScore = $positiveResult['total'] - $negativeResult['total'] + $recoveryResult['total'];

        $remainingBalance = $this->recoveryHistory->getRemainingBalance($audit->executive_id, $auditDate);

        return [
            'kpi'             => $kpiResult,
            'positive_points' => $positiveResult['total'],
            'negative_points' => $negativeResult['total'],
            'recovery_points' => $recoveryResult['total'],
            'final_score'     => $finalScore,
            'kpi_passed'      => $kpiResult['passed'],
            'recovery_breakdown' => $recoveryResult['breakdown'] ?? [],
            'remaining_recoverable_balance' => $remainingBalance,
            'recovery_capped_amount' => $recoveryResult['capped_amount'] ?? 0,
            'breakdown'       => [
                'positive' => $positiveResult['breakdown'],
                'negative' => $negativeResult['breakdown'],
                'recovery' => $recoveryResult['breakdown'],
                'kpi'      => $kpiResult['details'],
            ],
        ];
    }

    /**
     * Execute the full audit workflow inside a DB transaction.
     */
    public function execute(DailyAudit $audit, array $selectedViolations = []): array
    {
        return DB::transaction(function () use ($audit, $selectedViolations) {
            $executive = Executive::lockForUpdate()->find($audit->executive_id);
            $company   = $executive->company;
            $strategy  = $this->strategyResolver->resolve($company);

            // ── Build context ────────────────────────────────────────────────
            $context  = $strategy->buildContext($audit);
            $context['selected_violations'] = $selectedViolations;

            $kpiRules      = $this->rules->kpiRules($company->id);
            $positiveRules = $this->rules->positiveRules($company->id);
            $negativeRules = $this->rules->negativeRules($company->id);
            $recoveryRules = $this->rules->recoveryRules($company->id);

            // ── Stage 1: Positive Points ─────────────────────────────────────
            $kpiResult      = $strategy->validateKpi($context, $kpiRules);
            $positiveResult = $strategy->calculatePositive($context, $positiveRules);

            // ── Stage 2: Negative Deductions ─────────────────────────────────
            $negativeResult = $strategy->calculateNegative($context, $negativeRules, $selectedViolations);

            $auditDate   = \Carbon\Carbon::parse($audit->audit_date);
            $startOfWeek = $auditDate->clone()->startOfWeek(\Carbon\Carbon::MONDAY);

            // Rule 5 checks previous days of the current week only (not today, not future)
            $weekRangeEnd = $auditDate->clone()->subDay();
            $pastDebitsThisWeek = false;
            if ($weekRangeEnd->gte($startOfWeek)) {
                $query = DB::table('point_transactions')
                    ->where('executive_id', $audit->executive_id)
                    ->where('type', 'debit')
                    ->whereBetween('audit_date', [$startOfWeek->toDateString(), $weekRangeEnd->toDateString()]);

                if ($audit->id) {
                    $query->where('daily_audit_id', '!=', $audit->id);
                }

                $pastDebitsThisWeek = $query->exists();
            }
            $context['has_week_deductions'] = $pastDebitsThisWeek || ($negativeResult['total'] > 0);

            // ── Stage 3 & 4: Recovery Engine (gated) ─────────────────────────
            $recoveryResult = $this->runRecoveryEngine($audit, $context, $recoveryRules);

            $finalScore = $positiveResult['total'] - $negativeResult['total'] + $recoveryResult['total'];

            // ── Delete previous transactions for this audit (re-submit) ───────
            PointTransaction::where('daily_audit_id', $audit->id)
                ->where('category', '!=', 'recovery')
                ->delete();

            PointTransaction::where('daily_audit_id', $audit->id)
                ->where('category', 'recovery')
                ->delete();

            // ── Save scores onto the audit ────────────────────────────────────
            $audit->positive_points  = $positiveResult['total'];
            $audit->negative_points  = $negativeResult['total'];
            $audit->recovery_points  = $recoveryResult['total'];
            $audit->final_score      = $finalScore;
            $audit->kpi_status       = $kpiResult['passed'] ? 'passed' : 'failed';
            $audit->violation_status = $negativeResult['total'] > 0 ? 'active' : 'none';
            $audit->tier_at_audit    = $executive->current_tier;
            $audit->status           = 'pending';
            $audit->save();

            // ── Create positive & negative transactions ───────────────────────
            $runningTotal = $executive->current_score;

            foreach ($positiveResult['breakdown'] as $item) {
                $runningTotal += $item['points'];
                $this->createTransaction($audit, $executive, $item, 'credit', $runningTotal);
            }

            foreach ($negativeResult['breakdown'] as $item) {
                $runningTotal += $item['points'];
                $this->createTransaction($audit, $executive, $item, 'debit', $runningTotal);
            }

            // ── Stage 4 persist: Recovery via dedicated service ───────────────
            $runningTotal = $this->recoveryTransaction->persist(
                $audit,
                $executive,
                $recoveryResult['breakdown'],
                $runningTotal
            );

            // ── Update executive's cumulative score ───────────────────────────
            $oldScore = $executive->current_score;
            $newScore = $this->scoreEngine->recalculateCurrentScore($executive);
            $executive->refresh();

            // ── Determine and update tier ─────────────────────────────────────
            $oldTier = $executive->current_tier;
            $newTier = $this->tierEngine->determineTier($executive, $newScore);
            $executive->current_tier = $newTier;
            $executive->save();

            if ($oldTier !== $newTier) {
                TierHistory::create([
                    'executive_id'   => $executive->id,
                    'company_id'     => $company->id,
                    'daily_audit_id' => $audit->id,
                    'old_tier'       => $oldTier,
                    'new_tier'       => $newTier,
                    'change_reason'  => "Audit on {$audit->audit_date->toDateString()}: score changed from {$oldScore} to {$newScore}",
                    'score_at_change'=> $newScore,
                    'changed_at'     => now(),
                ]);
            }

            // ── Update monthly score ──────────────────────────────────────────
            $this->scoreEngine->updateMonthlyScore($executive, $audit);

            // ── Update streak counts ──────────────────────────────────────────
            $this->updateStreaks($executive, $audit);

            // ── Refresh leaderboard ───────────────────────────────────────────
            $this->leaderboard->refresh(
                $company->id,
                $audit->audit_date->year,
                $audit->audit_date->month,
            );

            // ── Record audit log ──────────────────────────────────────────────
            AuditLog::create([
                'auditable_type' => DailyAudit::class,
                'auditable_id'   => $audit->id,
                'action'         => 'created',
                'new_values'     => [
                    'positive'  => $positiveResult['total'],
                    'negative'  => $negativeResult['total'],
                    'recovery'  => $recoveryResult['total'],
                    'final'     => $finalScore,
                    'kpi'       => $audit->kpi_status,
                    'tier'      => $newTier,
                ],
                'description'    => "Daily audit for {$executive->name} on {$audit->audit_date->toDateString()}",
                'performed_by'   => Auth::id(),
                'ip_address'     => request()->ip(),
            ]);

            // ── Fire event ────────────────────────────────────────────────────
            event(new AuditSubmitted($audit, $executive));

            $remainingBalance = $this->recoveryHistory->getRemainingBalance($executive->id, $auditDate);

            return [
                'audit'           => $audit->fresh(),
                'executive'       => $executive->fresh(),
                'positive_points' => $positiveResult['total'],
                'negative_points' => $negativeResult['total'],
                'recovery_points' => $recoveryResult['total'],
                'final_score'     => $finalScore,
                'kpi_passed'      => $kpiResult['passed'],
                'new_tier'        => $newTier,
                'tier_changed'    => $oldTier !== $newTier,
                'recovery_breakdown' => $recoveryResult['breakdown'] ?? [],
                'remaining_recoverable_balance' => $remainingBalance,
                'recovery_capped_amount' => $recoveryResult['capped_amount'] ?? 0,
                'breakdown'       => [
                    'positive' => $positiveResult['breakdown'],
                    'negative' => $negativeResult['breakdown'],
                    'recovery' => $recoveryResult['breakdown'],
                    'kpi'      => $kpiResult['details'],
                ],
            ];
        });
    }

    /**
     * Reverse an audit — removes transactions and recalculates score.
     * The audit record itself is deleted by the controller after this.
     */
    public function reverse(DailyAudit $audit): void
    {
        DB::transaction(function () use ($audit) {
            $executive = Executive::lockForUpdate()->find($audit->executive_id);

            // Remove ALL transactions for this audit (positive, negative, recovery)
            PointTransaction::where('daily_audit_id', $audit->id)->delete();

            // Recalculate score from remaining transactions
            $this->scoreEngine->recalculateCurrentScore($executive);
            $executive->refresh();

            // Recalculate tier
            $newTier = $this->tierEngine->determineTier($executive, $executive->current_score);
            $executive->current_tier = $newTier;
            $executive->save();

            // Update monthly score
            $this->scoreEngine->updateMonthlyScore($executive, $audit);

            // Refresh leaderboard
            $this->leaderboard->refresh(
                $executive->company_id,
                $audit->audit_date->year,
                $audit->audit_date->month,
            );

            // Log the deletion
            AuditLog::create([
                'auditable_type' => DailyAudit::class,
                'auditable_id'   => $audit->id,
                'action'         => 'deleted',
                'old_values'     => [
                    'positive' => $audit->positive_points,
                    'negative' => $audit->negative_points,
                    'recovery' => $audit->recovery_points,
                    'final'    => $audit->final_score,
                ],
                'description'  => "Audit deleted for {$executive->name} on {$audit->audit_date->toDateString()}",
                'performed_by' => Auth::id(),
                'ip_address'   => request()->ip(),
            ]);
        });
    }

    // ── Private: Recovery Engine Pipeline ─────────────────────────────────────

    /**
     * Four-stage Recovery Engine.
     */
    private function runRecoveryEngine(
        DailyAudit $audit,
        array      $context,
        \Illuminate\Support\Collection $recoveryRules
    ): array {
        $auditDate   = \Carbon\Carbon::parse($audit->audit_date);
        $executiveId = $audit->executive_id;

        // ── Stage 3: Eligibility Gate ────────────────────────────────────────
        if (! $this->recoveryEligibility->canReceiveRecoveryPoints($executiveId, $auditDate, $audit->id)) {
            return $this->recoveryCalculation->calculate($context, $recoveryRules, 0);
        }

        // ── Stage 4: Calculate effective cap = min(dailyCap, remainingBalance) ─
        $dailyCap        = (int) ($this->rules->byCategory($audit->executive->company_id, 'recovery_cap')
                                ->first()?->threshold_value ?? 20);
        $remainingBalance = $this->recoveryHistory->getRemainingBalance($executiveId, $auditDate);
        $effectiveCap     = min($dailyCap, $remainingBalance);

        // ── Stage 4: Evaluate recovery achievement rules ──────────────────────
        return $this->recoveryCalculation->calculate($context, $recoveryRules, $effectiveCap);
    }

    // ── Private Helpers ────────────────────────────────────────────────────────

    private function createTransaction(DailyAudit $audit, Executive $executive, array $item, string $type, int $runningTotal): void
    {
        PointTransaction::create([
            'company_id'     => $executive->company_id,
            'executive_id'   => $executive->id,
            'daily_audit_id' => $audit->id,
            'rule_id'        => $item['rule_id'] ?? null,
            'audit_date'     => $audit->audit_date,
            'category'       => $item['category'],
            'rule_code'      => $item['rule_code'] ?? null,
            'rule_name'      => $item['rule_name'] ?? $item['message'],
            'points'         => abs($item['points']),
            'type'           => $type,
            'running_total'  => $runningTotal,
            'created_by'     => Auth::id(),
        ]);
    }

    private function updateStreaks(Executive $executive, DailyAudit $audit): void
    {
        $callMet    = $audit->connected_calls >= 40;
        $meetingMet = $audit->confirmed_meetings >= 1;

        if ($callMet) {
            $executive->call_streak_count = ($executive->call_streak_count ?? 0) + 1;
            if ($executive->call_streak_count > ($executive->best_call_streak ?? 0)) {
                $executive->best_call_streak = $executive->call_streak_count;
            }
        } else {
            $executive->call_streak_count = 0;
        }

        if ($meetingMet) {
            $executive->meeting_streak_count = ($executive->meeting_streak_count ?? 0) + 1;
            if ($executive->meeting_streak_count > ($executive->best_meeting_streak ?? 0)) {
                $executive->best_meeting_streak = $executive->meeting_streak_count;
            }
        } else {
            $executive->meeting_streak_count = 0;
        }

        $executive->streak_last_updated = $audit->audit_date;
        $executive->save();
    }
}
