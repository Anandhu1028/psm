<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RuleSetSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create the `rules` table if it is still missing ───────────────
        if (!Schema::hasTable('rules')) {
            Schema::create('rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rule_set_id')->constrained('rule_sets')->onDelete('cascade');
                $table->foreignId('university_id')->constrained('universities')->onDelete('cascade');
                $table->string('category', 50);
                $table->string('code', 100);
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->string('input_metric', 100)->nullable();
                $table->string('operator', 30)->nullable();
                $table->decimal('threshold_value', 10, 2)->nullable();
                $table->decimal('threshold_to', 10, 2)->nullable();
                $table->decimal('points', 10, 2)->nullable();
                $table->string('calculation_type', 50)->default('fixed');
                $table->json('condition_json')->nullable();
                $table->json('action_json')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['rule_set_id', 'code']);
            });
        }

        // ── 2. Seed a default active rule set for every university ────────────
        $universities = DB::table('universities')->get();

        foreach ($universities as $uni) {
            // Skip if an active rule set already exists for this university
            $existing = DB::table('rule_sets')
                ->where('university_id', $uni->id)
                ->where('status', 'active')
                ->first();

            if ($existing) {
                // Make sure rules exist for it, skip if yes
                $ruleCount = DB::table('rules')->where('rule_set_id', $existing->id)->count();
                if ($ruleCount > 0) {
                    continue;
                }
                $ruleSetId = $existing->id;
            } else {
                $ruleSetId = DB::table('rule_sets')->insertGetId([
                    'university_id'  => $uni->id,
                    'name'           => $uni->code . ' Default Rules v1',
                    'version'        => 1,
                    'status'         => 'active',
                    'effective_from' => now()->toDateString(),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            $this->insertRules($ruleSetId, $uni->id);
        }
    }

    /**
     * Insert all scoring rules that match the client-side JS calculator.
     */
    private function insertRules(int $ruleSetId, int $uniId): void
    {
        $now = now();
        $rules = [

            // ════════════════════════════════
            //  POSITIVE — CALLS (tiered)
            // ════════════════════════════════
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'calls_tier_high',
                'name'             => 'Connected Calls (65+)',
                'input_metric'     => 'connected_calls',
                'operator'         => '>=',
                'threshold_value'  => 65,
                'points'           => 8,
                'calculation_type' => 'threshold',
                'sort_order'       => 10,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'calls_tier_mid',
                'name'             => 'Connected Calls (50–64)',
                'input_metric'     => 'connected_calls',
                'operator'         => 'between',
                'threshold_value'  => 50,
                'threshold_to'     => 64,
                'points'           => 6,
                'calculation_type' => 'threshold',
                'sort_order'       => 11,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'calls_tier_low',
                'name'             => 'Connected Calls (40–49)',
                'input_metric'     => 'connected_calls',
                'operator'         => 'between',
                'threshold_value'  => 40,
                'threshold_to'     => 49,
                'points'           => 4,
                'calculation_type' => 'threshold',
                'sort_order'       => 12,
            ],

            // ════════════════════════════════
            //  POSITIVE — MEETINGS ARRANGED (tiered)
            // ════════════════════════════════
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'arranged_tier_high',
                'name'             => 'Meetings Arranged (4+)',
                'input_metric'     => 'meetings_arranged',
                'operator'         => '>=',
                'threshold_value'  => 4,
                'points'           => 8,
                'calculation_type' => 'threshold',
                'sort_order'       => 20,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'arranged_tier_mid',
                'name'             => 'Meetings Arranged (2–3)',
                'input_metric'     => 'meetings_arranged',
                'operator'         => 'between',
                'threshold_value'  => 2,
                'threshold_to'     => 3,
                'points'           => 5,
                'calculation_type' => 'threshold',
                'sort_order'       => 21,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'arranged_tier_low',
                'name'             => 'Meetings Arranged (1)',
                'input_metric'     => 'meetings_arranged',
                'operator'         => '=',
                'threshold_value'  => 1,
                'points'           => 3,
                'calculation_type' => 'threshold',
                'sort_order'       => 22,
            ],

            // ════════════════════════════════
            //  POSITIVE — MEETINGS ATTENDED (per unit × 4)
            // ════════════════════════════════
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'meetings_attended_per_unit',
                'name'             => 'Meetings Attended',
                'input_metric'     => 'meetings_attended',
                'points'           => 4,
                'calculation_type' => 'per_unit',
                'sort_order'       => 30,
            ],

            // ════════════════════════════════
            //  POSITIVE — CRM KPI BOOLEANS
            // ════════════════════════════════
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'kpi_first_contact_45m',
                'name'             => 'First Contact Compliance',
                'input_metric'     => 'first_contact_within_45_min',
                'points'           => 2,
                'calculation_type' => 'boolean',
                'sort_order'       => 40,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'kpi_lead_followup',
                'name'             => 'Lead Follow-up Compliance',
                'input_metric'     => 'all_leads_followed_up',
                'points'           => 2,
                'calculation_type' => 'boolean',
                'sort_order'       => 41,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'kpi_crm_disposition',
                'name'             => 'CRM Disposition Accuracy',
                'input_metric'     => 'crm_disposition_correct',
                'points'           => 2,
                'calculation_type' => 'boolean',
                'sort_order'       => 42,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'positive',
                'code'             => 'kpi_warm_lead_conversion',
                'name'             => 'Warm Lead Conversion',
                'input_metric'     => 'warm_lead_converted',
                'points'           => 5,
                'calculation_type' => 'boolean',
                'sort_order'       => 43,
            ],

            // ════════════════════════════════
            //  RECOVERY RULES
            // ════════════════════════════════
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'recovery',
                'code'             => 'recovery_high_calls',
                'name'             => 'Recovery: High Call Volume (65+)',
                'input_metric'     => 'connected_calls',
                'operator'         => '>=',
                'threshold_value'  => 65,
                'points'           => 6,
                'calculation_type' => 'threshold',
                'sort_order'       => 50,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'recovery',
                'code'             => 'recovery_good_meetings',
                'name'             => 'Recovery: Good Meeting Attendance (2+)',
                'input_metric'     => 'meetings_attended',
                'operator'         => '>=',
                'threshold_value'  => 2,
                'points'           => 6,
                'calculation_type' => 'threshold',
                'sort_order'       => 51,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'recovery',
                'code'             => 'recovery_full_kpi_clean',
                'name'             => 'Recovery: Full KPI Compliance & No Violations',
                'points'           => 8,
                'calculation_type' => 'boolean',
                'condition_json'   => json_encode([
                    'all_true' => ['first_contact_within_45_min', 'all_leads_followed_up', 'crm_disposition_correct'],
                    'no_negative_points' => true,
                ]),
                'sort_order'       => 52,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'recovery',
                'code'             => 'recovery_cap',
                'name'             => 'Recovery Cap',
                'threshold_value'  => 20,
                'calculation_type' => 'recovery_cap',
                'sort_order'       => 59,
            ],

            // ════════════════════════════════
            //  VIOLATIONS — CALL
            // ════════════════════════════════
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'call_calls_33_39',
                'name'             => 'Connected Calls 33–39',
                'points'           => 5,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'call']),
                'sort_order'       => 100,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'call_calls_27_32',
                'name'             => 'Connected Calls 27–32',
                'points'           => 10,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'call']),
                'sort_order'       => 101,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'call_calls_15_26',
                'name'             => 'Connected Calls 15–26',
                'points'           => 15,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'call']),
                'sort_order'       => 102,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'call_calls_below_15',
                'name'             => 'Connected Calls Below 15',
                'points'           => 20,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'call']),
                'sort_order'       => 103,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'call_zero_calls',
                'name'             => 'Zero Calls',
                'points'           => 25,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'call']),
                'sort_order'       => 104,
            ],

            // ════════════════════════════════
            //  VIOLATIONS — MEETING
            // ════════════════════════════════
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'meeting_zero_meetings',
                'name'             => 'Zero Meetings',
                'points'           => 10,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'meeting']),
                'sort_order'       => 110,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'meeting_3_day_no_meeting',
                'name'             => '3-Day No Meeting Streak',
                'points'           => 15,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'meeting']),
                'sort_order'       => 111,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'meeting_invalid_documentation',
                'name'             => 'Invalid Meeting Documentation',
                'points'           => 10,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'meeting']),
                'sort_order'       => 112,
            ],

            // ════════════════════════════════
            //  VIOLATIONS — LEAD
            // ════════════════════════════════
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'lead_no_first_contact',
                'name'             => 'No First Contact',
                'points'           => 5,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'lead']),
                'sort_order'       => 120,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'lead_no_follow_up',
                'name'             => 'No Follow-up',
                'points'           => 5,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'lead']),
                'sort_order'       => 121,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'lead_wrong_disposition',
                'name'             => 'Wrong CRM Disposition',
                'points'           => 5,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'lead']),
                'sort_order'       => 122,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'lead_warm_incorrectly_frozen',
                'name'             => 'Warm Lead Incorrectly Frozen',
                'points'           => 10,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'lead']),
                'sort_order'       => 123,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'lead_invalid_remarks',
                'name'             => 'Invalid Remarks',
                'points'           => 2,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'lead']),
                'sort_order'       => 124,
            ],

            // ════════════════════════════════
            //  VIOLATIONS — CONDUCT
            // ════════════════════════════════
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'conduct_data_tampering',
                'name'             => 'Data Tampering',
                'points'           => 20,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'conduct']),
                'sort_order'       => 130,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'conduct_false_justification',
                'name'             => 'False Justification',
                'points'           => 15,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'conduct']),
                'sort_order'       => 131,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'conduct_protocol_violation',
                'name'             => 'Communication Protocol Violation',
                'points'           => 10,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'conduct']),
                'sort_order'       => 132,
            ],
            [
                'rule_set_id'      => $ruleSetId,
                'university_id'    => $uniId,
                'category'         => 'negative',
                'code'             => 'conduct_customer_complaint',
                'name'             => 'Verified Customer Complaint',
                'points'           => 15,
                'calculation_type' => 'selected_violation',
                'action_json'      => json_encode(['create_violation' => true, 'violation_type' => 'conduct']),
                'sort_order'       => 133,
            ],
        ];

        foreach ($rules as &$rule) {
            $rule['created_at'] = now();
            $rule['updated_at'] = now();
            // Fill nullable columns so DB doesn't complain
            $rule['description']    = $rule['description']    ?? null;
            $rule['input_metric']   = $rule['input_metric']   ?? null;
            $rule['operator']       = $rule['operator']       ?? null;
            $rule['threshold_value'] = $rule['threshold_value'] ?? null;
            $rule['threshold_to']   = $rule['threshold_to']   ?? null;
            $rule['condition_json'] = $rule['condition_json'] ?? null;
            $rule['action_json']    = $rule['action_json']    ?? null;
            $rule['is_active']      = true;
        }
        unset($rule);

        DB::table('rules')->insert($rules);
    }
}
