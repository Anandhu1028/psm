<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleSetAudit extends Model
{
    protected $fillable = [
        'rule_set_id', 'university_id', 'published_by', 'published_at', 'snapshot', 'notes'
    ];

    protected $casts = [
        'snapshot' => 'array',
        'published_at' => 'datetime',
    ];

    public function ruleSet()
    {
        return $this->belongsTo(RuleSet::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }
}
