<?php

namespace App\Repositories;

use App\Models\Executive;
use App\Repositories\Contracts\ExecutiveRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentExecutiveRepository implements ExecutiveRepositoryInterface
{
    public function findById(int $id): ?Executive
    {
        return Executive::find($id);
    }

    public function findWithCompanyAndZone(int $id): ?Executive
    {
        return Executive::with(['company', 'zone'])->find($id);
    }

    public function allActive(): Collection
    {
        return Executive::with(['company', 'zone'])
            ->where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get();
    }

    public function allForCompany(int $companyId): Collection
    {
        return Executive::with('zone')
            ->where('company_id', $companyId)
            ->where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get();
    }

    public function allForZone(int $zoneId): Collection
    {
        return Executive::where('zone_id', $zoneId)
            ->where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Executive
    {
        return Executive::create($data);
    }

    public function update(Executive $executive, array $data): Executive
    {
        $executive->update($data);
        return $executive->fresh();
    }

    public function delete(Executive $executive): bool
    {
        return $executive->delete();
    }

    public function updateScore(Executive $executive, int $newScore): void
    {
        $executive->update(['current_score' => $newScore]);
    }

    public function updateTier(Executive $executive, string $tier): void
    {
        $executive->update(['current_tier' => $tier]);
    }

    public function updateMonthlyScore(Executive $executive, int $score): void
    {
        $executive->update(['monthly_score' => $score]);
    }

    public function getRankInCompany(Executive $executive): int
    {
        return Executive::where('company_id', $executive->company_id)
            ->where('status', 'active')
            ->where('current_score', '>', $executive->current_score)
            ->count() + 1;
    }
}
