<?php

namespace Database\Seeders;

use App\Models\Rule;
use App\Models\RuleSet;
use App\Models\University;
use Illuminate\Database\Seeder;

class DynamicRuleEngineSeeder extends Seeder
{
    public function run(): void
    {
        $tims = University::firstOrCreate(
            ['code' => 'TIMS'],
            [
                'name' => 'TIMS University',
                'status' => 'active',
                'theme_color' => '#8b5cf6',
                'description' => 'Pre-configured TIMS University.',
            ]
        );

        $focuz = University::firstOrCreate(
            ['code' => 'FOCUZ'],
            [
                'name' => 'FOCUZ University',
                'status' => 'active',
                'theme_color' => '#0ea5e9',
                'description' => 'FOCUZ University with rolling meeting KPI rules.',
            ]
        );

        $this->seedRuleSet($tims, 'TIMS Rules v1', $this->timsRules());
        $this->seedRuleSet($focuz, 'FOCUZ Rules v1', $this->focuzRules());
    }

    protected function seedRuleSet(University $university, string $name, array $rules): RuleSet
    {
        RuleSet::where('university_id', $university->id)
            ->where('status', 'active')
            ->update(['status' => 'archived']);

        $ruleSet = RuleSet::updateOrCreate(
            ['university_id' => $university->id, 'version' => 1],
            [
                'name' => $name,
                'status' => 'active',
                'effective_from' => now()->toDateString(),
            ]
        );

        foreach ($rules as $index => $rule) {
            $rule['rule_set_id'] = $ruleSet->id;
            $rule['university_id'] = $university->id;
            $rule['sort_order'] = $rule['sort_order'] ?? $index;

            Rule::updateOrCreate(
                ['rule_set_id' => $ruleSet->id, 'code' => $rule['code']],
                $rule
            );
        }

        return $ruleSet;
    }

    protected function timsRules(): array
    {
        return array_merge(
            $this->basePositiveRules(),
            [
                $this->kpi('kpi_calls_min_daily', 'Daily connected calls KPI', 'connected_calls', '>=', 40, null, 'Minimum 40 connected calls per day.'),
                $this->kpi('kpi_meetings_min_daily', 'Daily confirmed meeting KPI', 'meetings_arranged', '>=', 1, null, 'Minimum 1 confirmed meeting per day.'),
            ],
            $this->negativeRules(),
            $this->recoveryRules(),
            $this->tierRules(),
            $this->escalationAndPipRules()
        );
    }

    protected function focuzRules(): array
    {
        $rules = array_merge(
            $this->basePositiveRules(),
            [
                $this->kpi('kpi_calls_min_daily', 'Daily connected calls KPI', 'connected_calls', '>=', 40, null, 'Minimum 40 connected calls per day.'),
                [
                    'category' => 'kpi',
                    'code' => 'focuz_day_2_meeting_checkpoint',
                    'name' => 'FOCUZ Day 2 rolling meeting checkpoint',
                    'description' => 'Day 2 requires cumulative 2 meetings. No deduction is applied by this checkpoint.',
                    'calculation_type' => 'rolling_window',
                    'points' => 0,
                    'condition_json' => [
                        'metric' => 'meetings_arranged',
                        'window_days' => 3,
                        'checkpoint_day' => 2,
                        'minimum_cumulative' => 2,
                        'skip_days' => [1],
                    ],
                    'action_json' => [
                        'create_violation' => false,
                    ],
                ],
                [
                    'category' => 'negative',
                    'code' => 'focuz_day_3_meeting_checkpoint_failed',
                    'name' => 'FOCUZ Day 3 final meeting checkpoint',
                    'description' => 'Day 3 requires cumulative 3 meetings. Deduction applies only when this checkpoint fails.',
                    'calculation_type' => 'rolling_window',
                    'points' => -10,
                    'condition_json' => [
                        'metric' => 'meetings_arranged',
                        'window_days' => 3,
                        'checkpoint_day' => 3,
                        'minimum_cumulative' => 3,
                        'skip_days' => [1, 2],
                    ],
                    'action_json' => [
                        'deduct_points' => 10,
                        'create_violation' => true,
                        'violation_type' => 'meeting',
                    ],
                ],
            ],
            $this->negativeRules(),
            $this->recoveryRules(),
            $this->tierRules(),
            $this->escalationAndPipRules()
        );

        return collect($rules)
            ->reject(fn ($rule) => $rule['code'] === 'meetings_arranged_1')
            ->values()
            ->all();
    }

    protected function basePositiveRules(): array
    {
        return [
            $this->range('positive', 'calls_40_49', 'Calls volume: 40-49', 'connected_calls', 40, 49, 4),
            $this->range('positive', 'calls_50_64', 'Calls volume: 50-64', 'connected_calls', 50, 64, 6),
            $this->threshold('positive', 'calls_65_plus', 'Calls volume: 65+', 'connected_calls', '>=', 65, 8),
            $this->threshold('positive', 'meetings_arranged_1', 'Meetings arranged: 1', 'meetings_arranged', '=', 1, 3),
            $this->range('positive', 'meetings_arranged_2_3', 'Meetings arranged: 2-3', 'meetings_arranged', 2, 3, 5),
            $this->threshold('positive', 'meetings_arranged_4_plus', 'Meetings arranged: 4+', 'meetings_arranged', '>=', 4, 8),
            [
                'category' => 'attendance',
                'code' => 'attendance_bonus',
                'name' => 'Meeting attended bonus',
                'description' => 'Bonus points earned per attended meeting.',
                'input_metric' => 'meetings_attended',
                'points' => 4,
                'calculation_type' => 'per_unit',
            ],
            $this->boolean('positive', 'first_contact_45_min', 'First contact under 45 minutes', 'first_contact_within_45_min', 2),
            $this->boolean('positive', 'same_day_followup', 'Same-day follow-up checklist met', 'all_leads_followed_up', 2),
            $this->boolean('positive', 'correct_crm_disposition', 'Correct CRM disposition logged', 'crm_disposition_correct', 2),
            $this->boolean('positive', 'warm_lead_converted', 'Warm lead converted', 'warm_lead_converted', 5),
        ];
    }

    protected function negativeRules(): array
    {
        return [
            $this->violation('call_calls_33_39', 'Connected Calls 33-39', 'call', 5),
            $this->violation('call_calls_27_32', 'Connected Calls 27-32', 'call', 10),
            $this->violation('call_calls_15_26', 'Connected Calls 15-26', 'call', 15),
            $this->violation('call_calls_below_15', 'Connected Calls Below 15', 'call', 20),
            $this->violation('call_zero_calls', 'Zero Calls', 'call', 25),
            $this->violation('meeting_zero_meetings', 'Zero Meetings', 'meeting', 10),
            $this->violation('meeting_3_day_no_meeting', '3-Day No Meeting Streak', 'meeting', 15),
            $this->violation('meeting_invalid_documentation', 'Invalid Meeting Documentation', 'meeting', 10),
            $this->violation('lead_no_first_contact', 'No First Contact', 'lead', 5),
            $this->violation('lead_no_follow_up', 'No Follow-Up', 'lead', 5),
            $this->violation('lead_wrong_disposition', 'Wrong CRM Disposition', 'lead', 5),
            $this->violation('lead_warm_incorrectly_frozen', 'Warm Lead Incorrectly Frozen', 'lead', 10),
            $this->violation('lead_invalid_remarks', 'Invalid Remarks', 'lead', 2),
            $this->violation('conduct_data_tampering', 'Data Tampering', 'conduct', 20),
            $this->violation('conduct_false_justification', 'False Justification', 'conduct', 15),
            $this->violation('conduct_protocol_violation', 'Communication Protocol Violation', 'conduct', 10),
            $this->violation('conduct_customer_complaint', 'Verified Customer Complaint', 'conduct', 15),
        ];
    }

    protected function recoveryRules(): array
    {
        return [
            $this->threshold('recovery', 'recovery_calls_65_plus', 'Calls recovery bonus', 'connected_calls', '>=', 65, 6),
            $this->threshold('recovery', 'recovery_attended_2_plus', 'Meetings recovery bonus', 'meetings_attended', '>=', 2, 6),
            [
                'category' => 'recovery',
                'code' => 'recovery_perfect_compliance',
                'name' => 'Perfect compliance recovery bonus',
                'description' => 'Awarded when first contact, follow-up, and CRM disposition are all compliant.',
                'points' => 8,
                'calculation_type' => 'boolean',
                'input_metric' => 'first_contact_within_45_min',
                'condition_json' => [
                    'all_true' => ['first_contact_within_45_min', 'all_leads_followed_up', 'crm_disposition_correct'],
                    'no_negative_points' => true,
                ],
            ],
            [
                'category' => 'recovery',
                'code' => 'recovery_cap',
                'name' => 'Recovery points cap',
                'description' => 'Maximum recovery points allowed per daily log.',
                'threshold_value' => 20,
                'calculation_type' => 'recovery_cap',
            ],
        ];
    }

    protected function tierRules(): array
    {
        return [
            $this->tier('tier_platinum_min', 'Platinum', 'platinum', 1200),
            $this->tier('tier_gold_min', 'Gold', 'gold', 700),
            $this->tier('tier_silver_min', 'Silver', 'silver', 300),
            $this->tier('tier_bronze_min', 'Bronze', 'bronze', 0),
            [
                'category' => 'tier',
                'code' => 'tier_review_zone',
                'name' => 'Review Zone',
                'operator' => '<',
                'threshold_value' => 0,
                'calculation_type' => 'fixed',
                'action_json' => ['tier' => 'review_zone'],
            ],
        ];
    }

    protected function escalationAndPipRules(): array
    {
        return [
            $this->threshold('escalation', 'escalation_low_calls_threshold', 'Low calls daily threshold', 'connected_calls', '<', 40, 0),
            $this->threshold('escalation', 'escalation_violations_threshold', 'Repeated violations limit', 'active_violations_30_days', '>=', 3, 0),
            $this->threshold('pip', 'pip_passing_score', 'PIP passing score threshold', 'current_score', '>=', 300, 0),
        ];
    }

    protected function kpi(string $code, string $name, string $metric, string $operator, float $threshold, ?float $to, string $description): array
    {
        return [
            'category' => 'kpi',
            'code' => $code,
            'name' => $name,
            'description' => $description,
            'input_metric' => $metric,
            'operator' => $operator,
            'threshold_value' => $threshold,
            'threshold_to' => $to,
            'points' => 0,
            'calculation_type' => 'fixed',
        ];
    }

    protected function range(string $category, string $code, string $name, string $metric, float $from, float $to, float $points): array
    {
        return [
            'category' => $category,
            'code' => $code,
            'name' => $name,
            'input_metric' => $metric,
            'operator' => 'between',
            'threshold_value' => $from,
            'threshold_to' => $to,
            'points' => $points,
            'calculation_type' => 'range',
        ];
    }

    protected function threshold(string $category, string $code, string $name, string $metric, string $operator, float $threshold, float $points): array
    {
        return [
            'category' => $category,
            'code' => $code,
            'name' => $name,
            'input_metric' => $metric,
            'operator' => $operator,
            'threshold_value' => $threshold,
            'points' => $points,
            'calculation_type' => 'fixed',
        ];
    }

    protected function boolean(string $category, string $code, string $name, string $metric, float $points): array
    {
        return [
            'category' => $category,
            'code' => $code,
            'name' => $name,
            'input_metric' => $metric,
            'points' => $points,
            'calculation_type' => 'boolean',
        ];
    }

    protected function violation(string $code, string $name, string $type, float $points): array
    {
        return [
            'category' => 'negative',
            'code' => $code,
            'name' => $name,
            'points' => $points,
            'calculation_type' => 'selected_violation',
            'action_json' => [
                'create_violation' => true,
                'violation_type' => $type,
            ],
        ];
    }

    protected function tier(string $code, string $name, string $tier, float $minimum): array
    {
        return [
            'category' => 'tier',
            'code' => $code,
            'name' => $name,
            'operator' => '>=',
            'threshold_value' => $minimum,
            'calculation_type' => 'fixed',
            'action_json' => ['tier' => $tier],
        ];
    }
}
