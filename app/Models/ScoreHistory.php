<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreHistory extends Model
{
    protected $fillable = [
        'executive_id',
        'period',
        'daily_points_sum',
        'monthly_score',
        'rolling_6_month_score',
    ];

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }
}
