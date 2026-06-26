<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leaderboard extends Model
{
    protected $fillable = [
        'company_id',
        'executive_id',
        'year',
        'month',
        'rank',
        'current_score',
        'monthly_score',
        'tier',
        'trend',
        'previous_rank',
    ];

    protected $casts = [
        'year'          => 'integer',
        'month'         => 'integer',
        'rank'          => 'integer',
        'current_score' => 'integer',
        'monthly_score' => 'integer',
        'previous_rank' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }

    public function getTrendIconAttribute(): string
    {
        return match ($this->trend) {
            'up'     => '<i class="fa-solid fa-arrow-trend-up text-success"></i>',
            'down'   => '<i class="fa-solid fa-arrow-trend-down text-danger"></i>',
            default  => '<i class="fa-solid fa-minus text-secondary"></i>',
        };
    }
}
