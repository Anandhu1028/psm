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

        return view('reports.index', compact('data', 'type', 'universities', 'universityId'));
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
