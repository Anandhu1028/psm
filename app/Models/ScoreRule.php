<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreRule extends Model
{
    protected $fillable = [
        'university_id',
        'rule_group',
        'rule_key',
        'rule_name',
        'value_type',
        'rule_value',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rule_value' => 'float',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
