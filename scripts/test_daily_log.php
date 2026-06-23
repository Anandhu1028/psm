<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Executive;
use App\Models\DailyLog;
use App\Models\User;

$exec = Executive::first();
if (!$exec) {
    echo "No executive found\n";
    exit(1);
}

$date = now()->toDateString();
$existing = DailyLog::where('date', $date)->where('executive_id', $exec->id)->first();
if ($existing) {
    $log = $existing;
    $log->connected_calls = 72;
    $log->meetings_arranged = 2;
    $log->meetings_attended = 1;
    $log->first_contact_within_45_min = true;
    $log->all_leads_followed_up = true;
    $log->crm_disposition_correct = true;
    $log->warm_lead_converted = false;
} else {
    $log = new DailyLog([
        'date' => $date,
        'executive_id' => $exec->id,
        'connected_calls' => 72,
        'meetings_arranged' => 2,
        'meetings_attended' => 1,
        'first_contact_within_45_min' => true,
        'all_leads_followed_up' => true,
        'crm_disposition_correct' => true,
        'warm_lead_converted' => false,
    ]);
    $log->setRelation('executive', $exec);
    $log->university_id = $exec->university_id;
    // set created_by to an existing user to satisfy DB FK
    $user = User::first();
    $log->created_by = $user ? $user->id : 1;
}

$engine = app(\App\Services\DynamicRuleEngineService::class);
$result = $engine->preview($log, []);

echo "Preview Result:\n";
print_r($result);

// Now try calculateAndApply (will write to DB)
$applied = $engine->calculateAndApply($log, []);
echo "Applied net score: $applied\n";

$log->refresh();
print_r([
    'positive_points' => $log->positive_points,
    'negative_points' => $log->negative_points,
    'recovery_points' => $log->recovery_points,
    'calculated_score' => $log->calculated_score,
    'kpi_status' => $log->kpi_status,
]);
