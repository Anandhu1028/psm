<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Zone;
use App\Models\Executive;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $tims  = Company::where('code', 'TIMS')->firstOrFail();
        $focuz = Company::where('code', 'FOCUZ')->firstOrFail();

        // Zones for TIMS
        $timsZones = collect(['North Zone', 'South Zone', 'East Zone', 'West Zone'])->map(fn($name, $i) =>
            Zone::firstOrCreate(['company_id' => $tims->id, 'name' => $name], [
                'code'   => strtoupper(substr($name, 0, 1)) . 'Z',
                'status' => 'active',
            ])
        );

        // Zones for FOCUZ
        $focuzZones = collect(['Metro Zone', 'City Zone', 'Suburban Zone'])->map(fn($name) =>
            Zone::firstOrCreate(['company_id' => $focuz->id, 'name' => $name], [
                'code'   => strtoupper(str_replace(' ', '', $name)),
                'status' => 'active',
            ])
        );

        // Sample TIMS Executives
        $timsExecutives = [
            ['name' => 'Arjun Mehta',     'employee_id' => 'TIMS001', 'mobile' => '9876543210'],
            ['name' => 'Priya Sharma',    'employee_id' => 'TIMS002', 'mobile' => '9876543211'],
            ['name' => 'Rahul Verma',     'employee_id' => 'TIMS003', 'mobile' => '9876543212'],
            ['name' => 'Sneha Patel',     'employee_id' => 'TIMS004', 'mobile' => '9876543213'],
            ['name' => 'Deepak Nair',     'employee_id' => 'TIMS005', 'mobile' => '9876543214'],
            ['name' => 'Aishwarya Kumar','employee_id' => 'TIMS006', 'mobile' => '9876543215'],
            ['name' => 'Vikram Singh',    'employee_id' => 'TIMS007', 'mobile' => '9876543216'],
            ['name' => 'Kavya Reddy',     'employee_id' => 'TIMS008', 'mobile' => '9876543217'],
        ];

        foreach ($timsExecutives as $i => $data) {
            Executive::firstOrCreate(['employee_id' => $data['employee_id']], array_merge($data, [
                'company_id'    => $tims->id,
                'zone_id'       => $timsZones[$i % 4]->id,
                'status'        => 'active',
                'date_joined'   => now()->subMonths(rand(2, 18)),
                'current_score' => rand(100, 1500),
                'monthly_score' => rand(10, 200),
                'current_tier'  => ['bronze', 'silver', 'gold', 'platinum'][rand(0, 3)],
            ]));
        }

        // Sample FOCUZ Executives
        $focuzExecutives = [
            ['name' => 'Mohammed Farhan',  'employee_id' => 'FCZ001', 'mobile' => '8765432100'],
            ['name' => 'Ananya Joseph',    'employee_id' => 'FCZ002', 'mobile' => '8765432101'],
            ['name' => 'Kiran Menon',      'employee_id' => 'FCZ003', 'mobile' => '8765432102'],
            ['name' => 'Divya Krishnan',   'employee_id' => 'FCZ004', 'mobile' => '8765432103'],
            ['name' => 'Suresh Thomas',    'employee_id' => 'FCZ005', 'mobile' => '8765432104'],
            ['name' => 'Reshma Pillai',    'employee_id' => 'FCZ006', 'mobile' => '8765432105'],
        ];

        foreach ($focuzExecutives as $i => $data) {
            Executive::firstOrCreate(['employee_id' => $data['employee_id']], array_merge($data, [
                'company_id'    => $focuz->id,
                'zone_id'       => $focuzZones[$i % 3]->id,
                'status'        => 'active',
                'date_joined'   => now()->subMonths(rand(2, 18)),
                'current_score' => rand(100, 1500),
                'monthly_score' => rand(10, 200),
                'current_tier'  => ['bronze', 'silver', 'gold', 'platinum'][rand(0, 3)],
            ]));
        }
    }
}
