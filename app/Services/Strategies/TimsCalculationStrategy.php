<?php

namespace App\Services\Strategies;

use App\Contracts\CalculationStrategyInterface;
use App\Models\DailyAudit;
use Illuminate\Support\Collection;

/**
 * TIMS Calculation Strategy
 *
 * Execution order:
 * 1. Daily KPI Validation (40 calls, 1 meeting minimum)
 * 2. Daily Meeting Validation
 * 3. Daily Point Calculation
 */
class TimsCalculationStrategy implements CalculationStrategyInterface
{
    public function buildContext(DailyAudit $audit): array
    {
        $executive = $audit->executive;

        // Streak data from executive model (updated by streak service)
        $callStreak7   = ($executive->call_streak_count    ?? 0) >= 7;
        $meetingStreak7= ($executive->meeting_streak_count ?? 0) >= 7;

        return [
            // Core metrics
            'connected_calls'             => (int) $audit->connected_calls,
            'confirmed_meetings'          => (int) $audit->confirmed_meetings,
            'meetings_attended'           => (int) $audit->meetings_attended,
            'crm_followup'                => (bool) $audit->crm_followup,
            'crm_disposition_correct'     => (bool) $audit->crm_disposition_correct,
            'first_contact_within_45min'  => (bool) $audit->first_contact_within_45min,
            'all_leads_followed_up'       => (bool) $audit->all_leads_followed_up,
            'warm_lead_converted'         => (bool) $audit->warm_lead_converted,
            'cold_lead_reactivated'       => (bool) $audit->cold_lead_reactivated,
            // Streak context
            'call_streak_count'           => (int) ($executive->call_streak_count ?? 0),
            'meeting_streak_count'        => (int) ($executive->meeting_streak_count ?? 0),
            'call_streak_7'               => $callStreak7,
            'meeting_streak_7'            => $meetingStreak7,
            // Strategy type
            'strategy'                    => 'tims',
        ];
    }

    public function validateKpi(array $context, Collection $kpiRules): array
    {
        $failures = [];
        $details  = [];
        $passed   = true;

        foreach ($kpiRules as $rule) {
            $value  = (float) ($context[$rule->input_metric] ?? 0);
            $passes = $this->evaluateThreshold($value, $rule);

            $details[] = [
                'rule_id'   => $rule->id,
                'rule_code' => $rule->code,
                'rule_name' => $rule->name,
                'metric'    => $rule->input_metric,
                'value'     => $value,
                'required'  => $rule->threshold_value ?? $rule->threshold_min,
                'passed'    => $passes,
                'message'   => $passes
                    ? "✓ {$rule->name}: {$value}"
                    : "✗ {$rule->name}: {$value} (required: " . ($rule->threshold_value ?? $rule->threshold_min) . ")",
            ];

            if (! $passes) {
                $passed     = false;
                $failures[] = $rule->name;
            }
        }

        return compact('passed', 'failures', 'details');
    }

    public function calculatePositive(array $context, Collection $positiveRules): array
    {
        $total     = 0;
        $breakdown = [];

        foreach ($positiveRules as $rule) {
            $result = $this->applyRule($rule, $context);
            if ($result !== null) {
                $total += $result['points'];
                $breakdown[] = $result;
            }
        }
        return ['total' => $total, 'breakdown' => $breakdown];
    }

    public function calculateNegative(array $context, Collection $negativeRules, array $selectedViolations): array
    {
        $total     = 0;
        $breakdown = [];

        $context['selected_violations'] = $selectedViolations;

        foreach ($negativeRules as $rule) {
            $result = $this->applyRule($rule, $context);
            if ($result !== null && $result['points'] < 0) {
                $total     += $result['points'];
                $breakdown[] = $result;
            }
        }

        return ['total' => abs($total), 'breakdown' => $breakdown];
    }



    // ── Private Helpers ────────────────────────────────────────────────────────

    private function applyRule($rule, array $context): ?array
    {
        $points  = 0;
        $message = $rule->name;

        switch ($rule->calculation_type) {
            case 'range':
                $value = (float) ($context[$rule->input_metric] ?? 0);
                if ($rule->threshold_min !== null && $rule->threshold_max !== null) {
                    if ($value >= $rule->threshold_min && $value <= $rule->threshold_max) {
                        $points  = (float) $rule->points;
                        $message = "{$rule->name} ({$value} calls)";
                    }
                } elseif ($rule->threshold_min !== null) {
                    if ($value >= $rule->threshold_min) {
                        $points  = (float) $rule->points;
                        $message = "{$rule->name} ({$value} calls)";
                    }
                }
                break;

            case 'boolean':
                $passes = false;
                $cond   = $rule->condition_json ?? [];
                if (isset($cond['all_true']) && is_array($cond['all_true'])) {
                    $passes = collect($cond['all_true'])
                        ->every(fn($metric) => ! empty($context[$metric]));
                } elseif ($rule->input_metric) {
                    $passes = ! empty($context[$rule->input_metric]);
                }
                if ($passes) {
                    $points  = (float) $rule->points;
                }
                break;

            case 'streak':
                $cond   = $rule->condition_json ?? [];
                $metric = $cond['streak_metric'] ?? $rule->input_metric;
                if (! empty($context[$metric])) {
                    $points  = (float) $rule->points;
                    $message = "{$rule->name} (streak achieved)";
                }
                break;

            case 'per_unit':
                $value = (int) ($context[$rule->input_metric] ?? 0);
                if ($value > 0) {
                    $points  = $value * (float) $rule->points;
                    $message = "{$rule->name} (×{$value})";
                }
                break;

            case 'selected_violation':
                $selected = $context['selected_violations'] ?? [];
                if (in_array($rule->code, $selected, true)) {
                    $points  = -(float) abs($rule->points);
                    $message = $rule->name;
                }
                break;

            default:
                return null;
        }

        if ($points == 0) {
            return null;
        }

        return [
            'rule_id'    => $rule->id,
            'rule_code'  => $rule->code,
            'rule_name'  => $rule->name,
            'category'   => $rule->category,
            'points'     => (int) $points,
            'message'    => $message,
        ];
    }

    private function evaluateThreshold(float $value, $rule): bool
    {
        return match ($rule->operator) {
            '>='    => $value >= (float) $rule->threshold_value,
            '>'     => $value >  (float) $rule->threshold_value,
            '<='    => $value <= (float) $rule->threshold_value,
            '<'     => $value <  (float) $rule->threshold_value,
            '='     => $value == (float) $rule->threshold_value,
            'between' => $value >= (float) $rule->threshold_min
                      && $value <= (float) $rule->threshold_max,
            default => false,
        };
    }
}
