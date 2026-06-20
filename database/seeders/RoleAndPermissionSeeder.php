<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'manage_users',
            'manage_settings',
            'configure_rules',
            'enter_daily_logs',
            'manage_executives',
            'add_violations',
            'approve_audits',
            'view_reports',
            'view_deductions',
            'add_recommendations',
            'review_disputes',
            'view_escalations',
            'review_pips',
            'view_dashboards',
            'access_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $cro = Role::firstOrCreate(['name' => 'CRO']);
        $cro->givePermissionTo([
            'enter_daily_logs',
            'manage_executives',
            'add_violations',
            'approve_audits',
            'view_reports',
            'view_dashboards',
        ]);

        $manager = Role::firstOrCreate(['name' => 'Zonal Manager']);
        $manager->givePermissionTo([
            'view_reports',
            'view_deductions',
            'add_recommendations',
            'review_disputes',
            'view_dashboards',
        ]);

        $agm = Role::firstOrCreate(['name' => 'AGM']);
        $agm->givePermissionTo([
            'view_reports',
            'view_escalations',
            'view_dashboards',
        ]);

        $gm = Role::firstOrCreate(['name' => 'GM']);
        $gm->givePermissionTo([
            'view_reports',
            'view_dashboards',
            'review_pips',
        ]);

        $chairman = Role::firstOrCreate(['name' => 'Chairman']);
        $chairman->givePermissionTo([
            'view_reports',
            'view_dashboards',
            'view_escalations',
            'review_pips',
        ]);

        $developer = Role::firstOrCreate(['name' => 'Developer']);
        $developer->givePermissionTo([
            'access_logs',
            'view_dashboards',
        ]);
    }
}
