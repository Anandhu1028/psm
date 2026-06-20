<?php

namespace App\Http\Controllers\CRO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\Executive;
use App\Models\Violation;
use App\Models\ScoreRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function index()
    {
        $audits = Audit::with(['executive', 'auditor'])
            ->orderBy('audit_date', 'desc')
            ->paginate(20);

        return view('audits.index', compact('audits'));
    }

    public function create()
    {
        $executives = Executive::where('status', '!=', 'inactive')
            ->orderBy('name', 'asc')
            ->get();

        return view('audits.create', compact('executives'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'executive_id' => ['required', 'exists:executives,id'],
            'lead_identifier' => ['required', 'string', 'max:100'],
            'crm_entry_verified' => ['nullable', 'boolean'],
            'call_verification_status' => ['required', 'in:verified,discrepancy,fake_lead'],
            'violation_type' => ['nullable', 'string'],
            'audit_result' => ['required', 'in:pass,fail'],
            'audit_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);

        $validated['crm_entry_verified'] = $request->has('crm_entry_verified');
        $validated['audited_by'] = Auth::id();

        try {
            DB::beginTransaction();

            $audit = Audit::create($validated);
            $executive = Executive::findOrFail($validated['executive_id']);

            // If audit failed, trigger point deduction transaction and violation report!
            if ($audit->audit_result === 'fail') {
                $deductKey = 'call_violation';
                $vType = 'call';

                if ($audit->call_verification_status === 'fake_lead') {
                    $deductKey = 'conduct_violation';
                    $vType = 'conduct';
                }

                $rule = ScoreRule::where('rule_key', $deductKey)->first();
                $deductionPoints = $rule ? (int) $rule->rule_value : -10;

                // Create a violation
                $violation = Violation::create([
                    'executive_id' => $executive->id,
                    'violation_type' => $vType,
                    'points_deducted' => abs($deductionPoints),
                    'status' => 'active',
                    'date_committed' => $audit->audit_date,
                    'created_by' => Auth::id(),
                    'resolution_remarks' => "Generated via failed audit ID: {$audit->id}. Status: {$audit->call_verification_status}",
                ]);

                // Update score and ledger
                $executive->updateScoreAndTier(
                    $deductionPoints,
                    "Audit failure deduction for {$audit->lead_identifier}: " . ucfirst($audit->call_verification_status),
                    null,
                    $rule?->id
                );
            }

            DB::commit();

            return redirect()
                ->route('audits.index')
                ->with('success', 'Audit log successfully recorded and score recalculations applied.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error recording audit: ' . $e->getMessage()]);
        }
    }
}
