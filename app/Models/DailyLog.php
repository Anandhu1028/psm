<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyLog extends Model
{
    protected $fillable = [
        'university_id',
        'date',
        'executive_id',
        'connected_calls',
        'meetings_arranged',
        'meetings_attended',
        'first_contact_within_45_min',
        'all_leads_followed_up',
        'crm_disposition_correct',
        'warm_lead_converted',
        'conduct_violation',
        'cro_remarks',
        'calculated_score',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'first_contact_within_45_min' => 'boolean',
        'all_leads_followed_up' => 'boolean',
        'crm_disposition_correct' => 'boolean',
        'warm_lead_converted' => 'boolean',
        'conduct_violation' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($log) {
            if ($log->executive && !$log->university_id) {
                $log->university_id = $log->executive->university_id;
            }
        });
    }

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scoreTransactions(): HasMany
    {
        return $this->hasMany(ScoreTransaction::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }
}
