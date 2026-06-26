<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Leaderboard;
use App\Models\Zone;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __construct(private LeaderboardService $leaderboard) {}

    public function index(Request $request)
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $query = Leaderboard::with(['executive.zone', 'company'])
            ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
            ->when($request->zone_id,    fn($q) => $q->whereHas('executive', fn($eq) => $eq->where('zone_id', $request->zone_id)))
            ->where('year', $year)
            ->where('month', $month)
            ->orderBy('rank');

        $entries   = $query->paginate(50)->withQueryString();
        $companies = Company::active()->orderBy('name')->get();
        $zones     = Zone::active()->orderBy('name')->get();

        $months = collect(range(0, 11))->map(fn($i) => now()->subMonths($i))
            ->map(fn($d) => ['year' => $d->year, 'month' => $d->month, 'label' => $d->format('M Y')])
            ->values();

        return view('leaderboards.index', compact('entries', 'companies', 'zones', 'year', 'month', 'months'));
    }

    public function refresh(Request $request)
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        Company::active()->each(fn($c) => $this->leaderboard->refresh($c->id, $year, $month));

        return back()->with('success', "Leaderboard refreshed for {$month}/{$year}.");
    }
}
