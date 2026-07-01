<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    protected $fillable = [
        'company_id',
        'executive_id',
        'daily_audit_id',
        'rule_id',
        'audit_date',
        'category',
        'rule_code',
        'rule_name',
        'points',
        'type',
        'running_total',
        'evidence',
        'created_by',
    ];

    protected $casts = [
        'audit_date'    => 'date',
        'points'        => 'integer',
        'running_total' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }

    public function dailyAudit(): BelongsTo
    {
        return $this->belongsTo(DailyAudit::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getSignedPointsAttribute(): int
    {
        return $this->type === 'credit' ? $this->points : -$this->points;
    }

    public function getCategoryBadgeAttribute(): string
    {
        return match ($this->category) {
            'positive' => '<span class="badge bg-success-subtle text-success">Positive</span>',
            'negative' => '<span class="badge bg-danger-subtle text-danger">Negative</span>',
            'recovery' => '<span class="badge bg-info-subtle text-info">Recovery</span>',
            'quality_bonus' => '<span class="badge bg-violet-subtle text-violet">Quality Bonus</span>',
            default    => '<span class="badge bg-secondary-subtle text-secondary">KPI</span>',
        };
    }
}
