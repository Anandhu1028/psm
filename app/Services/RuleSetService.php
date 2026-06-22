<?php

namespace App\Services;

use App\Models\RuleSet;
use App\Models\University;

class RuleSetService
{
    public function activeForUniversity(int $universityId): ?RuleSet
    {
        return RuleSet::with('rules')
            ->where('university_id', $universityId)
            ->where('status', 'active')
            ->orderByDesc('version')
            ->first();
    }

    public function cloneActiveRules(University $source, University $target, ?int $createdBy = null): RuleSet
    {
        $sourceSet = $this->activeForUniversity($source->id);

        $version = ((int) RuleSet::where('university_id', $target->id)->max('version')) + 1;
        $targetSet = RuleSet::create([
            'university_id' => $target->id,
            'name' => $target->code . ' Rules v' . $version,
            'version' => $version,
            'status' => 'active',
            'effective_from' => now()->toDateString(),
            'cloned_from_rule_set_id' => $sourceSet?->id,
            'created_by' => $createdBy,
        ]);

        if ($sourceSet) {
            foreach ($sourceSet->rules as $rule) {
                $copy = $rule->replicate();
                $copy->rule_set_id = $targetSet->id;
                $copy->university_id = $target->id;
                $copy->save();
            }
        }

        return $targetSet;
    }
}
