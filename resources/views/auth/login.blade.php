<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - Rx Plus 365</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh; display: flex; }

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

        /* floating blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            opacity: .12;
            pointer-events: none;
        }

        /* ── Right panel ── */
        .right-panel {
            flex: 1;
            background: #f8faff;
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

        /* ── Feature pills ── */
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
    </style>
</head>
<body>

{{-- ── LEFT PANEL ── --}}
<div class="left-panel">
    {{-- Blobs --}}
    <div class="blob" style="width:320px;height:320px;background:#60a5fa;top:-80px;right:-80px;"></div>
    <div class="blob" style="width:200px;height:200px;background:#93c5fd;bottom:60px;left:-60px;"></div>
    <div class="blob" style="width:120px;height:120px;background:#bfdbfe;bottom:200px;right:40px;"></div>

    {{-- Logo --}}
    <div style="position:relative;z-index:1;">
        <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
            <div style="width:44px;height:44px;background:rgba(255,255,255,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;color:#fff;border:1px solid rgba(255,255,255,.25);">✚</div>
            <div>
                <p style="color:#fff;font-size:18px;font-weight:800;line-height:1;">Rx Plus 365</p>
                <p style="color:rgba(255,255,255,.65);font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;">Pharmacy</p>
            </div>
        </a>
    </div>

    {{-- Centre content --}}
    <div style="position:relative;z-index:1;">
        {{-- Big medical cross icon --}}
        <div style="width:100px;height:100px;border-radius:28px;display:flex;align-items:center;justify-content:center;font-size:48px;margin-bottom:28px;">
            <img src="{{ asset('Images/medical-report.png') }}" alt="Medical report">
        </div>
        <h2 style="color:#fff;font-size:30px;font-weight:900;line-height:1.2;margin-bottom:14px;">
            Your Health,<br>Our Priority
        </h2>
        <p style="color:rgba(255,255,255,.75);font-size:14px;line-height:1.7;max-width:300px;margin-bottom:32px;">
            Order genuine medicines online with fast delivery across Ahmedabad. Trusted by thousands of families.
        </p>

        {{-- Feature pills --}}
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div class="feature-pill">
             <div style="width:30px;height:10px;border-radius:28px;display:flex;align-items:center;justify-content:center;font-size:48px;margin:10px 0px 10px 0px;">    
            <img src="{{ asset('Images/delivery-bike.png') }}" alt="Medical report"></div> Free delivery on orders above ₹500
        
        </div>
            <div class="feature-pill"><div style="width:30px;height:10px;border-radius:28px;display:flex;align-items:center;justify-content:center;font-size:48px;margin:10px 0px 10px 0px;">
                
            <img src="{{ asset('Images/check.png') }}" alt="Medical report"></div>100% genuine medicines</div>


            <div class="feature-pill"><div style="width:30px;height:10px;border-radius:28px;display:flex;align-items:center;justify-content:center;font-size:48px;margin:10px 0px 10px 0px;"><img src="{{ asset('Images/atm-card.png') }}" alt="Medical report"></div> COD & online payment</div>
        </div>
    </div>

    {{-- Bottom --}}
    <div style="position:relative;z-index:1;">
        <p style="color:rgba(255,255,255,.45);font-size:12px;">© {{ date('Y') }} Rx Plus 365, Ahmedabad</p>
    </div>
</div>

{{-- ── RIGHT PANEL ── --}}
<div class="right-panel">
    <div class="form-card">

        {{-- Mobile logo --}}
        <div style="display:none;margin-bottom:28px;" class="mobile-logo">
            <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
                <div style="width:36px;height:36px;background:#2563eb;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;color:#fff;font-weight:900;">✚</div>
                <span style="font-size:18px;font-weight:800;color:#1e293b;">Rx Plus 365</span>
            </a>
        </div>

        {{-- Heading --}}
        <div style="margin-bottom:32px;">
            <h1 style="font-size:26px;font-weight:900;color:#0f172a;letter-spacing:-.5px;">Welcome back 👋</h1>
            <p style="margin-top:6px;font-size:14px;color:#64748b;">Sign in to your Rx Plus 365 account to continue.</p>
        </div>

        {{-- Flash --}}
        @if(session('status'))
            <div style="margin-bottom:20px;padding:12px 16px;background:linear-gradient(135deg,#1e3a8a,#2563eb,#3b82f6);border:1px solid rgba(255,255,255,.25);border-radius:12px;font-size:13px;color:#fff;box-shadow:0 12px 28px rgba(37,99,235,.28);">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="post" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:18px;">
            @csrf

            {{-- Email --}}
            <div class="field">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="you@example.com"
                       class="{{ $errors->has('email') ? 'error' : '' }}">
                @error('email')
                    <p style="margin-top:5px;font-size:12px;color:#ef4444;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="field">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Password</label>
                <div style="position:relative;">
                    <input type="password" id="pwd" name="password" required
                           placeholder="Your password"
                           style="padding-right:44px;"
                           class="{{ $errors->has('password') ? 'error' : '' }}">
                    <button type="button"
                            onclick="const i=document.getElementById('pwd');i.type=i.type==='password'?'text':'password'"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                @error('password')
                    <p style="margin-top:5px;font-size:12px;color:#ef4444;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember --}}
            <div style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="remember" id="remember"
                       style="width:16px;height:16px;accent-color:#2563eb;cursor:pointer;">
                <label for="remember" style="font-size:13px;color:#64748b;cursor:pointer;">Remember me for 30 days</label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit">Sign In →</button>
        </form>

        {{-- Divider --}}
        <div style="display:flex;align-items:center;gap:12px;margin:24px 0;">
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            <span style="font-size:12px;color:#94a3b8;font-weight:600;">NEW TO Rx Plus 365?</span>
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
        </div>

        <a href="{{ route('register') }}"
           style="display:block;text-align:center;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px;font-size:14px;font-weight:700;color:#1e40af;text-decoration:none;background:#fff;transition:all .2s;"
           onmouseover="this.style.borderColor='#2563eb';this.style.background='#eff6ff'"
           onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#fff'">
            Create a free account
        </a>
    </div>
</div>

<style>
@media (max-width: 900px) {
    .mobile-logo { display: flex !important; }
    .right-panel { padding: 32px 20px; }
}
</style>

</body>
</html>
