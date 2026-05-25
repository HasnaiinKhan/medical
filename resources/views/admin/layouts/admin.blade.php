<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — MediCart Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f6f8fb; overflow-x: hidden; }
        .container { max-width: 1200px; margin: 0 auto; }

        /* ── Sidebar base styles ── */
        .sidebar {
            width: 220px;
            background: linear-gradient(180deg, #1e3a8a 0%, #2563eb 100%);
            display: flex;
            flex-direction: column;
        }

        /* ── DESKTOP (lg+): sidebar is fixed in document flow, main has left margin ── */
        @media (min-width: 1024px) {
            .sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                flex-shrink: 0;
                overflow-y: auto;
            }
            .admin-main { flex: 1; min-width: 0; }
        }

        /* ── MOBILE/TABLET (<lg): sidebar is a fixed overlay drawer ── */
        @media (max-width: 1023px) {
            .sidebar {
                position: fixed;
                inset-y: 0;
                left: 0;
                z-index: 50;
                height: 100vh;
                overflow-y: auto;
                box-shadow: 4px 0 32px rgba(0,0,0,.22);
                transform: translateX(-100%);
                transition: transform 0.22s cubic-bezier(.4,0,.2,1);
            }
            .sidebar.is-open {
                transform: translateX(0);
            }
            .admin-main { width: 100%; }
        }

        .sidebar-link { display:flex; align-items:center; gap:10px; padding:9px 14px; border-radius:10px; font-size:13px; font-weight:600; color:rgba(255,255,255,.85); transition:all .15s; text-decoration:none; }
        .sidebar-link:hover, .sidebar-link.active { background:rgba(255,255,255,.14); color:#fff; }
        .sidebar-link svg { flex-shrink:0; }

        .stat-card { background:#fff; border-radius:12px; border:1px solid #e6eef8; padding:16px; box-shadow:0 4px 10px rgba(12,38,63,.04); }
        .admin-table th { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; padding:10px 12px; background:#fbfdff; border-bottom:1px solid #e6eef8; }
        .admin-table td { padding:10px 12px; font-size:13px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
        .admin-table tr:last-child td { border-bottom:none; }
        .admin-table tr:hover td { background:#fbfdff; }
        .badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:99px; font-size:11px; font-weight:700; }
        .btn-sm { padding:6px 12px; border-radius:8px; font-size:12px; font-weight:700; transition:all .15s; }
        .admin-flash-message { transition:opacity .2s ease, transform .2s ease; }
        .admin-flash-message.is-hiding { opacity:0; transform:translateY(-6px); }
    </style>
</head>
<body class="min-h-screen">

{{-- ══════════════════════════════════════════════════════
     WRAPPER — flex row on desktop, block on mobile
══════════════════════════════════════════════════════ --}}
<div class="lg:flex min-h-screen" id="admin-wrapper">

    {{-- ══════════════════════════════════════════
         SIDEBAR
         Desktop: sticky in flow | Mobile: drawer
    ══════════════════════════════════════════ --}}
    <aside class="sidebar" id="admin-sidebar">

        {{-- Logo row — with close button on mobile --}}
        <div class="flex items-center justify-between px-5 py-5 border-b border-white/10 flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-white font-black text-sm">✚</div>
                <div>
                    <p class="text-white font-extrabold text-sm leading-none">MediCart</p>
                    <p class="text-blue-300 text-[10px] font-semibold uppercase tracking-widest mt-0.5">Admin Panel</p>
                </div>
            </a>
            {{-- Close button — only visible on mobile --}}
            <button onclick="closeSidebar()"
                    class="lg:hidden flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white/70 hover:bg-white/20 hover:text-white transition-colors"
                    aria-label="Close menu">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-blue-300/60">Main</p>

            <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <p class="px-3 pt-4 pb-2 text-[10px] font-bold uppercase tracking-widest text-blue-300/60">Medicines</p>

            <a href="{{ route('admin.medicines.index') }}" onclick="closeSidebar()"
               class="sidebar-link {{ request()->routeIs('admin.medicines.index') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                All Medicines
            </a>
            <a href="{{ route('admin.medicines.create') }}" onclick="closeSidebar()"
               class="sidebar-link {{ request()->routeIs('admin.medicines.create') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Medicine
            </a>
            <a href="{{ route('admin.medicines.import.form') }}" onclick="closeSidebar()"
               class="sidebar-link {{ request()->routeIs('admin.medicines.import*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import CSV
            </a>
            <a href="{{ route('admin.medicines.export') }}" onclick="closeSidebar()"
               class="sidebar-link">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </a>

            <p class="px-3 pt-4 pb-2 text-[10px] font-bold uppercase tracking-widest text-blue-300/60">Orders</p>

            <a href="{{ route('admin.orders.index') }}" onclick="closeSidebar()"
               class="sidebar-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                All Orders
            </a>

            <a href="{{ route('admin.refunds.index') }}" onclick="closeSidebar()"
               class="sidebar-link {{ request()->routeIs('admin.refunds*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                Refunds
                @php $pendingRefunds = \App\Models\Refund::where('status','requested')->count(); @endphp
                @if($pendingRefunds > 0)
                    <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                        {{ $pendingRefunds }}
                    </span>
                @endif
            </a>

            <p class="px-3 pt-4 pb-2 text-[10px] font-bold uppercase tracking-widest text-blue-300/60">Catalogue</p>

            <a href="{{ route('admin.categories.index') }}" onclick="closeSidebar()"
               class="sidebar-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Categories
            </a>
        </nav>

        {{-- Bottom --}}
        <div class="px-3 py-4 border-t border-white/10 flex-shrink-0 space-y-0.5">
            <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-blue-300/60">System</p>
            <a href="{{ route('admin.settings.notifications') }}" onclick="closeSidebar()"
               class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Notifications
            </a>
            <a href="{{ route('home') }}" target="_blank" class="sidebar-link">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Shop
            </a>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left text-red-300 hover:text-red-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Dark overlay — mobile only, click to close --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         style="display:none; opacity:0; transition:opacity .22s;"
         onclick="closeSidebar()"
         aria-hidden="true">
    </div>

    {{-- ══════════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-w-0 admin-main">

        {{-- Top bar --}}
        <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3.5 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Hamburger — mobile only --}}
                <button onclick="openSidebar()"
                        class="lg:hidden flex-shrink-0 inline-flex items-center justify-center h-9 w-9 rounded-lg bg-slate-100 text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                        aria-label="Open menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-sm sm:text-base font-bold text-slate-900 truncate">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-xs text-slate-500 hidden sm:block mt-0.5">@yield('page-subtitle', 'MediCart Admin Panel')</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <div class="flex items-center gap-2 rounded-xl bg-slate-100 px-2.5 sm:px-3 py-1.5">
                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-black flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-xs font-semibold text-slate-700 hidden sm:inline max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                    <span class="rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-bold text-blue-700 hidden sm:inline">Admin</span>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('status'))
            <div class="admin-flash-message mx-4 sm:mx-6 mt-4 flex items-center gap-3 rounded-xl border border-white/20 px-4 py-3 text-sm font-medium text-white shadow-lg"
                 style="background:linear-gradient(to right,#1d4ed8,#3b82f6,#60a5fa);">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div class="admin-flash-message mx-4 sm:mx-6 mt-4 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">
                <svg class="h-4 w-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if(session('import_errors') && count(session('import_errors')))
            <div class="admin-flash-message mx-4 sm:mx-6 mt-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                <p class="text-xs font-bold text-amber-800 mb-1">Import warnings:</p>
                @foreach(session('import_errors') as $err)
                    <p class="text-xs text-amber-700">• {{ $err }}</p>
                @endforeach
            </div>
        @endif
        @if($errors->any())
            <div class="admin-flash-message mx-4 sm:mx-6 mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                @foreach($errors->all() as $err)
                    <p class="text-xs text-red-700">• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-3 sm:p-6">
            <div class="container">
                @yield('content')
            </div>
        </main>
    </div>

</div>{{-- end wrapper --}}

@stack('scripts')
<script>
/* ── Sidebar open/close (mobile only) ── */
function openSidebar() {
    var s = document.getElementById('admin-sidebar');
    var o = document.getElementById('sidebar-overlay');
    s.classList.add('is-open');
    o.style.display = 'block';
    requestAnimationFrame(function () { o.style.opacity = '1'; });
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    var s = document.getElementById('admin-sidebar');
    var o = document.getElementById('sidebar-overlay');
    s.classList.remove('is-open');
    o.style.opacity = '0';
    setTimeout(function () { o.style.display = 'none'; }, 220);
    document.body.style.overflow = '';
}
/* Close on Escape */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeSidebar();
});

/* ── Auto-dismiss flash messages ── */
document.querySelectorAll('.admin-flash-message').forEach(function (el) {
    setTimeout(function () {
        el.style.transition = 'opacity .2s, transform .2s';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-6px)';
        setTimeout(function () { el.remove(); }, 220);
    }, 4500);
});
</script>
</body>
</html>
