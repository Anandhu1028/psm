<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles/permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_dashboard',
            'enter_daily_audit',
            'verify_recovery',
            'manage_executives',
            'manage_users',
            'configure_rules',
            'view_reports',
            'manage_companies',
            'manage_zones',
            'view_leaderboards',
            'view_point_history',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $roles = [
            'Super Admin' => $permissions, // all permissions
            'CRO'         => ['view_dashboard', 'enter_daily_audit', 'verify_recovery', 'manage_executives', 'view_reports', 'manage_zones', 'view_leaderboards', 'view_point_history'],
            'GM'          => ['view_dashboard', 'enter_daily_audit', 'view_reports', 'view_leaderboards', 'view_point_history'],
            'AGM'         => ['view_dashboard', 'enter_daily_audit', 'view_reports', 'view_leaderboards', 'view_point_history'],
            'Zone Manager'=> ['view_dashboard', 'enter_daily_audit', 'view_reports', 'view_leaderboards', 'view_point_history'],
            'Team Leader' => ['view_dashboard', 'view_reports', 'view_leaderboards'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }
    }
}
