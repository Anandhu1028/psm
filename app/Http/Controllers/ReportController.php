<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Executive;
use App\Models\DailyLog;
use App\Models\Violation;
use App\Models\PipRecord;
use App\Models\Zone;
use App\Models\University;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'daily');
        $universityId = $request->get('university_id', session('active_university_id'));
        $data = collect();
        $universities = University::all();

        if ($type === 'daily') {
            $date = $request->get('date', Carbon::today()->toDateString());
            $data = DailyLog::with('executive')
                ->where('date', $date)
                ->when($universityId, function($q) use($universityId) {
                    $q->whereHas('executive', fn($eq) => $eq->where('university_id', $universityId));
                })
                ->get();
        } elseif ($type === 'weekly') {
            $start = $request->get('start_date', Carbon::today()->subDays(7)->toDateString());
            $end = $request->get('end_date', Carbon::today()->toDateString());
            
            $data = DailyLog::with('executive')
                ->whereBetween('date', [$start, $end])
                ->when($universityId, function($q) use($universityId) {
                    $q->whereHas('executive', fn($eq) => $eq->where('university_id', $universityId));
                })
                ->select('executive_id', 
                    DB::raw('SUM(connected_calls) as calls'),
                    DB::raw('SUM(meetings_arranged) as arranged'),
                    DB::raw('SUM(meetings_attended) as attended'),
                    DB::raw('SUM(calculated_score) as total_score')
                )
                ->groupBy('executive_id')
                ->get();
        } elseif ($type === 'monthly') {
            // Fetch all executives with their current scores and monthly aggregates
            $monthStr = $request->get('month', Carbon::now()->toDateString());
            $month = Carbon::parse($monthStr);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            $prevMonthStart = $month->copy()->subMonth()->startOfMonth();
            $prevMonthEnd = $month->copy()->subMonth()->endOfMonth();

            $executives = Executive::with('zone', 'university')
                ->when($universityId, fn($q) => $q->where('university_id', $universityId))
                ->where('status', 'active')
                ->get();

            $monthlyData = $executives->map(function($exec) use($monthStart, $monthEnd, $prevMonthStart, $prevMonthEnd) {
                // Current month metrics
                $currentMonth = DailyLog::where('executive_id', $exec->id)
                    ->whereBetween('date', [$monthStart, $monthEnd])
                    ->get();

                // Previous month metrics
                $prevMonth = DailyLog::where('executive_id', $exec->id)
                    ->whereBetween('date', [$prevMonthStart, $prevMonthEnd])
                    ->get();

                $currentScore = $currentMonth->sum('calculated_score');
                $prevScore = $prevMonth->sum('calculated_score');
                $scoreChange = $currentScore - $prevScore;

                // Tier color mapping
                $tierColors = [
                    'gold' => '#f39c12',
                    'platinum' => '#9b59b6',
                    'silver' => '#95a5a6',
                    'bronze' => '#d35400',
                    'review_zone' => '#e74c3c'
                ];

                return (object) [
                    'id' => $exec->id,
                    'name' => $exec->name,
                    'employee_id' => $exec->employee_id,
                    'current_tier' => $exec->current_tier,
                    'tierColor' => $tierColors[$exec->current_tier] ?? '#95a5a6',
                    'current_score' => $exec->current_score,
                    'prev_score' => $prevScore,
                    'score_change' => $scoreChange,
                    'conversion_target' => 10,
                    'conversions' => $currentMonth->sum(function($log) { return $log->warm_lead_converted ? 1 : 0; }),
                    'meetings_arranged' => $currentMonth->sum('meetings_arranged'),
                    'meetings_attended' => $currentMonth->sum('meetings_attended'),
                    'cro_notes' => 'Monitor performance trends'
                ];
            })->sortByDesc('current_score');
        } elseif ($type === 'zonal') {
            $data = Zone::withAvg(['executives as executives_avg_current_score' => function($q) use($universityId) {
                    if ($universityId) $q->where('university_id', $universityId);
                }], 'current_score')
                ->withCount(['executives as executives_count' => function($q) use($universityId) {
                    if ($universityId) $q->where('university_id', $universityId);
                }])
                ->get();
        } elseif ($type === 'tier') {
            $data = Executive::select('current_tier', DB::raw('count(*) as count'))
                ->when($universityId, fn($q) => $q->where('university_id', $universityId))
                ->groupBy('current_tier')
                ->get();
        } elseif ($type === 'violation') {
            $data = Violation::with(['executive', 'creator'])
                ->when($universityId, function($q) use($universityId) {
                    $q->whereHas('executive', fn($eq) => $eq->where('university_id', $universityId));
                })
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get();
        } elseif ($type === 'pip') {
            $data = PipRecord::with('executive')
                ->when($universityId, function($q) use($universityId) {
                    $q->whereHas('executive', fn($eq) => $eq->where('university_id', $universityId));
                })
                ->get();
        }

        $monthlyData = $monthlyData ?? null;
        return view('reports.index', compact('data', 'monthlyData', 'type', 'universities', 'universityId'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'daily');
        $universityId = $request->get('university_id', session('active_university_id'));
        $fileName = "tims_report_{$type}_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($type, $request, $universityId) {
            $file = fopen('php://output', 'w');

            if ($type === 'daily') {
                fputcsv($file, ['Date', 'Employee ID', 'Name', 'University', 'Calls Connected', 'Meetings Arranged', 'Meetings Attended', 'Daily Score']);
                $date = $request->get('date', Carbon::today()->toDateString());
                $logs = DailyLog::with(['executive.university'])
                    ->where('date', $date)
                    ->when($universityId, function($q) use($universityId) {
                        $q->whereHas('executive', fn($eq) => $eq->where('university_id', $universityId));
                    })
                    ->get();
                foreach ($logs as $row) {
                    fputcsv($file, [
                        $row->date->toDateString(),
                        $row->executive->employee_id,
                        $row->executive->name,
                        $row->executive->university->name ?? '—',
                        $row->connected_calls,
                        $row->meetings_arranged,
                        $row->meetings_attended,
                        $row->calculated_score
                    ]);
                }
            } elseif ($type === 'weekly') {
                fputcsv($file, ['Employee ID', 'Name', 'University', 'Cumulative Calls', 'Cumulative Arranged', 'Cumulative Attended', 'Points Earned']);
                $start = $request->get('start_date', Carbon::today()->subDays(7)->toDateString());
                $end = $request->get('end_date', Carbon::today()->toDateString());
                $rows = DailyLog::with(['executive.university'])
                    ->whereBetween('date', [$start, $end])
                    ->when($universityId, function($q) use($universityId) {
                        $q->whereHas('executive', fn($eq) => $eq->where('university_id', $universityId));
                    })
                    ->select('executive_id', 
                        DB::raw('SUM(connected_calls) as calls'),
                        DB::raw('SUM(meetings_arranged) as arranged'),
                        DB::raw('SUM(meetings_attended) as attended'),
                        DB::raw('SUM(calculated_score) as total_score')
                    )
                    ->groupBy('executive_id')
                    ->get();
                foreach ($rows as $row) {
                    fputcsv($file, [
                        $row->executive->employee_id,
                        $row->executive->name,
                        $row->executive->university->name ?? '—',
                        $row->calls,
                        $row->arranged,
                        $row->attended,
                        $row->total_score
                    ]);
                }
            } elseif ($type === 'monthly') {
                fputcsv($file, ['Executive', 'Employee ID', 'Current Tier', '6M Active Score', 'Prev Month Score', 'Monthly Change', 'Conversion Target', 'Conversions', 'Meetings Arranged', 'Meetings Attended', 'CRO Notes']);
                $monthStr = $request->get('month', Carbon::now()->toDateString());
                $month = Carbon::parse($monthStr);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();
                $prevMonthStart = $month->copy()->subMonth()->startOfMonth();
                $prevMonthEnd = $month->copy()->subMonth()->endOfMonth();

                $executives = Executive::with('zone', 'university')
                    ->when($universityId, fn($q) => $q->where('university_id', $universityId))
                    ->where('status', 'active')
                    ->orderByDesc('current_score')
                    ->get();

                foreach ($executives as $exec) {
                    $currentMonth = DailyLog::where('executive_id', $exec->id)
                        ->whereBetween('date', [$monthStart, $monthEnd])
                        ->get();
                    $prevMonth = DailyLog::where('executive_id', $exec->id)
                        ->whereBetween('date', [$prevMonthStart, $prevMonthEnd])
                        ->get();

                    $prevScore = $prevMonth->sum('calculated_score');
                    $scoreChange = $exec->current_score - $prevScore;

                    fputcsv($file, [
                        $exec->name,
                        $exec->employee_id,
                        str_replace('_', ' ', ucwords($exec->current_tier)),
                        $exec->current_score,
                        $prevScore,
                        $scoreChange,
                        10,
                        $currentMonth->sum(function($log) { return $log->warm_lead_converted ? 1 : 0; }),
                        $currentMonth->sum('meetings_arranged'),
                        $currentMonth->sum('meetings_attended'),
                        'Monitor performance trends'
                    ]);
                }
            } elseif ($type === 'zonal') {
                fputcsv($file, ['Zone Code', 'Zone Name', 'Executives Count', 'Average Score']);
                $zones = Zone::withAvg(['executives as executives_avg_current_score' => function($q) use($universityId) {
                        if ($universityId) $q->where('university_id', $universityId);
                    }], 'current_score')
                    ->withCount(['executives as executives_count' => function($q) use($universityId) {
                        if ($universityId) $q->where('university_id', $universityId);
                    }])
                    ->get();
                foreach ($zones as $row) {
                    fputcsv($file, [
                        $row->code,
                        $row->name,
                        $row->executives_count,
                        round($row->executives_avg_current_score, 1)
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
