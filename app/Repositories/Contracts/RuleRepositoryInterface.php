<?php

namespace App\Repositories\Contracts;

use App\Models\Rule;
use Illuminate\Support\Collection;

interface RuleRepositoryInterface
{
    public function allForCompany(int $companyId): Collection;
    public function activeForCompany(int $companyId): Collection;
    public function byCategory(int $companyId, string $category): Collection;
    public function kpiRules(int $companyId): Collection;
    public function positiveRules(int $companyId): Collection;
    public function negativeRules(int $companyId): Collection;
    public function recoveryRules(int $companyId): Collection;
    public function tierRules(int $companyId): Collection;
}
