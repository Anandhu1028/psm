<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(25);
        $roles = Role::orderBy('name')->get();
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data    = $request->validated();
        $roleName = $data['role'];
        unset($data['role'], $data['password_confirmation']);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->assignRole($roleName);

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} created with role {$roleName}.");
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:150'],
            'role'      => ['required', 'exists:roles,name'],
            'is_active' => ['boolean'],
            'phone'     => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name'      => $data['name'],
            'phone'     => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
        $user->syncRoles([$data['role']]);

        return back()->with('success', "User {$user->name} updated.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);
        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', "Password reset for {$user->name}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $user->delete();
        return back()->with('success', "User deleted.");
    }
}
