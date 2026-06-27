<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        $zones     = Zone::with('company')->orderBy('name')->paginate(25);
        $companies = Company::active()->orderBy('name')->get();
        return view('zones.index', compact('zones', 'companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id'  => ['required', 'exists:companies,id'],
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['nullable', 'string', 'max:50'],
            'status'      => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ]);

        Zone::create($data);
        return back()->with('success', "Zone '{$data['name']}' created.");
    }

    public function update(Request $request, Zone $zone)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $zone->update($data);
        return back()->with('success', "Zone updated.");
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return back()->with('success', "Zone deleted.");
    }

    /** AJAX: zones for a company */
    public function byCompany(Company $company)
    {
        return response()->json(
            $company->zones()->active()->orderBy('name')->get(['id', 'name'])
        );
    }

    public function analytics(Zone $zone)
    {
        $executives = $zone->executives;
        $execIds = $executives->pluck('id');

        // Aggregated totals
        $totals = \App\Models\DailyAudit::whereIn('executive_id', $execIds)
            ->selectRaw('
                SUM(positive_points) as total_positive,
                SUM(negative_points) as total_negative,
                SUM(recovery_points) as total_recovery,
                SUM(final_score) as net_score
            ')
            ->first();

        // Daily scores trend (last 30 days)
        $trend = \App\Models\DailyAudit::whereIn('executive_id', $execIds)
            ->selectRaw('
                audit_date,
                SUM(final_score) as net_score,
                SUM(positive_points) as positive,
                SUM(negative_points) as negative,
                SUM(recovery_points) as recovery
            ')
            ->groupBy('audit_date')
            ->orderBy('audit_date', 'asc')
            ->take(30)
            ->get();

        return response()->json([
            'zone' => [
                'id' => $zone->id,
                'name' => $zone->name,
                'code' => $zone->code,
                'company_name' => $zone->company->name ?? '—',
                'executives_count' => $executives->count(),
            ],
            'totals' => [
                'positive' => (int)($totals->total_positive ?? 0),
                'negative' => (int)($totals->total_negative ?? 0),
                'recovery' => (int)($totals->total_recovery ?? 0),
                'net_score' => (int)($totals->net_score ?? 0),
            ],
            'trend' => $trend->map(function($t) {
                $dt = $t->audit_date instanceof \Carbon\Carbon ? $t->audit_date : \Carbon\Carbon::parse($t->audit_date);
                return [
                    'date' => $dt->format('Y-m-d'),
                    'net_score' => (int)$t->net_score,
                    'positive' => (int)$t->positive,
                    'negative' => (int)$t->negative,
                    'recovery' => (int)$t->recovery,
                ];
            }),
        ]);
    }
}
