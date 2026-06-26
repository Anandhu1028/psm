<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rule extends Model
{
    protected $fillable = [
        'company_id',
        'category',
        'calculation_type',
        'code',
        'name',
        'points',
        'threshold_min',
        'threshold_max',
        'operator',
        'threshold_value',
        'input_metric',
        'is_active',
        'sort_order',
        'condition_json',
        'action_json',
        'description',
    ];

    protected $casts = [
        'points'         => 'float',
        'threshold_min'  => 'float',
        'threshold_max'  => 'float',
        'threshold_value'=> 'float',
        'is_active'      => 'boolean',
        'sort_order'     => 'integer',
        'condition_json' => 'array',
        'action_json'    => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
