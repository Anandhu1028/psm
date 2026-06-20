<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    protected $fillable = [
        'executive_id',
        'lead_identifier',
        'crm_entry_verified',
        'call_verification_status',
        'violation_type',
        'audit_result',
        'audit_date',
        'audited_by',
        'remarks',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'crm_entry_verified' => 'boolean',
    ];

    public function executive(): BelongsTo
    {
        return $this->belongsTo(Executive::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_by');
    }
}
