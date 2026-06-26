<?php

namespace App\Repositories;

use App\Models\DailyAudit;
use App\Repositories\Contracts\DailyAuditRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentDailyAuditRepository implements DailyAuditRepositoryInterface
{
    public function findById(int $id): ?DailyAudit
    {
        return DailyAudit::find($id);
    }

    public function findWithRelations(int $id): ?DailyAudit
    {
        return DailyAudit::with([
            'executive.company',
            'executive.zone',
            'company',
            'pointTransactions.rule',
            'tierHistories',
            'createdBy',
            'verifiedBy',
        ])->find($id);
    }

    public function findForExecutiveAndDate(int $executiveId, string $date): ?DailyAudit
    {
        return DailyAudit::where('executive_id', $executiveId)
            ->whereDate('audit_date', $date)
            ->first();
    }

    public function create(array $data): DailyAudit
    {
        return DailyAudit::create($data);
    }

    public function update(DailyAudit $audit, array $data): DailyAudit
    {
        $audit->update($data);
        return $audit->fresh();
    }

    public function delete(DailyAudit $audit): bool
    {
        return $audit->delete();
    }

    public function getForExecutive(int $executiveId, int $limit = 30): Collection
    {
        return DailyAudit::where('executive_id', $executiveId)
            ->orderByDesc('audit_date')
            ->limit($limit)
            ->get();
    }

    public function getRecentForDashboard(int $limit = 10): Collection
    {
        return DailyAudit::with(['executive.company', 'executive.zone', 'createdBy'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function todayCount(): int
    {
        return DailyAudit::whereDate('audit_date', today())->count();
    }

    public function todayPointsSummary(): array
    {
        $result = DailyAudit::whereDate('audit_date', today())
            ->select(
                DB::raw('SUM(positive_points) as total_positive'),
                DB::raw('SUM(negative_points) as total_negative'),
                DB::raw('SUM(recovery_points) as total_recovery'),
                DB::raw('SUM(final_score) as total_score'),
            )
            ->first();

        return [
            'total_positive' => (int) ($result->total_positive ?? 0),
            'total_negative' => (int) ($result->total_negative ?? 0),
            'total_recovery' => (int) ($result->total_recovery ?? 0),
            'total_score'    => (int) ($result->total_score ?? 0),
        ];
    }
}
