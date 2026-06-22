<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class University extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo',
        'description',
        'theme_color',
        'tier_colors',
        'status',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'tier_colors' => 'array',
        'settings' => 'array',
    ];

    public function executives(): HasMany
    {
        return $this->hasMany(Executive::class);
    }

    public function scoreRules(): HasMany
    {
        return $this->hasMany(ScoreRule::class);
    }

    public function ruleSets(): HasMany
    {
        return $this->hasMany(RuleSet::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }

    public function activeRuleSet()
    {
        return $this->hasOne(RuleSet::class)->where('status', 'active')->latestOfMany('version');
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    /**
     * Get the logo URL from public storage.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return Storage::url('universities/' . $this->logo);
        }
        return null;
    }

    /**
     * Get initials for placeholder avatar (e.g. TIMS -> T)
     */
    public function getInitialsAttribute(): string
    {
        if (empty($this->name)) {
            return 'U';
        }
        return strtoupper(substr(trim($this->name), 0, 1));
    }
}
