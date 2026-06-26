<?php

namespace App\Contracts;

use App\Models\DailyAudit;
use Illuminate\Support\Collection;

interface CalculationStrategyInterface
{
    /**
     * Validate KPI rules for the given audit context.
     *
     * @return array{passed: bool, failures: array, details: array}
     */
    public function validateKpi(array $context, Collection $kpiRules): array;

    /**
     * Calculate positive points from the audit context.
     *
     * @return array{total: int, breakdown: array}
     */
    public function calculatePositive(array $context, Collection $positiveRules): array;

    /**
     * Calculate negative points from the audit context and selected violations.
     *
     * @return array{total: int, breakdown: array}
     */
    public function calculateNegative(array $context, Collection $negativeRules, array $selectedViolations): array;

    /**
     * Calculate recovery points from the audit context.
     *
     * @return array{total: int, breakdown: array}
     */
    public function calculateRecovery(array $context, Collection $recoveryRules, int $maxRecovery): array;

    /**
     * Build the audit context array from a DailyAudit and its executive's history.
     */
    public function buildContext(DailyAudit $audit): array;
}
