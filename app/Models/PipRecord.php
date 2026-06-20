<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipRecord extends Model
{
    protected $table = 'pip_records';

    protected $fillable = [
        'executive_id',
        'start_date',
        'end_date',
        'target_score',
        'status',
        'remarks',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
