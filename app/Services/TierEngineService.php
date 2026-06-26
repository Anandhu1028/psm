<?php

namespace App\Services;

use App\Models\Executive;
use App\Repositories\Contracts\RuleRepositoryInterface;

/**
 * Determines tier based on the executive's company's tier rules from DB.
 * No hardcoded tier thresholds anywhere.
 */
class TierEngineService
{
    public function __construct(
        private RuleRepositoryInterface $rules,
    ) {}

    /**
     * Classify the executive's tier based on their score and company's tier rules.
     */
    public function determineTier(Executive $executive, int $score): string
    {
        $tierRules = $this->rules->tierRules($executive->company_id);

        if ($tierRules->isEmpty()) {
            return $this->fallbackTier($score);
        }

        // Tier rules have threshold_min (lower bound) and threshold_max (upper bound).
        // Special case: 'review_zone' fires when score < 0.
        foreach ($tierRules->sortByDesc('threshold_min') as $rule) {
            if ($this->matchesTierRule($score, $rule)) {
                return $rule->code; // code = tier name: platinum|gold|silver|bronze|review_zone
            }
        }

        return 'bronze';
    }

    private function matchesTierRule(int $score, $rule): bool
    {
        return match ($rule->calculation_type) {
            'tier_range' => $score >= (int) $rule->threshold_min
                         && ($rule->threshold_max === null || $score <= (int) $rule->threshold_max),
            'tier_negative' => $score < 0,
            default => false,
        };
    }

    /**
     * Fallback when no tier rules are seeded (should never happen in production).
     */
    private function fallbackTier(int $score): string
    {
        return match (true) {
            $score <    0 => 'review_zone',
            $score <  300 => 'bronze',
            $score <  700 => 'silver',
            $score < 1200 => 'gold',
            default       => 'platinum',
        };
    }
}
