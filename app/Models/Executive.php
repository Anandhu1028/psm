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
        'company_id',
        'zone_id',
        'employee_id',
        'name',
        'mobile',
        'email',
        'photo',
        'date_joined',
        'probation_end_date',
        'status',
        'notes',
        'current_score',
        'monthly_score',
        'current_tier',
        'call_streak_count',
        'meeting_streak_count',
        'best_call_streak',
        'best_meeting_streak',
        'streak_last_updated',
        'monthly_admission_target',
    ];

    protected $casts = [
        'date_joined'          => 'date',
        'probation_end_date'   => 'date',
        'streak_last_updated'  => 'date',
        'call_streak_count'    => 'integer',
        'meeting_streak_count' => 'integer',
        'best_call_streak'     => 'integer',
        'best_meeting_streak'  => 'integer',
        'current_score'        => 'integer',
        'monthly_score'        => 'integer',
        'monthly_admission_target' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function dailyAudits(): HasMany
    {
        return $this->hasMany(DailyAudit::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function monthlyScores(): HasMany
    {
        return $this->hasMany(MonthlyScore::class);
    }

    public function tierHistories(): HasMany
    {
        return $this->hasMany(TierHistory::class);
    }

    public function leaderboards(): HasMany
    {
        return $this->hasMany(Leaderboard::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForZone($query, int $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getTierBadgeClassAttribute(): string
    {
        return match ($this->current_tier) {
            'platinum'    => 'badge-tier-platinum',
            'gold'        => 'badge-tier-gold',
            'silver'      => 'badge-tier-silver',
            'bronze'      => 'badge-tier-bronze',
            'review_zone' => 'badge-tier-review',
            default       => 'badge-tier-bronze',
        };
    }

    public function getTierLabelAttribute(): string
    {
        return match ($this->current_tier) {
            'platinum'    => 'Platinum',
            'gold'        => 'Gold',
            'silver'      => 'Silver',
            'bronze'      => 'Bronze',
            'review_zone' => 'Review Zone',
            default       => 'Bronze',
        };
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        if (count($parts) >= 2) {
            return strtoupper($parts[0][0] . $parts[1][0]);
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}
