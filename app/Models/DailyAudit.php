<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyAudit extends Model
{
    protected $fillable = [
        'company_id',
        'executive_id',
        'audit_date',
        'audit_type',
        'status',
        'connected_calls',
        'confirmed_meetings',
        'meetings_attended',
        'crm_followup',
        'crm_disposition_correct',
        'first_contact_within_45min',
        'all_leads_followed_up',
        'warm_lead_converted',
        'cold_lead_reactivated',
        'rolling_day',
        'rolling_window_days',
        'rolling_meeting_count',
        'checkpoint_result',
        'positive_points',
        'negative_points',
        'recovery_points',
        'final_score',
        'kpi_status',
        'violation_status',
        'tier_at_audit',
        'evidence_path',
        'remarks',
        'created_by',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'audit_date'                => 'date',
        'verified_at'               => 'datetime',
        'crm_followup'              => 'boolean',
        'crm_disposition_correct'   => 'boolean',
        'first_contact_within_45min'=> 'boolean',
        'all_leads_followed_up'     => 'boolean',
        'warm_lead_converted'       => 'boolean',
        'cold_lead_reactivated'     => 'boolean',
        'connected_calls'           => 'integer',
        'confirmed_meetings'        => 'integer',
        'meetings_attended'         => 'integer',
        'rolling_day'               => 'integer',
        'rolling_window_days'       => 'integer',
        'rolling_meeting_count'     => 'integer',
        'positive_points'           => 'integer',
        'negative_points'           => 'integer',
        'recovery_points'           => 'integer',
        'final_score'               => 'integer',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function tierHistories(): HasMany
    {
        return $this->hasMany(TierHistory::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('audit_date', $date);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForExecutive($query, int $executiveId)
    {
        return $query->where('executive_id', $executiveId);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getEvidenceUrlAttribute(): ?string
    {
        if ($this->evidence_path) {
            return asset('storage/' . $this->evidence_path);
        }
        return null;
    }

    public function getKpiStatusBadgeAttribute(): string
    {
        return match ($this->kpi_status) {
            'passed'  => '<span class="badge bg-success-subtle text-success">KPI Passed</span>',
            'failed'  => '<span class="badge bg-danger-subtle text-danger">KPI Failed</span>',
            default   => '<span class="badge bg-secondary-subtle text-secondary">Pending</span>',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'verified' => '<span class="badge badge-status-verified">Verified</span>',
            'pending'  => '<span class="badge badge-status-pending">Pending</span>',
            'draft'    => '<span class="badge badge-status-draft">Draft</span>',
            default    => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
