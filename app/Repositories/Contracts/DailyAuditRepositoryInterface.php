<?php

namespace App\Repositories\Contracts;

use App\Models\DailyAudit;
use App\Models\Executive;
use Illuminate\Support\Collection;

interface DailyAuditRepositoryInterface
{
    public function findById(int $id): ?DailyAudit;
    public function findWithRelations(int $id): ?DailyAudit;
    public function findForExecutiveAndDate(int $executiveId, string $date): ?DailyAudit;
    public function create(array $data): DailyAudit;
    public function update(DailyAudit $audit, array $data): DailyAudit;
    public function delete(DailyAudit $audit): bool;
    public function getForExecutive(int $executiveId, int $limit = 30): Collection;
    public function getRecentForDashboard(int $limit = 10): Collection;
    public function todayCount(): int;
    public function todayPointsSummary(): array;
}
