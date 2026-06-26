<?php

namespace App\Events;

use App\Models\DailyAudit;
use App\Models\Executive;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuditSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly DailyAudit $audit,
        public readonly Executive  $executive,
    ) {}
}
