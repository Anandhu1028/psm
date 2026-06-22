<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleEvaluationResult extends Model
{
    protected $fillable = [
        'daily_log_id',
        'executive_id',
        'university_id',
        'rule_set_id',
        'rule_id',
        'rule_code',
        'category',
        'status',
        'points',
        'message',
        'context_snapshot',
    ];

    protected $casts = [
        'points' => 'float',
        'context_snapshot' => 'array',
    ];

    public function dailyLog(): BelongsTo
    {
        return $this->belongsTo(DailyLog::class);
    }

    public function ruleSet(): BelongsTo
    {
        return $this->belongsTo(RuleSet::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }
}
