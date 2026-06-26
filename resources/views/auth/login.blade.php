<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — CRO Performance Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="{{ asset('css/pms.css') }}" rel="stylesheet">
</head>
<body style="font-family: 'Inter', sans-serif;">
<div class="login-bg">
    <div class="login-card">

        <div class="login-logo">
            <i class="fa-solid fa-chart-line"></i>
        </div>

        <h1 class="login-title">Welcome Back</h1>
        <p class="login-sub">CRO Performance Management System</p>

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-3" style="font-size:.83rem; background:#fef2f2; color:#dc2626; border-left:4px solid #dc2626!important;">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#f8f9fc; border-color:#e4e8f0; border-right:none;">
                        <i class="fa-regular fa-envelope" style="color:#94a3b8; font-size:.85rem;"></i>
                    </span>
                    <input type="email" name="email" id="email"
                           class="form-control"
                           style="border-left:none;"
                           value="{{ old('email') }}"
                           placeholder="admin@pms.local"
                           required autofocus autocomplete="email">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#f8f9fc; border-color:#e4e8f0; border-right:none;">
                        <i class="fa-solid fa-lock" style="color:#94a3b8; font-size:.85rem;"></i>
                    </span>
                    <input type="password" name="password" id="password"
                           class="form-control"
                           style="border-left:none; border-right:none;"
                           placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="input-group-text" id="togglePwd"
                            style="background:#f8f9fc; border-color:#e4e8f0; border-left:none; cursor:pointer;">
                        <i class="fa-regular fa-eye" style="color:#94a3b8; font-size:.85rem;" id="togglePwdIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:.82rem; color:#64748b;">
                        Remember me
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-pms-primary w-100" style="padding:11px; font-size:.9rem;" id="loginBtn">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In
            </button>
        </form>

        <div class="mt-4 p-3 rounded-3" style="background:#f8f9fc; border:1px solid #e4e8f0;">
            <p style="font-size:.7rem; color:#94a3b8; text-align:center; margin-bottom:8px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">Demo Credentials</p>
            <div class="row g-1">
                @foreach([['admin@pms.local','Super Admin'],['cro@pms.local','CRO'],['gm@pms.local','GM']] as [$em,$role])
                <div class="col-4">
                    <button type="button" class="btn demo-cred-btn w-100"
                            style="background:#fff; border:1px solid #e4e8f0; border-radius:6px; padding:5px; font-size:.65rem; color:#475569; cursor:pointer; transition:all .15s;"
                            onclick="document.getElementById('email').value='{{ $em }}'; document.getElementById('password').value='password';">
                        <span style="font-weight:700; display:block; color:#0f172a;">{{ $role }}</span>
                        <span style="color:#94a3b8;">{{ $em }}</span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePwd').addEventListener('click', function() {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('togglePwdIcon');
    if (pwd.type === 'password') {
        pwd.type  = 'text';
        icon.className = 'fa-regular fa-eye-slash';
    } else {
        pwd.type  = 'password';
        icon.className = 'fa-regular fa-eye';
    }
});
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="pms-spinner d-inline-block me-2" style="width:14px;height:14px;"></span> Signing in…';
});
</script>
<style>
.pms-spinner { border:2px solid rgba(255,255,255,.3); border-top-color:#fff; border-radius:50%; animation:spin .6s linear infinite; vertical-align:middle; }
@keyframes spin { to { transform:rotate(360deg); } }
</style>
</body>
</html>