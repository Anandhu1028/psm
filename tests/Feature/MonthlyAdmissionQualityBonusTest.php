<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Executive;
use App\Models\Company;
use App\Models\Zone;
use App\Models\DailyAudit;
use App\Services\MonthlyPerformanceRankingService;
use App\Services\QualityBonusEngine;
use Carbon\Carbon;

class MonthlyAdmissionQualityBonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_three_quality_bonus_awarded_and_idempotent()
    {
        // Setup: create company, zone and executives
        $company = Company::create(['name' => 'ACME', 'code' => 'ACME', 'status' => 'active', 'calculation_strategy' => 'tims']);
        $zone = Zone::create(['company_id' => $company->id, 'name' => 'Zone 1', 'code' => 'Z1', 'status' => 'active']);

        $common = ['company_id' => $company->id, 'zone_id' => $zone->id, 'status' => 'active'];
        $e1 = Executive::create(array_merge($common, ['employee_id' => 'E1', 'name' => 'Exec One', 'monthly_admission_target' => 10]));
        $e2 = Executive::create(array_merge($common, ['employee_id' => 'E2', 'name' => 'Exec Two', 'monthly_admission_target' => 10]));
        $e3 = Executive::create(array_merge($common, ['employee_id' => 'E3', 'name' => 'Exec Three', 'monthly_admission_target' => 10]));
        $e4 = Executive::create(array_merge($common, ['employee_id' => 'E4', 'name' => 'Exec Four', 'monthly_admission_target' => 10]));

        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;

        // Create daily audits with admissions to give different achievements
        // e1: 9/10 => 90% eligible
        DailyAudit::create(['executive_id' => $e1->id, 'company_id' => $company->id, 'audit_date' => now(), 'audit_type' => 'tims', 'admissions_today' => 9]);
        // e2: 8/10 => 80% eligible
        DailyAudit::create(['executive_id' => $e2->id, 'company_id' => $company->id, 'audit_date' => now(), 'audit_type' => 'tims', 'admissions_today' => 8]);
        // e3: 7/10 => 70% not eligible
        DailyAudit::create(['executive_id' => $e3->id, 'company_id' => $company->id, 'audit_date' => now(), 'audit_type' => 'tims', 'admissions_today' => 7]);
        // e4: 10/10 => 100% eligible
        DailyAudit::create(['executive_id' => $e4->id, 'company_id' => $company->id, 'audit_date' => now(), 'audit_type' => 'tims', 'admissions_today' => 10]);

        $ranking = $this->app->make(MonthlyPerformanceRankingService::class);
        $engine = $this->app->make(QualityBonusEngine::class);

        $results = $ranking->calculate($month, $year, $company->id, null)->toArray();
        $eligible = array_values(array_filter($results, fn($row) => $row['eligible']));

        // Expect eligible executives: e4 (100%), e1 (90%), e2 (80%) -> top3
        $this->assertCount(4, $results);
        $this->assertCount(3, $eligible);
        $this->assertEquals($e4->id, $eligible[0]['executive']->id);
        $this->assertEquals($e1->id, $eligible[1]['executive']->id);
        $this->assertEquals($e2->id, $eligible[2]['executive']->id);

        $created = $engine->award($eligible, $month, $year);
        $this->assertCount(3, $created);

        // Second run should be idempotent
        $created2 = $engine->award($eligible, $month, $year);
        $this->assertCount(0, $created2);
    }

    public function test_monthly_quality_bonus_falls_back_to_top_three_by_achievement_when_no_one_meets_threshold()
    {
        $company = Company::create(['name' => 'GAMMA', 'code' => 'GAMMA', 'status' => 'active', 'calculation_strategy' => 'tims']);
        $zone = Zone::create(['company_id' => $company->id, 'name' => 'Zone B', 'code' => 'ZB', 'status' => 'active']);

        $common = ['company_id' => $company->id, 'zone_id' => $zone->id, 'status' => 'active'];
        $e1 = Executive::create(array_merge($common, ['employee_id' => 'G1', 'name' => 'Gamma One', 'monthly_admission_target' => 20]));
        $e2 = Executive::create(array_merge($common, ['employee_id' => 'G2', 'name' => 'Gamma Two', 'monthly_admission_target' => 20]));
        $e3 = Executive::create(array_merge($common, ['employee_id' => 'G3', 'name' => 'Gamma Three', 'monthly_admission_target' => 20]));
        $e4 = Executive::create(array_merge($common, ['employee_id' => 'G4', 'name' => 'Gamma Four', 'monthly_admission_target' => 20]));

        foreach ([
            [$e1, 7],
            [$e2, 6],
            [$e3, 5],
            [$e4, 4],
        ] as [$exec, $admissions]) {
            DailyAudit::create([
                'executive_id' => $exec->id,
                'company_id' => $company->id,
                'audit_date' => now(),
                'audit_type' => 'tims',
                'admissions_today' => $admissions,
            ]);
        }

        $ranking = $this->app->make(MonthlyPerformanceRankingService::class);
        $engine = $this->app->make(QualityBonusEngine::class);
        $results = $ranking->calculate(now()->month, now()->year, $company->id, null)->values()->all();

        $this->assertFalse($results[0]['eligible']);
        $this->assertFalse($results[1]['eligible']);
        $this->assertFalse($results[2]['eligible']);
        $this->assertFalse($results[3]['eligible']);

        $created = $engine->award(array_values(array_map(function ($row, $index) {
            $row['rank'] = $index + 1;
            return $row;
        }, array_slice($results, 0, 3), array_keys(array_slice($results, 0, 3)))), now()->month, now()->year);

        $this->assertCount(3, $created);
        $this->assertEquals([$e1->id, $e2->id, $e3->id], collect($created)->pluck('executive_id')->all());
    }

    public function test_ranking_service_includes_full_monthly_metrics_for_all_executives()
    {
        $company = Company::create(['name' => 'BETA', 'code' => 'BETA', 'status' => 'active', 'calculation_strategy' => 'tims']);
        $zone = Zone::create(['company_id' => $company->id, 'name' => 'Zone A', 'code' => 'ZA', 'status' => 'active']);

        $executive = Executive::create([
            'company_id' => $company->id,
            'zone_id' => $zone->id,
            'status' => 'active',
            'employee_id' => 'B1',
            'name' => 'Beta Exec',
            'monthly_admission_target' => 10,
        ]);

        DailyAudit::create([
            'executive_id' => $executive->id,
            'company_id' => $company->id,
            'audit_date' => now(),
            'audit_type' => 'tims',
            'admissions_today' => 7,
        ]);

        $ranking = $this->app->make(MonthlyPerformanceRankingService::class);
        $results = $ranking->calculate(now()->month, now()->year, $company->id, null);

        $metric = $results->firstWhere('executive.id', $executive->id);

        $this->assertNotNull($metric);
        $this->assertSame(7, $metric['admissions']);
        $this->assertSame(70.0, $metric['achievement']);
        $this->assertFalse($metric['eligible']);
        $this->assertNull($metric['rank']);
    }
}
