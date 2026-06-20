<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PipRecord;
use App\Models\Executive;
use Illuminate\Support\Facades\Auth;

class PipController extends Controller
{
    public function index()
    {
        $pips = PipRecord::with(['executive', 'closer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pips.index', compact('pips'));
    }

    public function create()
    {
        // Select candidates who are in Review Zone or low scores
        $executives = Executive::orderBy('name', 'asc')->get();

        return view('pips.create', compact('executives'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'executive_id' => ['required', 'exists:executives,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'target_score' => ['required', 'integer', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        $pip = PipRecord::create($validated);

        // Put executive under PIP review status
        $executive = Executive::findOrFail($validated['executive_id']);
        $executive->status = 'probation'; // Put on probationary monitoring
        $executive->save();

        return redirect()
            ->route('pips.index')
            ->with('success', "PIP successfully launched for {$executive->name}. Target score: {$pip->target_score} by {$pip->end_date->toDateString()}.");
    }
}
