<?php

namespace App\Services;

use App\Models\Executive;
use App\Models\Rule;

class TierService
{
    public function determineTier(Executive $executive, int $score): string
    {
        $ruleSet = app(RuleSetService::class)->activeForUniversity((int) $executive->university_id);

        if (!$ruleSet) {
            return $score < 0 ? 'review_zone' : 'bronze';
        }

        $tiers = Rule::where('rule_set_id', $ruleSet->id)
            ->where('category', 'tier')
            ->where('is_active', true)
            ->orderByDesc('threshold_value')
            ->get();

        foreach ($tiers as $tier) {
            $action = $tier->action_json ?? [];
            $tierCode = $action['tier'] ?? $tier->code;

            if ($tier->operator === '<' && $score < (float) $tier->threshold_value) {
                return $tierCode;
            }

            if (($tier->operator ?? '>=') === '>=' && $score >= (float) $tier->threshold_value) {
                return $tierCode;
            }
        }

        return $score < 0 ? 'review_zone' : 'bronze';
    }
}
