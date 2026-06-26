<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyScore extends Model
{
    protected $fillable = [
        'executive_id',
        'company_id',
        'year',
        'month',
        'positive_points',
        'negative_points',
        'recovery_points',
        'net_score',
        'audit_count',
    ];

    protected $casts = [
        'year'            => 'integer',
        'month'           => 'integer',
        'positive_points' => 'integer',
        'negative_points' => 'integer',
        'recovery_points' => 'integer',
        'net_score'       => 'integer',
        'audit_count'     => 'integer',
    ];

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
