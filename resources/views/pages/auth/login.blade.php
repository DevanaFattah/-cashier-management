<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — POS System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            overflow: hidden;
        }

        /* ── Background ── */
        .bg-scene {
            position: fixed; inset: 0; z-index: 0;
            background: #fafaf9;
        }
        /* Geometric grid pattern */
        .bg-scene::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(249,115,22,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(249,115,22,.06) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        /* Large orange orb top-right */
        .bg-scene::after {
            content: '';
            position: absolute;
            top: -160px; right: -160px;
            width: 560px; height: 560px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(251,146,60,.22) 0%, rgba(249,115,22,.06) 55%, transparent 75%);
            pointer-events: none;
        }
        /* Small orb bottom-left */
        .orb-bl {
            position: fixed;
            bottom: -100px; left: -100px;
            width: 340px; height: 340px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(249,115,22,.1) 0%, transparent 70%);
            pointer-events: none; z-index: 0;
        }

        /* ── Split Layout ── */
        .layout {
            position: relative; z-index: 1;
            height: 100vh;
            display: grid;
            grid-template-columns: 1fr 480px;
            grid-template-rows: 1fr;
        }

        /* ── Left panel — Brand ── */
        .brand-panel {
            display: flex; flex-direction: column;
            justify-content: center; padding: 60px;
            position: relative; overflow: hidden;
            height: 100%;
        }
        .brand-logo {
            display: inline-flex; align-items: center; gap: 12px;
            margin-bottom: 56px;
        }
        .brand-logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(249,115,22,.3);
        }
        .brand-logo-text {
            font-family: 'Syne', sans-serif;
            font-size: 18px; font-weight: 700;
            color: #0f172a; letter-spacing: -.02em;
        }
        .brand-headline {
            font-family: 'Syne', sans-serif;
            font-size: 52px; font-weight: 800;
            line-height: 1.05; letter-spacing: -.04em;
            color: #0f172a; margin-bottom: 20px;
        }
        .brand-headline span {
            background: linear-gradient(135deg, #f97316, #fb923c);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .brand-sub {
            font-size: 16px; font-weight: 300;
            color: #64748b; line-height: 1.7; max-width: 380px;
        }

        /* Feature list */
        .features {
            margin-top: 52px;
            display: flex; flex-direction: column; gap: 16px;
        }
        .feature-item {
            display: flex; align-items: center; gap: 14px;
            opacity: 0;
            animation: slideInLeft .5s cubic-bezier(.22,1,.36,1) forwards;
        }
        .feature-item:nth-child(1) { animation-delay: .1s; }
        .feature-item:nth-child(2) { animation-delay: .2s; }
        .feature-item:nth-child(3) { animation-delay: .3s; }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .feature-dot {
            width: 34px; height: 34px; flex-shrink: 0;
            background: rgba(249,115,22,.1); border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
        }
        .feature-text { font-size: 14px; color: #475569; font-weight: 400; }
        .feature-text strong { color: #0f172a; font-weight: 600; }

        /* Decorative card mockup */
        .deco-card {
            position: absolute; bottom: 60px; right: -20px;
            width: 220px;
            background: white;
            border-radius: 16px;
            padding: 16px;
            border: 1px solid rgba(249,115,22,.15);
            box-shadow: 0 24px 48px rgba(0,0,0,.08);
            opacity: 0;
            animation: floatIn .7s .4s cubic-bezier(.22,1,.36,1) forwards;
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .deco-card-row {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 10px;
        }
        .deco-card-label { font-size: 10px; color: #94a3b8; font-weight: 500; }
        .deco-card-val { font-size: 14px; font-weight: 700; color: #0f172a; font-family: 'Syne', sans-serif; }
        .deco-card-val.orange { color: #f97316; }
        .deco-bar { height: 4px; background: #f1f5f9; border-radius: 2px; margin-top: 8px; }
        .deco-bar-fill { height: 100%; border-radius: 2px; background: linear-gradient(90deg, #f97316, #fb923c); }

        /* ── Right panel — Form ── */
        .form-panel {
            background: white;
            display: flex; flex-direction: column; justify-content: center;
            padding: 48px 52px;
            position: relative;
            box-shadow: -24px 0 60px rgba(0,0,0,.06);
            border-left: 1px solid rgba(249,115,22,.08);
            height: 100%;
            overflow-y: auto;
        }

        .form-header { margin-bottom: 28px; }
        .form-eyebrow {
            font-size: 11px; font-weight: 600;
            letter-spacing: .15em; text-transform: uppercase;
            color: #f97316; margin-bottom: 8px;
        }
        .form-title {
            font-family: 'Syne', sans-serif;
            font-size: 26px; font-weight: 800;
            color: #0f172a; letter-spacing: -.03em;
            line-height: 1.15;
        }
        .form-sub { font-size: 13px; color: #94a3b8; margin-top: 5px; font-weight: 400; }

        /* Error alert */
        .alert-error {
            display: flex; align-items: flex-start; gap: 10px;
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 12px; padding: 12px 14px;
            margin-bottom: 20px;
        }
        .alert-error-text { font-size: 13px; color: #dc2626; line-height: 1.5; }

        /* Form fields */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 12px; font-weight: 600;
            letter-spacing: .05em; text-transform: uppercase;
            color: #64748b; margin-bottom: 8px;
        }
        .form-input-wrap { position: relative; }
        .form-input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #cbd5e1; transition: color .2s;
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 13px 14px 13px 42px;
            font-size: 14px; font-family: 'DM Sans', sans-serif;
            color: #0f172a;
            outline: none;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }
        .form-input:focus {
            border-color: #f97316;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(249,115,22,.1);
        }
        .form-input:focus + .form-input-icon-right,
        .form-input-wrap:focus-within .form-input-icon { color: #f97316; }
        .form-input.is-error { border-color: #f87171; background: #fff; }

        .form-error { font-size: 12px; color: #ef4444; margin-top: 6px; display: flex; align-items: center; gap-4px; }

        /* Password toggle */
        .toggle-pw {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #cbd5e1; padding: 4px; border-radius: 6px;
            transition: color .2s;
        }
        .toggle-pw:hover { color: #f97316; }

        /* Remember + Forgot */
        .form-meta {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 8px;
            cursor: pointer; font-size: 13px; color: #64748b;
        }
        .remember-check {
            width: 16px; height: 16px; border-radius: 4px;
            border: 1.5px solid #e2e8f0; accent-color: #f97316;
            cursor: pointer;
        }
        .forgot-link {
            font-size: 13px; font-weight: 500; color: #f97316;
            text-decoration: none; transition: color .2s;
        }
        .forgot-link:hover { color: #ea6c0c; text-decoration: underline; }

        /* Submit button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white; border: none;
            border-radius: 12px; padding: 14px;
            font-size: 15px; font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer; letter-spacing: .01em;
            box-shadow: 0 4px 16px rgba(249,115,22,.35);
            transition: transform .18s cubic-bezier(.34,1.56,.64,1), box-shadow .18s ease, filter .18s;
            position: relative; overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute; top: 0; left: -100%; width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.2), transparent);
            transition: left .4s ease;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(249,115,22,.4); }
        .btn-login:hover::before { left: 160%; }
        .btn-login:active { transform: scale(.98); }
        .btn-login:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Loading state */
        .btn-login.loading .btn-text { opacity: 0; }
        .btn-login.loading .btn-spinner { display: flex; }
        .btn-spinner {
            display: none; position: absolute; inset: 0;
            align-items: center; justify-content: center; gap: 6px;
        }
        .spinner-dot {
            width: 6px; height: 6px; border-radius: 50%; background: white;
            animation: spinnerDot .8s ease-in-out infinite;
        }
        .spinner-dot:nth-child(2) { animation-delay: .15s; }
        .spinner-dot:nth-child(3) { animation-delay: .3s; }
        @keyframes spinnerDot {
            0%, 80%, 100% { transform: scale(0.6); opacity: .4; }
            40% { transform: scale(1); opacity: 1; }
        }

        /* Divider */
        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 24px 0; color: #cbd5e1; font-size: 12px;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #f1f5f9;
        }

        /* Footer */
        .form-footer {
            margin-top: 28px; text-align: center;
            font-size: 12px; color: #94a3b8;
        }

        /* Entrance animation */
        .brand-panel { animation: fadeIn .5s ease forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>

<div class="bg-scene"></div>
<div class="orb-bl"></div>

<div class="layout">

    {{-- ── Left: Brand Panel ── --}}
    <div class="brand-panel">

        <div class="brand-logo">
            <div class="brand-logo-icon">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24">
                    <path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zm-9 8H7v-2h4v2zm6 0h-4v-2h4v2zM4 7V5a2 2 0 012-2h12a2 2 0 012 2v2H4z"/>
                </svg>
            </div>
            <span class="brand-logo-text">POS System</span>
        </div>

        <h1 class="brand-headline">
            Kelola toko<br>
            lebih <span>efisien.</span>
        </h1>
        <p class="brand-sub">
            Platform kasir modern untuk UMKM Indonesia. Transaksi cepat, laporan akurat, stok terkontrol.
        </p>

        <div class="features">
            <div class="feature-item">
                <div class="feature-dot">
                    <svg width="16" height="16" fill="none" stroke="#f97316" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <p class="feature-text"><strong>Transaksi kilat</strong> — proses checkout dalam hitungan detik</p>
            </div>
            <div class="feature-item">
                <div class="feature-dot">
                    <svg width="16" height="16" fill="none" stroke="#f97316" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <p class="feature-text"><strong>Laporan real-time</strong> — pantau pendapatan kapan saja</p>
            </div>
            <div class="feature-item">
                <div class="feature-dot">
                    <svg width="16" height="16" fill="none" stroke="#f97316" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p class="feature-text"><strong>Manajemen stok</strong> — notifikasi otomatis stok menipis</p>
            </div>
        </div>

        {{-- Decorative mini dashboard card --}}
        <div class="deco-card">
            <div class="deco-card-row">
                <span class="deco-card-label">Pendapatan Hari Ini</span>
            </div>
            <div class="deco-card-val orange">Rp 2.840.000</div>
            <div class="deco-bar"><div class="deco-bar-fill" style="width:72%"></div></div>
            <div class="deco-card-row" style="margin-top:10px;margin-bottom:0">
                <span class="deco-card-label">Transaksi</span>
                <span style="font-size:11px;font-weight:600;color:#16a34a;background:#f0fdf4;padding:2px 8px;border-radius:20px">+18%</span>
            </div>
            <div class="deco-card-val" style="font-size:20px">34</div>
        </div>
    </div>

    {{-- ── Right: Form Panel ── --}}
    <div class="form-panel">

        <div class="form-header">
            <p class="form-eyebrow">● Selamat datang</p>
            <h2 class="form-title">Masuk ke akun<br>Anda</h2>
            <p class="form-sub">Masukkan kredensial untuk melanjutkan</p>
        </div>

        {{-- Session error --}}
        @if (session('error'))
        <div class="alert-error">
            <svg width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="alert-error-text">{{ session('error') }}</p>
        </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
        <div class="alert-error">
            <svg width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <ul class="alert-error-text" style="list-style:none">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="form-input-wrap">
                    <span class="form-input-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@toko.com"
                        autocomplete="email"
                        class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                        required
                    >
                </div>
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="form-input-wrap">
                    <span class="form-input-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                        required
                    >
                    <button type="button" class="toggle-pw" onclick="togglePassword()" aria-label="Toggle password">
                        <svg id="eye-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="form-meta">
                <label class="remember-label">
                    <input type="checkbox" name="remember" class="remember-check" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login" id="btnLogin">
                <span class="btn-text">Masuk Sekarang</span>
                <span class="btn-spinner">
                    <span class="spinner-dot"></span>
                    <span class="spinner-dot"></span>
                    <span class="spinner-dot"></span>
                </span>
            </button>
        </form>

        <div class="divider">atau</div>

        <div class="form-footer">
            <p style="color:#64748b;font-size:13px">
                Butuh akses?
                <a href="mailto:admin@toko.com" style="color:#f97316;font-weight:500;text-decoration:none">Hubungi administrator</a>
            </p>
            <p style="margin-top:24px;color:#cbd5e1;font-size:11px">
                © {{ date('Y') }} POS System · Hak cipta dilindungi
            </p>
        </div>

    </div>
</div>

<script>
function togglePassword() {
    var input = document.getElementById('password');
    var icon  = document.getElementById('eye-icon');
    var show  = input.type === 'password';
    input.type = show ? 'text' : 'password';
    icon.innerHTML = show
        ? '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
}

document.getElementById('loginForm').addEventListener('submit', function() {
    var btn = document.getElementById('btnLogin');
    btn.classList.add('loading');
    btn.disabled = true;
});
</script>

</body>
</html>
