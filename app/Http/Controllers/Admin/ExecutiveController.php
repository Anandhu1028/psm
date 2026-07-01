<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExecutiveRequest;
use App\Http\Requests\UpdateExecutiveRequest;
use App\Models\Company;
use App\Models\Executive;
use App\Models\Zone;
use App\Repositories\Contracts\ExecutiveRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExecutiveController extends Controller
{
    public function __construct(
        private ExecutiveRepositoryInterface $executives,
        private \App\Services\MonthlyPerformanceRankingService $monthlyRanking,
    ) {}

    public function index(Request $request)
    {
        $query = Executive::with(['company', 'zone'])
            ->whereNull('deleted_at')
            ->when($request->search, fn($q) => $q->where(fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('employee_id', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhereHas('company', fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('zone', fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ))
            ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
            ->when($request->zone_id, fn($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('name');

        $executives = $query->paginate(25)->withQueryString();

        $companies = Company::active()->orderBy('name')->get();
        $zones     = Zone::active()->orderBy('name')->get();

        return view('executives.index', compact('executives', 'companies', 'zones'));
    }

    public function create()
    {
        $companies = Company::active()->orderBy('name')->get();
        $zones     = Zone::active()->orderBy('name')->get();
        return view('executives.create', compact('companies', 'zones'));
    }

    public function store(StoreExecutiveRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('executives/photos', 'public');
        }

        $executive = $this->executives->create($data);

        return redirect()->route('executives.show', $executive)
            ->with('success', "Executive {$executive->name} created successfully.");
    }

    public function show(Executive $executive)
    {
        $executive->load([
            'company',
            'zone',
            'dailyAudits' => fn($q) => $q->orderByDesc('audit_date')->limit(30),
            'pointTransactions' => fn($q) => $q->orderByDesc('created_at')->limit(50),
            'tierHistories' => fn($q) => $q->orderByDesc('changed_at')->limit(10),
        ]);

        $monthlyScores = $executive->monthlyScores()
            ->orderByDesc('year')->orderByDesc('month')
            ->limit(12)->get();

        $companies = Company::active()->orderBy('name')->get();

        // Monthly performance card for current month
        $now = now();
        $metrics = $this->monthlyRanking->calculate($now->month, $now->year, $executive->company_id, $executive->zone_id)
            ->firstWhere('executive.id', $executive->id) ?? null;

        return view('executives.show', compact('executive', 'monthlyScores', 'companies', 'metrics'));
    }

    public function export(Request $request)
    {
        $query = Executive::with(['company', 'zone'])
            ->whereNull('deleted_at')
            ->when($request->search, fn($q) => $q->where(fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('employee_id', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
            ))
            ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
            ->when($request->zone_id, fn($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('name')
            ->get();

        $rows = $query->map(fn($exec) => [
            'Name'        => $exec->name,
            'Employee ID' => $exec->employee_id,
            'Company'     => $exec->company?->name,
            'Zone'        => $exec->zone?->name,
            'Status'      => ucfirst($exec->status),
            'Current Score' => $exec->current_score,
            'Monthly Score' => $exec->monthly_score,
            'Tier'        => $exec->tier_label,
        ])->toArray();

        return Excel::download(new ArrayExport($rows), 'executives.xlsx');
    }

    public function exportProfile(Executive $executive)
    {
        $rows = [[
            'Name'        => $executive->name,
            'Employee ID' => $executive->employee_id,
            'Company'     => $executive->company?->name,
            'Zone'        => $executive->zone?->name,
            'Status'      => ucfirst($executive->status),
            'Current Score' => $executive->current_score,
            'Monthly Score' => $executive->monthly_score,
            'Tier'        => $executive->tier_label,
            'Mobile'      => $executive->mobile,
            'Email'       => $executive->email,
            'Joined'      => $executive->date_joined?->format('Y-m-d'),
        ]];

        return Excel::download(new ArrayExport($rows), "executive_{$executive->id}.xlsx");
    }

    public function edit(Executive $executive)
    {
        $companies = Company::active()->orderBy('name')->get();
        $zones     = Zone::active()->orderBy('name')->get();
        return view('executives.edit', compact('executive', 'companies', 'zones'));
    }

    public function update(UpdateExecutiveRequest $request, Executive $executive)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($executive->photo) {
                Storage::disk('public')->delete($executive->photo);
            }
            $data['photo'] = $request->file('photo')->store('executives/photos', 'public');
        }

        $this->executives->update($executive, $data);

        return redirect()->route('executives.show', $executive)
            ->with('success', "Executive {$executive->name} updated successfully.");
    }

    public function destroy(Executive $executive)
    {
        // Use permission-based check (Spatie) — allow users with 'manage_executives'
        if (! auth()->user()->can('manage_executives')) {
            abort(403, 'This action is unauthorized.');
        }
        $name = $executive->name;
        $this->executives->delete($executive);
        return redirect()->route('executives.index')
            ->with('success', "{$name} has been removed.");
    }
}
