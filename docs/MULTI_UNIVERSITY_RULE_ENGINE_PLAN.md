# Multi-University Rule Management System

## Current State

The application already has a working base for multi-university support:

- `universities` stores name, code, logo, theme color, description, status, and tier colors.
- `score_rules` are scoped by `university_id`.
- `executives`, `daily_logs`, `meetings`, and `violations` already carry `university_id`.
- University creation currently clones TIMS score rules.
- Logo display exists in the sidebar, university list, and university details.

The current blocker is that rule execution is still partially hardcoded:

- `PointEngineService::$violationRules` hardcodes negative rules.
- Calls, meeting, attendance, recovery, and tier logic are coded as `if/elseif` branches.
- FOCUZ rolling meeting checkpoints cannot be represented by the current scalar `score_rules.rule_value`.
- Rule changes are not versioned, so historical reports cannot show the exact rule version used.

## Target Architecture

Use a data-driven rule engine with university-specific rule sets and versioned rules.

```text
University
  Rule Set Version
    KPI Rules
    Positive Point Rules
    Negative Point Rules
    Meeting Rules
    Attendance Rules
    Lead Management Rules
    Recovery Rules
    Escalation Rules
    Tier Rules
    PIP Rules
    Probation Rules
```

Rule execution should follow this pipeline:

```text
Daily Log Input
  -> Resolve Executive University
  -> Load Active Rule Set Version
  -> Build Rule Context
  -> Evaluate KPI Rules
  -> Evaluate Positive Rules
  -> Evaluate Negative Rules
  -> Evaluate Recovery Rules
  -> Evaluate Tier Rules
  -> Persist Score Breakdown
  -> Trigger Escalation/PIP/Probation Rules
```

No university-specific logic should be implemented with `if ($university->code === 'FOCUZ')`. University behavior must come from rule records.

## Database Design

### `universities`

Already mostly present. Keep:

- `id`
- `name`
- `code`
- `logo`
- `description`
- `theme_color`
- `tier_colors`
- `status`
- `created_by`

Add:

- `settings json nullable` for university-level display/runtime settings.

### `rule_sets`

Stores versioned rule collections per university.

- `id`
- `university_id foreign`
- `name`
- `version integer`
- `status enum: draft, active, archived`
- `effective_from date nullable`
- `effective_to date nullable`
- `cloned_from_rule_set_id nullable`
- `created_by foreign nullable`
- timestamps

Indexes:

- unique `university_id, version`
- index `university_id, status`

### `rules`

Replaces or supersedes `score_rules` for dynamic execution.

- `id`
- `rule_set_id foreign`
- `university_id foreign`
- `category enum`
- `code string`
- `name string`
- `description text nullable`
- `input_metric string nullable`
- `operator string nullable`
- `threshold_value decimal nullable`
- `threshold_to decimal nullable`
- `points decimal nullable`
- `calculation_type enum: fixed, per_unit, range, aggregate, rolling_window, expression`
- `condition_json json nullable`
- `action_json json nullable`
- `sort_order integer default 0`
- `is_active boolean default true`
- timestamps

Categories:

- `kpi`
- `positive`
- `negative`
- `meeting`
- `attendance`
- `lead_management`
- `recovery`
- `escalation`
- `tier`
- `pip`
- `probation`

Example TIMS calls rule:

```json
{
  "category": "positive",
  "code": "calls_40_49",
  "input_metric": "connected_calls",
  "operator": "between",
  "threshold_value": 40,
  "threshold_to": 49,
  "points": 4,
  "calculation_type": "fixed"
}
```

Example FOCUZ day-3 rolling meeting rule:

```json
{
  "category": "negative",
  "code": "focuz_day_3_meeting_checkpoint_failed",
  "calculation_type": "rolling_window",
  "condition_json": {
    "metric": "meetings_arranged",
    "window_days": 3,
    "checkpoint_day": 3,
    "minimum_cumulative": 3,
    "deduct_only_on_checkpoint_failure": true,
    "skip_days": [1, 2]
  },
  "action_json": {
    "deduct_points": 10,
    "create_violation": true,
    "violation_type": "meeting"
  }
}
```

### `rule_evaluation_results`

Stores the detailed result of every evaluated daily log.

- `id`
- `daily_log_id foreign`
- `executive_id foreign`
- `university_id foreign`
- `rule_set_id foreign`
- `rule_id foreign nullable`
- `rule_code`
- `category`
- `status enum: passed, failed, skipped, applied`
- `points decimal default 0`
- `message`
- `context_snapshot json nullable`
- timestamps

This powers score reflection, profiles, dashboards, and reports.

### `daily_logs`

Already has point fields. Add:

- `rule_set_id nullable`
- `kpi_status enum: passed, failed, partial, not_evaluated default not_evaluated`
- `violation_status enum: none, active, resolved default none`
- `meeting_window_status json nullable`

`meeting_window_status` stores FOCUZ checkpoint details:

```json
{
  "day_number": 3,
  "window_days": 3,
  "cumulative_meetings": 2,
  "required_meetings": 3,
  "checkpoint": "day_3_final",
  "status": "failed"
}
```

### `score_transactions`

Already stores transaction ledger. Add:

- `rule_set_id nullable`
- `rule_evaluation_result_id nullable`
- `component enum: positive, negative, recovery, adjustment`

### `executives`

Already has university, tier, and score. Add:

- `photo nullable`
- optional `profile_settings json nullable`

### `rule_templates`

Optional, but useful for cloning across universities.

- `id`
- `name`
- `description`
- `source_university_id nullable`
- `source_rule_set_id nullable`
- `payload json`
- `created_by nullable`
- timestamps

## Migration Plan

1. Add `settings` to `universities`.
2. Create `rule_sets`.
3. Create `rules`.
4. Create `rule_evaluation_results`.
5. Add `rule_set_id`, `kpi_status`, `violation_status`, and `meeting_window_status` to `daily_logs`.
6. Add `rule_set_id`, `rule_evaluation_result_id`, and `component` to `score_transactions`.
7. Add `photo` to `executives`.
8. Create a migration command to convert existing `score_rules` into a TIMS active `rule_set`.
9. Keep `score_rules` temporarily as legacy data until all services switch to `rules`.
10. After verification, deprecate `score_rules` or leave it as a compatibility table.

## Models

Add:

- `RuleSet`
- `Rule`
- `RuleEvaluationResult`
- `RuleTemplate`

Update:

- `University` has many `ruleSets`, `rules`, `activeRuleSet`.
- `DailyLog` belongs to `ruleSet`, has many `ruleEvaluationResults`.
- `ScoreTransaction` belongs to `ruleSet` and `ruleEvaluationResult`.
- `Executive` gains `photo_url` and should resolve tier through the rule engine instead of hardcoded thresholds.

## Controllers

### `Admin\UniversityController`

Keep current CRUD/logo features. Add:

- `cloneRules(Request $request, University $university)`
- `activateRuleSet(University $university, RuleSet $ruleSet)`
- `createRuleSetDraft(University $university)`

### `Admin\UniversityRuleController`

New dedicated controller for `Settings -> University Rules`.

Methods:

- `index(Request $request)`
- `edit(University $university, RuleSet $ruleSet)`
- `update(Request $request, University $university, RuleSet $ruleSet)`
- `storeRule(Request $request, University $university, RuleSet $ruleSet)`
- `updateRule(Request $request, Rule $rule)`
- `destroyRule(Rule $rule)`
- `cloneTemplate(Request $request, University $university)`

### `CRO\DailyLogController`

Update:

- Load university-scoped rule fields for the selected executive.
- Use `RuleEngineService` for preview and final calculation.
- Add filters for date range, university, zone, executive, tier, KPI status, and violation status.

Add endpoint:

- `POST daily-logs/preview-score`

Returns:

```json
{
  "positive_points": 15,
  "negative_points": 3,
  "recovery_points": 4,
  "net_score": 16,
  "breakdown": [],
  "kpi_status": "passed",
  "meeting_window_status": {}
}
```

### `ExecutiveController`

Add or expand `show(Executive $executive)` as a full profile page with tabs:

- Daily Logs
- Point History
- KPI History
- Meeting Tracker
- Violations
- Recovery History
- Audit History
- PIP Records

## Services

### `RuleEngineService`

Primary public methods:

- `preview(DailyLogData $data): RuleEvaluationResultDto`
- `calculateAndApply(DailyLog $log, array $inputs = []): ScoreResult`
- `evaluateRules(Executive $executive, array $context, RuleSet $ruleSet): Collection`

Responsibilities:

- Load active rule set by university and log date.
- Build context from daily log, executive, history, meetings, violations, and audits.
- Evaluate all active rules.
- Return positive, negative, recovery, net score, KPI status, and rule breakdown.
- Persist `rule_evaluation_results`.
- Write `score_transactions`.

### `RuleContextBuilder`

Builds normalized metrics:

- `connected_calls`
- `meetings_arranged`
- `meetings_attended`
- `attendance_count`
- `first_contact_within_45_min`
- `all_leads_followed_up`
- `crm_disposition_correct`
- `warm_lead_converted`
- rolling meeting counts
- prior violations
- current tier
- current score
- PIP/probation state

### `RuleEvaluator`

Evaluates rule types:

- `fixed`
- `range`
- `per_unit`
- `aggregate`
- `rolling_window`
- `expression`

### `MeetingWindowService`

Handles FOCUZ rolling meeting KPI without FOCUZ-specific code:

- Calculates day number in rolling window.
- Calculates cumulative meetings.
- Determines day-2 checkpoint status.
- Determines day-3 final checkpoint status.
- Applies deduction only when the rule action says so.
- Stores `meeting_window_status`.

### `TierService`

Evaluates tier rules dynamically:

- Platinum
- Gold
- Silver
- Bronze
- Review Zone

Tier labels should come from rules, not enums hardcoded to TIMS.

### `EscalationRuleService`

Replaces hardcoded escalation thresholds in `EscalationService`.

Rule examples:

- low calls for N consecutive days
- repeated violations in X days
- review zone entry
- probation failure
- PIP missed target

### `RuleTemplateService`

Supports:

- Clone TIMS rules to FOCUZ.
- Clone any university active rule set into a new university.
- Create draft from active version.
- Publish draft as active version.

## TIMS Rule Seed

Create TIMS active rule set with:

### KPI

- Minimum confirmed meetings per day: 1
- Minimum connected calls per day: 40

### Positive

- Calls 40-49: +4
- Calls 50-64: +6
- Calls 65+: +8
- 1 meeting: +3
- 2-3 meetings: +5
- 4+ meetings: +8
- Attendance per attended meeting: +4

### Negative

Move the current `PointEngineService::$violationRules` into rule rows:

- call 33-39
- call 27-32
- call 15-26
- call below 15
- zero calls
- zero meetings
- three-day no-meeting streak
- invalid meeting documentation
- lead violations
- conduct violations

### Recovery

Move current recovery rules into rows:

- calls recovery
- meetings recovery
- perfect compliance recovery
- recovery cap

### Tier

- Platinum
- Gold
- Silver
- Bronze
- Review Zone

## FOCUZ Rule Seed

Create FOCUZ active rule set by cloning TIMS, then override:

### Calls KPI

- Minimum connected calls daily: 40

### Meeting KPI

- Day 1: no checkpoint, no deduction
- Day 2: minimum cumulative 2 meetings, checkpoint only
- Day 3: minimum cumulative 3 meetings, final checkpoint
- Day 4 onwards: rolling continuation
- Meeting deduction applies only when day-3 checkpoint fails

Represent as rolling-window rules:

- `focuz_meeting_day_1_skip`
- `focuz_meeting_day_2_checkpoint`
- `focuz_meeting_day_3_final_checkpoint`
- `focuz_meeting_day_4_rolling_continuation`

## Rule Management UI

Route:

- `GET /admin/university-rules`

Screen:

```text
Settings
  University Rules

University Selector: [TIMS v]

Tabs:
  KPI Rules
  Positive Rules
  Negative Rules
  Recovery Rules
  Escalation Rules
  Tier Rules
```

Each rule card should show:

- Rule name
- Code
- Category
- Active toggle
- Metric
- Operator
- Threshold(s)
- Points
- Calculation type
- Advanced JSON editor for condition/action
- Save button

Add actions:

- Clone Existing Rule Template
- Create Draft Version
- Publish Version
- Archive Version

## Dashboard UI

For the selected university, show a compact header:

- University logo or initials avatar
- University name
- Active rule version

Cards:

- Total Executives
- Active Executives
- Calls Today
- Meetings Today
- Attendance %
- KPI Compliance %
- Current Average Score
- Review Zone Count
- Active PIP Count

Implementation notes:

- Use a `DashboardMetricsService`.
- Avoid calculating all metrics inside `DashboardController`.
- Use `active_university_id` session as current scope.

## Daily Performance Page

Add filters:

- Date range
- University
- Zone
- Executive
- Tier
- KPI status
- Violation status

Add table columns:

- University logo + code badge
- Positive points
- Negative points
- Recovery points
- Net score
- KPI status
- Violation status

Add score preview on CRO entry:

```text
Positive Points   +15
Negative Points    -3
Recovery Points    +4
Net Score          +16
```

Use AJAX endpoint `daily-logs/preview-score`.

## Executive Profile UI

Header:

- Executive photo
- University logo
- Name and employee ID
- Zone
- Current tier
- Current score

Tabs:

- Daily Logs
- Point History
- KPI History
- Meeting Tracker
- Violations
- Recovery History
- Audit History
- PIP Records

Data sources:

- `daily_logs`
- `score_transactions`
- `rule_evaluation_results`
- `meetings`
- `violations`
- `audits`
- `pip_records`

## Reports

Filters:

- University
- Zone
- Executive
- Tier
- Date range

Report header:

- University logo or initials
- University name
- Current rule version
- Date range

Data:

- Daily performance
- Weekly summary
- Zone comparison
- Tier distribution
- Violation report
- PIP report

CSV/PDF exports must include:

- University logo
- University name
- Rule version used

## Implementation Sequence

1. Add database migrations for rule sets, dynamic rules, evaluation results, and added columns.
2. Add models and relationships.
3. Create seeders for TIMS and FOCUZ active rule sets.
4. Build `RuleTemplateService` and rule cloning flow.
5. Build `RuleContextBuilder`, `RuleEvaluator`, `MeetingWindowService`, and `RuleEngineService`.
6. Replace `PointEngineService` internals with the new rule engine.
7. Replace `Executive::determineTierForScore()` hardcoded thresholds with `TierService`.
8. Replace `EscalationService` hardcoded thresholds with `EscalationRuleService`.
9. Build `Admin\UniversityRuleController`.
10. Build `resources/views/admin/university_rules/index.blade.php`.
11. Update daily log create/index pages with filters, university badges, and score preview.
12. Add executive profile route/controller/view.
13. Update dashboards through `DashboardMetricsService`.
14. Update reports and exports with rule version and university branding.
15. Add tests for TIMS and FOCUZ rule scenarios.

## Required Tests

### TIMS

- 40-49 calls gives +4.
- 50-64 calls gives +6.
- 65+ calls gives +8.
- 1 meeting gives +3.
- 2-3 meetings gives +5.
- 4+ meetings gives +8.
- Attended meetings give +4 each.
- Negative rules are created from data, not service constants.
- Tier is resolved from tier rules.

### FOCUZ

- Day 1 has no meeting checkpoint deduction.
- Day 2 calculates cumulative meeting progress.
- Day 2 does not deduct when under checkpoint unless configured.
- Day 3 requires cumulative 3 meetings.
- Day 3 failed checkpoint applies configured deduction.
- Day 4 onwards uses rolling continuation.
- Meeting window status is persisted on the daily log.

### Multi-University

- TIMS and FOCUZ can use the same rule code with different values.
- Cloning TIMS to FOCUZ creates independent editable rules.
- Editing FOCUZ does not affect TIMS.
- Reports show the rule version used for each log.

## Non-Negotiable Rules

- No new hardcoded TIMS or FOCUZ values in services.
- No university-code conditionals for business logic.
- Every rule must be editable from the Admin Panel.
- Every calculation must persist a readable breakdown.
- Every daily log must know which rule version calculated it.
- Future universities must be addable by cloning and editing rules, without code changes.
