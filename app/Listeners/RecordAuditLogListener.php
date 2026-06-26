<?php

namespace App\Listeners;

use App\Events\AuditSubmitted;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordAuditLogListener implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(AuditSubmitted $event): void
    {
        // Additional async audit log entry for the executive score update
        AuditLog::create([
            'auditable_type' => get_class($event->executive),
            'auditable_id'   => $event->executive->id,
            'action'         => 'score_updated',
            'new_values'     => [
                'current_score' => $event->executive->current_score,
                'current_tier'  => $event->executive->current_tier,
                'monthly_score' => $event->executive->monthly_score,
            ],
            'description'  => "Score updated after audit #{$event->audit->id} on {$event->audit->audit_date->toDateString()}",
            'performed_by' => $event->audit->created_by,
        ]);
    }
}
