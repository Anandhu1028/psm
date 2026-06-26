<?php

namespace App\Repositories\Contracts;

use App\Models\Executive;
use Illuminate\Support\Collection;

interface ExecutiveRepositoryInterface
{
    public function findById(int $id): ?Executive;
    public function findWithCompanyAndZone(int $id): ?Executive;
    public function allActive(): Collection;
    public function allForCompany(int $companyId): Collection;
    public function allForZone(int $zoneId): Collection;
    public function create(array $data): Executive;
    public function update(Executive $executive, array $data): Executive;
    public function delete(Executive $executive): bool;
    public function updateScore(Executive $executive, int $newScore): void;
    public function updateTier(Executive $executive, string $tier): void;
    public function updateMonthlyScore(Executive $executive, int $score): void;
    public function getRankInCompany(Executive $executive): int;
}
