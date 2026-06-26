<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Zone;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount([
            'executives' => fn($q) => $q->whereNull('deleted_at'),
            'zones'      => fn($q) => $q->whereNull('deleted_at'),
        ])->orderBy('name')->get();

        return view('companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['zones.executives']);

        $topExecutives = $company->executives()
            ->with('zone')
            ->where('status', '!=', 'inactive')
            ->orderByDesc('current_score')
            ->limit(10)
            ->get();

        $tierDistribution = $company->executives()
            ->where('status', '!=', 'inactive')
            ->selectRaw('current_tier, COUNT(*) as count')
            ->groupBy('current_tier')
            ->pluck('count', 'current_tier');

        return view('companies.show', compact('company', 'topExecutives', 'tierDistribution'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:100'],
            'code'                 => ['required', 'string', 'max:20', 'unique:companies,code'],
            'calculation_strategy' => ['required', 'in:tims,focuz'],
            'theme_color'          => ['nullable', 'string', 'max:20'],
            'description'          => ['nullable', 'string'],
            'status'               => ['required', 'in:active,inactive'],
        ]);

        $data['created_by'] = auth()->id();
        Company::create($data);

        return redirect()->route('companies.index')->with('success', "Company {$data['name']} created.");
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'theme_color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'in:active,inactive'],
        ]);

        $company->update($data);
        return back()->with('success', "Company updated.");
    }

    public function destroy(Company $company)
    {
        // Soft guard — only allow if no executives
        if ($company->executives()->whereNull('deleted_at')->exists()) {
            return back()->with('error', 'Cannot delete a company with existing executives.');
        }
        $company->delete();
        return redirect()->route('companies.index')->with('success', "Company deleted.");
    }
}
