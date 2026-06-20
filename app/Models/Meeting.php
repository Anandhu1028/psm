<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Meeting extends Model
{
    protected $fillable = [
        'university_id',
        'executive_id',
        'lead_name',
        'meeting_date',
        'meeting_time',
        'meeting_type',
        'status',
        'crm_reference',
        'arranged_date',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'arranged_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($meeting) {
            if ($meeting->executive && !$meeting->university_id) {
                $meeting->university_id = $meeting->executive->university_id;
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

    /**
     * Check if meeting attended/scheduled within 2 days of arranging.
     */
    public function getPassedTwoDayCheckpointAttribute(): bool
    {
        if (!$this->arranged_date) return false;
        return $this->arranged_date->diffInDays($this->meeting_date) <= 2;
    }

    /**
     * Check if meeting attended/scheduled within 3 days of arranging.
     */
    public function getPassedThreeDayCheckpointAttribute(): bool
    {
        if (!$this->arranged_date) return false;
        return $this->arranged_date->diffInDays($this->meeting_date) <= 3;
    }
}
