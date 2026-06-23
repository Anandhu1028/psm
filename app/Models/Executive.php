<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Executive extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'university_id',
        'employee_id',
        'name',
        'phone',
        'email',
        'photo',
        'zone_id',
        'department_id',
        'date_joined',
        'probation_end_date',
        'reporting_manager_id',
        'status',
        'current_score',
        'current_tier',
        // Streak tracking
        'call_streak_count',
        'meeting_streak_count',
        'best_call_streak',
        'best_meeting_streak',
        'streak_last_updated',
    ];

    protected $casts = [
        'date_joined'          => 'date',
        'probation_end_date'   => 'date',
        'streak_last_updated'  => 'date',
        'call_streak_count'    => 'integer',
        'meeting_streak_count' => 'integer',
        'best_call_streak'     => 'integer',
        'best_meeting_streak'  => 'integer',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function reportingManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function scoreTransactions(): HasMany
    {
        return $this->hasMany(ScoreTransaction::class);
    }

    public function scoreHistories(): HasMany
    {
        return $this->hasMany(ScoreHistory::class);
    }

    public function tierHistories(): HasMany
    {
        return $this->hasMany(TierHistory::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(Escalation::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

    public function pipRecords(): HasMany
    {
        return $this->hasMany(PipRecord::class);
    }

    /**
     * Helper to adjust executive score and check tier transitions.
     */
    public function updateScoreAndTier(int $pointsChange, string $reason, ?int $dailyLogId = null, ?int $ruleId = null)
    {
        $oldScore = $this->current_score;
        $newScore = $oldScore + $pointsChange;
        $this->current_score = $newScore;

        $oldTier = $this->current_tier;
        $newTier = $this->determineTierForScore($newScore);
        $this->current_tier = $newTier;

        // Perform inside db save
        $this->save();

        // Audit points transaction
        $this->scoreTransactions()->create([
            'daily_log_id' => $dailyLogId,
            'rule_id' => $ruleId,
            'type' => $pointsChange >= 0 ? 'credit' : 'debit',
            'points' => abs($pointsChange),
            'running_total' => $newScore,
            'description' => $reason,
            'transaction_date' => now()->toDateString(),
        ]);

        // Audit tier change
        if ($oldTier !== $newTier) {
            $this->tierHistories()->create([
                'old_tier' => $oldTier,
                'new_tier' => $newTier,
                'change_reason' => "Score changed from {$oldScore} to {$newScore}. Reason: " . $reason,
                'changed_at' => now(),
            ]);
        }
    }

    /**
     * Determines tier tier-name based on score value.
     */
    public function determineTierForScore(int $score): string
    {
        return app(\App\Services\TierService::class)->determineTier($this, $score);
    }
}
