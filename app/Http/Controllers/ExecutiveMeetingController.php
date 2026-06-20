<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\Executive;
use Illuminate\Support\Facades\DB;

class ExecutiveMeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::with('executive')
            ->orderBy('meeting_date', 'desc')
            ->orderBy('meeting_time', 'desc')
            ->paginate(20);

        // Fetch meeting stats
        $totalMeetings = Meeting::count();
        $attendedCount = Meeting::where('status', 'attended')->count();
        $attendanceRate = $totalMeetings > 0 ? round(($attendedCount / $totalMeetings) * 100, 1) : 0;

        return view('meetings.index', compact('meetings', 'totalMeetings', 'attendedCount', 'attendanceRate'));
    }

    public function create()
    {
        $executives = Executive::where('status', '!=', 'inactive')
            ->orderBy('name', 'asc')
            ->get();

        return view('meetings.create', compact('executives'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'executive_id' => ['required', 'exists:executives,id'],
            'lead_name' => ['required', 'string', 'max:150'],
            'meeting_date' => ['required', 'date'],
            'meeting_time' => ['required'],
            'meeting_type' => ['required', 'in:zoom,phone,in_person'],
            'status' => ['required', 'in:scheduled,attended,missed,cancelled'],
            'crm_reference' => ['nullable', 'string', 'max:100'],
            'arranged_date' => ['required', 'date'],
        ]);

        $meeting = Meeting::create($validated);

        // If the meeting is marked as 'attended' upon creation, we should award point bonus!
        // In daily logging, we enter points dynamically, but if it is registered through meetings tracker,
        // we can also award points or update DailyLog. For consistency, points calculations are aggregated
        // in Daily Performance logs, but we track meetings individually here.
        
        return redirect()
            ->route('meetings.index')
            ->with('success', "Meeting scheduled for lead {$meeting->lead_name} successfully recorded.");
    }
}
