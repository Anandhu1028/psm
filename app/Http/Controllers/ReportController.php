<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DailyAudit;
use App\Models\Executive;
use App\Models\PointTransaction;
use App\Models\Zone;
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
        $format    = $request->format ?? 'pdf';
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

        // Excel export — simple CSV-like response
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}.csv"];
        $rows    = $this->toCsv($data, $type);
        return response($rows, 200, $headers);
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
        return DB::table('monthly_scores')
            ->join('executives', 'monthly_scores.executive_id', '=', 'executives.id')
            ->join('zones', 'executives.zone_id', '=', 'zones.id')
            ->join('companies', 'monthly_scores.company_id', '=', 'companies.id')
            ->when($companyId, fn($q) => $q->where('monthly_scores.company_id', $companyId))
            ->selectRaw('executives.name, executives.employee_id, zones.name as zone, companies.name as company, monthly_scores.year, monthly_scores.month, monthly_scores.net_score, monthly_scores.positive_points, monthly_scores.negative_points, monthly_scores.recovery_points')
            ->orderByDesc('monthly_scores.year')->orderByDesc('monthly_scores.month')
            ->get()->toArray();
    }

    private function toCsv(array $data, string $type): string
    {
        if (empty($data)) return "No data\n";
        $headers = array_keys((array) $data[0]);
        $csv     = implode(',', $headers) . "\n";
        foreach ($data as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string) $v) . '"', (array) $row)) . "\n";
        }
        return $csv;
    }
}
