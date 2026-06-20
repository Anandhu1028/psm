<?php

namespace App\Http\Controllers\CRO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyLog;
use App\Models\Executive;
use App\Services\PointEngineService;
use App\Services\EscalationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DailyLogController extends Controller
{
    protected $engine;
    protected $escalator;

    public function __construct(PointEngineService $engine, EscalationService $escalator)
    {
        $this->engine = $engine;
        $this->escalator = $escalator;
    }

    public function index()
    {
        $logs = DailyLog::with(['executive', 'creator'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('daily_logs.index', compact('logs'));
    }

    public function create()
    {
        $executives = Executive::where('status', '!=', 'inactive')
            ->orderBy('name', 'asc')
            ->get();

        return view('daily_logs.create', compact('executives'));
    }

    public function store(Request $request)
    {
        // Convert checkbox existence to explicit boolean values before validation runs
        $request->merge([
            'first_contact_within_45_min' => $request->has('first_contact_within_45_min') ? 1 : 0,
            'all_leads_followed_up' => $request->has('all_leads_followed_up') ? 1 : 0,
            'crm_disposition_correct' => $request->has('crm_disposition_correct') ? 1 : 0,
            'warm_lead_converted' => $request->has('warm_lead_converted') ? 1 : 0,
            'conduct_violation' => $request->has('conduct_violation') ? 1 : 0,
        ]);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'executive_id' => ['required', 'exists:executives,id'],
            'connected_calls' => ['required', 'integer', 'min:0'],
            'meetings_arranged' => ['required', 'integer', 'min:0'],
            'meetings_attended' => ['required', 'integer', 'min:0', 'lte:meetings_arranged'],
            'first_contact_within_45_min' => ['required', 'boolean'],
            'all_leads_followed_up' => ['required', 'boolean'],
            'crm_disposition_correct' => ['required', 'boolean'],
            'warm_lead_converted' => ['required', 'boolean'],
            'conduct_violation' => ['required', 'boolean'],
            'cro_remarks' => ['nullable', 'string'],
        ]);

        $validated['created_by'] = Auth::id();

        try {
            DB::beginTransaction();

            // Store Daily Log
            $log = DailyLog::updateOrCreate(
                [
                    'date' => $validated['date'],
                    'executive_id' => $validated['executive_id']
                ],
                $validated
            );

            // Compute Points and Apply
            $pointsEarned = $this->engine->calculateAndApply($log);

            // Trigger Escalation Audits
            $executive = Executive::find($validated['executive_id']);
            $this->escalator->checkForEscalations($executive);

            DB::commit();

            return redirect()
                ->route('daily_logs.index')
                ->with('success', "Daily performance log saved successfully. Points calculated: {$pointsEarned} points applied to {$executive->name}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while saving the log: ' . $e->getMessage()]);
        }
    }
}
