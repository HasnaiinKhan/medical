<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - Rx Plus 365</title>
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
        .mobile-logo {
            display: none;
            margin-bottom: 28px;
        }
        .mobile-logo-inner {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .mobile-logo-icon {
            width: 36px;
            height: 36px;
            background: #2563eb;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #fff;
            font-weight: 900;
        }
        .mobile-logo-name {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
        }

        /* ── Heading ── */
        .heading { margin-bottom: 8px; }
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

        /* ── Back icon ── */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #2563eb;
            text-decoration: none;
            margin-bottom: 28px;
            transition: color .2s;
        }
        .back-link:hover { color: #1e40af; }
        .back-link svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* ── Alert ── */
        .alert-success {
            margin-bottom: 20px;
            padding: 14px 16px;
            background: linear-gradient(135deg, #1e3a8a, #2563eb, #3b82f6);
            border-radius: 12px;
            font-size: 13px;
            color: #fff;
            font-weight: 600;
            line-height: 1.5;
            box-shadow: 0 8px 24px rgba(37,99,235,.28);
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
        .field-error {
            margin-top: 5px;
            font-size: 12px;
            color: #ef4444;
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
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37,99,235,.5);
        }
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

        /* ── Sign in link ── */
        .signin-row {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }
        .signin-row a {
            color: #2563eb;
            font-weight: 700;
            text-decoration: none;
        }
        .signin-row a:hover { text-decoration: underline; }

        /* ── Left panel elements ── */
        .left-logo {
            position: relative;
            z-index: 1;
        }
        .left-logo a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .left-logo-icon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 900;
            color: #fff;
            border: 1px solid rgba(255,255,255,.25);
        }
        .left-logo-name {
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            line-height: 1;
        }
        .left-logo-sub {
            color: rgba(255,255,255,.65);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .left-centre { position: relative; z-index: 1; }
        .left-centre h2 {
            color: #fff;
            font-size: 30px;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 14px;
        }
        .left-centre p {
            color: rgba(255,255,255,.75);
            font-size: 14px;
            line-height: 1.7;
            max-width: 300px;
            margin-bottom: 32px;
        }

        .feature-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 99px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
        }
        .pills { display: flex; flex-direction: column; gap: 10px; }

        .left-footer {
            position: relative;
            z-index: 1;
            color: rgba(255,255,255,.45);
            font-size: 12px;
        }

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
            <img src="{{ asset('Images/forgot-password.png') }}" alt="Medical report" style="width:95px;">
        </div>
        <h2>Reset Your<br>Password</h2>
        <p>Enter your registered email and we'll send you a secure link to reset your password.</p>
        <div class="pills">
            <div class="feature-pill">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Secure token-based reset
            </div>
            <div class="feature-pill">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Link expires in 60 minutes
            </div>
        </div>
    </div>

    <div class="left-footer">© {{ date('Y') }} Rx Plus 365, Ahmedabad</div>
</div>

{{-- ── RIGHT PANEL ── --}}
<div class="right-panel">
    <div class="form-card">

        <div class="mobile-logo">
            <a href="{{ route('home') }}" class="mobile-logo-inner">
                <div class="mobile-logo-icon">✚</div>
                <span class="mobile-logo-name">Rx Plus 365</span>
            </a>
        </div>

        <a href="{{ route('login') }}" class="back-link">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Sign In
        </a>

        <div class="heading">
            <h1>Forgot Password? </h1>
            <p>No worries! Enter your email address below and we'll send you a link to reset your password.</p>
        </div>

        @if(session('status'))
            <div class="alert-success" style="margin-top:20px;">
                ✓ {{ session('status') }}
            </div>
        @endif

        <form method="post" action="{{ route('password.email') }}" style="margin-top:28px;">
            @csrf

            <div class="field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       required autofocus
                       placeholder="you@example.com"
                       class="{{ $errors->has('email') ? 'error' : '' }}">
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-submit" id="submit-btn">
                <span class="btn-spinner" id="btn-spinner"></span>
                <span id="btn-text">Send Reset Link →</span>
            </button>
        </form>

        <div class="signin-row">
            Remembered your password?
            <a href="{{ route('login') }}">Sign In</a>
        </div>

    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function () {
    var btn     = document.getElementById('submit-btn');
    var spinner = document.getElementById('btn-spinner');
    var text    = document.getElementById('btn-text');
    btn.disabled           = true;
    spinner.style.display  = 'block';
    text.textContent       = 'Sending…';
});
</script>
</body>
</html>
