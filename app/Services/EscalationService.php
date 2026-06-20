<?php

namespace App\Services;

use App\Models\Executive;
use App\Models\Escalation;
use App\Models\ScoreRule;
use Carbon\Carbon;

class EscalationService
{
    /**
     * Check performance levels for an executive and raise escalations if thresholds are breached.
     */
    public function checkForEscalations(Executive $executive): void
    {
        $universityId = $executive->university_id;

        // Fetch thresholds from university specific rules
        if ($universityId) {
            $rules = ScoreRule::where('university_id', $universityId)
                ->whereIn('rule_key', [
                    'escalation_low_calls_threshold',
                    'escalation_violations_threshold',
                    'pip_passing_score'
                ])
                ->get()
                ->keyBy('rule_key');

            $lowCallsThreshold = isset($rules['escalation_low_calls_threshold']) ? (int) $rules['escalation_low_calls_threshold']->rule_value : 40;
            $violationsThreshold = isset($rules['escalation_violations_threshold']) ? (int) $rules['escalation_violations_threshold']->rule_value : 3;
            $pipPassingScore = isset($rules['pip_passing_score']) ? (int) $rules['pip_passing_score']->rule_value : 300;
        } else {
            $lowCallsThreshold = 40;
            $violationsThreshold = 3;
            $pipPassingScore = 300;
        }

        // 1. Check Negative Score / Review Zone
        if ($executive->current_score < 0 || $executive->current_tier === 'review_zone') {
            $this->triggerEscalation(
                $executive,
                'review_zone',
                'high',
                "Executive score is negative ({$executive->current_score}) or tier is in Review Zone."
            );
        }

        // 2. Check 3 consecutive low call days
        $recentLogs = $executive->dailyLogs()
            ->orderBy('date', 'desc')
            ->take(3)
            ->get();

        if ($recentLogs->count() === 3) {
            $allLow = true;
            foreach ($recentLogs as $log) {
                if ($log->connected_calls >= $lowCallsThreshold) {
                    $allLow = false;
                    break;
                }
            }
            if ($allLow) {
                $this->triggerEscalation(
                    $executive,
                    'low_calls',
                    'medium',
                    "Executive logged 3 consecutive low call days (< {$lowCallsThreshold} connected calls)."
                );
            }
        }

        // 3. Check Repeated Violations
        $recentViolationsCount = $executive->violations()
            ->where('status', 'active')
            ->where('date_committed', '>=', now()->subDays(30))
            ->count();

        if ($recentViolationsCount >= $violationsThreshold) {
            $this->triggerEscalation(
                $executive,
                'repeated_violations',
                'high',
                "Executive accrued {$recentViolationsCount} active violations within the last 30 days."
            );
        }

        // 4. Check Probation/PIP Failure
        if ($executive->status === 'probation' && $executive->probation_end_date && now()->gt($executive->probation_end_date)) {
            if ($executive->current_score < $pipPassingScore) {
                $this->triggerEscalation(
                    $executive,
                    'probation_failure',
                    'high',
                    "Executive probation period passed probation_end_date ({$executive->probation_end_date->toDateString()}) but cumulative score is below target threshold of {$pipPassingScore} points."
                );
            }
        }
    }

    /**
     * Create an escalation log if one is not already open.
     */
    protected function triggerEscalation(Executive $executive, string $type, string $severity, string $reason): void
    {
        $hasOpen = $executive->escalations()
            ->where('type', $type)
            ->where('status', 'open')
            ->exists();

        if (!$hasOpen) {
            $executive->escalations()->create([
                'type' => $type,
                'severity' => $severity,
                'status' => 'open',
                'trigger_reason' => $reason,
            ]);
        }
    }
}
