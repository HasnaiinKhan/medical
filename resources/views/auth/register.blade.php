<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - Rx Plus 365</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh; display: flex; }

        /* ── Left panel ── */
        .left-panel {
            width: 42%;
            background: linear-gradient(145deg, #0f172a 0%, #1e3a8a 45%, #1e40af 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
        }
        @media (max-width: 960px) { .left-panel { display: none; } }

        .blob {
            position: absolute;
            border-radius: 50%;
            opacity: .10;
            pointer-events: none;
        }

        /* ── Right panel ── */
        .right-panel {
            flex: 1;
            background: #f8faff;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 24px;
            overflow-y: auto;
        }
        .form-card {
            width: 100%;
            max-width: 440px;
            padding: 8px 0 40px;
        }

        /* ── Input ── */
        .field input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            font-family: inherit;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .field input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.15);
        }
        .field input.error { border-color: #ef4444; background: #fff5f5; }

        /* ── Password strength ── */
        .strength-bar { height: 4px; border-radius: 99px; background: #e2e8f0; margin-top: 8px; overflow: hidden; }
        .strength-fill { height: 100%; border-radius: 99px; transition: width .3s, background .3s; width: 0; }

        /* ── Button ── */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 4px 14px rgba(37,99,235,.4);
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(37,99,235,.5); }
        .btn-submit:active { transform: translateY(0); }

        /* ── Steps ── */
        .step-dot {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

{{-- ── LEFT PANEL ── --}}
<div class="left-panel">
    <div class="blob" style="width:350px;height:350px;background:#3b82f6;top:-100px;right:-100px;"></div>
    <div class="blob" style="width:220px;height:220px;background:#60a5fa;bottom:40px;left:-70px;"></div>

    {{-- Logo --}}
    <div style="position:relative;z-index:1;">
        <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
            <div style="width:44px;height:44px;background:rgba(255,255,255,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;color:#fff;border:1px solid rgba(255,255,255,.25);">✚</div>
            <div>
                <p style="color:#fff;font-size:18px;font-weight:800;line-height:1;">Rx Plus 365</p>
                <!-- <p style="color:rgba(255,255,255,.6);font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;">Pharmacy</p> -->
            </div>
        </a>
    </div>

    {{-- Centre --}}
    <div style="position:relative;z-index:1;">
        <div style="width:90px;height:90px;border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:44px;margin-bottom:24px;">
            <img src="{{ asset('Images/medical-team.png') }}" alt="Medical Team">
        </div>
        <h2 style="color:#fff;font-size:28px;font-weight:900;line-height:1.25;margin-bottom:12px;">
            Join Rx Plus 365<br>Today
        </h2>
        <p style="color:rgba(255,255,255,.7);font-size:14px;line-height:1.7;max-width:280px;margin-bottom:36px;">
            Create your free account and get access to thousands of genuine medicines delivered to your door.
        </p>

        {{-- How it works --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            @foreach([
                ['1', 'Create your account', 'Takes less than 2 minutes'],
                ['2', 'Browse medicines', 'Search by name, brand or category'],
                ['3', 'Order & track', 'Fast delivery across Ahmedabad'],
            ] as [$n, $title, $sub])
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="step-dot" style="background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.25);">{{ $n }}</div>
                <div>
                    <p style="color:#fff;font-size:13px;font-weight:700;">{{ $title }}</p>
                    <p style="color:rgba(255,255,255,.55);font-size:12px;">{{ $sub }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div style="position:relative;z-index:1;">
        <p style="color:rgba(255,255,255,.4);font-size:12px;">© {{ date('Y') }} Rx Plus 365, Ahmedabad</p>
    </div>
</div>

{{-- ── RIGHT PANEL ── --}}
<div class="right-panel">
    <div class="form-card">

        {{-- Mobile logo --}}
        <div style="display:none;margin-bottom:24px;" class="mobile-logo">
            <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
                <div style="width:36px;height:36px;background:#2563eb;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;color:#fff;font-weight:900;">✚</div>
                <span style="font-size:18px;font-weight:800;color:#1e293b;">Rx Plus 365</span>
            </a>
        </div>

        {{-- Heading --}}
        <div style="margin-bottom:28px;">
            <h1 style="font-size:26px;font-weight:900;color:#0f172a;letter-spacing:-.5px;">Create your account ✨</h1>
            <p style="margin-top:6px;font-size:14px;color:#64748b;">Join thousands of customers ordering medicines online.</p>
        </div>

        {{-- Form --}}
        <form method="post" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:16px;">
            @csrf

            {{-- Name --}}
            <div class="field">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="Rahul Sharma"
                       class="{{ $errors->has('name') ? 'error' : '' }}">
                @error('name')<p style="margin-top:5px;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div class="field">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="you@example.com"
                       class="{{ $errors->has('email') ? 'error' : '' }}">
                @error('email')<p style="margin-top:5px;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
            </div>

            {{-- Password --}}
            <div class="field">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Password</label>
                <div style="position:relative;">
                    <input type="password" id="pwd" name="password" required minlength="8"
                           placeholder="Minimum 8 characters"
                           style="padding-right:44px;"
                           class="{{ $errors->has('password') ? 'error' : '' }}"
                           oninput="checkStrength(this.value)">
                    <button type="button"
                            onclick="const i=document.getElementById('pwd');i.type=i.type==='password'?'text':'password'"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                {{-- Strength bar --}}
                <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                <p id="strength-text" style="font-size:11px;color:#94a3b8;margin-top:4px;"></p>
                @error('password')<p style="margin-top:5px;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
            </div>

            {{-- Confirm Password --}}
            <div class="field">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Confirm Password</label>
                <div style="position:relative;">
                    <input type="password" id="pwd2" name="password_confirmation" required
                           placeholder="Re-enter your password"
                           style="padding-right:44px;">
                    <button type="button"
                            onclick="const i=document.getElementById('pwd2');i.type=i.type==='password'?'text':'password'"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            {{-- Terms --}}
            <div style="display:flex;align-items:flex-start;gap:8px;">
                <input type="checkbox" id="terms" required
                       style="width:16px;height:16px;margin-top:2px;accent-color:#2563eb;cursor:pointer;flex-shrink:0;">
                <label for="terms" style="font-size:13px;color:#64748b;cursor:pointer;line-height:1.5;">
                    I agree to the <a href="#" style="color:#2563eb;font-weight:600;">Terms of Service</a> and <a href="#" style="color:#2563eb;font-weight:600;">Privacy Policy</a>
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit" style="margin-top:4px;">Create Account →</button>
        </form>

        {{-- Divider --}}
        <div style="display:flex;align-items:center;gap:12px;margin:24px 0;">
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            <span style="font-size:12px;color:#94a3b8;font-weight:600;">ALREADY HAVE AN ACCOUNT?</span>
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
        </div>

        <a href="{{ route('login') }}"
           style="display:block;text-align:center;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px;font-size:14px;font-weight:700;color:#1e40af;text-decoration:none;background:#fff;transition:all .2s;"
           onmouseover="this.style.borderColor='#2563eb';this.style.background='#eff6ff'"
           onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#fff'">
            Sign in instead
        </a>
    </div>
</div>

<style>
@media (max-width: 960px) {
    .mobile-logo { display: flex !important; }
    .right-panel { padding: 32px 20px; align-items: flex-start; }
}
</style>

<script>
function checkStrength(val) {
    const fill = document.getElementById('strength-fill');
    const text = document.getElementById('strength-text');
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { w: '0%',   bg: '#e2e8f0', label: '' },
        { w: '25%',  bg: '#ef4444', label: 'Weak' },
        { w: '50%',  bg: '#f59e0b', label: 'Fair' },
        { w: '75%',  bg: '#3b82f6', label: 'Good' },
        { w: '100%', bg: '#16a34a', label: 'Strong 💪' },
    ];
    const l = levels[score];
    fill.style.width = l.w;
    fill.style.background = l.bg;
    text.textContent = l.label;
    text.style.color = l.bg;
}
</script>

</body>
</html>
