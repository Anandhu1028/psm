<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyAuditRequest;
use App\Models\Company;
use App\Models\DailyAudit;
use App\Models\Executive;
use App\Models\Zone;
use App\Repositories\Contracts\DailyAuditRepositoryInterface;
use App\Services\AuditOrchestrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DailyAuditController extends Controller
{
    public function __construct(
        private AuditOrchestrationService     $orchestration,
        private DailyAuditRepositoryInterface $audits,
    ) {}

    public function index(Request $request)
{
    $auditDate = $request->input('audit_date', now()->toDateString());

    $query = DailyAudit::with(['executive.company', 'executive.zone', 'createdBy'])

        // Always filter by the selected date
        ->whereDate('audit_date', $auditDate)

        ->when($request->company_id, function ($q) use ($request) {
            $q->where('company_id', $request->company_id);
        })

        ->when($request->zone_id, function ($q) use ($request) {
            $q->whereHas('executive', function ($eq) use ($request) {
                $eq->where('zone_id', $request->zone_id);
            });
        })

        ->when($request->executive_id, function ($q) use ($request) {
            $q->where('executive_id', $request->executive_id);
        })

        ->when($request->kpi_status, function ($q) use ($request) {
            $q->where('kpi_status', $request->kpi_status);
        })

        ->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
        })

        ->orderByDesc('audit_date')
        ->orderByDesc('created_at');

    $audits = $query->paginate(25)->withQueryString();

    $companies = Company::active()->orderBy('name')->get();
    $zones = Zone::active()->orderBy('name')->get();
    $executives = Executive::where('status', '!=', 'inactive')
        ->orderBy('name')
        ->get();

    // Summary should also be for the selected date
    $todaySummary = [
        'count' => DailyAudit::whereDate('audit_date', $auditDate)->count(),
        'total_positive' => DailyAudit::whereDate('audit_date', $auditDate)->sum('positive_points'),
        'total_negative' => DailyAudit::whereDate('audit_date', $auditDate)->sum('negative_points'),
        'total_score' => DailyAudit::whereDate('audit_date', $auditDate)->sum('final_score'),
    ];

    return view('daily_audit.index', compact(
        'audits',
        'companies',
        'zones',
        'executives',
        'todaySummary'
    ));
}

    public function create()
    {
        $executives = Executive::with(['company', 'zone'])
            ->where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get();

        $recentAudits = DailyAudit::with(['executive.company', 'createdBy'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('daily_audit.create', compact('executives', 'recentAudits'));
    }

    public function store(StoreDailyAuditRequest $request)
    {
        $data      = $request->validated();
        $executive = Executive::with('company')->findOrFail($data['executive_id']);

        // Handle evidence upload
        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')
                ->store("evidence/{$executive->company->code}", 'public');
        }

        // Create the audit record
        $audit = DailyAudit::create([
            'company_id'                => $executive->company_id,
            'executive_id'              => $data['executive_id'],
            'audit_date'                => $data['audit_date'],
            'audit_type'                => strtolower($executive->company->calculation_strategy),
            'connected_calls'           => $data['connected_calls'],
            'confirmed_meetings'        => $data['confirmed_meetings'],
            'meetings_attended'         => $data['meetings_attended'],
            'crm_followup'              => $data['crm_followup'] ?? false,
            'crm_disposition_correct'   => $data['crm_disposition_correct'] ?? false,
            'first_contact_within_45min'=> $data['first_contact_within_45min'] ?? false,
            'all_leads_followed_up'     => $data['all_leads_followed_up'] ?? false,
            'warm_lead_converted'       => $data['warm_lead_converted'] ?? false,
            'cold_lead_reactivated'     => $data['cold_lead_reactivated'] ?? false,
            'rolling_day'               => $data['rolling_day'] ?? null,
            'rolling_window_days'       => $data['rolling_window_days'] ?? null,
            'rolling_meeting_count'     => $data['rolling_meeting_count'] ?? null,
            'checkpoint_result'         => $data['checkpoint_result'] ?? null,
            'evidence_path'             => $evidencePath,
            'remarks'                   => $data['remarks'] ?? null,
            'created_by'                => Auth::id(),
        ]);

        $audit->load('executive.company');

        try {
            $result = $this->orchestration->execute($audit, $data['violations'] ?? []);

            return redirect()->route('daily_audit.show', $result['audit'])
                ->with('success', "✓ Audit saved for {$executive->name}. Final Score: {$result['final_score']} pts | Tier: " . ucfirst(str_replace('_', ' ', $result['new_tier'])));
        } catch (\Exception $e) {
            $audit->delete();
            return back()->withInput()->withErrors(['error' => 'Audit calculation failed: ' . $e->getMessage()]);
        }
    }

    public function show(DailyAudit $dailyAudit)
    {
        $audit = $this->audits->findWithRelations($dailyAudit->id);
        return view('daily_audit.show', compact('audit'));
    }

    public function destroy(DailyAudit $dailyAudit)
    {
        $this->authorize('delete', $dailyAudit);

        $executiveName = $dailyAudit->executive->name;
        $date          = $dailyAudit->audit_date->toDateString();

        $this->orchestration->reverse($dailyAudit);
        $dailyAudit->delete();

        return redirect()->route('daily_audit.index')
            ->with('success', "Audit for {$executiveName} on {$date} has been deleted and scores reversed.");
    }

    /**
     * AJAX: Preview score without saving.
     **/
    public function previewScore(Request $request)
    {
        $data      = $request->validate(['executive_id' => 'required|exists:executives,id', 'audit_date' => 'required|date']);
        $executive = Executive::with('company')->findOrFail($data['executive_id']);

        $audit = new DailyAudit(array_merge($request->all(), [
            'company_id'   => $executive->company_id,
            'executive_id' => $executive->id,
        ]));
        $audit->setRelation('executive', $executive);

        try {
            $result = $this->orchestration->preview($audit, $request->input('violations', []));
            return response()->json($result);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * AJAX: Get executive info + company data when executive is selected.
     */
    public function executiveData(Executive $executive)
    {
        $executive->load(['company', 'zone']);

        $recentAudits = DailyAudit::where('executive_id', $executive->id)
            ->orderByDesc('audit_date')
            ->limit(7)
            ->get(['audit_date', 'final_score', 'positive_points', 'negative_points', 'kpi_status']);

        $rank = Executive::where('company_id', $executive->company_id)
            ->where('status', 'active')
            ->where('current_score', '>', $executive->current_score)
            ->count() + 1;

        return response()->json([
            'executive' => [
                'id'            => $executive->id,
                'name'          => $executive->name,
                'employee_id'   => $executive->employee_id,
                'company_id'    => $executive->company_id,
                'company_name'  => $executive->company->name,
                'company_strategy' => $executive->company->calculation_strategy,
                'zone_name'     => $executive->zone->name,
                'current_score' => $executive->current_score,
                'monthly_score' => $executive->monthly_score,
                'current_tier'  => $executive->current_tier,
                'tier_label'    => $executive->tier_label,
                'rank'          => $rank,
                'call_streak'   => $executive->call_streak_count,
                'meeting_streak'=> $executive->meeting_streak_count,
                
            ],
            'recent_audits' => $recentAudits,
            'strategy'      => $executive->company->calculation_strategy,
        ]);
    }
}
