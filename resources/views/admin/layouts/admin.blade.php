<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — MediCart Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W+A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f6f8fb; overflow-x: hidden; }
        .container { max-width: 1200px; margin: 0 auto; }

        /* ── Sidebar ── */
        .sidebar {
            width: 220px;
            background: linear-gradient(180deg,#1e3a8a 0%,#2563eb 100%);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Desktop: sidebar is sticky in the flex row */
        @media (min-width: 768px) {
            .sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                flex-shrink: 0;
                z-index: auto;
            }
            #admin-sidebar-backdrop { display: none !important; }
        }

        /* Mobile: sidebar is a fixed overlay — NOT in the flex row */
        @media (max-width: 767px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                z-index: 9999;
                transform: translateX(-100%);
                transition: transform 0.28s ease;
                box-shadow: 4px 0 20px rgba(0,0,0,0.15);
                overflow-y: auto;
                overflow-x: hidden;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 24px;
            }
            .sidebar.is-open {
                transform: translateX(0);
            }
            /* Sidebar takes NO space in the flex row on mobile */
            .sidebar-flex-placeholder {
                display: none;
            }
        }

        #admin-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 9998;
        }
        #admin-sidebar-backdrop.is-open { display: block; }

        .sidebar-link { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            padding: 8px 14px; 
            border-radius: 10px; 
            font-size: 13px; 
            font-weight: 600; 
            color: rgba(255,255,255,.88); 
            transition: all .15s;
            text-decoration: none;
        }
        .sidebar-link:hover, .sidebar-link.active { 
            background: rgba(255,255,255,.12); 
            color: #fff; 
        }
        .sidebar-link svg { flex-shrink: 0; }

        .stat-card { 
            background: #fff; 
            border-radius: 12px; 
            border: 1px solid #e6eef8; 
            padding: 16px; 
            box-shadow: 0 4px 10px rgba(12,38,63,0.04); 
        }
        .stat-card .label { font-size: 12px; color: #64748b; font-weight: 700; }
        .stat-card .value { font-size: 20px; font-weight: 800; color: #0f172a; }

        .admin-table th { 
            font-size: 11px; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: .06em; 
            color: #64748b; 
            padding: 10px 12px; 
            background: #fbfdff; 
            border-bottom: 1px solid #e6eef8; 
        }
        .admin-table td { 
            padding: 10px 12px; 
            font-size: 13px; 
            border-bottom: 1px solid #f1f5f9; 
            vertical-align: middle; 
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background: #fbfdff; }

        /* Mobile table: horizontal scroll */
        @media (max-width: 767px) {
            .admin-table-wrap { 
                overflow-x: auto; 
                -webkit-overflow-scrolling: touch;
                margin: 0 -12px;
                padding: 0 12px;
            }
            .admin-table { min-width: 600px; }
            .admin-table th, .admin-table td { 
                padding: 8px 10px; 
                white-space: nowrap; 
                font-size: 12px;
            }
            header h1 { font-size: 14px; }
            main.flex-1 { padding: 12px !important; }
            .stat-card .value { font-size: 18px; }
        }

        .badge { 
            display: inline-flex; 
            align-items: center; 
            padding: 3px 9px; 
            border-radius: 99px; 
            font-size: 11px; 
            font-weight: 700; 
        }
        .btn-sm { 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-size: 12px; 
            font-weight: 700; 
            transition: all .15s; 
        }
        .admin-flash-message { 
            transition: opacity .2s ease, transform .2s ease; 
        }
        .admin-flash-message.is-hiding { 
            opacity: 0; 
            transform: translateY(-6px); 
        }

        .admin-main { flex: 1; min-width: 0; }

        /* Prevent page content from sliding under the sticky header */
        .admin-main > header + * {
            position: relative;
            z-index: 0;
        }

        /* All page content sits below the header */
        main.flex-1 > * {
            position: relative;
            z-index: 0;
        }

        /* Hamburger button — mobile only */
        @media (max-width: 767px) {
            #admin-sidebar-open { display: inline-flex !important; }
            #admin-sidebar-close { display: flex !important; }
        }
        @media (min-width: 768px) {
            #admin-sidebar-open { display: none !important; }
            #admin-sidebar-close { display: none !important; }
        }

        /* Mobile bottom nav */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            z-index: 35;
            padding: 8px 0;
        }

        @media (max-width: 767px) {
            .bottom-nav {
                display: flex;
            }
            main.flex-1 {
                padding-bottom: 80px !important;
            }
        }

        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 8px 4px;
            text-decoration: none;
            color: #64748b;
            font-size: 11px;
            font-weight: 600;
            transition: all .15s;
        }

        .bottom-nav-item:hover,
        .bottom-nav-item.active {
            color: #2563eb;
        }

        .bottom-nav-item svg {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body class="min-h-screen">
<div class="flex min-h-screen w-full">

    {{-- ===== SIDEBAR (fixed overlay on mobile, sticky on desktop) ===== --}}
    <aside id="admin-sidebar" class="sidebar">

        {{-- Logo + close button --}}
        <div class="flex items-center justify-between gap-2.5 px-5 py-5 border-b border-white/10 flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-white font-black text-sm">✚</div>
                <div>
                    <p class="text-white font-extrabold text-sm leading-none">MediCart</p>
                    <p class="text-blue-300 text-[10px] font-semibold uppercase tracking-widest mt-0.5">Admin Panel</p>
                </div>
            </div>
            <button id="admin-sidebar-close"
                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-white hover:bg-white/20 transition-colors flex-shrink-0 md:hidden"
                    aria-label="Close menu"
                    style="display:none;">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-2 space-y-1 flex flex-col">
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-blue-400/70 mb-2">Main</p>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-blue-400/70 mt-4 mb-2">Medicines</p>

            <a href="{{ route('admin.medicines.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.medicines.index') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                All Medicines
            </a>
            <a href="{{ route('admin.medicines.create') }}"
               class="sidebar-link {{ request()->routeIs('admin.medicines.create') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Medicine
            </a>
            <a href="{{ route('admin.medicines.import.form') }}"
               class="sidebar-link {{ request()->routeIs('admin.medicines.import*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import CSV
            </a>
            <a href="{{ route('admin.medicines.export') }}"
               class="sidebar-link">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </a>

            <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-blue-400/70 mt-4 mb-2">Orders</p>

            <a href="{{ route('admin.orders.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                All Orders
            </a>

            <a href="{{ route('admin.refunds.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.refunds*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                Refunds
                @php $pendingRefunds = cache()->remember('admin_pending_refunds', 60, fn() => \App\Models\Refund::where('status','requested')->count()); @endphp
                @if($pendingRefunds > 0)
                    <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                        {{ $pendingRefunds }}
                    </span>
                @endif
            </a>

            <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-blue-400/70 mt-4 mb-2">Catalogue</p>

            <a href="{{ route('admin.categories.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Categories
            </a>
        </nav>

        {{-- Bottom --}}
        <div class="px-3 py-4 border-t border-white/10 flex-shrink-0">
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-blue-400/70 mb-2">System</p>
            <a href="{{ route('admin.settings.notifications') }}"
               class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Notifications
            </a>
            <a href="{{ route('home') }}" target="_blank" class="sidebar-link mt-1">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Shop
            </a>
            <form method="post" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left text-red-300 hover:text-red-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Sidebar backdrop — mobile only --}}
    <div id="admin-sidebar-backdrop"></div>

    {{-- ===== MAIN ===== --}}
    <div class="flex-1 flex flex-col min-w-0 admin-main">

        {{-- Top bar --}}
        <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3 sm:py-3.5 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                {{-- Hamburger — mobile only --}}
                <button id="admin-sidebar-open"
                        class="md:hidden flex-shrink-0 inline-flex items-center justify-center h-9 w-9 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 active:bg-slate-300 transition-colors"
                        aria-label="Open menu"
                        style="display:none;">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight truncate">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 hidden sm:block truncate">@yield('page-subtitle', 'MediCart Admin Panel')</p>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                <div class="flex items-center gap-2 rounded-xl bg-slate-100 px-2.5 sm:px-3 py-1.5">
                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-black flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-xs font-semibold text-slate-700 hidden sm:inline max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                    <span class="rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-bold text-blue-700 hidden sm:inline">Admin</span>
                </div>
            </div>
        </header>

        {{-- Flash --}}
        @if(session('status'))
            <div class="admin-flash-message mx-3 sm:mx-6 mt-4 flex items-center gap-3 rounded-xl border border-white/20 px-4 py-3 text-sm font-medium text-white shadow-lg" style="background: linear-gradient(to right, #1d4ed8, #3b82f6, #60a5fa);">
                <svg class="h-4 w-4 flex-shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span class="flex-1">{{ session('status') }}</span>
            </div>
        @endif
        @if(session('import_errors') && count(session('import_errors')))
            <div class="admin-flash-message mx-3 sm:mx-6 mt-2 rounded-xl border border-white/20 bg-gradient-to-r from-blue-800 via-blue-600 to-blue-400 px-4 py-3 text-white shadow-lg">
                <p class="text-xs font-bold text-white mb-1">Import warnings:</p>
                @foreach(session('import_errors') as $err)
                    <p class="text-xs text-blue-50">• {{ $err }}</p>
                @endforeach
            </div>
        @endif
        @if($errors->any())
            <div class="admin-flash-message mx-3 sm:mx-6 mt-4 rounded-xl border border-white/20 bg-gradient-to-r from-blue-800 via-blue-600 to-blue-400 px-4 py-3 text-white shadow-lg">
                @foreach($errors->all() as $err)
                    <p class="text-xs text-blue-50">• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        {{-- Content --}}
        <main class="flex-1 p-3 sm:p-6 overflow-x-hidden">
            <div class="container px-0 sm:px-0">
                @yield('content')
            </div>
        </main>
    </div>
</div>

@stack('scripts')
<script>
document.querySelectorAll('.admin-flash-message').forEach(function (message) {
    window.setTimeout(function () {
        message.classList.add('is-hiding');
        window.setTimeout(function () { message.remove(); }, 220);
    }, 4500);
});

/* ── Admin sidebar open/close (mobile only) ── */
(function () {
    var sidebar  = document.getElementById('admin-sidebar');
    var backdrop = document.getElementById('admin-sidebar-backdrop');
    var closeBtn = document.getElementById('admin-sidebar-close');
    var openBtn  = document.getElementById('admin-sidebar-open');

    function openSidebar() {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    if (openBtn)  openBtn.addEventListener('click', openSidebar);
    if (closeBtn)  closeBtn.addEventListener('click', closeSidebar);
    if (backdrop)  backdrop.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

    // Expose so bottom-nav or other elements can open it if needed
    window.adminOpenSidebar  = openSidebar;
    window.adminCloseSidebar = closeSidebar;
})();
</script>

{{-- Bottom Navigation — Mobile Only --}}
<nav class="bottom-nav">
    <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Home</span>
    </a>
    <a href="{{ route('admin.medicines.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.medicines*') ? 'active' : '' }}" title="Medicines">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <span>Medicines</span>
    </a>
    <a href="{{ route('admin.orders.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}" title="Orders">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span>Orders</span>
    </a>
    <a href="{{ route('admin.refunds.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.refunds*') ? 'active' : '' }}" title="Refunds">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
        <span>Refunds</span>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" title="Categories">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        <span>Categories</span>
    </a>
</nav>

</body>
</html>
