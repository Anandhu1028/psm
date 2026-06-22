<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rule extends Model
{
    protected $fillable = [
        'rule_set_id',
        'university_id',
        'category',
        'code',
        'name',
        'description',
        'input_metric',
        'operator',
        'threshold_value',
        'threshold_to',
        'points',
        'calculation_type',
        'condition_json',
        'action_json',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'threshold_value' => 'float',
        'threshold_to' => 'float',
        'points' => 'float',
        'condition_json' => 'array',
        'action_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function ruleSet(): BelongsTo
    {
        return $this->belongsTo(RuleSet::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
