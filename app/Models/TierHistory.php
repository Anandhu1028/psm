<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TierHistory extends Model
{
    protected $fillable = [
        'executive_id',
        'old_tier',
        'new_tier',
        'change_reason',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }
}
