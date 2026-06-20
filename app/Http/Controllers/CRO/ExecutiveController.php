<?php

namespace App\Http\Controllers\CRO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Executive;
use App\Models\Zone;
use App\Models\Department;
use App\Models\User;
use App\Models\University;
use Illuminate\Support\Facades\Auth;

class ExecutiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Executive::with(['zone', 'department', 'university']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Zone filter
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        // University filter
        $activeUniId = session('active_university_id');
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
        } elseif ($activeUniId) {
            $query->where('university_id', $activeUniId);
        }

        // Tier filter
        if ($request->filled('tier')) {
            $query->where('current_tier', $request->tier);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Department filter
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Date Range filter
        if ($request->filled('date_from')) {
            $query->whereDate('date_joined', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_joined', '<=', $request->date_to);
        }

        $executives = $query->orderBy('name', 'asc')->paginate(15);
        $zones = Zone::all();
        $departments = Department::all();
        $managers = User::role(['Zonal Manager', 'CRO', 'Super Admin'])->get();
       $universities = University::all();
        $allUniversities = $universities;

        $activeUniversity = null;

        if ($activeUniId) {
            $activeUniversity = University::find($activeUniId);
        }

       return view('executives.index', [
            'executives'      => $executives,
            'zones'           => $zones,
            'universities'    => $universities,
            'allUniversities' => $allUniversities,
            'departments'     => $departments,
            'managers'        => $managers,
            'activeUniversity'=> $activeUniversity,
        ]);
    }

    public function create()
    {
        $zones = Zone::all();
        $departments = Department::all();
        $managers = User::role(['Zonal Manager', 'CRO', 'Super Admin'])->get();
        $universities = University::all();

        return view('executives.create', compact('zones', 'departments', 'managers', 'universities'));
    }

    public function store(Request $request)
    {
        // Auto-generate employee_id if omitted
        if (!$request->filled('employee_id')) {
            $latest = Executive::orderBy('id', 'desc')->first();
            $num = $latest ? ((int) preg_replace('/[^0-9]/', '', $latest->employee_id) + 1) : 1;
            $request->merge(['employee_id' => 'EMP' . str_pad($num, 3, '0', STR_PAD_LEFT)]);
        }

        // Auto-generate probation_end_date if omitted (6 months from date_joined)
        if (!$request->filled('probation_end_date') && $request->filled('date_joined')) {
            $dateJoined = new \DateTime($request->date_joined);
            $dateJoined->modify('+6 months');
            $request->merge(['probation_end_date' => $dateJoined->format('Y-m-d')]);
        }

        $validated = $request->validate([
            'university_id' => ['required', 'exists:universities,id'],
            'employee_id' => ['required', 'string', 'max:50', 'unique:executives,employee_id'],
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', 'unique:executives,email'],
            'zone_id' => ['required', 'exists:zones,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'date_joined' => ['required', 'date'],
            'probation_end_date' => ['required', 'date', 'after:date_joined'],
            'reporting_manager_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:active,inactive,probation'],
            'current_tier' => ['nullable', 'in:bronze,silver,gold,platinum,review_zone'],
        ]);

        $validated['current_score'] = 0;
        $validated['current_tier'] = $request->input('current_tier', 'bronze');

        $executive = Executive::create($validated);

        // Record initial tier history
        $executive->tierHistories()->create([
            'old_tier' => 'none',
            'new_tier' => $validated['current_tier'],
            'change_reason' => 'Initial profile setup.',
            'changed_at' => now(),
        ]);

        return redirect()
            ->route('executives.index')
            ->with('success', "Executive {$executive->name} has been added successfully to the system roster.");
    }

    public function scorecard(Executive $executive)
    {
        $executive->load([
            'zone',
            'department',
            'reportingManager',
            'university',
            'tierHistories',
            'scoreTransactions' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(20);
            },
            'violations' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'pipRecords' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'escalations' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        return view('executives.scorecard', compact('executive'));
    }

    public function destroy(Executive $executive)
    {
        $name = $executive->name;
        $executive->delete();

        return redirect()
            ->route('executives.index')
            ->with('success', "Executive {$name} has been deleted successfully from the system roster.");
    }
}
