<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Executive;
use Carbon\Carbon;

/**
 * StreakService
 *
 * Tracks consecutive-day call and meeting streaks for each executive.
 * Rules:
 *  - Call streak: each day that connected_calls >= 40 increments the streak.
 *  - Meeting streak: each day that meetings_attended >= 1 increments the streak.
 *  - Sundays are excluded (streak neither increments nor resets on Sundays).
 *  - Any working day that misses the threshold resets the streak to 0.
 *  - A streak bonus rule is awarded by the DynamicRuleEngineService when
 *    streak_count reaches 7 (injected into context as call_streak_7 / meeting_streak_7).
 */
class StreakService
{
    /** Minimum calls in a day to count towards the call streak */
    public const CALL_THRESHOLD = 40;

    /** Minimum meetings attended in a day to count towards the meeting streak */
    public const MEETING_THRESHOLD = 1;

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Recalculate and persist the call streak for the executive based on
     * the daily log that was just saved. Call this AFTER the log is committed.
     */
    public function updateCallStreak(Executive $executive, DailyLog $log): void
    {
        $date = Carbon::parse($log->date);

        // Sundays don't count — skip without resetting
        if ($date->isSunday()) {
            return;
        }

        $qualifies = (int) $log->connected_calls >= self::CALL_THRESHOLD;

        if ($qualifies) {
            $newStreak = (int) $executive->call_streak_count + 1;
        } else {
            $newStreak = 0;
        }

        $executive->call_streak_count   = $newStreak;
        $executive->best_call_streak    = max((int) $executive->best_call_streak, $newStreak);
        $executive->streak_last_updated = $date->toDateString();
        $executive->save();
    }

    /**
     * Recalculate and persist the meeting streak for the executive.
     */
    public function updateMeetingStreak(Executive $executive, DailyLog $log): void
    {
        $date = Carbon::parse($log->date);

        if ($date->isSunday()) {
            return;
        }

        $qualifies = (int) $log->meetings_attended >= self::MEETING_THRESHOLD;

        if ($qualifies) {
            $newStreak = (int) $executive->meeting_streak_count + 1;
        } else {
            $newStreak = 0;
        }

        $executive->meeting_streak_count  = $newStreak;
        $executive->best_meeting_streak   = max((int) $executive->best_meeting_streak, $newStreak);
        $executive->streak_last_updated   = $date->toDateString();
        $executive->save();
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Helpers used by the Rule Engine context builder
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns true when the call streak will reach 7 consecutive qualifying days
     * after the current log is applied (streak_count already updated by store()).
     */
    public function hasCallStreak7(Executive $executive): bool
    {
        return (int) $executive->call_streak_count >= 7;
    }

    /**
     * Returns true when the meeting streak will reach 7 consecutive qualifying days.
     */
    public function hasMeetingStreak7(Executive $executive): bool
    {
        return (int) $executive->meeting_streak_count >= 7;
    }

    /**
     * Build an array of streak context values suitable for injection into the
     * DynamicRuleEngineService context (used by streak-type rules).
     */
    public function contextFor(Executive $executive): array
    {
        return [
            'call_streak_count'    => (int) $executive->call_streak_count,
            'meeting_streak_count' => (int) $executive->meeting_streak_count,
            'call_streak_7'        => (int) $executive->call_streak_count >= 7,
            'meeting_streak_7'     => (int) $executive->meeting_streak_count >= 7,
            'best_call_streak'     => (int) $executive->best_call_streak,
            'best_meeting_streak'  => (int) $executive->best_meeting_streak,
        ];
    }
}
