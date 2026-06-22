<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Rule;
use Carbon\Carbon;

class MeetingWindowService
{
    public function statusFor(DailyLog $log, Rule $rule): array
    {
        $condition = $rule->condition_json ?? [];
        $windowDays = (int) ($condition['window_days'] ?? 3);
        $checkpointDay = (int) ($condition['checkpoint_day'] ?? $windowDays);
        $minimum = (int) ($condition['minimum_cumulative'] ?? 0);
        $metric = $condition['metric'] ?? 'meetings_arranged';
        $skipDays = $condition['skip_days'] ?? [];

        $date = Carbon::parse($log->date)->startOfDay();
        $startDate = $date->copy()->subDays($windowDays - 1)->toDateString();

        $priorLogs = DailyLog::where('executive_id', $log->executive_id)
            ->whereBetween('date', [$startDate, $date->toDateString()])
            ->where('id', '!=', $log->id)
            ->orderBy('date')
            ->get();

        $logsInWindow = $priorLogs->push($log)->sortBy('date')->values();
        $dayNumber = min($logsInWindow->count(), $windowDays);
        $cumulative = (int) $logsInWindow->sum($metric);

        $status = 'skipped';
        if (in_array($dayNumber, $skipDays, true)) {
            $status = 'skipped';
        } elseif ($dayNumber < $checkpointDay) {
            $status = $cumulative >= $minimum ? 'on_track' : 'pending';
        } else {
            $status = $cumulative >= $minimum ? 'passed' : 'failed';
        }

        return [
            'day_number' => $dayNumber,
            'window_days' => $windowDays,
            'checkpoint_day' => $checkpointDay,
            'cumulative_meetings' => $cumulative,
            'required_meetings' => $minimum,
            'checkpoint' => 'day_' . $checkpointDay,
            'status' => $status,
        ];
    }
}
