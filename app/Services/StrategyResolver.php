<?php

namespace App\Services;

use App\Contracts\CalculationStrategyInterface;
use App\Services\Strategies\TimsCalculationStrategy;
use App\Services\Strategies\FocuzCalculationStrategy;
use App\Models\Company;
use InvalidArgumentException;

/**
 * Resolves the correct calculation strategy based on the company's
 * `calculation_strategy` field. No manual company selection ever.
 */
class StrategyResolver
{
    public function __construct(
        private TimsCalculationStrategy  $timsStrategy,
        private FocuzCalculationStrategy $focuzStrategy,
    ) {}

    public function resolve(Company $company): CalculationStrategyInterface
    {
        return match (strtolower($company->calculation_strategy)) {
            'tims'  => $this->timsStrategy,
            'focuz' => $this->focuzStrategy,
            default => throw new InvalidArgumentException(
                "Unknown calculation strategy: [{$company->calculation_strategy}] for company [{$company->name}]"
            ),
        };
    }
}
