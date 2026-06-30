<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Rx Plus 365</title>
    <script src="https://kit.fontawesome.com/e2d123f69f.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f8faff;
        }

        /* ── Left panel ── */
        .left-panel {
            width: 45%;
            background: linear-gradient(145deg, #1e3a8a 0%, #1e40af 40%, #2563eb 70%, #3b82f6 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
        }
        @media (max-width: 900px) { .left-panel { display: none; } }

        .blob {
            position: absolute;
            border-radius: 50%;
            opacity: .12;
            pointer-events: none;
        }

        /* ── Right panel ── */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            overflow-y: auto;
        }

        .form-card {
            width: 100%;
            max-width: 420px;
        }

        /* ── Mobile logo ── */
        .mobile-logo { display: none; margin-bottom: 28px; }
        .mobile-logo a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .mobile-logo-icon {
            width: 36px; height: 36px;
            background: #2563eb;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #fff; font-weight: 900;
        }
        .mobile-logo-name { font-size: 18px; font-weight: 800; color: #1e293b; }

        /* ── Heading ── */
        .heading { margin-bottom: 28px; }
        .heading h1 {
            font-size: 26px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -.5px;
        }
        .heading p {
            margin-top: 8px;
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        /* ── Form ── */
        form { display: flex; flex-direction: column; gap: 18px; }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }
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
        .field-error { margin-top: 5px; font-size: 12px; color: #ef4444; }

        /* password eye toggle wrapper */
        .input-wrap { position: relative; }
        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            line-height: 0;
        }

        /* ── Strength bar ── */
        .strength-bar-wrap {
            margin-top: 8px;
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: width .3s, background .3s;
        }
        .strength-label {
            margin-top: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
        }

        /* ── Submit ── */
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(37,99,235,.5); }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled {
            opacity: .75;
            cursor: not-allowed;
            transform: none;
        }

        /* spinner */
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            flex-shrink: 0;
        }

        /* ── Left panel ── */
        .left-logo { position: relative; z-index: 1; }
        .left-logo a { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
        .left-logo-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,.2);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 900; color: #fff;
            border: 1px solid rgba(255,255,255,.25);
        }
        .left-logo-name { color: #fff; font-size: 18px; font-weight: 800; line-height: 1; }
        .left-logo-sub  { color: rgba(255,255,255,.65); font-size: 10px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; }

        .left-centre { position: relative; z-index: 1; }
        .left-centre h2 { color: #fff; font-size: 30px; font-weight: 900; line-height: 1.2; margin-bottom: 14px; }
        .left-centre p  { color: rgba(255,255,255,.75); font-size: 14px; line-height: 1.7; max-width: 300px; margin-bottom: 32px; }

        .tips { display: flex; flex-direction: column; gap: 8px; }
        .tip-item {
            display: flex; align-items: flex-start; gap: 10px;
            color: rgba(255,255,255,.8);
            font-size: 13px; font-weight: 600;
        }
        .tip-icon {
            width: 22px; height: 22px; border-radius: 50%;
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 11px;
        }

        .left-footer { position: relative; z-index: 1; color: rgba(255,255,255,.45); font-size: 12px; }

        @media (max-width: 900px) {
            .mobile-logo { display: block; }
            .right-panel { padding: 32px 20px; }
        }
    </style>
</head>
<body>

{{-- ── LEFT PANEL ── --}}
<div class="left-panel">
    <div class="blob" style="width:320px;height:320px;background:#60a5fa;top:-80px;right:-80px;"></div>
    <div class="blob" style="width:200px;height:200px;background:#93c5fd;bottom:60px;left:-60px;"></div>
    <div class="blob" style="width:120px;height:120px;background:#bfdbfe;bottom:200px;right:40px;"></div>

    <div class="left-logo">
        <a href="{{ route('home') }}">
            <div class="left-logo-icon">✚</div>
            <div>
                <p class="left-logo-name">Rx Plus 365</p>
                <p class="left-logo-sub">Pharmacy</p>
            </div>
        </a>
    </div>

    <div class="left-centre">
        <div style="width:100px;height:100px;border-radius:28px;display:flex;align-items:center;justify-content:center;margin-bottom:28px;">
            <img src="{{ asset('Images/key.png') }}" alt="Medical report" style="width:95px;">
        </div>
        <h2>Create a<br>New Password</h2>
        <p>Choose a strong password to keep your account safe.</p>
        <div class="tips">
            <div class="tip-item"><div class="tip-icon">✓</div> At least 8 characters</div>
            <div class="tip-item"><div class="tip-icon">✓</div> Mix of letters and numbers</div>
            <div class="tip-item"><div class="tip-icon">✓</div> Avoid personal information</div>
        </div>
    </div>

    <div class="left-footer">© {{ date('Y') }} Rx Plus 365, Ahmedabad</div>
</div>

{{-- ── RIGHT PANEL ── --}}
<div class="right-panel">
    <div class="form-card">

        <div class="mobile-logo">
            <a href="{{ route('home') }}">
                <div class="mobile-logo-icon">✚</div>
                <span class="mobile-logo-name">Rx Plus 365</span>
            </a>
        </div>

        <div class="heading">
            <h1>Set New Password <i class="fa-solid fa-lock" style="color: rgb(66, 0, 255);"></i></h1>
            <p>Your new password must be at least 8 characters long.</p>
        </div>

        <form method="post" action="{{ route('password.update') }}">
            @csrf

            {{-- Hidden fields --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div class="field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $email ?? '') }}"
                       required autofocus
                       placeholder="you@example.com"
                       class="{{ $errors->has('email') ? 'error' : '' }}">
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- New password --}}
            <div class="field">
                <label for="password">New Password</label>
                <div class="input-wrap">
                    <input type="password" id="password" name="password"
                           required
                           placeholder="Minimum 8 characters"
                           oninput="checkStrength(this.value)"
                           style="padding-right:44px;"
                           class="{{ $errors->has('password') ? 'error' : '' }}">
                    <button type="button" class="eye-btn"
                            onclick="toggleEye('password', this)">
                        <svg id="eye-password" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                <div class="strength-bar-wrap">
                    <div class="strength-bar" id="strength-bar"></div>
                </div>
                <p class="strength-label" id="strength-label"></p>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm password --}}
            <div class="field">
                <label for="password_confirmation">Confirm New Password</label>
                <div class="input-wrap">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           required
                           placeholder="Repeat your new password"
                           style="padding-right:44px;">
                    <button type="button" class="eye-btn"
                            onclick="toggleEye('password_confirmation', this)">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submit-btn">
                <span class="btn-spinner" id="btn-spinner"></span>
                <span id="btn-text">Reset Password →</span>
            </button>
        </form>

    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function () {
    var btn     = document.getElementById('submit-btn');
    var spinner = document.getElementById('btn-spinner');
    var text    = document.getElementById('btn-text');
    btn.disabled           = true;
    spinner.style.display  = 'block';
    text.textContent       = 'Resetting…';
});

function toggleEye(fieldId, btn) {
    var input = document.getElementById(fieldId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function checkStrength(val) {
    var bar   = document.getElementById('strength-bar');
    var label = document.getElementById('strength-label');
    if (!val) { bar.style.width = '0%'; label.textContent = ''; return; }

    var score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
    if (/\d/.test(val))   score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    var configs = [
        { pct: '20%', color: '#ef4444', text: 'Very weak'  },
        { pct: '40%', color: '#f97316', text: 'Weak'       },
        { pct: '60%', color: '#eab308', text: 'Fair'       },
        { pct: '80%', color: '#22c55e', text: 'Strong'     },
        { pct: '100%',color: '#16a34a', text: 'Very strong'},
    ];
    var c = configs[Math.min(score - 1, 4)] || configs[0];
    bar.style.width      = c.pct;
    bar.style.background = c.color;
    label.textContent    = c.text;
    label.style.color    = c.color;
}
</script>

</body>
</html>
