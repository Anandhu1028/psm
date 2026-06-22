<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Run roles & permissions seeder
        $this->call(RoleAndPermissionSeeder::class);

        // 2. Run scoring rules configuration seeder
        $this->call(ScoreRuleSeeder::class);
        $this->call(DynamicRuleEngineSeeder::class);

        // 3. Create demo users for each role
        $roles = [
            'Super Admin' => 'admin@tims.com',
            'CRO' => 'cro@tims.com',
            'Zonal Manager' => 'manager@tims.com',
            'AGM' => 'agm@tims.com',
            'GM' => 'gm@tims.com',
            'Chairman' => 'chairman@tims.com',
            'Developer' => 'developer@tims.com',
        ];

        foreach ($roles as $roleName => $email) {
            $user = User::firstOrCreate([
                'email' => $email
            ], [
                'name' => $roleName . ' Account',
                'password' => bcrypt('password'),
            ]);

            // Assign Spatie Role
            $user->syncRoles([$roleName]);
        }

        // 4. Create zones and departments
        $northZone = \App\Models\Zone::firstOrCreate(['code' => 'ZONE_NORTH'], [
            'name' => 'North Zone',
            'manager_id' => User::where('email', 'manager@tims.com')->first()?->id,
        ]);

        $southZone = \App\Models\Zone::firstOrCreate(['code' => 'ZONE_SOUTH'], [
            'name' => 'South Zone',
            'manager_id' => User::where('email', 'manager@tims.com')->first()?->id,
        ]);

        $dept = \App\Models\Department::firstOrCreate(['code' => 'DEPT_ADM'], [
            'name' => 'Admissions Department',
        ]);

        // 5. Create demo executives (counselors) who do NOT have logins
        $tims = \App\Models\University::where('code', 'TIMS')->first();
        $timsId = $tims ? $tims->id : null;

        $executivesData = [
            [
                'employee_id' => 'EMP001',
                'name' => 'John Doe',
                'phone' => '+919988776655',
                'email' => 'john.doe@tims.com',
                'zone_id' => $northZone->id,
                'department_id' => $dept->id,
                'date_joined' => '2026-01-01',
                'probation_end_date' => '2026-07-01',
                'reporting_manager_id' => User::where('email', 'cro@tims.com')->first()?->id,
                'status' => 'active',
                'current_score' => 450,
                'current_tier' => 'silver',
                'university_id' => $timsId,
            ],
            [
                'employee_id' => 'EMP002',
                'name' => 'Alice Smith',
                'phone' => '+918877665544',
                'email' => 'alice.smith@tims.com',
                'zone_id' => $northZone->id,
                'department_id' => $dept->id,
                'date_joined' => '2026-05-15',
                'probation_end_date' => '2026-11-15',
                'reporting_manager_id' => User::where('email', 'cro@tims.com')->first()?->id,
                'status' => 'probation',
                'current_score' => 120,
                'current_tier' => 'bronze',
                'university_id' => $timsId,
            ],
            [
                'employee_id' => 'EMP003',
                'name' => 'Bob Johnson',
                'phone' => '+917766554433',
                'email' => 'bob.johnson@tims.com',
                'zone_id' => $southZone->id,
                'department_id' => $dept->id,
                'date_joined' => '2025-10-01',
                'probation_end_date' => '2026-04-01',
                'reporting_manager_id' => User::where('email', 'cro@tims.com')->first()?->id,
                'status' => 'active',
                'current_score' => 820,
                'current_tier' => 'gold',
                'university_id' => $timsId,
            ],
            [
                'employee_id' => 'EMP004',
                'name' => 'Sarah Connor',
                'phone' => '+916655443322',
                'email' => 'sarah.connor@tims.com',
                'zone_id' => $southZone->id,
                'department_id' => $dept->id,
                'date_joined' => '2026-02-15',
                'probation_end_date' => '2026-08-15',
                'reporting_manager_id' => User::where('email', 'cro@tims.com')->first()?->id,
                'status' => 'active',
                'current_score' => -10,
                'current_tier' => 'review_zone',
                'university_id' => $timsId,
            ]
        ];

        foreach ($executivesData as $exec) {
            \App\Models\Executive::updateOrCreate(
                ['employee_id' => $exec['employee_id']],
                $exec
            );
        }
    }
}
