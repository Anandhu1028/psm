<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — CRO Performance Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="{{ asset('css/pms.css') }}" rel="stylesheet">
    <style>
        :root{
            --bg-canvas:#090d16;
            --white:#ffffff;
            --indigo-light:#818cf8;
            --indigo:#6366f1;
            --indigo-deep:#4f46e5;
            --slate:#9ca3af;
            --slate-soft:#6b7280;
            --ink:#f3f4f6;
            --line:rgba(255, 255, 255, 0.08);
            --line-soft:rgba(255, 255, 255, 0.04);
            --error-bg:rgba(239, 68, 68, 0.1);
            --error:#f87171;
            --font-display:'Inter', sans-serif;
            --font-body:'Inter', sans-serif;
            --font-mono:'IBM Plex Mono', monospace;
        }

        *{ box-sizing:border-box; }

        html,body{
            margin:0;
            min-height:100vh;
            font-family:var(--font-body);
            background:var(--bg-canvas);
            color:var(--ink);
            -webkit-font-smoothing:antialiased;
        }

        body{
            position:relative;
            background:
                radial-gradient(circle at 12% 15%, rgba(99, 102, 241, 0.12), transparent 45%),
                radial-gradient(circle at 88% 85%, rgba(124, 58, 237, 0.10), transparent 45%),
                var(--bg-canvas);
        }

        .login-wrap{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:48px 24px;
        }

        /* ---------- Card shell ---------- */
        .login-card{
            position:relative;
            width:100%;
            max-width:980px;
            display:grid;
            grid-template-columns:minmax(0,44%) minmax(0,56%);
            background:rgba(17, 24, 39, 0.6);
            backdrop-filter:blur(16px);
            -webkit-backdrop-filter:blur(16px);
            border-radius:26px;
            overflow:hidden;
            border:1px solid var(--line);
            box-shadow:0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255,255,255,0.05);
        }
        .login-card::before{
            content:"";
            position:absolute;
            top:0; left:0; right:0;
            height:3px;
            background:linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899);
            z-index:2;
        }

        /* ---------- Brand panel ---------- */
        .brand-panel{
            position:relative;
            background:linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(79, 70, 229, 0.02) 100%);
            color:#ffffff;
            padding:54px 46px 40px;
            display:flex;
            flex-direction:column;
            overflow:hidden;
            border-right:1px solid var(--line);
        }

        .brand-mark{
            display:flex;
            align-items:center;
            gap:14px;
            margin-bottom:auto;
        }

        .brand-mark-badge{
            width:44px; height:44px;
            border-radius:11px;
            background:linear-gradient(135deg, #6366f1, #7c3aed);
            display:flex; align-items:center; justify-content:center;
            font-family:var(--font-display);
            font-weight:800;
            font-size:.84rem;
            letter-spacing:.04em;
            color:#ffffff;
            flex:none;
            box-shadow:0 8px 20px -6px rgba(99, 102, 241, 0.5);
        }

        .brand-mark-text{ line-height:1.2; }
        .brand-mark-text .m1{
            display:block;
            font-size:.62rem;
            font-weight:800;
            letter-spacing:.18em;
            text-transform:uppercase;
            color:#818cf8;
        }
        .brand-mark-text .m2{
            display:block;
            font-size:.8rem;
            font-weight:600;
            color:#d1d5db;
        }

        .brand-copy{ margin-top:50px; }

        .brand-eyebrow{
            font-family:var(--font-mono);
            font-size:.66rem;
            font-weight:600;
            letter-spacing:.18em;
            text-transform:uppercase;
            color:#818cf8;
            margin-bottom:16px;
        }

        .brand-headline{
            font-family:var(--font-display);
            font-weight:800;
            font-size:2.2rem;
            line-height:1.2;
            letter-spacing:-.02em;
            margin:0 0 16px;
            color:#ffffff;
        }
        .brand-headline em{
            font-style:normal;
            color:#a78bfa;
            text-shadow:0 0 20px rgba(167, 139, 250, 0.35);
        }

        .brand-sub{
            font-size:.9rem;
            line-height:1.6;
            color:#9ca3af;
            max-width:34ch;
            margin:0;
        }

        .brand-chart-wrap{ margin-top:36px; }
        .brand-chart svg{ width:100%; height:auto; display:block; }

        .chart-line{
            stroke-dasharray:520;
            stroke-dashoffset:520;
            animation:drawline 1.4s .25s ease-out forwards;
            filter:drop-shadow(0 2px 8px rgba(99,102,241,0.4));
        }
        .chart-dot{
            opacity:0;
            animation:dotin .5s ease-out forwards;
        }
        .chart-tag{
            opacity:0;
            animation:dotin .5s .9s ease-out forwards;
        }
        .chart-pulse{
            opacity:0;
            animation:pulsering 2.2s 1.1s ease-out infinite;
            transform-origin:center;
        }
        @keyframes drawline{ to{ stroke-dashoffset:0; } }
        @keyframes dotin{ to{ opacity:1; } }
        @keyframes pulsering{
            0%{ opacity:.55; transform:scale(.4); }
            80%{ opacity:0; transform:scale(2.6); }
            100%{ opacity:0; transform:scale(2.6); }
        }

        .brand-stats{
            margin-top:34px;
            padding-top:22px;
            border-top:1px solid var(--line);
            display:flex;
            gap:24px;
            flex-wrap:wrap;
        }
        .brand-stat{
            font-size:.7rem;
            color:#9ca3af;
            display:flex;
            align-items:center;
            gap:7px;
            font-weight:500;
        }
        .brand-stat i{ color:#818cf8; font-size:.68rem; }

        /* ---------- Form panel ---------- */
        .form-panel{
            background:rgba(10, 15, 26, 0.4);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:54px 48px;
        }

        .form-inner{ width:100%; max-width:380px; }

        .form-heading{ margin-bottom:30px; }
        .form-heading h1{
            font-family:var(--font-display);
            font-weight:800;
            font-size:1.65rem;
            color:#ffffff;
            margin:0 0 6px;
        }
        .form-heading p{
            font-size:.85rem;
            color:#9ca3af;
            margin:0;
        }

        .alert-pms{
            font-size:.83rem;
            background:var(--error-bg);
            color:var(--error);
            border:none;
            border-left:3px solid #ef4444;
            border-radius:10px;
            padding:11px 14px;
            margin-bottom:22px;
        }

        .field{ margin-bottom:18px; }
        .field label{
            display:block;
            font-size:.73rem;
            font-weight:600;
            letter-spacing:.02em;
            color:#d1d5db;
            margin-bottom:7px;
        }

        .field-control{ position:relative; }
        .field-control i.f-icon{
            position:absolute;
            left:14px; top:50%;
            transform:translateY(-50%);
            color:#6b7280;
            font-size:.82rem;
            pointer-events:none;
        }
        .field-control input{
            width:100%;
            height:46px;
            padding:0 14px 0 40px;
            border:1px solid rgba(255, 255, 255, 0.08);
            border-radius:11px;
            background:rgba(255, 255, 255, 0.03);
            font-family:var(--font-body);
            font-size:.9rem;
            color:#ffffff;
            outline:none;
            transition:all .15s;
        }
        .field-control input::placeholder{ color:#4b5563; }
        .field-control input:focus{
            border-color:#6366f1;
            background:rgba(255, 255, 255, 0.05);
            box-shadow:0 0 0 3px rgba(99, 102, 241, 0.25);
        }
        .field-control input#password{ padding-right:42px; }

        .pwd-toggle{
            position:absolute;
            right:9px; top:50%;
            transform:translateY(-50%);
            width:28px; height:28px;
            border:none;
            background:transparent;
            color:#6b7280;
            border-radius:7px;
            cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            transition:all .15s;
        }
        .pwd-toggle:hover{ background:rgba(255, 255, 255, 0.08); color:#d1d5db; }

        .row-between{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:24px;
        }
        .remember-check{
            display:flex; align-items:center; gap:8px;
            font-size:.82rem; color:#9ca3af;
        }
        .remember-check input{ accent-color:#6366f1; width:15px; height:15px; cursor:pointer; }

        .btn-pms-primary{
            width:100%;
            height:48px;
            border:none;
            border-radius:11px;
            background:linear-gradient(135deg, #6366f1, #4f46e5);
            color:#fff;
            font-size:.88rem;
            font-weight:600;
            letter-spacing:.01em;
            display:flex; align-items:center; justify-content:center;
            cursor:pointer;
            box-shadow:0 10px 22px -10px rgba(99, 102, 241, 0.35);
            transition:all .18s;
        }
        .btn-pms-primary:hover{ background:linear-gradient(135deg, #4f46e5, #3b82f6); box-shadow:0 12px 26px -10px rgba(99, 102, 241, 0.45); transform: translateY(-1px); }
        .btn-pms-primary:active{ transform:translateY(1px); }
        .btn-pms-primary:disabled{ opacity:.75; cursor:default; }
        .btn-pms-primary i{ color:#ffffff; }

        .trust-line{
            margin-top:16px;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            font-size:.72rem;
            color:#6b7280;
        }
        .trust-line i{ color:#818cf8; font-size:.68rem; }

        .demo-box{
            margin-top:28px;
            padding:16px;
            border-radius:14px;
            background:rgba(255, 255, 255, 0.02);
            border:1px solid rgba(255, 255, 255, 0.05);
        }
        .demo-label{
            font-family:var(--font-mono);
            font-size:.62rem;
            font-weight:600;
            letter-spacing:.14em;
            text-transform:uppercase;
            color:#6b7280;
            margin-bottom:10px;
            text-align:center;
        }
        .demo-grid{
            display:grid;
            grid-template-columns:repeat(3, 1fr);
            gap:8px;
        }
        .demo-cred-btn{
            background:rgba(255, 255, 255, 0.04);
            border:1px solid rgba(255, 255, 255, 0.08);
            border-radius:9px;
            padding:9px 6px;
            cursor:pointer;
            transition:all .15s;
            text-align:center;
        }
        .demo-cred-btn:hover{
            border-color:#6366f1;
            background:rgba(255, 255, 255, 0.08);
            box-shadow:0 8px 16px -8px rgba(99, 102, 241, 0.35);
            transform:translateY(-2px);
        }
        .demo-cred-btn .role{
            display:block;
            font-size:.7rem;
            font-weight:700;
            color:#ffffff;
            margin-bottom:2px;
        }
        .demo-cred-btn .em{
            display:block;
            font-family:var(--font-mono);
            font-size:.59rem;
            color:#9ca3af;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        .pms-spinner{
            border:2px solid rgba(255,255,255,.3);
            border-top-color:#fff;
            border-radius:50%;
            animation:spin .6s linear infinite;
            vertical-align:middle;
        }
        @keyframes spin{ to{ transform:rotate(360deg); } }

        @media (max-width:900px){
            .login-wrap{ padding:28px 16px; }
            .login-card{ grid-template-columns:1fr; max-width:460px; border-radius:22px; }
            .brand-panel{
                padding:32px 28px;
                min-height:auto;
                border-right:none;
                border-bottom:1px solid var(--line);
            }
            .brand-copy{ margin-top:30px; }
            .brand-headline{ font-size:1.85rem; }
            .brand-chart-wrap{ display:none; }
            .brand-stats{ margin-top:22px; padding-top:18px; }
            .form-panel{ padding:36px 28px 44px; }
        }

        @media (prefers-reduced-motion: reduce){
            .chart-line, .chart-dot, .chart-tag, .chart-pulse{ animation:none !important; opacity:1 !important; stroke-dashoffset:0 !important; }
            *{ transition:none !important; }
        }
    </style>
</head>
<body>

<div class="login-wrap">
<div class="login-card">

    <!-- Brand panel -->
    <aside class="brand-panel">
        <div class="brand-mark">
            <div class="brand-mark-badge">CRO</div>
            <div class="brand-mark-text">
                <span class="m1">Performance</span>
                <span class="m2">Management System</span>
            </div>
        </div>

        <div class="brand-copy">
            <div class="brand-eyebrow">Performance command center</div>
            <h1 class="brand-headline">Every metric.<br>One <em>clear</em> picture.</h1>
            <p class="brand-sub">Sign in to track targets, compare regions, and keep every team aligned in real time.</p>

            <div class="brand-chart-wrap">
                <div class="brand-chart">
                    <svg viewBox="0 0 400 170" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="lineGradient" x1="0" y1="0" x2="400" y2="0">
                                <stop offset="0%" stop-color="#6366f1"/>
                                <stop offset="100%" stop-color="#ec4899"/>
                            </linearGradient>
                        </defs>

                        <line x1="0" y1="40" x2="400" y2="40" stroke="rgba(255,255,255,.04)" stroke-width="1"/>
                        <line x1="0" y1="85" x2="400" y2="85" stroke="rgba(255,255,255,.04)" stroke-width="1"/>
                        <line x1="0" y1="130" x2="400" y2="130" stroke="rgba(255,255,255,.04)" stroke-width="1"/>

                        <path class="chart-line" d="M5,128 C45,124 70,118 96,104 C124,89 148,108 176,92 C206,75 226,96 256,78 C286,60 308,70 336,46 C356,30 366,28 392,16"
                              stroke="url(#lineGradient)" stroke-width="2.5" stroke-linecap="round"/>

                        <circle class="chart-dot" style="animation-delay:.4s" cx="5" cy="128" r="3.5" fill="#6366f1"/>
                        <circle class="chart-dot" style="animation-delay:.55s" cx="96" cy="104" r="3.5" fill="#6366f1"/>
                        <circle class="chart-dot" style="animation-delay:.7s" cx="176" cy="92" r="3.5" fill="#6366f1"/>
                        <circle class="chart-dot" style="animation-delay:.85s" cx="256" cy="78" r="3.5" fill="#6366f1"/>

                        <circle class="chart-pulse" cx="392" cy="16" r="6" fill="none" stroke="#ec4899" stroke-width="2"/>
                        <circle class="chart-dot" style="animation-delay:1s" cx="336" cy="46" r="4.5" fill="#ec4899"/>
                        <circle class="chart-dot" style="animation-delay:1s" cx="392" cy="16" r="6" fill="#ec4899"/>

                        <text class="chart-tag" x="343" y="10" font-family="IBM Plex Mono, monospace" font-weight="700" font-size="12" fill="#ec4899">+18%</text>

                        <text x="5" y="156" font-family="Inter, sans-serif" font-size="10" fill="#6b7280">Jan</text>
                        <text x="96" y="156" font-family="Inter, sans-serif" font-size="10" fill="#6b7280">Feb</text>
                        <text x="176" y="156" font-family="Inter, sans-serif" font-size="10" fill="#6b7280">Mar</text>
                        <text x="256" y="156" font-family="Inter, sans-serif" font-size="10" fill="#6b7280">Apr</text>
                        <text x="336" y="156" font-family="Inter, sans-serif" font-size="10" fill="#6b7280">May</text>
                        <text x="380" y="156" font-family="Inter, sans-serif" font-size="10" fill="#6b7280">Jun</text>
                    </svg>
                </div>
            </div>

            <div class="brand-stats">
                <div class="brand-stat"><i class="fa-solid fa-arrows-rotate"></i> Live data sync</div>
                <div class="brand-stat"><i class="fa-solid fa-shield-halved"></i> Role-based access</div>
                <div class="brand-stat"><i class="fa-solid fa-file-shield"></i> Audit-ready reports</div>
            </div>
        </div>
    </aside>

    <!-- Form panel -->
    <main class="form-panel">
        <div class="form-inner">

            <div class="form-heading">
                <h1>Welcome back</h1>
                <p>Sign in to your account to continue.</p>
            </div>

            @if($errors->any())
                <div class="alert-pms">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf

                <div class="field">
                    <label for="email">Email address</label>
                    <div class="field-control">
                        <i class="fa-regular fa-envelope f-icon"></i>
                        <input type="email" name="email" id="email"
                               value="{{ old('email') }}"
                               placeholder="admin@pms.local"
                               required autofocus autocomplete="email">
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="field-control">
                        <i class="fa-solid fa-lock f-icon"></i>
                        <input type="password" name="password" id="password"
                               placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="pwd-toggle" id="togglePwd" aria-label="Show password">
                            <i class="fa-regular fa-eye" id="togglePwdIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="row-between">
                    <label class="remember-check">
                        <input type="checkbox" name="remember" id="remember">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn-pms-primary" id="loginBtn">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In
                </button>

                <div class="trust-line">
                    <i class="fa-solid fa-lock"></i> Secured, encrypted connection
                </div>
            </form>

            <div class="demo-box">
                <p class="demo-label">Demo credentials</p>
                <div class="demo-grid">
                    @foreach([['admin@pms.local','Super Admin'],['cro@pms.local','CRO'],['gm@pms.local','GM']] as [$em,$role])
                    <button type="button" class="demo-cred-btn"
                            onclick="document.getElementById('email').value='{{ $em }}'; document.getElementById('password').value='password';">
                        <span class="role">{{ $role }}</span>
                        <span class="em">{{ $em }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

        </div>
    </main>

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
</body>
</html>