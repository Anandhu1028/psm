<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo',
        'description',
        'theme_color',
        'calculation_strategy',
        'status',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function executives(): HasMany
    {
        return $this->hasMany(Executive::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }

    public function dailyAudits(): HasMany
    {
        return $this->hasMany(DailyAudit::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
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

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr(trim($this->name), 0, 2));
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    public function getActiveExecutivesCountAttribute(): int
    {
        return $this->executives()->where('status', 'active')->count();
    }
}
