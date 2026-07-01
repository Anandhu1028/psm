<?php

namespace App\Services\Recovery;

use App\Models\DailyAudit;
use Illuminate\Support\Collection;

/**
 * RecoveryCalculationService
 *
 * Single responsibility: evaluate which recovery achievements are
 * unlocked today and apply the effective recovery cap.
 *
 * Each recovery rule evaluates independently once the executive is
 * eligible for recovery. Perfect Compliance is the only recovery achievement
 * that depends on meeting the mandatory KPI.
 */
class RecoveryCalculationService
{
    /**
     * Evaluate all recovery rules against today's audit context,
     * then apply the effective cap sequentially across breakdown items.
     *
     * @param  array       $context       Audit context built by the strategy
     * @param  Collection  $recoveryRules Active recovery rules for the company
     * @param  int         $effectiveCap  min(dailyCap, remainingBalance) — must be > 0
     * @return array{total: int, raw_total: int, capped_amount: int, breakdown: array[]}
     */
    public function calculate(
        array      $context,
        Collection $recoveryRules,
        int        $effectiveCap
    ): array {
        $earned = [];
        $breakdown = [];
        $rawTotal = 0;

        // 1. Evaluate all recovery rules to find uncapped points
        foreach ($recoveryRules as $rule) {
            $result = $this->applyRule($rule, $context);
            if ($result !== null) {
                $rawTotal += $result['points'];
                $earned[] = $result;
            }
        }

        // 2. If effectiveCap is 0 or less, everything is capped/allocated to 0
        if ($effectiveCap <= 0) {
            foreach ($earned as $item) {
                $item['points_earned'] = $item['points'];
                $item['points'] = 0;
                $breakdown[] = $item;
            }
            // Add any rules that didn't earn points (to show +0 in detailed preview)
            foreach ($recoveryRules as $rule) {
                $found = collect($breakdown)->contains('rule_code', $rule->code);
                if (!$found) {
                    $breakdown[] = [
                        'rule_id' => $rule->id,
                        'rule_code' => $rule->code,
                        'rule_name' => $rule->name,
                        'category' => 'recovery',
                        'points_earned' => 0,
                        'points' => 0,
                        'message' => $rule->name,
                    ];
                }
            }

            // Sort breakdown by rules' sort order to match DB seeding order
            $sortedBreakdown = [];
            foreach ($recoveryRules as $rule) {
                foreach ($breakdown as $item) {
                    if ($item['rule_code'] === $rule->code) {
                        $sortedBreakdown[] = $item;
                        break;
                    }
                }
            }

            return [
                'total' => 0,
                'raw_total' => $rawTotal,
                'capped_amount' => $rawTotal,
                'breakdown' => $sortedBreakdown
            ];
        }

        // 3. Sequentially allocate up to $effectiveCap
        $allocated = 0;
        foreach ($earned as $item) {
            $item['points_earned'] = $item['points'];
            if ($allocated >= $effectiveCap) {
                $item['points'] = 0;
            } else {
                $available = $effectiveCap - $allocated;
                if ($item['points'] > $available) {
                    $item['points'] = $available;
                }
                $allocated += $item['points'];
            }
            $breakdown[] = $item;
        }

        // 4. Fill in any active recovery rules that did not earn points (so they show up as +0)
        foreach ($recoveryRules as $rule) {
            $found = collect($breakdown)->contains('rule_code', $rule->code);
            if (!$found) {
                $breakdown[] = [
                    'rule_id' => $rule->id,
                    'rule_code' => $rule->code,
                    'rule_name' => $rule->name,
                    'category' => 'recovery',
                    'points_earned' => 0,
                    'points' => 0,
                    'message' => $rule->name,
                ];
            }
        }

        // Sort breakdown by rules' sort order to match DB seeding order
        $sortedBreakdown = [];
        foreach ($recoveryRules as $rule) {
            foreach ($breakdown as $item) {
                if ($item['rule_code'] === $rule->code) {
                    $sortedBreakdown[] = $item;
                    break;
                }
            }
        }

        return [
            'total' => $allocated,
            'raw_total' => $rawTotal,
            'capped_amount' => max(0, $rawTotal - $allocated),
            'breakdown' => $sortedBreakdown
        ];
    }

    /**
     * Apply a single recovery rule to the audit context.
     * Returns a valid item array even with 0 points if rule evaluated to 0.
     */
    private function applyRule($rule, array $context): ?array
    {
        $code = $rule->code;
        $points = 0;

        if (str_ends_with($code, '65_calls')) {
            $calls = (int) ($context['connected_calls'] ?? 0);
            if ($calls >= 65) {
                $points = 6;
            }
        } elseif (str_ends_with($code, '2_plus_attended') || str_ends_with($code, '2_attended')) {
            $meetings = (int) ($context['meetings_attended'] ?? 0);
            if ($meetings >= 2) {
                $points = 6;
            }
        } elseif (str_ends_with($code, 'perfect_compliance')) {
            $kpiMet = ((int) ($context['connected_calls'] ?? 0)) >= 40
                && ((int) ($context['confirmed_meetings'] ?? 0)) >= 1;
            $crmPerfect = !empty($context['crm_followup'])
                && !empty($context['crm_disposition_correct'])
                && !empty($context['first_contact_within_45min'])
                && !empty($context['all_leads_followed_up']);
            if ($kpiMet && $crmPerfect) {
                $points = 8;
            }
        } elseif (str_ends_with($code, 'crm_45min')) {
            if (!empty($context['first_contact_within_45min'])) {
                $points = 4;
            }
        } elseif (str_ends_with($code, 'zero_violations_week')) {
            $hasDeductions = !empty($context['has_week_deductions']);
            if (!$hasDeductions) {
                $points = 10;
            }
        } else {
            return null;
        }

        return [
            'rule_id'   => $rule->id,
            'rule_code' => $rule->code,
            'rule_name' => $rule->name,
            'category'  => 'recovery',
            'points'    => $points,
            'message'   => $rule->name,
        ];
    }
}
