<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Rule;
use App\Models\RuleEvaluationResult;
use App\Models\ScoreTransaction;
use App\Models\Violation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DynamicRuleEngineService
{
    public function __construct(
        protected RuleSetService $ruleSets,
        protected MeetingWindowService $meetingWindows,
        protected StreakService $streaks
    ) {
    }

    public function preview(DailyLog $log, array $selectedViolations = []): array
    {
        $ruleSet = $this->ruleSets->activeForUniversity((int) $log->university_id);

        if (!$ruleSet) {
            return [
                'positive_points' => 0,
                'negative_points' => 0,
                'recovery_points' => 0,
                'net_score' => 0,
                'kpi_status' => 'not_evaluated',
                'violation_status' => empty($selectedViolations) ? 'none' : 'active',
                'meeting_window_status' => null,
                'breakdown' => [],
            ];
        }

        $context = $this->context($log, $selectedViolations);
        $evaluations = $this->evaluate($log, $ruleSet->rules, $context);

        $positive = (int) $evaluations->where('component', 'positive')->sum('points');
        $negative = abs((int) $evaluations->where('component', 'negative')->sum('points'));
        $recovery = (int) $evaluations->where('component', 'recovery')->sum('points');
        $cap = $this->recoveryCap($ruleSet->rules);

        if ($cap !== null && $recovery > $cap) {
            $recovery = $cap;
        }

        $kpiFailures = $evaluations->where('category', 'kpi')->where('status', 'failed')->count();
        $kpiStatus = $kpiFailures > 0 ? 'failed' : 'passed';

        $meetingWindowStatus = $evaluations->first(fn ($item) => !empty($item['meeting_window_status']))['meeting_window_status'] ?? null;

        return [
            'rule_set_id' => $ruleSet->id,
            'positive_points' => $positive,
            'negative_points' => $negative,
            'recovery_points' => $recovery,
            'net_score' => $positive - $negative + $recovery,
            'kpi_status' => $kpiStatus,
            'violation_status' => $negative > 0 ? 'active' : 'none',
            'meeting_window_status' => $meetingWindowStatus,
            'breakdown' => $evaluations->values()->all(),
        ];
    }

    public function calculateAndApply(DailyLog $log, array $selectedViolations = []): int
    {
        return DB::transaction(function () use ($log, $selectedViolations) {
            $executive = $log->executive()->lockForUpdate()->first();

            $this->revertExisting($log);
            Violation::where('daily_log_id', $log->id)->delete();
            RuleEvaluationResult::where('daily_log_id', $log->id)->delete();

            $result = $this->preview($log, $selectedViolations);

            $log->rule_set_id = $result['rule_set_id'] ?? null;
            $log->positive_points = $result['positive_points'];
            $log->negative_points = $result['negative_points'];
            $log->recovery_points = $result['recovery_points'];
            $log->calculated_score = $result['net_score'];
            $log->kpi_status = $result['kpi_status'];
            $log->violation_status = $result['violation_status'];
            $log->meeting_window_status = $result['meeting_window_status'];
            $log->conduct_violation = collect($selectedViolations)->contains(fn ($key) => str_starts_with($key, 'conduct_'));
            $log->save();

            foreach ($result['breakdown'] as $item) {
                $evaluation = RuleEvaluationResult::create([
                    'daily_log_id' => $log->id,
                    'executive_id' => $executive->id,
                    'university_id' => $executive->university_id,
                    'rule_set_id' => $result['rule_set_id'],
                    'rule_id' => $item['rule_id'] ?? null,
                    'rule_code' => $item['rule_code'],
                    'category' => $item['category'],
                    'status' => $item['status'],
                    'points' => $item['points'],
                    'message' => $item['message'],
                    'context_snapshot' => $item['context'] ?? null,
                ]);

                $points = (int) $item['points'];
                if ($points !== 0) {
                    $this->applyScore($executive, $log, $evaluation, $points, $item);
                }

                if (($item['create_violation'] ?? false) && $points < 0) {
                    Violation::create([
                        'university_id' => $executive->university_id,
                        'executive_id' => $executive->id,
                        'daily_log_id' => $log->id,
                        'violation_type' => $item['violation_type'] ?? 'conduct',
                        'points_deducted' => abs($points),
                        'description' => $item['message'],
                        'violation_subtype' => $item['rule_code'],
                        'status' => 'active',
                        'date_committed' => $log->date,
                        'created_by' => $log->created_by ?: auth()->id() ?: 1,
                    ]);
                }
            }

            $executive->current_tier = app(TierService::class)->determineTier($executive, (int) $executive->current_score);
            $executive->save();

            return (int) $result['net_score'];
        });
    }

    protected function context(DailyLog $log, array $selectedViolations): array
    {
        $executive = $log->relationLoaded('executive')
            ? $log->executive
            : $log->executive()->first();

        $streakContext = $executive
            ? $this->streaks->contextFor($executive)
            : ['call_streak_count' => 0, 'meeting_streak_count' => 0, 'call_streak_7' => false, 'meeting_streak_7' => false, 'best_call_streak' => 0, 'best_meeting_streak' => 0];

        return array_merge([
            'connected_calls'             => (int)  $log->connected_calls,
            'meetings_arranged'           => (int)  $log->meetings_arranged,
            'meetings_attended'           => (int)  $log->meetings_attended,
            'first_contact_within_45_min' => (bool) $log->first_contact_within_45_min,
            'all_leads_followed_up'       => (bool) $log->all_leads_followed_up,
            'crm_disposition_correct'     => (bool) $log->crm_disposition_correct,
            'warm_lead_converted'         => (bool) $log->warm_lead_converted,
            'cold_lead_reactivated'       => (bool) ($log->cold_lead_reactivated ?? false),
            'selected_violations'         => $selectedViolations,
        ], $streakContext);
    }

    protected function evaluate(DailyLog $log, Collection $rules, array $context): Collection
    {
        return $rules
            ->where('is_active', true)
            ->sortBy('sort_order')
            ->map(fn (Rule $rule) => $this->evaluateRule($log, $rule, $context))
            ->filter()
            ->values();
    }

    protected function evaluateRule(DailyLog $log, Rule $rule, array $context): ?array
    {
        if ($rule->calculation_type === 'recovery_cap') {
            return null;
        }

        $points = 0;
        $status = 'skipped';
        $message = $rule->name;
        $component = $this->componentFor($rule);
        $createViolation = false;
        $violationType = null;
        $meetingWindowStatus = null;

        if ($rule->calculation_type === 'per_unit') {
            $value = (int) ($context[$rule->input_metric] ?? 0);
            if ($value > 0) {
                $points = $value * (float) $rule->points;
                $status = 'applied';
                $message = "{$rule->name} ({$value})";
            }
        } elseif ($rule->calculation_type === 'boolean') {
            $condition = $rule->condition_json ?? [];
            $allTrue = $condition['all_true'] ?? null;
            $passes = $allTrue
                ? collect($allTrue)->every(fn ($metric) => !empty($context[$metric]))
                : !empty($context[$rule->input_metric]);

            if (($condition['no_negative_points'] ?? false) && !empty($context['selected_violations'])) {
                $passes = false;
            }

            if ($passes) {
                $points = (float) $rule->points;
                $status = $rule->category === 'kpi' ? 'passed' : 'applied';
            } elseif ($rule->category === 'kpi') {
                $status = 'failed';
            }
        } elseif ($rule->calculation_type === 'streak') {
            // Streak rules fire when the matching streak context flag is true.
            // condition_json example: {"streak_metric": "call_streak_7"}
            $condition = $rule->condition_json ?? [];
            $metric = $condition['streak_metric'] ?? $rule->input_metric;
            $passes = !empty($context[$metric]);

            if ($passes) {
                $points = (float) $rule->points;
                $status = 'applied';
                $message = "{$rule->name} (streak achieved)";
            }
        } elseif ($rule->calculation_type === 'selected_violation') {
            if (in_array($rule->code, $context['selected_violations'], true)) {
                $points = -abs((float) $rule->points);
                $status = 'applied';
                $createViolation = (bool) (($rule->action_json['create_violation'] ?? true));
                $violationType = $rule->action_json['violation_type'] ?? $this->inferViolationType($rule->code);
            }
        } elseif ($rule->calculation_type === 'rolling_window') {
            $meetingWindowStatus = $this->meetingWindows->statusFor($log, $rule);
            if ($meetingWindowStatus['status'] === 'failed') {
                $points = -abs((float) (($rule->action_json['deduct_points'] ?? $rule->points ?? 0)));
                $status = 'failed';
                $createViolation = (bool) (($rule->action_json['create_violation'] ?? false));
                $violationType = $rule->action_json['violation_type'] ?? 'meeting';
            } else {
                $status = in_array($meetingWindowStatus['status'], ['passed', 'on_track'], true) ? 'passed' : 'skipped';
            }
            $message = "{$rule->name}: {$meetingWindowStatus['status']}";
        } else {
            $value = (float) ($context[$rule->input_metric] ?? 0);
            $matched = $this->matches($value, $rule);
            if ($matched) {
                $points = (float) $rule->points;
                $status = $rule->category === 'kpi' ? 'passed' : 'applied';
            } elseif ($rule->category === 'kpi') {
                $status = 'failed';
            }
        }

        if ($points == 0 && !in_array($status, ['failed', 'passed'], true)) {
            return null;
        }

        return [
            'rule_id' => $rule->id,
            'rule_code' => $rule->code,
            'category' => $rule->category,
            'component' => $component,
            'status' => $status,
            'points' => $points,
            'message' => $message,
            'context' => $context,
            'create_violation' => $createViolation,
            'violation_type' => $violationType,
            'meeting_window_status' => $meetingWindowStatus,
        ];
    }

    protected function matches(float $value, Rule $rule): bool
    {
        return match ($rule->operator) {
            '>=' => $value >= (float) $rule->threshold_value,
            '>' => $value > (float) $rule->threshold_value,
            '<=' => $value <= (float) $rule->threshold_value,
            '<' => $value < (float) $rule->threshold_value,
            '=' => $value == (float) $rule->threshold_value,
            'between' => $value >= (float) $rule->threshold_value && $value <= (float) $rule->threshold_to,
            default => false,
        };
    }

    protected function componentFor(Rule $rule): string
    {
        return match ($rule->category) {
            'positive', 'attendance', 'lead_management' => 'positive',
            'negative' => 'negative',
            'recovery' => 'recovery',
            default => 'kpi',
        };
    }

    protected function recoveryCap(Collection $rules): ?int
    {
        $rule = $rules->firstWhere('calculation_type', 'recovery_cap');
        return $rule ? (int) $rule->threshold_value : null;
    }

    protected function revertExisting(DailyLog $log): void
    {
        $executive = $log->executive;
        $oldTransactions = ScoreTransaction::where('daily_log_id', $log->id)->get();

        foreach ($oldTransactions as $tx) {
            $executive->current_score += $tx->type === 'credit' ? -$tx->points : $tx->points;
        }

        $executive->save();
        ScoreTransaction::where('daily_log_id', $log->id)->delete();
    }

    protected function applyScore($executive, DailyLog $log, RuleEvaluationResult $evaluation, int $points, array $item): void
    {
        $oldScore = (int) $executive->current_score;
        $newScore = $oldScore + $points;
        $oldTier = $executive->current_tier;
        $newTier = app(TierService::class)->determineTier($executive, $newScore);

        $executive->current_score = $newScore;
        $executive->current_tier = $newTier;
        $executive->save();

        $executive->scoreTransactions()->create([
            'daily_log_id' => $log->id,
            'rule_id' => null,
            'rule_set_id' => $evaluation->rule_set_id,
            'rule_evaluation_result_id' => $evaluation->id,
            'type' => $points >= 0 ? 'credit' : 'debit',
            'component' => $item['component'],
            'points' => abs($points),
            'running_total' => $newScore,
            'description' => $item['message'],
            'transaction_date' => now()->toDateString(),
        ]);

        if ($oldTier !== $newTier) {
            $executive->tierHistories()->create([
                'old_tier' => $oldTier,
                'new_tier' => $newTier,
                'change_reason' => "Score changed from {$oldScore} to {$newScore}. Reason: {$item['message']}",
                'changed_at' => now(),
            ]);
        }
    }

    protected function inferViolationType(string $code): string
    {
        return str_contains($code, '_') ? explode('_', $code)[0] : 'conduct';
    }
}
