<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Executive;
use App\Models\DailyAudit;
use App\Models\User;
use App\Services\AuditOrchestrationService;

echo "==================================================\n";
echo "PMS Audit Orchestration Verification Script\n";
echo "==================================================\n";

$orchestrator = app(AuditOrchestrationService::class);
$user = User::where('email', 'admin@pms.local')->first();

if (!$user) {
    echo "ERROR: Admin user not found! Did you run seeders?\n";
    exit(1);
}

// ==================================================
// 1. TIMS Verification
// ==================================================
echo "\n--- Testing TIMS Audit ---\n";
$tims = Company::where('calculation_strategy', 'tims')->first();
if (!$tims) {
    echo "ERROR: TIMS company not found!\n";
    exit(1);
}

$timsExec = Executive::where('company_id', $tims->id)->first();
if (!$timsExec) {
    echo "ERROR: TIMS executive not found!\n";
    exit(1);
}

echo "Found TIMS Executive: {$timsExec->name} (Score: {$timsExec->current_score}, Tier: {$timsExec->current_tier})\n";

// Let's perform a preview of an audit with 50 calls, 2 meetings, crm_followup=true, etc.
$auditTims = new DailyAudit([
    'company_id' => $tims->id,
    'executive_id' => $timsExec->id,
    'audit_date' => now()->toDateString(),
    'audit_type' => 'tims',
    'connected_calls' => 50,
    'confirmed_meetings' => 2,
    'meetings_attended' => 2,
    'crm_followup' => true,
    'crm_disposition_correct' => true,
    'first_contact_within_45min' => true,
    'all_leads_followed_up' => true,
    'warm_lead_converted' => false,
    'cold_lead_reactivated' => false,
    'created_by' => $user->id,
]);
$auditTims->setRelation('executive', $timsExec);

$timsPreview = $orchestrator->preview($auditTims, []);
echo "TIMS Preview Result:\n";
echo "  KPI Passed: " . ($timsPreview['kpi']['passed'] ? 'YES' : 'NO') . "\n";
echo "  Positive Points: {$timsPreview['positive_points']}\n";
echo "  Negative Points: {$timsPreview['negative_points']}\n";
echo "  Recovery Points: {$timsPreview['recovery_points']}\n";
echo "  Final Score: {$timsPreview['final_score']}\n";
print_r($timsPreview['breakdown']);

// Let's actually execute it and write to DB
echo "Saving TIMS Audit...\n";
DailyAudit::where('executive_id', $timsExec->id)->where('audit_date', now()->toDateString())->delete();
$auditTims->save();
$timsResult = $orchestrator->execute($auditTims, []);
$timsExec->refresh();
echo "TIMS Audit Executed successfully!\n";
echo "  Audit ID: {$auditTims->id}\n";
echo "  Executive Score After: {$timsExec->current_score}\n";
echo "  Executive Tier After: {$timsExec->current_tier}\n";

// ==================================================
// 2. FOCUZ Verification
// ==================================================
echo "\n--- Testing FOCUZ Audit ---\n";
$focuz = Company::where('calculation_strategy', 'focuz')->first();
if (!$focuz) {
    echo "ERROR: FOCUZ company not found!\n";
    exit(1);
}

$focuzExec = Executive::where('company_id', $focuz->id)->first();
if (!$focuzExec) {
    echo "ERROR: FOCUZ executive not found!\n";
    exit(1);
}

echo "Found FOCUZ Executive: {$focuzExec->name} (Score: {$focuzExec->current_score}, Tier: {$focuzExec->current_tier})\n";

// Day 1 FOCUZ audit: 45 calls, 1 rolling meeting, rolling day = 1
$auditFocuz = new DailyAudit([
    'company_id' => $focuz->id,
    'executive_id' => $focuzExec->id,
    'audit_date' => now()->toDateString(),
    'audit_type' => 'focuz',
    'connected_calls' => 45,
    'confirmed_meetings' => 1,
    'meetings_attended' => 1,
    'crm_followup' => true,
    'crm_disposition_correct' => true,
    'first_contact_within_45min' => true,
    'all_leads_followed_up' => true,
    'rolling_day' => 1,
    'rolling_meeting_count' => 1,
    'checkpoint_result' => 'passed',
    'created_by' => $user->id,
]);
$auditFocuz->setRelation('executive', $focuzExec);

$focuzPreview = $orchestrator->preview($auditFocuz, []);
echo "FOCUZ Day 1 Preview Result:\n";
echo "  KPI Passed: " . ($focuzPreview['kpi']['passed'] ? 'YES' : 'NO') . "\n";
echo "  Positive Points: {$focuzPreview['positive_points']}\n";
echo "  Negative Points: {$focuzPreview['negative_points']}\n";
echo "  Recovery Points: {$focuzPreview['recovery_points']}\n";
echo "  Final Score: {$focuzPreview['final_score']}\n";

echo "Saving FOCUZ Audit...\n";
DailyAudit::where('executive_id', $focuzExec->id)->where('audit_date', now()->toDateString())->delete();
$auditFocuz->save();
$focuzResult = $orchestrator->execute($auditFocuz, []);
$focuzExec->refresh();
echo "FOCUZ Audit Executed successfully!\n";
echo "  Audit ID: {$auditFocuz->id}\n";
echo "  Executive Score After: {$focuzExec->current_score}\n";
echo "  Executive Tier After: {$focuzExec->current_tier}\n";

echo "\nVerification script completed successfully!\n";
