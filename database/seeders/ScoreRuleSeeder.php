<?php

namespace Database\Seeders;

use App\Models\ScoreRule;
use App\Models\University;
use Illuminate\Database\Seeder;

class ScoreRuleSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure TIMS University exists
        $university = University::firstOrCreate(
            ['code' => 'TIMS'],
            [
                'name' => 'TIMS University',
                'status' => 'active',
                'theme_color' => '#8b5cf6',
                'tier_colors' => [
                    'platinum' => '#c084fc',
                    'gold' => '#f59e0b',
                    'silver' => '#9ca3af',
                    'bronze' => '#b45309',
                    'review_zone' => '#ef4444',
                ],
                'description' => 'Pre-configured TIMS University.',
            ]
        );

        $rules = [
            // Calls
            [
                'rule_group' => 'calls',
                'rule_key' => 'calls_40_49',
                'rule_name' => 'Calls volume: 40-49',
                'value_type' => 'points',
                'rule_value' => 4.00,
                'description' => 'Points awarded for completing 40 to 49 calls in a single day.',
            ],
            [
                'rule_group' => 'calls',
                'rule_key' => 'calls_50_64',
                'rule_name' => 'Calls volume: 50-64',
                'value_type' => 'points',
                'rule_value' => 6.00,
                'description' => 'Points awarded for completing 50 to 64 calls in a single day.',
            ],
            [
                'rule_group' => 'calls',
                'rule_key' => 'calls_65_plus',
                'rule_name' => 'Calls volume: 65+',
                'value_type' => 'points',
                'rule_value' => 8.00,
                'description' => 'Points awarded for completing 65 or more calls in a single day.',
            ],

            // Meetings Arranged
            [
                'rule_group' => 'meetings',
                'rule_key' => 'meetings_arranged_1',
                'rule_name' => 'Meetings arranged: 1',
                'value_type' => 'points',
                'rule_value' => 3.00,
                'description' => 'Points awarded for arranging 1 meeting in a single day.',
            ],
            [
                'rule_group' => 'meetings',
                'rule_key' => 'meetings_arranged_2_3',
                'rule_name' => 'Meetings arranged: 2-3',
                'value_type' => 'points',
                'rule_value' => 5.00,
                'description' => 'Points awarded for arranging 2 to 3 meetings in a single day.',
            ],
            [
                'rule_group' => 'meetings',
                'rule_key' => 'meetings_arranged_4_plus',
                'rule_name' => 'Meetings arranged: 4+',
                'value_type' => 'points',
                'rule_value' => 8.00,
                'description' => 'Points awarded for arranging 4 or more meetings in a single day.',
            ],

            // Attendance
            [
                'rule_group' => 'meetings',
                'rule_key' => 'attendance_bonus',
                'rule_name' => 'Meeting Attended Bonus',
                'value_type' => 'points',
                'rule_value' => 4.00,
                'description' => 'Bonus points earned per attended meeting.',
            ],

            // Lead Management KPIs
            [
                'rule_group' => 'lead_mgmt',
                'rule_key' => 'first_contact_45_min',
                'rule_name' => 'First contact under 45 minutes',
                'value_type' => 'points',
                'rule_value' => 2.00,
                'description' => 'Points awarded if first contact is made in less than 45 minutes.',
            ],
            [
                'rule_group' => 'lead_mgmt',
                'rule_key' => 'same_day_followup',
                'rule_name' => 'Same day follow up checklist met',
                'value_type' => 'points',
                'rule_value' => 2.00,
                'description' => 'Points awarded if all assigned leads are followed up on the same day.',
            ],
            [
                'rule_group' => 'lead_mgmt',
                'rule_key' => 'correct_crm_disposition',
                'rule_name' => 'Correct CRM disposition logged',
                'value_type' => 'points',
                'rule_value' => 2.00,
                'description' => 'Points awarded if CRM disposition rules are properly logged.',
            ],

            // Conversion
            [
                'rule_group' => 'conversion',
                'rule_key' => 'warm_lead_converted',
                'rule_name' => 'Warm lead converted',
                'value_type' => 'points',
                'rule_value' => 5.00,
                'description' => 'Points awarded per warm lead successfully converted to admission.',
            ],

            // Recovery / Cover-up
            [
                'rule_group' => 'recovery',
                'rule_key' => 'cover_up_bonus',
                'rule_name' => 'Cover-up Recovery Bonus',
                'value_type' => 'points',
                'rule_value' => 3.00,
                'description' => 'Special incentive awarded for overcoming consecutive low targets.',
            ],

            // Violations / Negative Points
            [
                'rule_group' => 'violation',
                'rule_key' => 'call_violation',
                'rule_name' => 'Call Violation Deduction',
                'value_type' => 'points',
                'rule_value' => -5.00,
                'description' => 'Penalty points deducted for direct call violations.',
            ],
            [
                'rule_group' => 'violation',
                'rule_key' => 'meeting_violation',
                'rule_name' => 'Meeting Violation Deduction',
                'value_type' => 'points',
                'rule_value' => -10.00,
                'description' => 'Penalty points deducted for failing critical meeting verification.',
            ],
            [
                'rule_group' => 'violation',
                'rule_key' => 'lead_violation',
                'rule_name' => 'Lead Management Violation Deduction',
                'value_type' => 'points',
                'rule_value' => -5.00,
                'description' => 'Penalty points deducted for lead guidelines infraction.',
            ],
            [
                'rule_group' => 'violation',
                'rule_key' => 'conduct_violation',
                'rule_name' => 'Conduct Violation Deduction',
                'value_type' => 'points',
                'rule_value' => -15.00,
                'description' => 'Penalty points deducted for conduct code violation.',
            ],

            // Tier Rules
            [
                'rule_group' => 'tier',
                'rule_key' => 'tier_platinum_min',
                'rule_name' => 'Platinum Tier Minimum Score',
                'value_type' => 'points',
                'rule_value' => 1200.00,
                'description' => 'Minimum score required to qualify for the Platinum performance tier.',
            ],
            [
                'rule_group' => 'tier',
                'rule_key' => 'tier_gold_min',
                'rule_name' => 'Gold Tier Minimum Score',
                'value_type' => 'points',
                'rule_value' => 700.00,
                'description' => 'Minimum score required to qualify for the Gold performance tier.',
            ],
            [
                'rule_group' => 'tier',
                'rule_key' => 'tier_silver_min',
                'rule_name' => 'Silver Tier Minimum Score',
                'value_type' => 'points',
                'rule_value' => 300.00,
                'description' => 'Minimum score required to qualify for the Silver performance tier.',
            ],
            [
                'rule_group' => 'tier',
                'rule_key' => 'tier_bronze_min',
                'rule_name' => 'Bronze Tier Minimum Score',
                'value_type' => 'points',
                'rule_value' => 0.00,
                'description' => 'Minimum score required to qualify for the Bronze performance tier.',
            ],

            // PIP & Escalation Rules
            [
                'rule_group' => 'pip',
                'rule_key' => 'pip_passing_score',
                'rule_name' => 'PIP Passing Score Threshold',
                'value_type' => 'points',
                'rule_value' => 300.00,
                'description' => 'Minimum cumulative score required to successfully complete probation or PIP.',
            ],
            [
                'rule_group' => 'escalation',
                'rule_key' => 'escalation_low_calls_threshold',
                'rule_name' => 'Low Calls Daily Threshold',
                'value_type' => 'points',
                'rule_value' => 40.00,
                'description' => 'Daily calls threshold below which is flagged as a low call volume day.',
            ],
            [
                'rule_group' => 'escalation',
                'rule_key' => 'escalation_violations_threshold',
                'rule_name' => 'Repeated Violations Limit',
                'value_type' => 'points',
                'rule_value' => 3.00,
                'description' => 'Number of active violations within 30 days that triggers high severity escalation.',
            ],
        ];

        foreach ($rules as $rule) {
            $rule['university_id'] = $university->id;
            ScoreRule::updateOrCreate(
                [
                    'university_id' => $university->id,
                    'rule_key' => $rule['rule_key']
                ],
                $rule
            );
        }
    }
}
