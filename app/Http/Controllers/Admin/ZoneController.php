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
}
