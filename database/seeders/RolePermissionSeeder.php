<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Permission list
        $perms = [
            'view dashboard',
            'view executives', 'manage executives',
            'view zones', 'manage zones',
            'view companies', 'manage companies',
            'view daily audit', 'create daily audit', 'edit daily audit', 'delete daily audit',
            'view audit history',
            'view reports',
            'view leaderboard',
            'view monthly ranking',
            'manage rule engine',
            'manage recovery engine',
            'manage quality bonus',
            'view transactions',
            'manage users', 'manage roles', 'manage settings'
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Roles mapping (names taken from app config or desired mapping)
        $roles = [
            'Super Admin',
            'CRO',
            'GM',
            'AGM',
            'Zone Manager',
            'Team Leader',
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // Assign permissions per role
        // Super Admin: everything
        $super = Role::where('name', 'Super Admin')->first();
        $super->syncPermissions(Permission::all());

        // CRO: broad access except user/role/system settings
        $cro = Role::where('name', 'CRO')->first();
        $croPerms = Permission::whereNotIn('name', ['manage users', 'manage roles', 'manage settings'])->get();
        $cro->syncPermissions($croPerms);

        // GM & AGM: read-only management access
        $gm = Role::where('name', 'GM')->first();
        $agm = Role::where('name', 'AGM')->first();
        $gmRead = Permission::whereIn('name', [
            'view dashboard', 'view reports', 'view leaderboard', 'view monthly ranking', 'view executives'
        ])->get();
        $gm->syncPermissions($gmRead);
        $agm->syncPermissions($gmRead);

        // Zone Manager: zone-scoped operations
        $zm = Role::where('name', 'Zone Manager')->first();
        $zmPerms = Permission::whereIn('name', [
            'view dashboard', 'view daily audit', 'view audit history', 'view reports', 'view transactions', 'view leaderboard', 'view zones'
        ])->get();
        $zm->syncPermissions($zmPerms);

        // Team Leader: team-scoped operations (read/write daily audit)
        $tl = Role::where('name', 'Team Leader')->first();
        $tlPerms = Permission::whereIn('name', [
            'view dashboard', 'view daily audit', 'create daily audit', 'view audit history', 'view reports', 'view leaderboard'
        ])->get();
        $tl->syncPermissions($tlPerms);
    }
}
