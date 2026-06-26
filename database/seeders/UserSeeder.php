<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Super Administrator', 'email' => 'admin@pms.local',    'role' => 'Super Admin'],
            ['name' => 'CRO Manager',         'email' => 'cro@pms.local',      'role' => 'CRO'],
            ['name' => 'General Manager',      'email' => 'gm@pms.local',       'role' => 'GM'],
            ['name' => 'Assistant GM',         'email' => 'agm@pms.local',      'role' => 'AGM'],
            ['name' => 'Zone Manager North',   'email' => 'zone@pms.local',     'role' => 'Zone Manager'],
            ['name' => 'Team Leader A',        'email' => 'tl@pms.local',       'role' => 'Team Leader'],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(['email' => $userData['email']], array_merge($userData, [
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]));

            $user->syncRoles([$role]);
        }
    }
}
