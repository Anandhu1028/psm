<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreTransaction extends Model
{
    protected $fillable = [
        'executive_id',
        'daily_log_id',
        'rule_id',
        'type',
        'points',
        'running_total',
        'description',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }

    public function dailyLog(): BelongsTo
    {
        return $this->belongsTo(DailyLog::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(ScoreRule::class);
    }
}
