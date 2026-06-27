<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExecutiveRequest;
use App\Http\Requests\UpdateExecutiveRequest;
use App\Models\Company;
use App\Models\Executive;
use App\Models\Zone;
use App\Repositories\Contracts\ExecutiveRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ExecutiveController extends Controller
{
    public function __construct(
        private ExecutiveRepositoryInterface $executives,
    ) {}

    public function index()
    {
        $executives = Executive::with(['company', 'zone'])
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->paginate(25);

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
        $executive->load(['company', 'zone', 'dailyAudits' => fn($q) => $q->orderByDesc('audit_date')->limit(30), 'pointTransactions' => fn($q) => $q->orderByDesc('created_at')->limit(50), 'tierHistories' => fn($q) => $q->orderByDesc('changed_at')->limit(10)]);

        $monthlyScores = $executive->monthlyScores()
            ->orderByDesc('year')->orderByDesc('month')
            ->limit(12)->get();

        $companies = Company::active()->orderBy('name')->get();

        return view('executives.show', compact('executive', 'monthlyScores', 'companies'));
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
        $this->authorize('delete', $executive);
        $name = $executive->name;
        $this->executives->delete($executive);
        return redirect()->route('executives.index')
            ->with('success', "{$name} has been removed.");
    }
}
