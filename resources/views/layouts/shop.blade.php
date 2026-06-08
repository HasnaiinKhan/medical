<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Medikart') - Pharmacy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://kit.fontawesome.com/e2d123f69f.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>


        html{
    font-size:17px;
}

@media (min-width:1440px){
    html{
        font-size:18px;
    }
}
        .new {
            width: 100%;
            max-width: 200px;
        }
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #bfdbfe;
            --accent: #3b82f6;
            --surface: #ffffff;
            --surface-2: #f8faff;
            --border: #bfdbfe;
            --text: #1e3a5f;
            --text-muted: #5b7fa6;
            --radius: 14px;
            --shadow-sm: 0 1px 3px rgba(59,130,246,.08), 0 1px 2px rgba(59,130,246,.05);
            --shadow-md: 0 4px 16px rgba(59,130,246,.12), 0 2px 6px rgba(59,130,246,.06);
            --shadow-lg: 0 12px 40px rgba(59,130,246,.14), 0 4px 12px rgba(59,130,246,.07);
        }


        *, *::before, *::after { box-sizing: border-box; }
         html {
            overflow-x: hidden;
            max-width: 100%;
        }
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background: #f0f7ff;
            color: var(--text);
            overflow-x: hidden;
            max-width: 100%;
            -webkit-font-smoothing: antialiased;
            /* Space for bottom mobile nav */
            padding-bottom: env(safe-area-inset-bottom, 0);
        }
        @media (max-width: 639px) {
            body { padding-bottom: calc(64px + env(safe-area-inset-bottom, 0px)); }
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── Header ── */
        .site-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 40%, #2563eb 70%, #3b82f6 100%);
            box-shadow: 0 2px 20px rgba(30,58,138,.35);
            position: relative;
            z-index: 60;   /* above the drawer (z-index: 45) */
        }
        .pin-bar {
            background: rgba(30,58,138,.25);
            border-top: 1px solid rgba(255,255,255,.15);
        }

        /* ── Logo ── */
        .logo-text {
            background: linear-gradient(135deg, #fff 30%, #b0d0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Buttons ── */
        .btn-primary {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: #fff;
            font-weight: 700;
            border-radius: var(--radius);
            transition: all .25s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 2px 8px rgba(37,99,235,.35);
            position: relative;
            overflow: hidden;
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
            opacity: 0;
            transition: opacity .25s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37,99,235,.5);
        }
        .btn-primary:hover::after { opacity: 1; }
        .btn-primary:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(37,99,235,.35); }

        .btn-outline {
            background: #fff;
            border: 1.5px solid var(--border);
            color: var(--text);
            font-weight: 600;
            border-radius: var(--radius);
            transition: all .25s cubic-bezier(.4,0,.2,1);
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #eff6ff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37,99,235,.15);
        }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
            transition: transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s cubic-bezier(.4,0,.2,1), border-color .25s;
        }
        .card:hover {
            border-color: #93c5fd;
            box-shadow: 0 12px 32px rgba(37,99,235,.12);
            transform: translateY(-3px);
        }
        .card-static {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
        }

        /* ── Medicine card image zoom ── */
        .med-img-wrap {
            overflow: hidden;
            border-radius: 14px 14px 0 0;
            background: linear-gradient(135deg, #dce8f8, #e8f0fb);
        }
        .med-img-wrap img {
            transition: transform .4s cubic-bezier(.4,0,.2,1);
        }
        .card:hover .med-img-wrap img {
            transform: scale(1.07);
        }

        /* ── Category pill ── */
        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .02em;
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        /* ── Badges ── */
        .badge-discount {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 6px;
            letter-spacing: .03em;
        }
        .badge-rx {
            background: #fef3c7;
            color: #92400e;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            border: 1px solid #fde68a;
        }

        /* ── Cart badge ── */
        .cart-badge {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            font-size: 10px;
            font-weight: 800;
        }

        /* ── Nav link underline ── */
        .nav-link { position: relative; }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 50%;
            width: 0; height: 2px;
            background: rgba(255,255,255,.8);
            border-radius: 2px;
            transition: all .2s;
            transform: translateX(-50%);
        }
        .nav-link:hover::after { width: 80%; }

        /* ── Alerts ── */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-animate {
            animation: slideDown .3s ease;
            transition: opacity .2s ease, transform .2s ease;
        }
        .alert-animate.is-hiding {
            opacity: 0;
            transform: translateY(-8px);
        }

        /* ── Section headings ── */
        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -.02em;
        }

        /* ── Input focus ── */
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(32,64,128,.15);
        }

        /* ── Skeleton shimmer ── */
        @keyframes shimmer {
            0%   { background-position: -400px 0; }
            100% { background-position: 400px 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 800px 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 8px;
        }

        /* ── Smooth page transitions ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        main > * { animation: fadeUp .35s ease both; }

        /* ── Gradient hero ── */
        .gradient-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 40%, #2563eb 70%, #3b82f6 100%);
        }

        /* ── Category card hover ── */
        .category-card {
            border-radius: 18px;
            transition: transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s, border-color .25s;
        }
        .category-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 10px 28px rgba(37,99,235,.15);
            border-color: #93c5fd !important;
        }
        .category-card .cat-icon {
            transition: transform .3s cubic-bezier(.34,1.56,.64,1);
        }
        .category-card:hover .cat-icon {
            transform: scale(1.2) rotate(-6deg);
        }

        /* ── Pagination ── */
        nav[aria-label="pagination"] span,
        nav[aria-label="pagination"] a {
            border-radius: 10px !important;
            font-weight: 600;
            font-size: 13px;
        }
        .whyus {
            width:100px;
            height:800px;
        }

        .hover:hover {
            color: blue;
            background-color:white;
            cursor: pointer;
        }

        /* ── Premium animations ── */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(18px); }
            to   { opacity:1; transform:translateY(0); }
        }
        main > * { animation: fadeUp .4s cubic-bezier(.4,0,.2,1) both; }
        main > *:nth-child(2) { animation-delay:.06s; }
        main > *:nth-child(3) { animation-delay:.12s; }
        main > *:nth-child(4) { animation-delay:.18s; }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,.18) !important;
            transition: box-shadow .2s, border-color .2s;
        }

        .trust-card {
            transition: transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s;
        }
        .trust-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(37,99,235,.12);
        }

        .medicine-card {
            transition: transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s, border-color .25s;
        }
        .medicine-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(37,99,235,.14);
            border-color: #93c5fd !important;
        }
        .medicine-card:hover img {
            transform: scale(1.06);
        }
        .medicine-card img {
            transition: transform .4s cubic-bezier(.4,0,.2,1);
        }

        .order-row {
            transition: background .2s, box-shadow .2s;
        }
        .order-row:hover {
            background: #f0f7ff;
            box-shadow: inset 3px 0 0 #2563eb;
        }

        a { transition: color .18s, opacity .18s; }

        .badge-discount, .badge-rx {
            transition: transform .2s cubic-bezier(.34,1.56,.64,1);
        }
        .badge-discount:hover, .badge-rx:hover {
            transform: scale(1.1);
        }

        .sidebar-link {
            transition: background .18s, color .18s, transform .18s;
        }
        .sidebar-link:hover {
            transform: translateX(3px);
        }

        /* ── Hide Alpine cloak until JS ready ── */
        [x-cloak] { display: none !important; }

        /* ══════════════════════════════════════
           HEADER NAV — plain CSS
           ══════════════════════════════════════ */

        /* Nav wrapper — flex row, right-aligned */
        .hdr-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-left: auto;   /* push everything to the right */
        }

        /* Desktop-only items hidden on mobile */
        .hdr-desktop-only { display: none; }
        @media (min-width: 640px) {
            .hdr-desktop-only { display: inline-flex; }
        }

        /* Shared nav link style */
        .hdr-nav-link {
            align-items: center;
            gap: 6px;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,.85);
            text-decoration: none;
            background: transparent;
            transition: background .15s, color .15s;
        }
        .hdr-nav-link:hover { background: rgba(255,255,255,.10); color: #fff; }
        .hdr-nav-link--amber { color: #fde68a; }
        .hdr-nav-link--amber:hover { color: #fff; }

        /* Cart button */
        .hdr-cart-btn {
            position: relative;
            align-items: center;
            gap: 6px;
            border-radius: 12px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.20);
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            transition: background .15s;
        }
        .hdr-cart-btn:hover { background: rgba(255,255,255,.25); }

        /* User menu wrapper */
        .hdr-user-menu { position: relative; }
        .hdr-user-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 12px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.20);
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            transition: background .15s;
        }
        .hdr-user-btn:hover { background: rgba(255,255,255,.25); }
        .hdr-user-avatar {
            width: 24px; height: 24px;
            border-radius: 50%;
            background: rgba(96,165,250,.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 900; color: #fff;
            flex-shrink: 0;
        }
        .hdr-user-name {
            max-width: 80px;
            overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
            font-size: 13px;
        }

        /* Dropdown panel */
        .hdr-dropdown {
            position: absolute;
            right: 0; top: calc(100% + 8px);
            width: 210px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 16px 48px rgba(0,0,0,.14);
            z-index: 50;
            overflow: hidden;
        }
        .hdr-dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .hdr-dropdown-name  { font-size: 12px; font-weight: 700; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .hdr-dropdown-email { font-size: 11px; color: #94a3b8; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .hdr-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            text-decoration: none;
            transition: background .12s, color .12s;
        }
        .hdr-dropdown-item:hover { background: #f8fafc; color: #2563eb; }
        .hdr-dropdown-item--amber { color: #b45309; }
        .hdr-dropdown-item--amber:hover { background: #fffbeb; color: #92400e; }
        .hdr-dropdown-item--red { color: #dc2626; }
        .hdr-dropdown-item--red:hover { background: #fef2f2; }
        .hdr-dropdown-item--btn {
            width: 100%; border: none; background: transparent; cursor: pointer; text-align: left;
        }

        /* Sign In / Register (desktop guest) */
        .hdr-signin-btn {
            display: inline-flex; align-items: center;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.25);
            padding: 8px 14px;
            font-size: 13px; font-weight: 600;
            color: #fff; text-decoration: none;
            transition: background .15s;
        }
        .hdr-signin-btn:hover { background: rgba(255,255,255,.10); }
        .hdr-register-btn {
            display: inline-flex; align-items: center;
            border-radius: 12px;
            background: #fff;
            padding: 8px 14px;
            font-size: 13px; font-weight: 700;
            color: #1d4ed8; text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,.10);
            transition: background .15s;
        }
        .hdr-register-btn:hover { background: #eff6ff; }

        /* ── Hamburger button — mobile only ── */
        .hdr-hamburger {
            display: none;              /* hidden on desktop */
            align-items: center;
            justify-content: center;
            width: 38px; height: 38px;
            border-radius: 999px;                        /* capsule / pill shape */
            background: #2563eb;                         /* solid blue */
            border: 2px solid rgba(255,255,255,.30);
            color: #fff;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .15s, box-shadow .15s;
            margin-left: auto;
            box-shadow: 0 2px 10px rgba(37,99,235,.45);
        }
        .hdr-hamburger:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 16px rgba(37,99,235,.6);
        }
        @media (max-width: 639px) {
            .hdr-hamburger { display: inline-flex; }
        }

        /* ══════════════════════════════════════
           MOBILE MENU PANEL
           ══════════════════════════════════════ */

        /* Backdrop */
        .mob-menu-backdrop {
            position: fixed;
            inset: 0;
            z-index: 40;   /* below header and drawer */
            background: rgba(15,23,42,.40);
        }

        /* Slide-in drawer — full height from the right */
        .mob-menu-panel {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 280px;
            max-width: 85vw;
            z-index: 45;   /* below header (z-index: 60) so hamburger stays visible */
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
            box-shadow: -8px 0 40px rgba(30,58,138,.50);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .mob-menu-inner {
            padding: 20px 14px 32px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 100%;
        }

        /* Close button row at the top of the drawer */
        .mob-menu-close-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .mob-menu-brand {
            font-size: 16px;
            font-weight: 900;
            color: #fff;
            letter-spacing: -.01em;
        }
        .mob-menu-close-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px; height: 34px;
            border-radius: 999px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.20);
            color: #fff;
            cursor: pointer;
            transition: background .15s;
            flex-shrink: 0;
        }
        .mob-menu-close-btn:hover { background: rgba(255,255,255,.25); }

        /* Search bar */
        .mob-search-form { position: relative; }
        .mob-search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.95);
            border-radius: 999px;           /* pill shape */
            padding: 10px 8px 10px 14px;
            box-shadow: 0 4px 16px rgba(30,58,138,.25);
        }
        .mob-search-input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 13px;
            color: #0f172a;
            outline: none;
            min-width: 0;
        }
        .mob-search-input::placeholder { color: #94a3b8; }
        .mob-search-btn {
            border: none;
            border-radius: 999px;           /* pill shape */
            background: #2563eb;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 8px 16px;
            cursor: pointer;
            transition: background .15s;
            flex-shrink: 0;
        }
        .mob-search-btn:hover { background: #1d4ed8; }

        /* Divider label */
        .mob-menu-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.45);
            padding: 0 6px;
        }

        /* Nav links list */
        .mob-nav-links {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .mob-menu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 999px;           /* pill / capsule shape */
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,.92);
            text-decoration: none;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
            transition: background .15s, border-color .15s, color .15s;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }
        .mob-menu-link:hover {
            background: rgba(255,255,255,.20);
            border-color: rgba(255,255,255,.25);
            color: #fff;
        }
        .mob-menu-link svg { flex-shrink: 0; opacity: .85; }

        /* Variants */
        .mob-menu-link--amber {
            background: rgba(251,191,36,.12);
            border-color: rgba(251,191,36,.25);
            color: #fde68a;
        }
        .mob-menu-link--amber:hover {
            background: rgba(251,191,36,.22);
            border-color: rgba(251,191,36,.4);
            color: #fef3c7;
        }
        .mob-menu-link--red {
            background: rgba(239,68,68,.12);
            border-color: rgba(239,68,68,.25);
            color: #fca5a5;
        }
        .mob-menu-link--red:hover {
            background: rgba(239,68,68,.22);
            border-color: rgba(239,68,68,.4);
            color: #fecaca;
        }
        .mob-menu-link--register {
            background: rgba(255,255,255,.18);
            border-color: rgba(255,255,255,.30);
            color: #fff;
            font-weight: 700;
        }
        .mob-menu-link--btn { border: none; cursor: pointer; }

        /* Cart count pill */
        .mob-cart-pill {
            margin-left: auto;
            min-width: 22px;
            height: 22px;
            background: #fff;
            border-radius: 999px;
            padding: 0 7px;
            font-size: 11px;
            font-weight: 800;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* User info card */
        .mob-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 14px;
            padding: 11px 14px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.15);
        }
        .mob-user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 900; color: #1d4ed8;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(30,58,138,.25);
        }
        .mob-user-name  { font-size: 12px; font-weight: 700; color: #fff; }
        .mob-user-email { font-size: 10px; color: rgba(255,255,255,.55); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 180px; }

        /* ═══════════════════════════════════
           BOTTOM MOBILE NAV BAR
           ─────────────────────────────────── */
        #mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 20px rgba(30,58,138,.10);
            padding-bottom: env(safe-area-inset-bottom, 0px);
            display: none; /* hidden on desktop via JS media query */
        }
        @media (max-width: 639px) {
            #mobile-bottom-nav { display: flex; }
        }
        .mob-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            padding: 10px 4px 8px;
            text-decoration: none;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .02em;
            transition: color .18s;
            position: relative;
        }
        .mob-nav-item svg {
            width: 22px;
            height: 22px;
            stroke: currentColor;
            transition: stroke .18s, transform .18s;
        }
        .mob-nav-item.active,
        .mob-nav-item:hover {
            color: #2563eb;
        }
        .mob-nav-item.active svg,
        .mob-nav-item:hover svg {
            transform: translateY(-2px);
        }
        .mob-nav-item.active::before {
            content: '';
            position: absolute;
            top: 0; left: 50%;
            transform: translateX(-50%);
            width: 28px; height: 3px;
            background: #2563eb;
            border-radius: 0 0 4px 4px;
        }
        .mob-nav-cart-badge {
            position: absolute;
            top: 6px;
            left: 50%;
            margin-left: 6px;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            border-radius: 99px;
            font-size: 9px;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        /* ── Pin bar compact on mobile ── */
        @media (max-width: 639px) {
            .pin-bar .mx-auto {
                flex-wrap: wrap;
                gap: 6px;
            }
            .pin-bar input {
                min-width: 0;
                flex: 1 1 120px;
            }

              /* ── WhatsApp button: lift above bottom nav on mobile ── */
        
            /* Covers the most common WhatsApp button patterns */
            .whatsapp-float,
            .whatsapp-button,
            .whatsapp-btn,
            [class*="whatsapp"],
            [id*="whatsapp"],
            a[href*="wa.me"],
            a[href*="whatsapp"] {
             #wa-widget {
                bottom: calc(72px + env(safe-area-inset-bottom, 0px)) !important;
            }
        }

       
        
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">

    {{-- ===== TOP HEADER ===== --}}
    <header id="site-header" class="sticky top-0 z-50 site-header">

        {{-- x-data wraps EVERYTHING in the header so mobile menu state is accessible --}}
        <div x-data="{ mobileMenuOpen: false }">

            {{-- Main nav bar --}}
            <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
            
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group flex-shrink-0">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-lg font-black text-white group-hover:bg-white/25 transition-all ring-1 ring-white/20">
                        ✚
                    </div>
                    <div class="leading-none">
                        <span class="logo-text text-xl font-extrabold tracking-tight">Medikart</span>
                        <span class="ml-1.5 hidden text-[10px] font-semibold text-white sm:inline tracking-widest logo-text">Pharmacy</span>
                    </div>
                </a>

                 

                {{-- Search bar (desktop only) --}}
                <form action="{{ route('medicines.index') }}" method="get"
                      class="relative z-[120] hidden flex-1 max-w-xl mx-6 lg:flex"
                      data-medicine-suggest-form>
                    <div class="flex w-full items-center rounded-xl overflow-hidden bg-white/95 shadow-lg ring-1 ring-white/30">
                        <input type="search" name="q" value="{{ request('q') }}"
                               data-medicine-suggest-input
                               autocomplete="off"
                               aria-autocomplete="list"
                               aria-expanded="false"
                               placeholder="Search medicines, brands, categories…"
                               class="flex-1 px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 bg-transparent focus:outline-none">
                        <button type="submit"
                                class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white text-sm font-bold transition-colors">
                            Search
                        </button>
                    </div>
                    <div data-medicine-suggestions
                         class="absolute left-0 right-0 top-full z-[130] mt-2 hidden w-full min-w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                    </div>
                </form>

                {{-- Nav links (desktop) --}}
                <nav class="hdr-nav">

                    {{-- ── Hamburger button — mobile only ── --}}
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="hdr-hamburger"
                            :aria-expanded="mobileMenuOpen"
                            aria-label="Toggle menu">
                        <svg x-show="!mobileMenuOpen" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <a href="{{ route('medicines.index') }}" class="nav-link hdr-nav-link hdr-desktop-only">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Medicines
                    </a>

                    @auth
                        <a href="{{ route('orders.index') }}" class="nav-link hdr-nav-link hdr-desktop-only">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            My Orders
                        </a>
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="nav-link hdr-nav-link hdr-nav-link--amber hdr-desktop-only">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Admin
                            </a>
                        @endif
                    @endauth

                    {{-- Cart (desktop only — mobile uses bottom nav) --}}
                    <a id="cart-nav-link" href="{{ route('cart.index') }}" class="hdr-cart-btn hdr-desktop-only">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Cart
                        <span id="cart-count-badge" class="cart-badge {{ ($cartCount ?? 0) > 0 ? '' : 'hidden' }}">{{ $cartCount ?? 0 }}</span>
                    </a>

                    @auth
                        <div class="hdr-user-menu hdr-desktop-only" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="hdr-user-btn">
                                <span class="hdr-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                <span class="hdr-user-name">{{ auth()->user()->name }}</span>
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.6;transition:transform .2s;" :style="open ? 'transform:rotate(180deg)' : ''"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="hdr-dropdown">
                                <div class="hdr-dropdown-header">
                                    <p class="hdr-dropdown-name">{{ auth()->user()->name }}</p>
                                    <p class="hdr-dropdown-email">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('orders.index') }}" class="hdr-dropdown-item">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    My Orders
                                </a>
                                @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="hdr-dropdown-item hdr-dropdown-item--amber">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Admin Panel
                                </a>
                                @endif
                                <form method="post" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="hdr-dropdown-item hdr-dropdown-item--red hdr-dropdown-item--btn">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hdr-signin-btn hdr-desktop-only">Sign In</a>
                        <a href="{{ route('register') }}" class="hdr-register-btn hdr-desktop-only">Register</a>
                    @endguest
                </nav>
            </div>

            {{-- ── Mobile menu backdrop ── --}}
            <div x-show="mobileMenuOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mob-menu-backdrop"
                 @click="mobileMenuOpen = false"
                 aria-hidden="true"></div>

            {{-- ── Mobile right-side drawer ── --}}
            <div x-show="mobileMenuOpen" x-cloak
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-full"
                 class="mob-menu-panel">
                <div class="mob-menu-inner">

                    {{-- Close row --}}
                    <div class="mob-menu-close-row">
                        <span class="mob-menu-brand">✚ Medikart</span>
                        <button @click="mobileMenuOpen = false" class="mob-menu-close-btn" aria-label="Close menu">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Mobile search --}}
                    <form action="{{ route('medicines.index') }}" method="get" class="mob-search-form" data-medicine-suggest-form>
                        <div class="mob-search-box">
                            <svg width="16" height="16" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="search" name="q" value="{{ request('q') }}" data-medicine-suggest-input
                                   autocomplete="off"
                                   placeholder="Search medicines, brands…"
                                   class="mob-search-input">
                            <button type="submit" class="mob-search-btn">Search</button>
                        </div>
                        <div data-medicine-suggestions
                             class="absolute left-0 right-0 top-full z-[130] mt-2 hidden w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                        </div>
                    </form>

                    {{-- Mobile nav links --}}
                    <nav class="mob-nav-links">
                        <a href="{{ route('medicines.index') }}" @click="mobileMenuOpen = false" class="mob-menu-link">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Medicines
                        </a>

                        <a href="{{ route('cart.index') }}" @click="mobileMenuOpen = false" class="mob-menu-link mob-menu-link--cart">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Cart
                            <span class="mob-cart-pill">{{ $cartCount ?? 0 }}</span>
                        </a>

                        @auth
                            <a href="{{ route('orders.index') }}" @click="mobileMenuOpen = false" class="mob-menu-link">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                My Orders
                            </a>
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="mob-menu-link mob-menu-link--amber">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Admin Dashboard
                                </a>
                            @endif

                            {{-- User info row --}}
                            <div class="mob-user-info">
                                <div class="mob-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                <div>
                                    <p class="mob-user-name">{{ auth()->user()->name }}</p>
                                    <p class="mob-user-email">{{ auth()->user()->email }}</p>
                                </div>
                            </div>

                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="mob-menu-link mob-menu-link--red mob-menu-link--btn">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="mob-menu-link">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="mob-menu-link mob-menu-link--register">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                Register
                            </a>
                        @endguest
                    </nav>
                </div>
            </div>

        </div>{{-- end x-data --}}

        {{-- Pincode bar --}}
        <div class="pin-bar">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-4 py-2 sm:px-6">
                <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-white flex-shrink-0">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Delivery
                </div>
                <div class="flex flex-1 flex-wrap items-center gap-2 min-w-0">
                    <input id="pin-input" maxlength="6" inputmode="numeric" placeholder="Enter pincode…"
                           class="new h-auto rounded-lg border border-white/15 bg-white/10 px-3 py-1.5 text-xs text-white placeholder:text-blue-300/60 focus:border-white/40 focus:bg-white/15 focus:outline-none transition-all min-w-0">
                    <button type="button" id="pin-lookup-btn"
                            class="rounded-lg bg-white/10 border border-white/20 px-3 py-1.5 text-xs font-bold text-white hover:bg-white/20 transition-all flex-shrink-0">
                        Check
                    </button>
                    <button type="button" id="pin-confirm-btn" hidden
                            class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-500 transition-all shadow-sm flex-shrink-0">
                        ✓ Deliver here
                    </button>
                </div>
                <div id="pin-status" class="text-xs font-semibold text-white w-full sm:w-auto"></div>
            </div>
        </div>

    </header>

    {{-- ===== PAGE LOADER ===== --}}
    <style>
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 55%, #2563eb 100%);
            transition: opacity .45s ease, visibility .45s ease;
        }
        #page-loader.hide {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .loader-pill {
            position: relative;
            width: 90px;
            height: 36px;
            border-radius: 99px;
            overflow: hidden;
            box-shadow: 0 0 0 3px rgba(255,255,255,.15), 0 8px 32px rgba(0,0,0,.3);
            animation: pillBounce 1.1s ease-in-out infinite;
        }
        .loader-pill-left {
            position: absolute;
            left: 0; top: 0;
            width: 50%; height: 100%;
            background: linear-gradient(135deg, #dce8f8, #b0d0f0);
        }
        .loader-pill-right {
            position: absolute;
            right: 0; top: 0;
            width: 50%; height: 100%;
            background: linear-gradient(135deg, #1e40af, #2563eb);
        }
        .loader-pill-divider {
            position: absolute;
            left: 50%; top: 0;
            width: 2px; height: 100%;
            background: rgba(255,255,255,.5);
            transform: translateX(-50%);
        }
        .loader-pill::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,.35) 50%, transparent 65%);
            animation: pillShine 1.6s ease-in-out infinite;
        }
        @keyframes pillBounce {
            0%,100% { transform: translateY(0) rotate(-4deg); }
            50%      { transform: translateY(-10px) rotate(4deg); }
        }
        @keyframes pillShine {
            0%   { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
        .loader-orbit {
            position: absolute;
            width: 110px;
            height: 110px;
            animation: orbitSpin 2.4s linear infinite;
        }
        .loader-orbit-dot {
            position: absolute;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,.55);
            top: 0; left: 50%;
            transform: translateX(-50%);
        }
        .loader-orbit-dot:nth-child(2) {
            top: auto; bottom: 0;
            background: rgba(176,208,240,.7);
        }
        @keyframes orbitSpin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        .loader-cross {
            margin-top: 28px;
            position: relative;
            width: 28px; height: 28px;
            animation: crossPulse 1.1s ease-in-out infinite;
        }
        .loader-cross::before,
        .loader-cross::after {
            content: '';
            position: absolute;
            background: rgba(255,255,255,.9);
            border-radius: 3px;
        }
        .loader-cross::before { width: 100%; height: 30%; top: 35%; left: 0; }
        .loader-cross::after  { width: 30%; height: 100%; top: 0; left: 35%; }
        @keyframes crossPulse {
            0%,100% { transform: scale(1);    opacity: .9; }
            50%      { transform: scale(1.25); opacity: 1; }
        }
        .loader-brand {
            margin-top: 20px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.02em;
            background: linear-gradient(135deg, #fff 30%, #b0d0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .loader-bar-track {
            margin-top: 32px;
            width: 180px;
            height: 4px;
            background: rgba(255,255,255,.12);
            border-radius: 99px;
            overflow: hidden;
        }
        .loader-bar-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #80c0e0, #b0d0f0, #dce8f8);
            border-radius: 99px;
            transition: width .08s linear;
            box-shadow: 0 0 8px rgba(128,192,224,.6);
        }
        .loader-bg-pill {
            position: absolute;
            border-radius: 99px;
            opacity: .07;
            animation: floatUp linear infinite;
        }
        @keyframes floatUp {
            from { transform: translateY(110vh) rotate(0deg); }
            to   { transform: translateY(-20vh) rotate(360deg); }
        }
    </style>

    <div id="page-loader" role="status" aria-label="Loading Medikart">
        @foreach([
            ['w'=>18,'h'=>7,'l'=>8,'delay'=>0,'dur'=>7],
            ['w'=>28,'h'=>11,'l'=>22,'delay'=>1.2,'dur'=>9],
            ['w'=>14,'h'=>6,'l'=>40,'delay'=>0.5,'dur'=>6],
            ['w'=>22,'h'=>9,'l'=>58,'delay'=>2,'dur'=>8],
            ['w'=>16,'h'=>7,'l'=>72,'delay'=>0.8,'dur'=>7.5],
            ['w'=>30,'h'=>12,'l'=>85,'delay'=>1.6,'dur'=>10],
            ['w'=>12,'h'=>5,'l'=>93,'delay'=>3,'dur'=>6.5],
        ] as $p)
            <div class="loader-bg-pill" style="width:{{ $p['w'] }}px;height:{{ $p['h'] }}px;left:{{ $p['l'] }}%;background:white;animation-duration:{{ $p['dur'] }}s;animation-delay:{{ $p['delay'] }}s;"></div>
        @endforeach

        <div style="position:relative;display:flex;align-items:center;justify-content:center;width:110px;height:110px;">
            <div class="loader-orbit">
                <div class="loader-orbit-dot"></div>
                <div class="loader-orbit-dot"></div>
            </div>
            <div class="loader-pill">
                <div class="loader-pill-left"></div>
                <div class="loader-pill-right"></div>
                <div class="loader-pill-divider"></div>
            </div>
        </div>
        <div class="loader-cross"></div>
        <p class="loader-brand">Medikart</p>
        <div class="loader-bar-track">
            <div class="loader-bar-fill" id="loader-bar"></div>
        </div>
    </div>

    <script>
    (function () {
        const loader = document.getElementById('page-loader');
        const bar    = document.getElementById('loader-bar');
        let pct = 0, rafId = null;
        function animateTo(target, speed) {
            cancelAnimationFrame(rafId);
            (function step() {
                if (pct < target) {
                    pct = Math.min(pct + speed, target);
                    bar.style.width = pct + '%';
                    rafId = requestAnimationFrame(step);
                }
            })();
        }
        animateTo(70, 1.2);
        setTimeout(() => animateTo(90, 0.25), 600);
        function finish() {
            cancelAnimationFrame(rafId);
            pct = 90;
            animateTo(100, 2.5);
            setTimeout(() => loader.classList.add('hide'), 420);
            setTimeout(() => loader.remove(), 900);
        }
        if (document.readyState === 'complete') { finish(); }
        else {
            window.addEventListener('load', finish, { once: true });
            setTimeout(finish, 4000);
        }
    })();
    </script>

    {{-- Flash messages --}}
    <div id="flash-message-root" class="pointer-events-none fixed right-4 z-[99999] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-3 sm:w-80" style="z-index: 99999;">
        @if (session('status'))
            <div class="alert-animate pointer-events-auto flex items-center gap-3 rounded-2xl border border-transparent bg-gradient-to-r from-blue-500 to-blue-700 px-4 py-3 text-sm font-medium text-white shadow-lg">
                <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert-animate pointer-events-auto flex items-center gap-3 rounded-2xl border border-white/20 bg-gradient-to-r from-blue-800 to-blue-600 px-4 py-3 text-sm font-medium text-white shadow-lg">
                <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-white/15">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Main content --}}
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-700 text-white font-black text-base shadow-md">✚</div>
                        <span class="text-lg font-extrabold text-slate-900 tracking-tight">Medikart</span>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed">Your trusted online pharmacy in Ahmedabad. Genuine medicines, fast delivery, cash on delivery.</p>
                    <div class="mt-4 flex gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">✓ Verified</span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">
                        <div class=lock style="width:20px;">
                        <img src="{{ asset('images/locked.png') }}" 
                        alt="lock"> 
                        </div>
                        Secure</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider">Quick Links</h4>
                    <ul class="space-y-1.5">
                        <li>
                            <a href="{{ route('home') }}"
                               class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-700 hover:pl-4">
                                <span class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-house" style="color: rgb(30,48,80);"></i>
                                    Home
                                </span>
                                <svg class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 transition-opacity text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('medicines.index') }}"
                               class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-700 hover:pl-4">
                                <span class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-capsules" style="color: rgb(30,48,80);"></i>
                                    All Medicines
                                </span>
                                <svg class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 transition-opacity text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cart.index') }}"
                               class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-700 hover:pl-4">
                                <span class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-cart-shopping" style="color: rgb(30,48,80);"></i>
                                    My Cart
                                </span>
                                <svg class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 transition-opacity text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        @auth
                        <li>
                            <a href="{{ route('orders.index') }}"
                               class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-700 hover:pl-4">
                                <span class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-box-open" style="color: rgb(30,48,80);"></i>
                                    My Orders
                                </span>
                                <svg class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 transition-opacity text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider">Delivery Info</h4>
                    <ul class="space-y-2.5 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-600 text-xs">✓</span>Free delivery on ₹500+</li>
                        <li class="flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-600 text-xs">✓</span>Cash on delivery</li>
                        <li class="flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-600 text-xs">✓</span>Online payment via Razorpay</li>
                        <li class="flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-600 text-xs">✓</span>Ahmedabad area — 32+ pincodes</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider">Find Us</h4>
                    <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1836.3173545045674!2d72.53278670825017!3d23.000456439426888!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e85809d72668f%3A0xb5733724a2cd1031!2sMedikart%20Pharmacy!5e0!3m2!1sen!2sin!4v1778824188302!5m2!1sen!2sin"
                            width="100%"
                            height="180"
                            style="border:0;display:block;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <p class="mt-4 text-xs text-slate-500 flex items-start gap-1 leading-relaxed">
                        📍 Shop 54/04, opp. Unigold Hospital Main Gate, near Jivraj, Sarni kamdhar Society, Police Chowki, Jivraj Park, Ahmedabad, Gujarat 380051
                    </p>
                </div>
            </div>

            <div class="mt-8 border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-slate-400">© {{ date('Y') }} Medikart — Ahmedabad, Gujarat</p>
                <p class="text-xs text-slate-400">Built by <a href="https://softgenixinfotech.com/" class="text-blue-500 hover:underline" style="margin-right:25px;">SoftGenix Infotech</a></p>
            </div>
        </div>
    </footer>

    {{-- ═══════════════════════════════════════
         STICKY BOTTOM MOBILE NAV BAR
         Only shown on screens < 640px (sm breakpoint)
         ─────────────────────────────────────── --}}
    <nav id="mobile-bottom-nav" aria-label="Mobile navigation">

        {{-- Home --}}
        <a href="{{ route('home') }}"
           class="mob-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Home
        </a>

        {{-- Medicines --}}
        <a href="{{ route('medicines.index') }}"
           class="mob-nav-item {{ request()->routeIs('medicines.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Medicines
        </a>

        {{-- Cart --}}
        <a href="{{ route('cart.index') }}"
           class="mob-nav-item {{ request()->routeIs('cart.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            @if(($cartCount ?? 0) > 0)
                <span class="mob-nav-cart-badge" id="mob-cart-badge">{{ $cartCount ?? 0 }}</span>
            @else
                <span class="mob-nav-cart-badge {{ ($cartCount ?? 0) > 0 ? '' : 'hidden' }}" id="mob-cart-badge">{{ $cartCount ?? 0 }}</span>
            @endif
            Cart
        </a>

        {{-- Account / Sign In --}}
        @auth
            <a href="{{ route('orders.index') }}"
               class="mob-nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Orders
            </a>
        @else
            <a href="{{ route('login') }}"
               class="mob-nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Sign In
            </a>
        @endauth
    </nav>

    <script>
    (function () {
        const siteHeader = document.getElementById('site-header');
        const cartNavLink = document.getElementById('cart-nav-link');
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const badge = document.getElementById('cart-count-badge');
        const mobBadge = document.getElementById('mob-cart-badge');
        const flashRoot = document.getElementById('flash-message-root');
        let flashTimeoutId = null;

        /* ── Dynamic header height for mobile menu positioning ── */
        function setHeaderHeight() {
            if (siteHeader) {
                document.documentElement.style.setProperty('--header-h', siteHeader.offsetHeight + 'px');
            }
        }
        setHeaderHeight();
        window.addEventListener('resize', setHeaderHeight);

        function positionFlashRoot() {
            if (!flashRoot || !siteHeader) return;
            const headerBottom = siteHeader.getBoundingClientRect().bottom;
            const topOffset = Math.max(16, Math.round(headerBottom + 12));
            flashRoot.style.top = `${topOffset}px`;
            if (window.innerWidth < 640 || !cartNavLink) {
                flashRoot.style.right = '1rem';
                flashRoot.style.left = '1rem';
                flashRoot.style.width = 'calc(100% - 2rem)';
                return;
            }
            flashRoot.style.right = '1rem';
            flashRoot.style.left = 'auto';
            flashRoot.style.width = `${Math.min(320, window.innerWidth - 32)}px`;
        }

        function dismissFlash() {
            if (!flashRoot) return;
            flashRoot.querySelectorAll('.alert-animate').forEach((a) => a.classList.add('is-hiding'));
            window.setTimeout(() => { flashRoot.replaceChildren(); }, 220);
        }

        function scheduleFlashDismiss(delay = 3500) {
            window.clearTimeout(flashTimeoutId);
            flashTimeoutId = window.setTimeout(dismissFlash, delay);
        }

        function showFlash(message, type = 'success') {
            if (!flashRoot || !message) return;
            const isError = type === 'error';
            const alert = document.createElement('div');
            alert.className = isError
                ? 'alert-animate pointer-events-auto flex items-center gap-3 rounded-2xl border border-white/20 bg-gradient-to-r from-blue-800 to-blue-600 px-4 py-3 text-sm font-medium text-white shadow-lg'
                : 'alert-animate pointer-events-auto flex items-center gap-3 rounded-2xl border border-transparent bg-gradient-to-r from-blue-500 to-blue-700 px-4 py-3 text-sm font-medium text-white shadow-lg';
            alert.innerHTML = `
                <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-white/15">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="${isError ? 'M6 18L18 6M6 6l12 12' : 'M5 13l4 4L19 7'}"/>
                    </svg>
                </div>`;
            const text = document.createElement('span');
            text.textContent = message;
            alert.appendChild(text);
            positionFlashRoot();
            flashRoot.replaceChildren(alert);
            scheduleFlashDismiss();
        }

        positionFlashRoot();
        window.addEventListener('resize', positionFlashRoot);
        window.addEventListener('scroll', positionFlashRoot, { passive: true });
        if (flashRoot && flashRoot.children.length > 0) { scheduleFlashDismiss(); }

        function updateCartBadge(count) {
            if (typeof count !== 'number') return;
            /* desktop badge */
            if (badge) {
                badge.textContent = count;
                badge.classList.toggle('hidden', count < 1);
            }
            /* mobile bottom nav badge */
            if (mobBadge) {
                mobBadge.textContent = count;
                mobBadge.classList.toggle('hidden', count < 1);
            }
        }

        function formatCurrency(paise) { return '₹' + (paise / 100).toFixed(2); }

        function updateCartCounts(linesCount) {
            const countEl = document.getElementById('cart-items-count');
            if (!countEl || typeof linesCount !== 'number') return;
            countEl.textContent = linesCount > 0
                ? `${linesCount} ${linesCount === 1 ? 'item' : 'items'} in your cart`
                : 'Your cart is empty';
        }

        function updateCartSummary(subtotalPaise, linesCount) {
            const subtotalEl = document.getElementById('cart-summary-subtotal');
            const countEl = document.getElementById('cart-summary-items-count');
            const labelEl = document.getElementById('cart-summary-items-label');
            const deliveryTextEl = document.getElementById('cart-delivery-text');
            const totalAmountEl = document.getElementById('cart-total-amount');
            if (typeof subtotalPaise === 'number' && subtotalEl) subtotalEl.textContent = formatCurrency(subtotalPaise);
            if (typeof linesCount === 'number' && countEl && labelEl) {
                countEl.textContent = linesCount;
                labelEl.textContent = linesCount === 1 ? 'item' : 'items';
            }
            if (typeof subtotalPaise === 'number' && deliveryTextEl && totalAmountEl) {
                const deliveryFee = subtotalPaise >= 50000 ? 0 : 4000;
                deliveryTextEl.textContent = deliveryFee === 0
                    ? '🎉 You qualify for free delivery!'
                    : `Add ₹${((50000 - subtotalPaise) / 100).toFixed(2)} more for free delivery!`;
                totalAmountEl.textContent = formatCurrency(subtotalPaise + deliveryFee);
                const deliveryFeeEl = document.getElementById('cart-summary-delivery-fee');
                if (deliveryFeeEl) {
                    deliveryFeeEl.textContent = deliveryFee === 0 ? 'FREE' : formatCurrency(deliveryFee);
                    deliveryFeeEl.className = deliveryFee === 0 ? 'font-semibold text-blue-700' : 'font-medium text-slate-900';
                }
            }
        }

        function showEmptyCart() {
            const pageContent = document.getElementById('cart-page-content');
            if (!pageContent) return;
            pageContent.innerHTML = `
                <div class="col-span-3 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white py-16 text-center shadow-sm">
                    <img src="${@json(asset('images/emptycart1.png'))}"
                         alt="Empty cart"
                         class="h-36 w-auto object-contain mb-4 opacity-80"
                         loading="lazy">
                    <h2 class="text-xl font-bold text-slate-700">Your cart is empty</h2>
                    <p class="mt-2 text-sm text-slate-500 max-w-xs">Looks like you haven't added any medicines yet. Browse our catalogue to get started.</p>
                    <a href="${@json(route('medicines.index', [], false))}"
                       class="btn-primary mt-6 inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-md">
                        Browse Medicines →
                    </a>
                </div>`;
        }

        const cartUpdateTimers = new WeakMap();
        document.addEventListener('input', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLInputElement) || target.name !== 'quantity') return;
            const form = target.closest('form.js-cart-update-form');
            if (!form) return;
            const existing = cartUpdateTimers.get(form);
            if (existing) clearTimeout(existing);
            cartUpdateTimers.set(form, setTimeout(() => { form.requestSubmit(); }, 650));
        });

        document.addEventListener('click', function (event) {
            const target = event.target;
            const minus = target.closest('.js-card-qty-minus');
            if (minus) {
                event.preventDefault();
                const form = minus.closest('form.js-cart-update-form');
                if (!form) return;
                const qtyInput = form.querySelector('input[name="quantity"]');
                if (!qtyInput) return;
                qtyInput.value = Math.max(0, Number(qtyInput.value || 0) - 1);
                form.requestSubmit();
                return;
            }
            const plus = target.closest('.js-card-qty-plus');
            if (plus) {
                event.preventDefault();
                const form = plus.closest('form.js-cart-update-form');
                if (!form) return;
                const qtyInput = form.querySelector('input[name="quantity"]');
                if (!qtyInput) return;
                qtyInput.value = Math.min(99, Number(qtyInput.value || 0) + 1);
                form.requestSubmit();
                return;
            }
            const card = target.closest('.medicine-card[data-product-url]');
            if (!card) return;
            if (target.closest('form') || target.closest('button') || target.closest('a')) return;
            const url = card.dataset.productUrl;
            if (url) window.location.href = url;
        });

        document.addEventListener('submit', async function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;
            const isAddToCart   = form.classList.contains('js-add-to-cart-form');
            const isCartUpdate  = form.classList.contains('js-cart-update-form');
            const isCartRemove  = form.classList.contains('js-cart-remove-form');
            if (!isAddToCart && !isCartUpdate && !isCartRemove) return;
            event.preventDefault();
            const submitButton = form.querySelector('button[type="submit"]');
            const originalLabel = submitButton?.innerHTML;
            const loadingLabel = isCartRemove ? 'Removing...' : isCartUpdate ? 'Updating...' : 'Adding...';
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-70', 'cursor-not-allowed');
                submitButton.innerHTML = loadingLabel;
            }
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    body: new FormData(form),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Could not update the cart.');
                updateCartBadge(Number(data.cartCount || 0));
                if (typeof data.linesCount === 'number') updateCartCounts(data.linesCount);
                if (typeof data.subtotalPaise === 'number') updateCartSummary(data.subtotalPaise, data.linesCount);
                const card = form.closest('.medicine-card[data-product-id]');
                if (card) {
                    const addForm    = card.querySelector('.js-add-to-cart-form');
                    const updateForm = card.querySelector('.js-cart-update-form');
                    if ((isAddToCart || isCartUpdate) && updateForm && typeof data.quantity === 'number') {
                        const qtyInput = updateForm.querySelector('input[name="quantity"]');
                        if (qtyInput) qtyInput.value = data.quantity;
                    }
                    if (isAddToCart) {
                        addForm?.classList.add('hidden');
                        updateForm?.classList.remove('hidden');
                    }
                    if (isCartUpdate && typeof data.quantity === 'number' && updateForm) {
                        if (data.quantity === 0) {
                            updateForm.classList.add('hidden');
                            addForm?.classList.remove('hidden');
                        } else {
                            updateForm.classList.remove('hidden');
                            addForm?.classList.add('hidden');
                        }
                    }
                }
                if (isCartUpdate) {
                    const itemId = form.dataset.cartMedicineId;
                    const row = itemId ? document.querySelector(`[data-cart-row-id="${itemId}"]`) : null;
                    if (row) {
                        const qtyInput = row.querySelector('input[name="quantity"]');
                        if (qtyInput && typeof data.quantity === 'number') qtyInput.value = data.quantity;
                        const lineTotal = document.querySelector(`[data-cart-line-total-id="${itemId}"]`);
                        if (lineTotal && typeof data.lineTotalPaise === 'number') lineTotal.textContent = formatCurrency(data.lineTotalPaise);
                    }
                }
                if (isCartRemove) {
                    const itemId = form.dataset.cartMedicineId;
                    const row = itemId ? document.querySelector(`[data-cart-row-id="${itemId}"]`) : null;
                    if (row) row.remove();
                    if (typeof data.linesCount === 'number' && data.linesCount === 0) showEmptyCart();
                }
                showFlash(data.message || (isCartRemove ? 'Removed from cart.' : 'Cart updated.'));
            } catch (error) {
                showFlash(error.message || 'Something went wrong. Please try again.', 'error');
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-70', 'cursor-not-allowed');
                    submitButton.innerHTML = originalLabel;
                }
            }
        });
    })();
    </script>

    <script>
    (function () {
        const token   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const input   = document.getElementById('pin-input');
        const btnLookup  = document.getElementById('pin-lookup-btn');
        const btnConfirm = document.getElementById('pin-confirm-btn');
        const status  = document.getElementById('pin-status');
        let lastOk = null;
        if (!input) return;

        async function lookup() {
            const pin = (input.value || '').replace(/\D/g, '');
            if (pin.length !== 6) {
                status.textContent = '⚠ Enter a 6-digit pincode.';
                status.className = 'text-xs font-medium text-amber-300';
                btnConfirm.hidden = true;
                lastOk = null;
                return;
            }
            status.textContent = 'Checking…';
            status.className = 'text-xs font-medium text-white';
            try {
                const url = new URL(@json(route('pincode.lookup', [], false)), window.location.origin);
                url.searchParams.set('pin', pin);
                const res  = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (!data.ok) {
                    status.textContent = '✗ ' + (data.message || 'Not serviceable.');
                    status.className = 'text-xs font-medium text-red-300';
                    btnConfirm.hidden = true;
                    lastOk = null;
                    return;
                }
                lastOk = pin;
                status.textContent = '📍 ' + data.label;
                status.className = 'text-xs font-medium text-white';
                btnConfirm.hidden = false;
            } catch (e) {
                status.textContent = '✗ Network error. Try again.';
                status.className = 'text-xs font-medium text-red-300';
            }
        }

        async function confirmDelivery() {
            if (!lastOk) return;
            try {
                const res  = await fetch(@json(route('delivery_pin.set', [], false)), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ pin: lastOk }),
                });
                const data = await res.json();
                if (!data.ok) {
                    status.textContent = '✗ ' + (data.message || 'Could not save.');
                    status.className = 'text-xs font-medium text-red-300';
                    return;
                }
                status.textContent = '✓ Delivering to: ' + data.label;
                status.className = 'text-xs font-medium text-white';
                btnConfirm.hidden = true;
            } catch (e) {
                status.textContent = '✗ Network error. Try again.';
                status.className = 'text-xs font-medium text-red-300';
            }
        }

        btnLookup.addEventListener('click', lookup);
        btnConfirm.addEventListener('click', confirmDelivery);
        input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); lookup(); } });
        input.addEventListener('input', function () { if (this.value.replace(/\D/g,'').length === 6) lookup(); });
    })();
    </script>

    <script>
    (function () {
        const suggestionsUrl = @json(route('medicines.suggestions', [], false));
        const debounceTimers = new WeakMap();
        const activeRequest  = new WeakMap();

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }

        function getSuggestionParts(form) {
            if (!(form instanceof HTMLFormElement) || !form.matches('[data-medicine-suggest-form]')) return null;
            const input = form.querySelector('[data-medicine-suggest-input]');
            const box   = form.querySelector('[data-medicine-suggestions]');
            if (!(input instanceof HTMLInputElement) || !(box instanceof HTMLElement)) return null;
            return { form, input, box };
        }

        function hideSuggestions(form) {
            const p = getSuggestionParts(form);
            if (!p) return;
            p.box.classList.add('hidden');
            p.box.innerHTML = '';
            p.input.setAttribute('aria-expanded', 'false');
        }

        function showSuggestions(form) {
            const p = getSuggestionParts(form);
            if (!p) return;
            p.box.classList.remove('hidden');
            p.input.setAttribute('aria-expanded', 'true');
        }

        function renderLoadingSuggestions(form) {
            const p = getSuggestionParts(form);
            if (!p) return;
            p.box.innerHTML = Array.from({ length: 3 }).map(() => `
                <div class="flex animate-pulse items-center gap-3 px-4 py-3">
                    <div class="h-14 w-14 rounded-xl bg-slate-200"></div>
                    <div class="min-w-0 flex-1 space-y-2">
                        <div class="h-4 w-3/4 rounded-full bg-slate-200"></div>
                        <div class="h-3 w-1/2 rounded-full bg-slate-200"></div>
                    </div>
                </div>`).join('');
            showSuggestions(form);
        }

        function renderSuggestions(form, items) {
            const p = getSuggestionParts(form);
            if (!p) return;
            if (!items.length) {
                p.box.innerHTML = `<div class="px-4 py-3 text-sm text-slate-500">No matching medicines found.</div>`;
                showSuggestions(form);
                return;
            }
            p.box.innerHTML = items.map((item) => `
                <a href="${escapeHtml(item.url)}"
                   class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-slate-50 ${item.prescription_required ? 'bg-amber-50/40' : ''}">
                    <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-xl border border-slate-100 bg-slate-50 p-1">
                        <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" class="h-full w-full object-contain" loading="lazy">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="truncate text-sm font-semibold text-slate-900">${escapeHtml(item.name)}</p>
                            ${item.prescription_required ? '<span class="rounded-md bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-800">Rx</span>' : ''}
                        </div>
                        <p class="truncate text-xs text-slate-500">${escapeHtml(item.manufacturer || '')}</p>
                        <div class="mt-1 flex items-center justify-between gap-2">
                            <span class="truncate text-[11px] font-medium uppercase tracking-wide text-blue-700">${escapeHtml(item.category || '')}</span>
                            <span class="text-sm font-bold text-slate-900">&#8377;${escapeHtml(item.price)}</span>
                        </div>
                    </div>
                </a>`).join('');
            showSuggestions(form);
        }

        async function fetchSuggestions(form) {
            const p = getSuggestionParts(form);
            if (!p) return;
            const query = p.input.value.trim();
            if (query.length < 2) { hideSuggestions(form); return; }
            const requestId = (activeRequest.get(form) || 0) + 1;
            activeRequest.set(form, requestId);
            const url = new URL(suggestionsUrl, window.location.origin);
            url.searchParams.set('q', query);
            const category = String(new FormData(form).get('category') || '').trim();
            if (category) url.searchParams.set('category', category);
            renderLoadingSuggestions(form);
            try {
                const response = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) throw new Error();
                const data = await response.json();
                if (requestId !== activeRequest.get(form)) return;
                renderSuggestions(form, Array.isArray(data.suggestions) ? data.suggestions : []);
            } catch (e) { hideSuggestions(form); }
        }

        document.addEventListener('input', function (event) {
            if (!(event.target instanceof HTMLInputElement) || !event.target.matches('[data-medicine-suggest-input]')) return;
            const form = event.target.closest('form[data-medicine-suggest-form]');
            if (!form) return;
            const existing = debounceTimers.get(form);
            if (existing) window.clearTimeout(existing);
            debounceTimers.set(form, window.setTimeout(() => fetchSuggestions(form), 220));
        });

        document.addEventListener('focusin', function (event) {
            if (!(event.target instanceof HTMLInputElement) || !event.target.matches('[data-medicine-suggest-input]')) return;
            const form = event.target.closest('form[data-medicine-suggest-form]');
            const p = getSuggestionParts(form);
            if (!p) return;
            if (p.input.value.trim().length >= 2 && p.box.innerHTML.trim() !== '') showSuggestions(form);
        });

        document.addEventListener('keydown', function (event) {
            if (!(event.target instanceof HTMLInputElement) || !event.target.matches('[data-medicine-suggest-input]')) return;
            if (event.key === 'Escape') {
                const form = event.target.closest('form[data-medicine-suggest-form]');
                if (form) hideSuggestions(form);
            }
        });

        document.addEventListener('click', function (event) {
            document.querySelectorAll('form[data-medicine-suggest-form]').forEach(function (form) {
                if (!form.contains(event.target)) hideSuggestions(form);
            });
        });

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.matches('[data-medicine-suggest-form]')) return;
            hideSuggestions(form);
        });
    })();
    </script>

    <style>
        .wave-heading .wave-char { display: inline-block; transition: transform .25s ease, color .25s ease; }
        .wave-heading:hover .wave-char { animation: waveChar .5s ease forwards; }
        @keyframes waveChar {
            0%   { transform: translateY(0); }
            30%  { transform: translateY(-6px); }
            60%  { transform: translateY(2px); }
            100% { transform: translateY(0); }
        }
    </style>
    <script>
    (function () {
        function wrapLetters(el) {
            if (el.dataset.waved) return;
            el.dataset.waved = '1';
            el.classList.add('wave-heading');
            el.childNodes.forEach(node => {
                if (node.nodeType !== Node.TEXT_NODE) return;
                const text = node.textContent;
                if (!text.trim()) return;
                const frag = document.createDocumentFragment();
                [...text].forEach((ch, i) => {
                    if (ch === ' ') { frag.appendChild(document.createTextNode(' ')); }
                    else {
                        const span = document.createElement('span');
                        span.className = 'wave-char';
                        span.style.animationDelay = (i * 35) + 'ms';
                        span.textContent = ch;
                        frag.appendChild(span);
                    }
                });
                node.replaceWith(frag);
            });
        }
        document.querySelectorAll('h1, h2, h3').forEach(wrapLetters);
    })();
    </script>

    @stack('scripts')

    {{-- ═══════════════════════════════════════
         MEDIBOT — AI Medicine Assistant
         ─────────────────────────────────────── --}}
    <style>
        /* ── Chatbot widget ── */
        #medibot-btn {
            position: fixed;
            bottom: calc(72px + env(safe-area-inset-bottom, 0px) + 12px);
            left: 14px;
            z-index: 9990;
            width: auto;
            height: 44px;
            border-radius: 50px;
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: #fff;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(37,99,235,.45);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 12px;
            transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
        }
        @media (min-width: 640px) {
            #medibot-btn {
                bottom: 24px;
                left: 18px;
                height: 56px;
                gap: 10px;
                padding: 0 16px;
            }
        }
        #medibot-btn:hover { transform: scale(1.1); box-shadow: 0 8px 28px rgba(37,99,235,.55); }
        #medibot-btn .bot-icon { transition: opacity .2s, transform .2s; width: 20px; height: 20px; }
        @media (min-width: 640px) {
            #medibot-btn .bot-icon { width: 24px; height: 24px; }
        }
        #medibot-btn .close-icon { position: absolute; opacity: 0; transform: rotate(-90deg) scale(.6); transition: opacity .2s, transform .2s; }
        #medibot-btn span { transition: opacity .2s; font-size: 12px; }
        @media (min-width: 640px) {
            #medibot-btn span { font-size: 14px; }
        }
        /* Hide label text on mobile to keep button compact */
        @media (max-width: 639px) {
            #medibot-btn span { display: none; }
        }
        #medibot-btn.is-open .bot-icon { opacity: 0; transform: rotate(90deg) scale(.6); }
        #medibot-btn.is-open .close-icon { opacity: 1; transform: rotate(0deg) scale(1); }
        #medibot-btn.is-open span { opacity: 0; pointer-events: none; }

        /* Pulse ring */
        #medibot-btn::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid rgba(37,99,235,.4);
            animation: botPulse 2.5s ease-in-out infinite;
        }
        @keyframes botPulse {
            0%,100% { transform: scale(1); opacity: .6; }
            50%      { transform: scale(1.18); opacity: 0; }
        }

        /* Tooltip */
        #medibot-tooltip {
            position: fixed;
            left: 70px;
            z-index: 9990;
            background: #1e3a8a;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 20px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity .2s, transform .2s;
            box-shadow: 0 4px 12px rgba(30,58,138,.3);
        }
        @media (min-width: 640px) {
            #medibot-tooltip { left: 82px; }
        }
        #medibot-tooltip::after {
            content: '';
            position: absolute;
            left: -6px; top: 50%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-right-color: #1e3a8a;
            border-left: 0;
        }
        #medibot-tooltip.show { opacity: 1; transform: translateX(0); }

        /* Chat window */
        #medibot-window {
            position: fixed;
            left: 10px;
            bottom: calc(72px + env(safe-area-inset-bottom, 0px) + 60px);
            z-index: 9989;
            width: calc(100vw - 20px);
            max-width: 320px;
            height: 420px;
            max-height: calc(100vh - 160px);
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(30,58,138,.2), 0 4px 16px rgba(0,0,0,.08);
            display: flex; flex-direction: column;
            overflow: hidden;
            transform: scale(.92) translateY(16px);
            opacity: 0;
            pointer-events: none;
            transition: transform .3s cubic-bezier(.34,1.56,.64,1), opacity .25s ease;
        }
        @media (min-width: 640px) {
            #medibot-window {
                bottom: 96px;
                left: 18px;
                width: 380px;
                max-width: 380px;
                height: 520px;
                max-height: calc(100vh - 180px);
                border-radius: 24px;
            }
        }
        #medibot-window.is-open {
            transform: scale(1) translateY(0);
            opacity: 1;
            pointer-events: all;
        }

        /* Header */
        .bot-header {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            padding: 14px 16px;
            display: flex; align-items: center; gap: 10px;
            flex-shrink: 0;
        }
        .bot-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .bot-status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #4ade80;
            animation: statusPulse 2s ease-in-out infinite;
        }
        @keyframes statusPulse {
            0%,100% { opacity: 1; } 50% { opacity: .4; }
        }

        /* Messages */
        #bot-messages {
            flex: 1;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding: 14px 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            scroll-behavior: smooth;
        }
        #bot-messages::-webkit-scrollbar { width: 4px; }
        #bot-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .bot-msg, .user-msg {
            max-width: 88%;
            font-size: 13px;
            line-height: 1.5;
            padding: 9px 13px;
            border-radius: 16px;
            word-break: break-word;
        }
        .bot-msg {
            background: #f1f5f9;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            align-self: flex-start;
        }
        .user-msg {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: #fff;
            border-bottom-right-radius: 4px;
            align-self: flex-end;
        }

        /* Product card in chat */
        .bot-product-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
            transition: box-shadow .2s, transform .2s;
            text-decoration: none;
            color: inherit;
        }
        .bot-product-card:hover { box-shadow: 0 6px 18px rgba(37,99,235,.12); transform: translateY(-1px); }
        .bot-product-img {
            width: 52px; height: 52px;
            border-radius: 10px;
            object-fit: contain;
            background: #f8faff;
            flex-shrink: 0;
            border: 1px solid #e2e8f0;
            padding: 4px;
        }
        .bot-product-name { font-size: 12px; font-weight: 700; color: #1e293b; line-height: 1.3; }
        .bot-product-mfr  { font-size: 11px; color: #64748b; margin-top: 1px; }
        .bot-product-price { font-size: 13px; font-weight: 800; color: #1e40af; margin-top: 3px; }
        .bot-product-mrp  { font-size: 11px; color: #94a3b8; text-decoration: line-through; margin-left: 4px; }
        .bot-add-btn {
            margin-left: auto;
            flex-shrink: 0;
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity .2s, transform .2s;
            white-space: nowrap;
        }
        .bot-add-btn:hover { opacity: .9; transform: scale(1.04); }
        .bot-add-btn:disabled { opacity: .6; cursor: not-allowed; }

        /* Quick chips */
        .bot-chips { display: flex; flex-wrap: wrap; gap: 6px; align-self: flex-start; }
        .bot-chip {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 11px;
            border-radius: 99px;
            cursor: pointer;
            transition: background .15s, transform .15s;
            white-space: nowrap;
        }
        .bot-chip:hover { background: #dbeafe; transform: scale(1.04); }

        /* Typing indicator */
        .bot-typing { display: flex; gap: 4px; align-items: center; padding: 10px 14px; }
        .bot-typing span {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #94a3b8;
            animation: typingBounce .9s ease-in-out infinite;
        }
        .bot-typing span:nth-child(2) { animation-delay: .15s; }
        .bot-typing span:nth-child(3) { animation-delay: .3s; }
        @keyframes typingBounce {
            0%,60%,100% { transform: translateY(0); }
            30%          { transform: translateY(-6px); }
        }

        /* Input area */
        .bot-input-area {
            border-top: 1px solid #f1f5f9;
            padding: 10px 12px;
            display: flex;
            gap: 8px;
            align-items: flex-end;
            flex-shrink: 0;
            background: #fff;
        }
        #bot-input {
            flex: 1;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 9px 13px;
            font-size: 13px;
            font-family: inherit;
            resize: none;
            outline: none;
            max-height: 80px;
            line-height: 1.4;
            transition: border-color .2s;
        }
        #bot-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
        #bot-send {
            width: 38px; height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: opacity .2s, transform .2s;
        }
        #bot-send:hover { opacity: .9; transform: scale(1.06); }
        #bot-send:disabled { opacity: .5; cursor: not-allowed; }

        /* See more link */
        .bot-see-more {
            font-size: 12px;
            font-weight: 700;
            color: #2563eb;
            text-decoration: none;
            align-self: flex-start;
            padding: 4px 0;
        }
        .bot-see-more:hover { text-decoration: underline; }
    </style>

    {{-- Floating button --}}
    <button id="medibot-btn" aria-label="Open MediBot chat assistant">
        <svg class="bot-icon h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <span class="text-sm font-bold whitespace-nowrap">Need help? Ask me</span>
        <svg class="close-icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    {{-- Tooltip --}}
    <div id="medibot-tooltip">💊 Need help? Ask me</div>

    {{-- Chat window --}}
    <div id="medibot-window" role="dialog" aria-label="MediBot chat" aria-modal="true">

        {{-- Header --}}
        <div class="bot-header">
            <div class="bot-avatar">💊</div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white leading-tight">MedCare AI</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <div class="bot-status-dot"></div>
                    <p class="text-xs text-blue-200">Smart Pharmacy Assistant · Online</p>
                </div>
            </div>
            <button id="medibot-close" class="flex h-7 w-7 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25 transition-colors" aria-label="Close chat">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div id="bot-messages"></div>

        {{-- Input --}}
        <div class="bot-input-area">
            <textarea id="bot-input" rows="1" placeholder="Describe your symptoms…" aria-label="Type your message"></textarea>
            <button id="bot-send" aria-label="Send message">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </div>
    </div>

    <script>
    (function () {
        const btn      = document.getElementById('medibot-btn');
        const win      = document.getElementById('medibot-window');
        const closeBtn = document.getElementById('medibot-close');
        const messages = document.getElementById('bot-messages');
        const input    = document.getElementById('bot-input');
        const sendBtn  = document.getElementById('bot-send');
        const tooltip  = document.getElementById('medibot-tooltip');
        const token    = document.querySelector('meta[name="csrf-token"]')?.content;
        const chatUrl  = @json(route('chatbot.chat'));
        const cartUrl  = @json(route('cart.add'));
        const orderDetailUrl = @json(route('chatbot.orderDetail'));

        let isOpen = false;
        let tooltipTimer = null;

        // ── Open / close ──────────────────────────────────────────────────────
        function openChat() {
            isOpen = true;
            btn.classList.add('is-open');
            win.classList.add('is-open');
            tooltip.classList.remove('show');
            clearTimeout(tooltipTimer);
            if (messages.children.length === 0) showWelcome();
            setTimeout(() => input.focus(), 350);
        }
        function closeChat() {
            isOpen = false;
            btn.classList.remove('is-open');
            win.classList.remove('is-open');
        }

        btn.addEventListener('click', () => isOpen ? closeChat() : openChat());
        closeBtn.addEventListener('click', closeChat);
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && isOpen) closeChat(); });

        // Tooltip on hover
        btn.addEventListener('mouseenter', () => {
            if (!isOpen) { tooltipTimer = setTimeout(() => tooltip.classList.add('show'), 600); }
        });
        btn.addEventListener('mouseleave', () => {
            clearTimeout(tooltipTimer);
            tooltip.classList.remove('show');
        });

        // ── Welcome message ───────────────────────────────────────────────────
        function showWelcome() {
            appendBotMsg("👋 Hi! I'm <strong>MedCare AI</strong>, your smart pharmacy assistant at Medikart.<br><br>I can help you find medicines, track orders, answer delivery questions, and more.<br><br><em>How can I help you today?</em>");
            setTimeout(() => {
                const chips = ['I have a fever', 'Headache relief', 'Track my order', 'Delivery info', 'Payment options', 'Cold & Cough'];
                appendChips(chips);
            }, 400);
        }

        // ── Append helpers ────────────────────────────────────────────────────
        function appendBotMsg(html) {
            const div = document.createElement('div');
            div.className = 'bot-msg';
            div.innerHTML = html;
            messages.appendChild(div);
            scrollBottom();
        }
        function appendUserMsg(text) {
            const div = document.createElement('div');
            div.className = 'user-msg';
            div.textContent = text;
            messages.appendChild(div);
            scrollBottom();
        }
        function appendTyping() {
            const div = document.createElement('div');
            div.className = 'bot-msg bot-typing-wrap';
            div.innerHTML = '<div class="bot-typing"><span></span><span></span><span></span></div>';
            messages.appendChild(div);
            scrollBottom();
            return div;
        }
        function appendChips(labels) {
            const wrap = document.createElement('div');
            wrap.className = 'bot-chips';
            labels.forEach(label => {
                const chip = document.createElement('button');
                chip.className = 'bot-chip';
                chip.textContent = label;
                chip.addEventListener('click', () => {
                    wrap.remove();
                    sendMessage(label);
                });
                wrap.appendChild(chip);
            });
            messages.appendChild(wrap);
            scrollBottom();
        }
        function appendOrders(orders, chips, isSelectable) {
            orders.forEach(order => {
                const wrap = document.createElement('div');
                wrap.style.cssText = 'align-self:flex-start;width:100%;';

                const card = document.createElement('div');

                const statusEmoji = {
                    'placed': '📋',
                    'confirmed': '✅',
                    'shipped': '🚚',
                    'delivered': '🎉',
                    'cancelled': '❌',
                    'payment_failed': '⚠️',
                    'cancellation_requested': '🔄',
                    'refund_initiated': '💸',
                    'refunded': '✅',
                }[order.status] || '📦';

                const statusText = order.status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

                if (isSelectable) {
                    // Selectable card — user picks which order to view
                    card.style.cssText = 'background:#fff;border:1.5px solid #bfdbfe;border-radius:14px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,.05);cursor:pointer;transition:border-color .2s,box-shadow .2s;';
                    card.innerHTML = `
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#1e293b;">Order #${escHtml(order.order_number)}</div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">${escHtml(order.date)} · ${order.items_count} item(s)</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:13px;font-weight:700;color:#2563eb;">₹${escHtml(order.total)}</div>
                                <div style="font-size:11px;margin-top:2px;">${statusEmoji} ${escHtml(statusText)}</div>
                            </div>
                        </div>
                        <div style="margin-top:8px;font-size:11px;color:#2563eb;font-weight:600;">Tap to view details →</div>
                    `;
                    card.addEventListener('mouseenter', () => { card.style.borderColor = '#2563eb'; card.style.boxShadow = '0 4px 16px rgba(37,99,235,.15)'; });
                    card.addEventListener('mouseleave', () => { card.style.borderColor = '#bfdbfe'; card.style.boxShadow = '0 2px 8px rgba(0,0,0,.05)'; });
                    card.addEventListener('click', () => fetchOrderDetail(order.id, order.order_number));
                } else {
                    // Display-only card (full detail view)
                    card.style.cssText = 'background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,.05);';
                    card.innerHTML = `
                        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px;">
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#1e293b;">Order #${escHtml(order.order_number)}</div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">${escHtml(order.date)}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:12px;font-weight:700;color:#2563eb;">₹${escHtml(order.total)}</div>
                                <div style="font-size:10px;color:#64748b;margin-top:2px;">${order.items_count} item(s)</div>
                            </div>
                        </div>
                        <div style="border-top:1px solid #f1f5f9;padding-top:8px;margin-top:8px;">
                            <div style="font-size:11px;color:#475569;margin-bottom:4px;">
                                <strong>Status:</strong> ${statusEmoji} ${escHtml(statusText)}
                            </div>
                            <div style="font-size:11px;color:#475569;margin-bottom:4px;">
                                <strong>Delivery:</strong> ${escHtml(order.delivery_address || 'N/A')}
                            </div>
                            <div style="font-size:11px;color:#475569;">
                                <strong>Payment:</strong> ${escHtml(order.payment_method || 'N/A')}
                            </div>
                        </div>
                    `;
                }

                wrap.appendChild(card);
                messages.appendChild(wrap);
            });

            // Add chips only for non-selectable (detail) view
            if (!isSelectable && chips && chips.length > 0) {
                const chipWrap = document.createElement('div');
                chipWrap.className = 'bot-chips';
                chips.forEach(chipText => {
                    const chip = document.createElement('button');
                    chip.className = 'bot-chip';
                    chip.textContent = chipText;
                    chip.addEventListener('click', () => {
                        chipWrap.remove();
                        sendMessage(chipText);
                    });
                    chipWrap.appendChild(chip);
                });
                messages.appendChild(chipWrap);
            }

            scrollBottom();
        }

        // ── Fetch full order detail when user selects an order ────────────────
        async function fetchOrderDetail(orderId, orderNumber) {
            appendUserMsg('Order #' + orderNumber);
            const typing = appendTyping();

            try {
                const fd = new FormData();
                fd.append('order_id', orderId);
                const res  = await fetch(orderDetailUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    body: fd,
                });
                const data = await res.json();
                typing.remove();

                appendBotMsg(data.reply || 'Here are your order details:');

                if (data.order) {
                    renderOrderDetail(data.order);
                }

                setTimeout(() => {
                    appendChips(['Track another order', 'Delivery info', 'Payment options', 'Return & Refund']);
                }, 300);
            } catch {
                typing.remove();
                appendBotMsg('Sorry, something went wrong fetching your order. Please try again.');
            }
        }

        // ── Render full order detail card ─────────────────────────────────────
        function renderOrderDetail(order) {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'align-self:flex-start;width:100%;';

            const statusEmoji = order.status_emoji || '📦';
            const statusLabel = order.status_label || order.status;

            // Build items rows
            const itemRows = (order.items || []).map(item => `
                <div style="display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #f8fafc;font-size:11px;">
                    <div style="flex:1;min-width:0;padding-right:8px;">
                        <div style="font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(item.name)}</div>
                        <div style="color:#64748b;">Qty: ${escHtml(String(item.qty))} × ₹${escHtml(item.price)}</div>
                    </div>
                    <div style="font-weight:700;color:#1e40af;white-space:nowrap;">₹${escHtml(item.subtotal)}</div>
                </div>
            `).join('');

            const refundBtn = order.can_refund
                ? `<a href="${escHtml(order.refund_url)}" style="display:inline-block;margin-top:10px;padding:6px 14px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;">↩️ Request Refund</a>`
                : '';

            wrap.innerHTML = `
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.07);">
                    <!-- Header -->
                    <div style="background:linear-gradient(135deg,#1e3a8a,#2563eb);padding:12px 14px;color:#fff;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:13px;font-weight:800;">Order #${escHtml(order.order_number)}</div>
                                <div style="font-size:10px;opacity:.8;margin-top:2px;">${escHtml(order.date)}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:14px;font-weight:800;">₹${escHtml(order.total)}</div>
                                <div style="font-size:10px;opacity:.8;margin-top:2px;">${statusEmoji} ${escHtml(statusLabel)}</div>
                            </div>
                        </div>
                    </div>
                    <!-- Body -->
                    <div style="padding:12px 14px;">
                        <!-- Items -->
                        <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Items Ordered</div>
                        <div style="margin-bottom:10px;">${itemRows}</div>
                        <!-- Totals -->
                        <div style="background:#f8faff;border-radius:10px;padding:8px 10px;font-size:11px;margin-bottom:10px;">
                            <div style="display:flex;justify-content:space-between;color:#64748b;margin-bottom:3px;">
                                <span>Subtotal</span><span>₹${escHtml(order.subtotal)}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;color:#64748b;margin-bottom:3px;">
                                <span>Delivery Fee</span><span>${order.delivery_fee === '0.00' ? '<span style="color:#16a34a;font-weight:700;">FREE</span>' : '₹' + escHtml(order.delivery_fee)}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-weight:800;color:#1e293b;border-top:1px solid #e2e8f0;padding-top:5px;margin-top:5px;">
                                <span>Total</span><span style="color:#2563eb;">₹${escHtml(order.total)}</span>
                            </div>
                        </div>
                        <!-- Info -->
                        <div style="font-size:11px;color:#475569;line-height:1.7;">
                            <div><strong>Payment:</strong> ${escHtml(order.payment_method)} · <span style="color:${order.payment_status === 'Paid' ? '#16a34a' : '#f59e0b'};font-weight:600;">${escHtml(order.payment_status)}</span></div>
                            <div><strong>Deliver to:</strong> ${escHtml(order.delivery_address || 'N/A')}</div>
                        </div>
                        <!-- Actions -->
                        <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="${escHtml(order.order_url)}" style="display:inline-block;padding:6px 14px;background:linear-gradient(135deg,#1e40af,#2563eb);color:#fff;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;">View Full Details →</a>
                            ${refundBtn}
                        </div>
                    </div>
                </div>
            `;

            messages.appendChild(wrap);
            scrollBottom();
        }
        function appendProducts(products, searchUrl) {
            products.forEach(p => {
                const wrap = document.createElement('div');
                wrap.style.cssText = 'align-self:flex-start;width:100%;';

                const card = document.createElement('a');
                card.className = 'bot-product-card';
                card.href = p.url;
                card.target = '_blank';
                card.rel = 'noopener';

                const rxBadge = p.prescription_required
                    ? '<span style="font-size:9px;font-weight:700;background:#fef3c7;color:#92400e;padding:1px 5px;border-radius:4px;margin-left:4px;">Rx</span>'
                    : '';
                const discount = p.discount > 0
                    ? `<span style="font-size:10px;font-weight:700;background:#dbeafe;color:#1e40af;padding:1px 5px;border-radius:4px;margin-left:4px;">${p.discount}% OFF</span>`
                    : '';
                const mrp = p.discount > 0
                    ? `<span class="bot-product-mrp">₹${p.mrp}</span>`
                    : '';

                card.innerHTML = `
                    <img class="bot-product-img" src="${escHtml(p.image)}" alt="${escHtml(p.name)}" loading="lazy" onerror="this.style.opacity='.3'">
                    <div style="flex:1;min-width:0;">
                        <div class="bot-product-name">${escHtml(p.name)}${rxBadge}</div>
                        <div class="bot-product-mfr">${escHtml(p.manufacturer || '')}</div>
                        <div style="display:flex;align-items:baseline;flex-wrap:wrap;">
                            <span class="bot-product-price">₹${escHtml(p.price)}</span>${mrp}${discount}
                        </div>
                    </div>`;

                // Add to cart button
                const addBtn = document.createElement('button');
                addBtn.className = 'bot-add-btn';
                addBtn.innerHTML = '🛒 Add';
                addBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    addBtn.disabled = true;
                    addBtn.textContent = '…';
                    try {
                        const fd = new FormData();
                        fd.append('medicine_id', p.id);
                        fd.append('quantity', '1');
                        const res = await fetch(cartUrl, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                            body: fd,
                        });
                        const data = await res.json();
                        if (data.ok) {
                            addBtn.textContent = '✓ Added';
                            addBtn.style.background = '#16a34a';
                            // Update cart badge
                            const badge = document.getElementById('cart-count-badge');
                            const mobBadge = document.getElementById('mob-cart-badge');
                            if (badge) { badge.textContent = data.cartCount; badge.classList.toggle('hidden', data.cartCount < 1); }
                            if (mobBadge) { mobBadge.textContent = data.cartCount; mobBadge.classList.toggle('hidden', data.cartCount < 1); }
                        } else {
                            addBtn.textContent = '✗ Error';
                            addBtn.disabled = false;
                        }
                    } catch {
                        addBtn.textContent = '✗ Error';
                        addBtn.disabled = false;
                    }
                });
                card.appendChild(addBtn);
                wrap.appendChild(card);
                messages.appendChild(wrap);
            });

            scrollBottom();
        }

        function scrollBottom() {
            requestAnimationFrame(() => { messages.scrollTop = messages.scrollHeight; });
        }
        function escHtml(str) {
            return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        // ── Send message ──────────────────────────────────────────────────────
        async function sendMessage(text) {
            text = text.trim();
            if (!text) return;

            appendUserMsg(text);
            input.value = '';
            input.style.height = 'auto';
            sendBtn.disabled = true;

            const typing = appendTyping();

            try {
                const fd = new FormData();
                fd.append('message', text);
                const res  = await fetch(chatUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    body: fd,
                });
                const data = await res.json();
                typing.remove();

                appendBotMsg(data.reply || 'Sorry, I could not process that.');

                // Order selection — user must tap one to see details
                if (data.order_selection && data.orders && data.orders.length > 0) {
                    appendOrders(data.orders, [], true);
                    // No generic chips — the order cards are the CTA
                }
                // Direct order detail (e.g. user typed a specific order number)
                else if (data.order) {
                    renderOrderDetail(data.order);
                    setTimeout(() => {
                        appendChips(['Track another order', 'Delivery info', 'Payment options', 'Return & Refund']);
                    }, 300);
                }
                // Legacy order list (non-selectable)
                else if (data.orders && data.orders.length > 0) {
                    appendOrders(data.orders, data.chips || [], false);
                }
                // Product results
                else if (data.products && data.products.length > 0) {
                    appendProducts(data.products, data.search_url || null);
                    setTimeout(() => {
                        appendChips(['Headache', 'Fever', 'Acidity', 'Track order', 'Delivery info']);
                    }, 300);
                } else if (data.search_url) {
                    const link = document.createElement('a');
                    link.className = 'bot-see-more';
                    link.href = data.search_url;
                    link.textContent = 'Search in our store →';
                    messages.appendChild(link);
                    scrollBottom();
                    setTimeout(() => {
                        appendChips(['Headache', 'Fever', 'Acidity', 'Track order', 'Delivery info']);
                    }, 300);
                } else {
                    // Generic follow-up chips for plain text replies
                    setTimeout(() => {
                        appendChips(['Headache', 'Fever', 'Acidity', 'Track order', 'Delivery info']);
                    }, 300);
                }

            } catch {
                typing.remove();
                appendBotMsg('Sorry, something went wrong. Please try again.');
            } finally {
                sendBtn.disabled = false;
                input.focus();
            }
        }

        // ── Input events ──────────────────────────────────────────────────────
        sendBtn.addEventListener('click', () => sendMessage(input.value));
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage(input.value);
            }
        });
        // Auto-resize textarea
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 80) + 'px';
        });

        // Show tooltip after 3s on first load
        setTimeout(() => {
            if (!isOpen) tooltip.classList.add('show');
            setTimeout(() => tooltip.classList.remove('show'), 4000);
        }, 3000);

        // ── Scroll chatbot messages with mouse wheel when hovering the chat window ──
        win.addEventListener('wheel', function (e) {
            const scrollable = messages;
            const delta      = e.deltaY;
            const atTop      = scrollable.scrollTop === 0;
            const atBottom   = scrollable.scrollTop + scrollable.clientHeight >= scrollable.scrollHeight - 1;

            // If there's room to scroll inside the messages box, take over the event
            if (!(atTop && delta < 0) && !(atBottom && delta > 0)) {
                e.preventDefault();
                e.stopPropagation();
                scrollable.scrollTop += delta;
            }
        }, { passive: false });
    })();
    </script>

    <x-whatsapp-button />
</body>
</html>
