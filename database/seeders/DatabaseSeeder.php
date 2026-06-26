<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class, // 1. Permissions & Roles
            UserSeeder::class,           // 2. System Users
            CompanySeeder::class,        // 3. TIMS & FOCUZ Companies
            TimsRuleSeeder::class,       // 4. All TIMS Rules (from spec §7.1)
            FocuzRuleSeeder::class,      // 5. All FOCUZ Rules (from spec §7.2)
            DemoDataSeeder::class,       // 6. Zones & Sample Executives
        ]);
    }
}
