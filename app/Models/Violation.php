<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Violation extends Model
{
    protected $fillable = [
        'university_id',
        'executive_id',
        'daily_log_id',
        'violation_type',
        'points_deducted',
        'description',
        'violation_subtype',
        'status',
        'resolution_remarks',
        'date_committed',
        'created_by',
    ];

    protected $casts = [
        'date_committed' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($violation) {
            if ($violation->executive && !$violation->university_id) {
                $violation->university_id = $violation->executive->university_id;
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

    public function dailyLog(): BelongsTo
    {
        return $this->belongsTo(DailyLog::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
