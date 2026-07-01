<?php

namespace App\Http\Controllers;

use App\Exports\ArrayExport;
use App\Models\Company;
use App\Models\DailyAudit;
use App\Models\Executive;
use App\Models\PointTransaction;
use App\Models\Zone;
use App\Services\MonthlyPerformanceRankingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $companies  = Company::active()->orderBy('name')->get();
        $zones      = Zone::active()->orderBy('name')->get();
        $executives = Executive::where('status', '!=', 'inactive')->orderBy('name')->get();

        $type      = $request->type ?? 'daily';
        $dateFrom  = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo    = $request->date_to   ?? now()->toDateString();
        $companyId = $request->company_id;
        $zoneId    = $request->zone_id;
        $execId    = $request->executive_id;

        $data = $this->buildReport($type, $dateFrom, $dateTo, $companyId, $zoneId, $execId);

        return view('reports.index', compact(
            'companies', 'zones', 'executives',
            'type', 'dateFrom', 'dateTo', 'data'
        ));
    }

    public function export(Request $request)
    {
        $format    = $request->input('format', 'pdf');
        $type      = $request->type ?? 'daily';
        $dateFrom  = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo    = $request->date_to   ?? now()->toDateString();
        $data      = $this->buildReport($type, $dateFrom, $dateTo, $request->company_id, $request->zone_id, $request->executive_id);

        $filename = "report_{$type}_{$dateFrom}_{$dateTo}";

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', compact('type', 'dateFrom', 'dateTo', 'data'))
                ->setPaper('a4', 'landscape');
            return $pdf->download("{$filename}.pdf");
        }

        $rows = $this->formatExportRows($data);

        if ($format === 'csv') {
            return Excel::download(new ArrayExport($rows), "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new ArrayExport($rows), "{$filename}.xlsx");
    }

    private function formatExportRows(array $data): array
    {
        return array_map(fn($row) => $this->flattenExportData((array) $row), $data);
    }

    private function flattenExportData(array $data, string $prefix = ''): array
    {
        $flattened = [];

        foreach ($data as $key => $value) {
            $column = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                foreach ($this->flattenExportData($value, $column) as $nestedKey => $nestedValue) {
                    $flattened[$nestedKey] = $nestedValue;
                }
                continue;
            }

            if (is_object($value)) {
                $value = method_exists($value, 'toArray') ? $value->toArray() : (string) $value;
            }

            $flattened[$column] = $value;
        }

        return $flattened;
    }

    private function buildReport(string $type, string $from, string $to, ?string $companyId, ?string $zoneId, ?string $execId): array
    {
        return match ($type) {
            'daily'    => $this->dailyReport($from, $to, $companyId, $zoneId, $execId),
            'executive'=> $this->executiveReport($from, $to, $companyId, $zoneId, $execId),
            'zone'     => $this->zoneReport($from, $to, $companyId),
            'violation'=> $this->violationReport($from, $to, $companyId, $zoneId, $execId),
            'recovery' => $this->recoveryReport($from, $to, $companyId),
            'monthly'  => $this->monthlyReport($companyId),
            default    => $this->dailyReport($from, $to, $companyId, $zoneId, $execId),
        };
    }

    private function dailyReport($from, $to, $companyId, $zoneId, $execId): array
    {
        return DailyAudit::with(['executive.company', 'executive.zone', 'createdBy'])
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($zoneId,    fn($q) => $q->whereHas('executive', fn($eq) => $eq->where('zone_id', $zoneId)))
            ->when($execId,    fn($q) => $q->where('executive_id', $execId))
            ->whereBetween('audit_date', [$from, $to])
            ->orderByDesc('audit_date')
            ->get()
            ->toArray();
    }

    private function executiveReport($from, $to, $companyId, $zoneId, $execId): array
    {
        return Executive::with(['company', 'zone'])
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($zoneId,    fn($q) => $q->where('zone_id', $zoneId))
            ->when($execId,    fn($q) => $q->where('id', $execId))
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($e) => [
                'name'          => $e->name,
                'employee_id'   => $e->employee_id,
                'company'       => $e->company->name,
                'zone'          => $e->zone->name,
                'current_score' => $e->current_score,
                'monthly_score' => $e->monthly_score,
                'tier'          => $e->tier_label,
                'status'        => $e->status,
            ])
            ->toArray();
    }

    private function zoneReport($from, $to, $companyId): array
    {
        return DB::table('executives')
            ->join('zones', 'executives.zone_id', '=', 'zones.id')
            ->join('companies', 'executives.company_id', '=', 'companies.id')
            ->when($companyId, fn($q) => $q->where('executives.company_id', $companyId))
            ->whereNull('executives.deleted_at')
            ->selectRaw('zones.name as zone, companies.name as company, COUNT(executives.id) as execs, AVG(executives.current_score) as avg_score, SUM(executives.current_score) as total_score')
            ->groupBy('zones.id', 'zones.name', 'companies.id', 'companies.name')
            ->orderByDesc('avg_score')
            ->get()->toArray();
    }

    private function violationReport($from, $to, $companyId, $zoneId, $execId): array
    {
        return PointTransaction::with(['executive.company', 'executive.zone'])
            ->where('type', 'debit')
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($execId,    fn($q) => $q->where('executive_id', $execId))
            ->whereBetween('audit_date', [$from, $to])
            ->orderByDesc('audit_date')
            ->get()->toArray();
    }

    private function recoveryReport($from, $to, $companyId): array
    {
        return PointTransaction::with(['executive.company'])
            ->where('category', 'recovery')
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('audit_date', [$from, $to])
            ->orderByDesc('audit_date')
            ->get()->toArray();
    }

    private function monthlyReport($companyId): array
    {
        $month = request()->input('month', now()->month);
        $year  = request()->input('year', now()->year);
        $zoneId = request()->input('zone_id');

        $ranking = app(MonthlyPerformanceRankingService::class);

        return $ranking->calculate($month, $year, $companyId, $zoneId)
            ->map(function ($row) use ($year, $month) {
                $exec = $row['executive'];

                // Sum positive (credit) and negative (debit) points for the month
                $positive = (int) DB::table('point_transactions')
                    ->where('executive_id', $exec->id)
                    ->whereYear('audit_date', $year)
                    ->whereMonth('audit_date', $month)
                    ->where('type', 'credit')
                    ->sum('points');

                $negative = (int) DB::table('point_transactions')
                    ->where('executive_id', $exec->id)
                    ->whereYear('audit_date', $year)
                    ->whereMonth('audit_date', $month)
                    ->where('type', 'debit')
                    ->sum('points');

                $net = $positive - $negative;

                return (object) [
                    'executive_id'  => $exec->id,
                    'name'          => $exec->name,
                    'employee_id'   => $exec->employee_id,
                    'company'       => $exec->company?->name,
                    'zone'          => $exec->zone?->name,
                    'monthly_target'=> $row['target'],
                    'admissions'    => $row['admissions'],
                    'remaining'     => $row['remaining'],
                    'achievement'   => $row['achievement'],
                    'eligible'      => $row['eligible'],
                    'rank'          => $row['rank'],
                    'bonus_awarded' => DB::table('point_transactions')
                        ->where('executive_id', $exec->id)
                        ->where('category', 'quality_bonus')
                        ->whereYear('audit_date', $year)
                        ->whereMonth('audit_date', $month)
                        ->exists() ? '+15' : null,
                    'year'          => $year,
                    'month'         => $month,
                    'positive_points'=> $positive,
                    'negative_points'=> $negative,
                    'net_score'     => $net,
                ];
            })
            ->values()
            ->toArray();
    }
}
