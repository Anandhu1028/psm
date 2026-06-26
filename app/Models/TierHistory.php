<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TierHistory extends Model
{
    protected $fillable = [
        'executive_id',
        'company_id',
        'daily_audit_id',
        'old_tier',
        'new_tier',
        'change_reason',
        'score_at_change',
        'changed_at',
    ];

    protected $casts = [
        'changed_at'    => 'datetime',
        'score_at_change' => 'integer',
    ];

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function dailyAudit(): BelongsTo
    {
        return $this->belongsTo(DailyAudit::class);
    }
}
