<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$user = User::where('email', 'cro@pms.local')->first();
echo 'CRO user exists: ' . ($user ? 'yes' : 'no') . PHP_EOL;
if ($user) {
    echo 'CRO user roles: ' . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;
    echo 'CRO user permissions: ' . implode(', ', $user->getPermissionNames()->toArray()) . PHP_EOL;
}

$role = Role::where('name', 'CRO')->first();
echo 'CRO role exists: ' . ($role ? 'yes' : 'no') . PHP_EOL;
if ($role) {
    echo 'CRO role permissions: ' . implode(', ', $role->permissions->pluck('name')->toArray()) . PHP_EOL;
}
