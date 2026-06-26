<?php

namespace App\Policies;

use App\Models\DailyAudit;
use App\Models\User;

class DailyAuditPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('enter_daily_audit') || $user->can('view_reports');
    }

    public function view(User $user, DailyAudit $audit): bool
    {
        return $user->can('enter_daily_audit') || $user->can('view_reports');
    }

    public function create(User $user): bool
    {
        return $user->can('enter_daily_audit');
    }

    public function update(User $user, DailyAudit $audit): bool
    {
        return $user->can('enter_daily_audit') && $audit->status !== 'verified';
    }

    public function delete(User $user, DailyAudit $audit): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('CRO');
    }

    public function verify(User $user, DailyAudit $audit): bool
    {
        return $user->can('verify_recovery');
    }
}
