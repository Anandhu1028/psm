@extends('layouts.app')
@section('title', 'Users')
@section('breadcrumb')
<ol class="breadcrumb"><li class="breadcrumb-item active">Users</li></ol>
@endsection
@section('content')
<div class="pms-page-header">
    <div>
        <h1 class="pms-page-title"><i class="fa-solid fa-user-gear me-2" style="color:var(--pms-accent);"></i>System Users</h1>
        <p class="pms-page-subtitle">Manage user accounts and role assignments</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-pms-primary">
        <i class="fa-solid fa-user-plus me-2"></i>Add User
    </a>
</div>

<div class="pms-table-wrapper">
    <div class="users-table-scroll">
        <table class="pms-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Last Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--pms-accent),#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.7rem;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($user->name,0,2)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.83rem;color:var(--pms-text-primary);">{{ $user->name }}</div>
                                @if($user->phone)<div style="font-size:.68rem;color:var(--pms-text-muted);">{{ $user->phone }}</div>@endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.8rem;color:var(--pms-text-secondary);">{{ $user->email }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:var(--pms-accent-light);color:var(--pms-accent);font-size:.72rem;">
                            {{ $user->getRoleNames()->first() ?? '—' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:{{ $user->is_active?'var(--pms-success-subtle)':'var(--pms-danger-subtle)' }};color:{{ $user->is_active?'var(--pms-success)':'var(--pms-danger)' }};">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-center" style="font-size:.75rem;color:var(--pms-text-muted);">
                        {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : '—' }}
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm" style="background:var(--pms-warning-light);color:var(--pms-warning);border:none;padding:4px 8px;border-radius:6px;font-size:.72rem;"
                                    onclick="editUser({{ $user->id }},'{{ $user->name }}','{{ $user->phone }}','{{ $user->getRoleNames()->first() }}',{{ $user->is_active?'true':'false' }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn btn-sm" style="background:var(--pms-info-light);color:var(--pms-info);border:none;padding:4px 8px;border-radius:6px;font-size:.72rem;"
                                    onclick="resetPwd({{ $user->id }},'{{ $user->name }}')" title="Reset Password">
                                <i class="fa-solid fa-key"></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <form id="del-user-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST">@csrf @method('DELETE')</form>
                            <button type="button" class="btn btn-sm"
                                    style="background:var(--pms-danger-light);color:var(--pms-danger);border:none;padding:4px 8px;border-radius:6px;font-size:.72rem;"
                                    data-confirm-delete="{{ $user->name }}"
                                    data-form-id="del-user-{{ $user->id }}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="pms-empty"><i class="fa-solid fa-users"></i><p>No users found.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($users->hasPages())
<div class="d-flex justify-content-center mt-4">{{ $users->links() }}</div>
@endif

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius-lg);">
            <div class="modal-header" style="border-color:var(--pms-border);">
                <h5 class="modal-title" style="font-weight:700;font-size:.95rem;">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" id="editUserName" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" id="editUserPhone" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Role *</label>
                        <select name="role" id="editUserRole" class="form-select" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="editUserActive" value="1">
                        <label class="form-check-label" for="editUserActive">Account Active</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--pms-border);">
                    <button type="button" class="btn btn-pms-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-pms-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reset Password Modal --}}
<div class="modal fade" id="resetPwdModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius-lg);">
            <div class="modal-header" style="border-color:var(--pms-border);">
                <h5 class="modal-title" style="font-weight:700;font-size:.95rem;">Reset Password — <span id="resetUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPwdForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">New Password *</label><input type="password" name="password" class="form-control" required minlength="8" placeholder="Min 8 characters"></div>
                    <div class="mb-3"><label class="form-label">Confirm Password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
                </div>
                <div class="modal-footer" style="border-color:var(--pms-border);">
                    <button type="button" class="btn btn-pms-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-pms-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function editUser(id, name, phone, role, isActive) {
    document.getElementById('editUserForm').action = `/users/${id}`;
    document.getElementById('editUserName').value  = name;
    document.getElementById('editUserPhone').value = phone || '';
    document.getElementById('editUserRole').value  = role;
    document.getElementById('editUserActive').checked = isActive;
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}
function resetPwd(id, name) {
    document.getElementById('resetPwdForm').action = `/users/${id}/reset-password`;
    document.getElementById('resetUserName').textContent = name;
    new bootstrap.Modal(document.getElementById('resetPwdModal')).show();
}
</script>
@endpush
