<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Escalation extends Model
{
    protected $fillable = [
        'executive_id',
        'type',
        'severity',
        'status',
        'trigger_reason',
        'resolved_at',
        'resolved_by',
        'resolution_remarks',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
