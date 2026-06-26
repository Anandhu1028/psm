<?php

namespace App\Repositories;

use App\Models\Rule;
use App\Repositories\Contracts\RuleRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentRuleRepository implements RuleRepositoryInterface
{
    private function cached(string $key, callable $fn): Collection
    {
        return Cache::remember($key, now()->addMinutes(10), $fn);
    }

    public function allForCompany(int $companyId): Collection
    {
        return $this->cached("rules.company.{$companyId}.all", function () use ($companyId) {
            return Rule::where('company_id', $companyId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        });
    }

    public function activeForCompany(int $companyId): Collection
    {
        return $this->cached("rules.company.{$companyId}.active", function () use ($companyId) {
            return Rule::where('company_id', $companyId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        });
    }

    public function byCategory(int $companyId, string $category): Collection
    {
        return $this->cached("rules.company.{$companyId}.cat.{$category}", function () use ($companyId, $category) {
            return Rule::where('company_id', $companyId)
                ->where('category', $category)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    public function kpiRules(int $companyId): Collection
    {
        return $this->byCategory($companyId, 'kpi');
    }

    public function positiveRules(int $companyId): Collection
    {
        return $this->byCategory($companyId, 'positive');
    }

    public function negativeRules(int $companyId): Collection
    {
        return $this->byCategory($companyId, 'negative');
    }

    public function recoveryRules(int $companyId): Collection
    {
        return $this->byCategory($companyId, 'recovery');
    }

    public function tierRules(int $companyId): Collection
    {
        return $this->byCategory($companyId, 'tier');
    }
}
