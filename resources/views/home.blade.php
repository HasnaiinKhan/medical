@extends('layouts.shop')

@section('title', 'Home')
@section('content')

<style>
/* ── Scroll reveal ── */
.reveal { opacity:0; transform:translateY(20px); transition:opacity .5s ease, transform .5s ease; }
.reveal.visible { opacity:1; transform:translateY(0); }
.delay-1{transition-delay:.08s} .delay-2{transition-delay:.16s}
.delay-3{transition-delay:.24s} .delay-4{transition-delay:.32s}

/* ── Hero gradient ── */
@keyframes heroFlow {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
.hero-gradient-bg {
    background: linear-gradient(135deg, #1e3a8a, #1e40af, #2563eb, #3b82f6, #1e40af, #1e3a8a);
    background-size: 300% 300%;
    animation: heroFlow 8s ease infinite;
}

/* ── Hero shimmer text ── */
.hero-shimmer {
    background: linear-gradient(90deg, #fff 0%, #93c5fd 50%, #fff 100%);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
/* ── Promo banner shine ── */
@keyframes promoShine {
    from { transform:translateX(-100%) skewX(-20deg); }
    to   { transform:translateX(300%)  skewX(-20deg); }
}
.promo-shine::after {
    content:''; position:absolute; top:0; left:0; width:40%; height:100%;
    background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);
    animation:promoShine 3s ease-in-out infinite;
}

/* ── Cat card icon bounce ── */
.cat-card { transition: transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s, border-color .25s; }
.cat-card:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 10px 24px rgba(37,99,235,.14); border-color:#93c5fd !important; }
.cat-card .cat-icon { transition: transform .3s cubic-bezier(.34,1.56,.64,1); }
.cat-card:hover .cat-icon { transform: scale(1.2) rotate(-6deg); }

/* ── Med card ── */
.med-card { transition: transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s, border-color .25s; }
.med-card:hover { transform: translateY(-5px); box-shadow: 0 14px 36px rgba(37,99,235,.15); border-color:#93c5fd !important; }
.med-card .med-img { transition: transform .4s cubic-bezier(.4,0,.2,1); }
.med-card:hover .med-img { transform: scale(1.07); }
.margeen {
    margin-left:20px;
    padding:10px;}
/* =========================================
   CATEGORY SECTION
========================================= */

.category-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
}

/* Card */
.category-card{
    position:relative;
    display:flex;
    flex-direction:column;

    background:#fff;
    border:1px solid #f1f5f9;
    border-radius:18px;

    overflow:hidden;

    text-decoration:none;

    transition:all .3s ease;
}

/* Hover */
.category-card:hover{
    transform:translateY(-5px);
    box-shadow:0 12px 30px rgba(0,0,0,.08);
}

/* Image */
.category-image{
    position:relative;
    height:75px;
    overflow:hidden;
}

/* Desktop image fix */
@media (min-width:768px){
    .category-image{
        height:140px;
    }
}

@media (min-width:1024px){
    .category-image{
        height:190px;
    }
}

.category-img{
    width:100%;
    height:100%;
    object-fit:cover;

    transition:transform .5s ease;
}

.category-card:hover .category-img{
    transform:scale(1.08);
}

/* Overlay */
.category-overlay{
    position:absolute;
    inset:0;

    opacity:0;
    transition:opacity .3s ease;
}

.category-card:hover .category-overlay{
    opacity:.88;
}

/* Content */
.category-content{
    padding:10px 12px;
    border-top:1px solid #e2e8f0;
}

/* Title */
.category-title{
    font-size:13px;
    font-weight:700;
    color:#0f172a;

    line-height:1.3;

    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;

    transition:color .3s ease;
}

.category-card:hover .category-title{
    color:#2563eb;
}

/* Link */
.category-link{
    margin-top:4px;

    font-size:11px;
    font-weight:600;

    color:#94a3b8;

    transition:color .3s ease;
}

.category-card:hover .category-link{
    color:#3b82f6;
}

/* Bottom line */
.category-bottom{
    height:3px;
    width:100%;
}

/* Tablet */
@media (min-width:768px){

    .category-grid{
        gap:14px;
    }

    .category-title{
        font-size:14px;
    }
}

/* Desktop */
@media (min-width:1024px){

    .category-grid{
        grid-template-columns:repeat(5,1fr);
        gap:18px;
    }

    .category-content{
        padding:14px;
    }

    .category-title{
        font-size:15px;
    }

    .category-link{
        font-size:12px;
    }
}

</style>

{{-- ============================================================
     HERO
     ============================================================ --}}

<<<<<<< HEAD
<section class="relative mb-6 overflow-hidden rounded-2xl shadow-2xl hero-gradient-bg min-h-[420px] sm:min-h-[500px]">
=======
<section class="relative mb-6 overflow-hidden rounded-2xl shadow-2xl hero-gradient-bg min-h-[320px] sm:min-h-[380px]">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb

         {{-- Hero image --}}
    <div class="pointer-events-none absolute bottom-0 right-6 hidden sm:block">
        <img src="{{ asset('Images/handwithphonemedicine.png') }}"
             alt="Order medicines on your phone"
<<<<<<< HEAD
             class="h-128 w-auto object-contain object-bottom"
             style="margin-bottom:100px;"
=======
             class="mb-12 h-[200px] w-auto object-contain object-bottom"
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
             draggable="false">
    </div>

         {{-- Content --}}
    <div class="relative px-7 py-12 sm:px-12 sm:py-16 max-w-2xl">

        {{-- Live badge --}}
        <div class="reveal mb-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-bold text-white"
             style="background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);">
            <span class="h-2 w-2 rounded-full flex-shrink-0" style="background:#ef4444;"></span>
            Delivering across Ahmedabad — 32+ pincodes
        </div>

        {{-- Headline --}}
        <h1 class="reveal delay-1 text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl tracking-tight">
            Medicines<br>
            <span class="hero-shimmer">to your doorstep</span>
        </h1>

        {{-- Subtext --}}
        <p class="reveal delay-2 mt-4 max-w-md text-base hero-shimmer leading-relaxed">
            Genuine medicines online. Free delivery on orders above ₹500. Pay cash or online.
        </p>

        {{-- Search --}}
        <form action="{{ route('medicines.index') }}" method="get"
              class="reveal delay-3 mt-7 flex max-w-lg flex-col gap-2 sm:flex-row">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Search medicine or brand…"
                       class="w-full rounded-xl border-0 bg-white/95 py-3 pl-11 pr-4 text-sm text-slate-800 placeholder:text-slate-400 shadow-lg focus:outline-none focus:ring-2 focus:ring-white/50">
            </div>
            <button type="submit"
                    class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-blue-800 shadow-lg hover:bg-blue-50 transition-colors">
                Search
            </button>
        </form>

                      {{-- Quick tags --}}
        <div class="reveal delay-4 mt-4 flex flex-wrap gap-2">
            @foreach(['Paracetamol', 'Vitamin D', 'Antacid', 'Sunscreen', 'Dolo 650'] as $tag)
                <a href="{{ route('medicines.index', ['q' => $tag]) }}"
                   class="rounded-full px-3 py-1 text-xs font-semibold text-white/90"
                   style="background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);">
                    {{ $tag }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     STATS COUNTER ROW
     ============================================================ --}}
<!-- <section class="reveal mb-10">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach([
            ['num'=>'45+',  'label'=>'Medicines',       'icon'=>'💊', 'color'=>'text-blue-700'],
            ['num'=>'32+',  'label'=>'Delivery Areas',  'icon'=>'📍', 'color'=>'text-blue-600'],
            ['num'=>'10',   'label'=>'Categories',      'icon'=>'🗂️', 'color'=>'text-purple-600'],
            ['num'=>'100%', 'label'=>'Genuine Products','icon'=>'✅', 'color'=>'text-blue-700'],
        ] as $i => $stat)
            <div class="reveal delay-{{ $i+1 }} trust-card flex flex-col items-center gap-1 rounded-2xl border border-slate-200 bg-white py-5 px-4 text-center shadow-sm">
                <span class="text-2xl mb-1">{{ $stat['icon'] }}</span>
                <p class="counter-num text-2xl font-black {{ $stat['color'] }}">{{ $stat['num'] }}</p>
                <p class="text-xs font-semibold text-slate-500">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>
</section> -->

{{-- ============================================================
     TRUST BADGES
     ============================================================ --}}
<section class="mb-10">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['img'=>'MedicalDelhiveryboy.png', 'title'=>'Free Delivery',     'sub'=>'On orders above ₹500',       'bg'=>'from-blue-50 to-blue-50',  'border'=>'border-blue-100'],
            ['img'=>'drugs.png',       'title'=>'Genuine Medicines', 'sub'=>'100% authentic products',    'bg'=>'from-blue-50 to-indigo-50',   'border'=>'border-blue-100'],
            ['img'=>'buy.png',   'title'=>'COD & Online Pay',  'sub'=>'Flexible payment options',   'bg'=>'from-amber-50 to-orange-50',  'border'=>'border-amber-100'],
            ['img'=>'manifest.png','title'=>'Easy Ordering',     'sub'=>'Order from your phone',      'bg'=>'from-purple-50 to-violet-50', 'border'=>'border-purple-100'],
        ] as $i => $badge)
            <div class="reveal delay-{{ $i+1 }} trust-card flex items-center gap-4 rounded-2xl border {{ $badge['border'] }} bg-gradient-to-br {{ $badge['bg'] }} px-5 py-4 shadow-sm">
                <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-xl bg-white/70 flex items-center justify-center shadow-sm">
                    <img src="{{ asset('Images/' . $badge['img']) }}"
                         alt="{{ $badge['title'] }}"
                         class="h-11 w-11 object-contain"
                         loading="lazy">
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">{{ $badge['title'] }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $badge['sub'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>



{{-- ============================================================
     CATEGORIES
     ============================================================ --}}
<section class="mb-12 overflow-hidden">

    <div class="mb-6 flex items-end justify-between">
        <div>
            <h2 class="section-heading reveal text-3xl font-black text-slate-900 tracking-tight">
                Shop by Category
            </h2>
        </div>

        <a href="{{ route('medicines.index') }}"
           class="reveal text-sm font-bold text-blue-700 hover:text-blue-800 transition-colors hover:underline">
            View all →
        </a>
    </div>

    @php
    $catIcons = [
        'fever-pain'   => ['img'=>'real/fever.png', 'color'=>'#ef4444', 'light'=>'#fef2f2', 'grad'=>'from-red-500 to-orange-400'],
        'vitamins'     => ['img'=>'real/vitamins.png', 'color'=>'#6366f1', 'light'=>'#eef2ff', 'grad'=>'from-indigo-500 to-blue-400'],
        'digestive'    => ['img'=>'real/digestive.png', 'color'=>'#22c55e', 'light'=>'#f0fdf4', 'grad'=>'from-green-500 to-emerald-400'],
        'diabetes'     => ['img'=>'real/diabetes.png', 'color'=>'#ec4899', 'light'=>'#fdf2f8', 'grad'=>'from-pink-500 to-rose-400'],
        'heart-bp'     => ['img'=>'real/heart.png', 'color'=>'#f43f5e', 'light'=>'#fff1f2', 'grad'=>'from-rose-500 to-red-400'],
        'skin'         => ['img'=>'real/skin.png', 'color'=>'#f59e0b', 'light'=>'#fffbeb', 'grad'=>'from-amber-500 to-yellow-400'],
        'cold-allergy' => ['img'=>'real/cold.png', 'color'=>'#0ea5e9', 'light'=>'#f0f9ff', 'grad'=>'from-sky-500 to-cyan-400'],
        'eye-ear'      => ['img'=>'real/eye.png', 'color'=>'#8b5cf6', 'light'=>'#f5f3ff', 'grad'=>'from-violet-500 to-purple-400'],
        'bone-joint'   => ['img'=>'real/bone.png', 'color'=>'#64748b', 'light'=>'#f8fafc', 'grad'=>'from-slate-500 to-gray-400'],
        'immunity'     => ['img'=>'real/immune.png', 'color'=>'#2563eb', 'light'=>'#eff6ff', 'grad'=>'from-blue-600 to-blue-400'],
    ];
    @endphp

    <div class="category-grid">

        @foreach ($categories as $cat)

            @php
                $meta = $catIcons[$cat->slug]
                    ?? ['img'=>'medicine.png', 'color'=>'#2563eb', 'light'=>'#eff6ff', 'grad'=>'from-blue-600 to-blue-400'];

                $delay = ($loop->index % 5) + 1;
            @endphp

            <a href="{{ route('medicines.index', ['category' => $cat->slug]) }}"
               class="reveal delay-{{ $delay }} category-card group">

                {{-- Image Area --}}
                <div class="category-image"
                     style="background: {{ $meta['light'] }};">

                    {{-- Hover Gradient --}}
                    <div class="category-overlay bg-gradient-to-br {{ $meta['grad'] }}"></div>

                    {{-- Image --}}
                    <img src="{{ asset('Images/' . $meta['img']) }}"
                         alt="{{ $cat->name }}"
                         class="category-img"
                         loading="lazy"
                         onerror="this.style.opacity='.2'">

                </div>

                {{-- Text --}}
                <div class="category-content"
                     style="border-color: {{ $meta['color'] }}22;">

                    <p class="category-title">
                        {{ $cat->name }}
                    </p>

                    <p class="category-link">
                        Browse →
                    </p>
                </div>

                {{-- Bottom Gradient --}}
                <div class="category-bottom bg-gradient-to-r {{ $meta['grad'] }}"></div>

            </a>

        @endforeach

    </div>
</section>

{{-- ============================================================
     FEATURED MEDICINES
     ============================================================ --}}
<section class="mb-12">
    <div class="mb-6 flex items-end justify-between">
        <div>
            <h2 class="section-heading reveal text-2xl font-black text-slate-900 tracking-tight">Popular Picks</h2>
            <p class="reveal delay-1 mt-1 text-sm text-slate-500">Best-selling medicines at great prices</p>
        </div>
        <a href="{{ route('medicines.index') }}"
           class="reveal text-sm font-bold text-blue-700 hover:text-blue-800 transition-colors hover:underline">
            View all →
        </a>
    </div>

    @php $cartItems = app(\App\Services\CartService::class)->items(); @endphp
    <div class="popular-scroll">
        @foreach ($featured as $m)
            @php
            $colors = ['from-blue-50 to-blue-100','from-blue-50 to-indigo-100','from-purple-50 to-violet-100','from-amber-50 to-orange-100','from-rose-50 to-pink-100','from-sky-50 to-cyan-100'];
            $color  = $colors[$loop->index % count($colors)];
            $delay  = ($loop->index % 4) + 1;
            @endphp
            <article data-product-url="{{ route('medicines.show', $m) }}" data-product-id="{{ $m->id }}" class="medicine-card cursor-pointer reveal delay-{{ $delay }} med-card flex flex-col rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">

                {{-- Image --}}
                <div class="relative h-36 overflow-hidden bg-gradient-to-br {{ $color }}">
                    <img src="{{ $m->imageUrl() }}"
                         alt="{{ $m->name }}"
                         class="med-img h-full w-full object-contain object-center p-3"
                         loading="lazy"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="absolute inset-0 hidden items-center justify-center">
                        <span class="text-5xl font-black text-slate-300/50 select-none">{{ strtoupper(substr($m->name,0,1)) }}</span>
                    </div>
                    @if($m->prescription_required)
                        <span class="absolute top-2 left-2 rounded-lg bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800 ring-1 ring-amber-200 uppercase tracking-wide">Rx</span>
                    @endif
                    @if($m->mrp_paise > $m->price_paise)
                        <span class="absolute top-2 right-2 rounded-lg bg-blue-600 px-2 py-0.5 text-[10px] font-black text-white uppercase tracking-wide">
                            {{ $m->discountPercent() }}% OFF
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex flex-1 flex-col p-4">
                    <span class="inline-block rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-800 mb-1.5 w-fit">{{ $m->category->name }}</span>
                    <h3 class="line-clamp-2 text-sm font-bold text-slate-900 leading-snug">{{ $m->name }}</h3>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $m->manufacturer }}</p>

                    <div class="mt-2.5 flex items-baseline gap-1.5">
                        <span class="text-lg font-black text-slate-900">₹{{ number_format($m->priceRupees(), 2) }}</span>
                        @if($m->mrp_paise > $m->price_paise)
                            <span class="text-xs text-slate-400 line-through">₹{{ number_format($m->mrpRupees(), 2) }}</span>
                            <span class="text-xs font-bold text-blue-700">Save ₹{{ number_format($m->mrpRupees() - $m->priceRupees(), 2) }}</span>
                        @endif
                    </div>

                    <div class="mt-auto pt-3">
                        <form method="post" action="{{ route('cart.add') }}" class="js-add-to-cart-form w-full {{ isset($cartItems[$m->id]) && $cartItems[$m->id] > 0 ? 'hidden' : '' }}">
                            @csrf
                            <input type="hidden" name="medicine_id" value="{{ $m->id }}">
                            <button type="submit"
                                    class="btn-primary btn-ripple w-full rounded-2xl py-3 text-sm font-black text-white shadow-sm">
                                Add to Cart
                            </button>
                        </form>

                        <form method="post" action="{{ route('cart.update', $m) }}"
                              class="js-cart-update-form flex w-full items-center justify-between rounded-2xl border border-slate-200 overflow-hidden mt-3 {{ isset($cartItems[$m->id]) && $cartItems[$m->id] > 0 ? '' : 'hidden' }}"
                              data-cart-medicine-id="{{ $m->id }}">
                            @csrf
                            @method('PATCH')
                            <button type="button"
                                    class="js-card-qty-minus w-14 bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors font-bold text-lg leading-none"
                                    aria-label="Decrease quantity">−</button>
                            <input type="number" name="quantity" value="{{ $cartItems[$m->id] ?? 1 }}" min="0" max="99" readonly
                                   class="flex-1 border-x border-slate-200 bg-white py-3 text-center text-sm font-semibold focus:outline-none" />
                            <button type="button"
                                    class="js-card-qty-plus w-14 bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors font-bold text-lg leading-none"
                                    aria-label="Increase quantity">+</button>
                        </form>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>


{{-- ============================================================
     SHOP BY BRAND
     ============================================================ --}}
<section class="mb-12">
    <div class="mb-6 flex items-end justify-between">
        <div>
            <h2 class="section-heading reveal text-2xl font-black text-slate-900 tracking-tight">Shop by Brand</h2>
            <p class="reveal delay-1 mt-1 text-sm text-slate-500">Click a brand to browse all their products</p>
        </div>
        <a href="{{ route('medicines.index') }}"
           class="reveal text-sm font-bold text-blue-700 hover:text-blue-800 transition-colors hover:underline">
            View all →
        </a>
    </div>

    @php
    $brands = [
    ['name' => 'Himalaya',     'img' => 'images/HimalayaLogo.png',    'color' => 'from-green-50 to-emerald-50',  'border' => 'border-green-200', 'text' => 'text-green-800'],
    ['name' => 'Cipla',        'img' => 'images/CiplaLogo.png',       'color' => 'from-blue-50 to-sky-50',       'border' => 'border-green-200', 'text' => 'text-blue-800'],
    ['name' => 'Sun Pharma',   'img' => 'images/sunpharmalogo.png',   'color' => 'from-orange-50 to-amber-50',   'border' => 'border-green-200', 'text' => 'text-orange-800'],
    ['name' => 'Abbott',       'img' => 'images/Abbottlogo.png',      'color' => 'from-red-50 to-rose-50',       'border' => 'border-green-200', 'text' => 'text-red-800'],
    ['name' => 'Dr. Reddy\'s', 'img' => 'images/DrReddyslogo.png',   'color' => 'from-indigo-50 to-violet-50',  'border' => 'border-green-200', 'text' => 'text-indigo-800'],
    ['name' => 'Dabur',        'img' => 'images/Daburlogo.png',       'color' => 'from-yellow-50 to-amber-50',   'border' => 'border-green-200', 'text' => 'text-yellow-800'],
    ['name' => 'Zydus',        'img' => 'images/zyduslogo.png',       'color' => 'from-teal-50 to-cyan-50',      'border' => 'border-green-200', 'text' => 'text-teal-800'],
    ['name' => 'Micro Labs',   'img' => 'images/microlabslogo.png',   'color' => 'from-slate-50 to-gray-50',     'border' => 'border-green-200', 'text' => 'text-slate-800'],
    ['name' => 'Sanofi',       'img' => 'images/sanofilogo.png',      'color' => 'from-slate-50 to-gray-50',     'border' => 'border-green-200', 'text' => 'text-slate-800'],
];
    @endphp

   <div class="brand-scroll" style="-webkit-overflow-scrolling:touch; scrollbar-width:none;">
        @foreach($brands as $i => $brand)
            <a href="{{ route('medicines.index', ['q' => $brand['name']]) }}"
               class="reveal delay-{{ ($i % 5) + 1 }} group flex flex-col items-center gap-2.5 flex-shrink-0">

                {{-- Round logo circle --}}
               <div class="brand-circle relative rounded-full bg-white border-2 {{ $brand['border'] }} shadow-md
                            flex items-center justify-center overflow-hidden
                            transition-all duration-300
                            group-hover:shadow-xl ">

                    {{-- Coloured bg fill on hover --}}
                    <div class="absolute inset-0 rounded-full bg-gradient-to-br {{ $brand['color'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    @if($brand['img'])
                        <img src="{{ $brand['img'] }}"
                             alt="{{ $brand['name'] }}"
                             class="brand-logo relative z-10 object-contain transition-transform duration-300"
                             loading="lazy"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <span class="relative z-10 hidden items-center justify-center text-xs font-extrabold text-center leading-tight px-1 {{ $brand['text'] }}">
                            {{ $brand['name'] }}
                        </span>
                    @else
                        <span class="relative z-10 flex items-center justify-center text-xs font-extrabold text-center leading-tight px-2 {{ $brand['text'] }}">
                            {{ $brand['name'] }}
                        </span>
                    @endif
                </div>

                {{-- Brand name below --}}
                <p class="text-sm font-semibold text-slate-600 text-center leading-tight group-hover:text-blue-700 transition-colors w-20">
                    {{ $brand['name'] }}
                </p>
            </a>
        @endforeach
    </div>
</section>



{{-- ============================================================
     INFO SECTIONS — Lung Research + Natural Medicine
     ============================================================ --}}
<section class="mb-12 space-y-6">

    {{-- Row 1: Text LEFT · Image RIGHT (50/50) --}}
    <div class="info-section reveal grid grid-cols-1 sm:grid-cols-2 bg-white border border-slate-200 shadow-sm">

        {{-- Left: content --}}
       <div class="p-4 info-content flex flex-col justify-center lg:px-12">
            <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 mb-4 uppercase tracking-wide w-fit">
                <i class="fa-solid fa-lungs" style="color: rgb(30, 48, 80);"></i> Lung Health
            </span>
            <h2 class="text-2xl font-extrabold text-slate-900 leading-tight lg:text-3xl">
                Advanced Lung Research &amp; Respiratory Care
            </h2>
            <p class="mt-3 text-sm text-slate-500 leading-relaxed">
                Stay ahead with the latest breakthroughs in pulmonary medicine. From bronchodilators to nebulizers, we stock clinically proven respiratory medicines to help you breathe easier every day.
            </p>
            <ul class="mt-4 space-y-2">
                @foreach(['Bronchodilators & Inhalers', 'Nebulizer Solutions', 'Cough & Cold Relief', 'Allergy Management'] as $point)
                    <li class="flex items-center gap-2 text-sm text-slate-700">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-bold flex-shrink-0">✓</span>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('medicines.index', ['category' => 'cold-allergy']) }}"
               class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-blue-700 transition-colors shadow-sm w-fit">
                Shop Respiratory
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        {{-- Right: image fills exactly half --}}
        <div class="info-image bg-gradient-to-br from-blue-50 to-sky-100 flex items-center justify-center">
            <img src="{{ asset('Images/lungresearch.png') }}"
                 alt="Lung Research"
                 class="w-full h-full object-contain max-h-80 drop-shadow-xl"
                 loading="lazy">
        </div>
    </div>

    {{-- Row 2: Image LEFT · Text RIGHT (50/50) --}}
   <div class="info-section reveal grid grid-cols-1 sm:grid-cols-2 bg-white border border-slate-200 shadow-sm">

        {{-- Left: image fills exactly half --}}
       <div class="info-image bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center order-2 sm:order-1">
            <img src="{{ asset('Images/Naturalmedicine.png') }}"
                 alt="Natural Medicine"
                 class="w-full h-full object-contain max-h-80 drop-shadow-xl"
                 loading="lazy">
        </div>

        {{-- Right: content --}}
        <div class="p-4 info-content flex flex-col justify-center order-1 sm:order-2 lg:px-12">
            <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 mb-4 uppercase tracking-wide w-fit">
                🌿 Natural Wellness
            </span>
            <h2 class="text-2xl font-extrabold text-slate-900 leading-tight lg:text-3xl">
                Nature-Backed Medicines &amp; Herbal Remedies
            </h2>
            <p class="mt-3 text-sm text-slate-500 leading-relaxed">
                Harness the power of nature with our curated range of herbal and Ayurvedic medicines. Trusted ingredients, modern formulations — gentle on your body, effective on symptoms.
            </p>
            <ul class="mt-4 space-y-2">
                @foreach(['Ayurvedic Supplements', 'Herbal Immunity Boosters', 'Natural Pain Relief', 'Vitamin & Mineral Blends'] as $point)
                     <li class="flex items-center gap-2 text-sm text-slate-700">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-bold flex-shrink-0">✓</span>
                     {{ $point }}
                    </li>
                @endforeach
            </ul>
           <a href="{{ route('medicines.index', ['category' => 'cold-allergy']) }}"
               class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-blue-700 transition-colors shadow-sm w-fit">
                Shop Natural
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>

</section>

{{-- ============================================================
     SUCCESS NUMBERS — PREMIUM
     ============================================================ --}}
<section class="mb-12 reveal">

<style>

/* =========================================
   INFO SECTIONS MOBILE FIX
========================================= */

@media (max-width:767px){

    /* Main card */
    .info-section{
        border-radius:24px;
        overflow:hidden;

        min-height:auto !important;
    }

    /* Content */
    .info-content{
        padding:22px 18px !important;
    }

    /* Heading */
    .info-content h2{
        font-size:1.45rem !important;
        line-height:1.25 !important;
    }

    /* Paragraph */
    .info-content p{
        font-size:.92rem !important;
        line-height:1.7 !important;
    }

    /* Image container */
    .info-image{
        min-height:220px !important;
        padding:18px !important;
    }

    /* Image */
    .info-image img{
        max-height:220px !important;
        object-fit:contain;
    }

    /* Features list */
    .info-content ul{
        margin-top:16px !important;
    }

    .info-content li{
        font-size:.9rem !important;
        align-items:flex-start !important;
    }

    /* Buttons */
    .info-content a{
        width:100%;
        justify-content:center;

        padding:14px 18px !important;
        border-radius:16px !important;

        margin-top:20px !important;
    }
}

/* Brand circle */
.brand-circle{
    width:96px;
    height:96px;

    padding:12px; /* IMPORTANT */

    border-radius:9999px;

    overflow:hidden;

    transition:
        transform .25s ease,
        box-shadow .25s ease;
}

/* Logo */
.brand-logo{
    width:100%;
    height:100%;

    object-fit:contain;

    transform:scale(.9);

    transition:transform .3s ease;
}

/* Hover */
.group:hover .brand-logo{
    transform:scale(1);
}



/* =========================================
   BRAND SCROLL FIX
========================================= */

.brand-scroll{

    display:flex;
    gap:28px;

    overflow-x:auto;
    overflow-y:hidden;

    padding:10px 4px 16px;

    -webkit-overflow-scrolling:touch;
    scroll-behavior:smooth;

    scrollbar-width:none;
    -ms-overflow-style:none;

    align-items:flex-start;
}

.brand-scroll::-webkit-scrollbar{
    display:none;
}

/* Brand item */
.brand-scroll a{
    flex-shrink:0;
}

/* Logo circle */
.brand-scroll .brand-circle{
    position:relative;

    width:96px;
    height:96px;
    padding:10px;
    border-radius:9999px;

    overflow:hidden;

    transition:
        transform .25s ease,
        box-shadow .25s ease;
}

/* Hover */
@media (hover:hover){

    .brand-scroll .brand-circle:hover{
        transform:scale(1.05);
    }
}

/* Prevent vertical movement */
.brand-scroll .brand-circle,
.brand-scroll img{
    will-change:transform;
    backface-visibility:hidden;
}



        /* =========================================
   POPULAR PICKS MOBILE HORIZONTAL SCROLL
========================================= */

/* Popular section container */
/* Popular section container */
.popular-scroll{
    display:flex;
    gap:14px;
    overflow-x:auto;
    padding-bottom:8px;
     overflow-y:hidden;
     /* touch-action:pan-x;  */
    scroll-behavior:smooth;

    -ms-overflow-style:none;
    scrollbar-width:none;
}

.popular-scroll::-webkit-scrollbar{
    display:none;
}

/* Card size */
.popular-scroll .med-card{
    min-width:220px;
    max-width:220px;
    flex-shrink:0;
}

/* Tablet */
@media (min-width:768px){
    .popular-scroll{
        display:grid;
        grid-template-columns:repeat(2, 1fr);
        overflow:visible;
    }

    .popular-scroll .med-card{
        min-width:unset;
        max-width:unset;
    }
}

/* Desktop */
@media (min-width:1024px){
    .popular-scroll{
        grid-template-columns:repeat(4, 1fr);
    }
}


    /* Floating orbs behind section */
    .stats-section { position: relative; }
    .stats-section::before, .stats-section::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        pointer-events: none;
        z-index: 0;
    }
    .stats-section::before {
        width: 300px; height: 300px;
        background: rgba(37,99,235,0.08);
        top: -60px; left: -60px;
        animation: orbFloat 6s ease-in-out infinite;
    }
    .stats-section::after {
        width: 250px; height: 250px;
        background: rgba(245,158,11,0.07);
        bottom: -40px; right: -40px;
        animation: orbFloat 8s ease-in-out infinite reverse;
    }
    @keyframes orbFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50%       { transform: translateY(-20px) scale(1.05); }
    }

    /* Pulse ring around icon */
    .stat-icon-wrap { position: relative; }
    .stat-icon-wrap::before {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: 1rem;
        border: 2px solid currentColor;
        opacity: 0;
        transform: scale(0.85);
        transition: opacity .4s, transform .4s;
    }
    .stat-card:hover .stat-icon-wrap::before {
        opacity: 0.25;
        transform: scale(1);
    }

    /* Shimmer sweep on card hover */
    .stat-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.18) 50%, transparent 60%);
        transform: translateX(-100%);
        transition: transform 0s;
        border-radius: inherit;
        pointer-events: none;
        z-index: 20;
    }
    .stat-card:hover::after {
        transform: translateX(100%);
        transition: transform .55s ease;
    }

    /* Number glow */
    .stat-num-glow {
        text-shadow: 0 0 40px currentColor;
        transition: text-shadow .3s;
    }

    /* Card base */
    .stat-card {
        position: relative;
        overflow: hidden;
        transition: transform .3s cubic-bezier(.34,1.56,.64,1), box-shadow .3s;
    }
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 24px 48px rgba(0,0,0,0.13);
    }

    /* Star pop animation */
    @keyframes starPop {
        0%   { transform: scale(0) rotate(-30deg); opacity: 0; }
        70%  { transform: scale(1.3) rotate(5deg);  opacity: 1; }
        100% { transform: scale(1) rotate(0deg);    opacity: 1; }
    }
    .star-animate { display: inline-block; opacity: 0; }
    .star-animate.popped { animation: starPop .35s cubic-bezier(.34,1.56,.64,1) forwards; }

    /* Counter */
    .stat-num { transition: color .3s; }

    /* ── Progress bars (pure CSS, no Tailwind dynamic classes) ── */
    
   /* ── Progress bars ── */
.stat-bar-track {
<<<<<<< HEAD
    margin-top: 14px;
    height: 6px;
    width: 80px;
    border-radius: 999px;
    overflow: hidden;
    background: rgba(0,0,0,0.10);
}
.stat-bar-fill {
    height: 100%;
    width: 35%;
    border-radius: 999px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
=======
    margin-top: 16px;
    height: 4px;
    width: 64px;
    border-radius: 999px;
    overflow: hidden;
    background: rgba(0,0,0,0.06);
}
.stat-bar-fill {
    height: 100%;
    width: 0%;
    border-radius: 999px;
    transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
}
.stat-bar-fill.blue  { background: #3b82f6; }
.stat-bar-fill.amber { background: #f59e0b; }
.stat-bar-fill.green { background: #22c55e; }

<<<<<<< HEAD
/* Expand to full on card hover */
.stat-card:hover .stat-bar-fill { width: 100%; }

/* Bar track turns white/translucent on hover */
.stat-card:hover .stat-bar-track { background: rgba(255,255,255,0.25); }
.stat-card:hover .stat-bar-fill  { background: rgba(255,255,255,0.9); }

/* Number & accent colors — default */
.stat-num-blue   { color: #1d4ed8; }
.stat-plus-blue  { color: #93c5fd; }
.stat-num-amber  { color: #d97706; }
.stat-plus-amber { color: #fbbf24; }
.stat-num-green  { color: #15803d; }
.stat-plus-green { color: #86efac; }
.stat-star       { color: #f59e0b; }

/* On hover — all numbers, accents and stars turn white */
.stat-card:hover .stat-num-blue,
.stat-card:hover .stat-plus-blue,
.stat-card:hover .stat-num-amber,
.stat-card:hover .stat-plus-amber,
.stat-card:hover .stat-num-green,
.stat-card:hover .stat-plus-green,
.stat-card:hover .stat-star { color: #fff !important; }
=======
/* Only expand on card hover */
.stat-card:hover .stat-bar-fill { width: 100%; }
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    </style>

    {{-- Section Header --}}
    <div class="stats-section relative z-10 mb-8 text-center">
        <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-1.5 text-xs font-bold text-blue-700 uppercase tracking-widest shadow-sm mb-3">
            <i class="fa-solid fa-handshake text-blue-600"></i>
            Trusted by our customers
        </div>
        <div class="text-3xl font-black text-slate-900 tracking-tight">
            Proven
            <span style="background:linear-gradient(90deg,#1d4ed8,#3b82f6,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                Success
            </span>
            in Numbers
        </d>
        <p class="mt-2 text-sm text-slate-500 mb-4">Every number reflects a real customer, real delivery, real trust.</p>
    </div>

    {{-- Cards --}}
    <div class="mb-4  stats-section relative z-10 grid grid-cols-1 gap-5 sm:grid-cols-3">

        {{-- ── Card 1: Services ── --}}
<<<<<<< HEAD
        <div class="stat-card reveal delay-1 rounded-2xl bg-white border border-slate-100 shadow-md px-5 py-5 text-center cursor-default group">
=======
        <div class="p-4 stat-card reveal delay-1 rounded-2xl bg-white border border-slate-100 shadow-md p-7 text-center cursor-default group">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
            <div class="absolute -top-8 -left-8 h-32 w-32 rounded-full bg-blue-500 opacity-0 group-hover:opacity-10 blur-2xl transition-opacity duration-500"></div>
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-700 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <div class="relative z-10 flex flex-col items-center">
<<<<<<< HEAD
                <div class="stat-icon-wrap inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 group-hover:bg-white/20 transition-colors duration-300 mb-3 text-blue-600 group-hover:text-white">
=======
                <div class="stat-icon-wrap inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 group-hover:bg-white/20 transition-colors duration-300 mb-4 text-blue-600 group-hover:text-white">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                    <i class="fa-solid fa-box-open fa-lg"></i>
                </div>

                <div class="flex items-start justify-center leading-none mb-1">
<<<<<<< HEAD
                    <span class="stat-num stat-num-glow stat-num-blue text-4xl font-black transition-colors duration-300" data-target="32">0</span>
                    <span class="stat-plus-blue text-xl font-black mt-1 ml-0.5 transition-colors duration-300">+</span>
=======
                    <span class="stat-num stat-num-glow text-5xl font-black group-hover:text-white" data-target="32" style="color:#1d4ed8;">0</span>
                    <span class="text-2xl font-black mt-1 ml-0.5 group-hover:text-white transition-colors duration-300" style="color:#93c5fd;">+</span>
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                </div>

                <p class="text-sm font-bold text-slate-800 group-hover:text-white transition-colors duration-300">Services Offered</p>
                <p class="mt-1 text-xs text-slate-400 group-hover:text-white/70 transition-colors duration-300">Medicines, cosmetics & more</p>

                <div class="stat-bar-track">
                    <div class="stat-bar-fill blue"></div>
                </div>
            </div>
        </div>

        {{-- ── Card 2: Rating ── --}}
<<<<<<< HEAD
        <div class="stat-card reveal delay-2 rounded-2xl bg-white border border-slate-100 shadow-md px-5 py-5 text-center cursor-default group">
=======
        <div class="p-4 stat-card reveal delay-2 rounded-2xl bg-white border border-slate-100 shadow-md p-7 text-center cursor-default group">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
            <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-amber-400 opacity-0 group-hover:opacity-10 blur-2xl transition-opacity duration-500"></div>
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <div class="relative z-10 flex flex-col items-center">
<<<<<<< HEAD
                <div class="stat-icon-wrap inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 group-hover:bg-white/20 transition-colors duration-300 mb-3 text-amber-500 group-hover:text-white">
=======
                <div class="stat-icon-wrap inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 group-hover:bg-white/20 transition-colors duration-300 mb-4 text-amber-500 group-hover:text-white">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                    <i class="fa-solid fa-star fa-lg"></i>
                </div>

                <div class="flex items-baseline justify-center gap-0.5 mb-1">
<<<<<<< HEAD
                    <span class="stat-num-glow stat-num-amber text-4xl font-black transition-colors duration-300">5.0</span>
                    <span class="stat-plus-amber text-lg font-bold transition-colors duration-300">/5</span>
=======
                    <span class="stat-num-glow text-5xl font-black group-hover:text-white transition-colors duration-300" style="color:#d97706;">5.0</span>
                    <span class="text-xl font-bold group-hover:text-white/80 transition-colors duration-300" style="color:#fbbf24;">/5</span>
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                </div>

                <div class="flex justify-center gap-1 my-1.5 stars-row">
                    @for($i = 0; $i < 5; $i++)
<<<<<<< HEAD
                        <span class="star-animate stat-star text-xl transition-colors duration-300"
                              style="animation-delay:{{ $i * 0.08 }}s;">★</span>
=======
                        <span class="star-animate text-xl group-hover:text-white transition-colors duration-300"
                              style="color:#f59e0b; animation-delay:{{ $i * 0.08 }}s;">★</span>
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                    @endfor
                </div>

                <p class="text-sm font-bold text-slate-800 group-hover:text-white transition-colors duration-300">Average Rating</p>
                <p class="mt-1 text-xs text-slate-400 group-hover:text-white/70 transition-colors duration-300">Based on 35 Google Reviews</p>

                <div class="stat-bar-track">
                    <div class="stat-bar-fill amber"></div>
                </div>
            </div>
        </div>

        {{-- ── Card 3: Happy Customers ── --}}
<<<<<<< HEAD
        <div class="stat-card reveal delay-3 rounded-2xl bg-white border border-slate-100 shadow-md px-5 py-5 text-center cursor-default group">
=======
        <div class="p-4 stat-card reveal delay-3 rounded-2xl bg-white border border-slate-100 shadow-md p-7 text-center cursor-default group">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
            <div class="absolute -bottom-8 -right-8 h-32 w-32 rounded-full bg-green-500 opacity-0 group-hover:opacity-10 blur-2xl transition-opacity duration-500"></div>
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-700 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <div class="relative z-10 flex flex-col items-center">
<<<<<<< HEAD
                <div class="stat-icon-wrap inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-green-50 group-hover:bg-white/20 transition-colors duration-300 mb-3 text-green-600 group-hover:text-white">
=======
                <div class="stat-icon-wrap inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 group-hover:bg-white/20 transition-colors duration-300 mb-4 text-green-600 group-hover:text-white">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                    <i class="fa-solid fa-users fa-lg"></i>
                </div>

                <div class="flex items-start justify-center leading-none mb-1">
<<<<<<< HEAD
                    <span class="stat-num stat-num-glow stat-num-green text-4xl font-black transition-colors duration-300" data-target="300">0</span>
                    <span class="stat-plus-green text-xl font-black mt-1 ml-0.5 transition-colors duration-300">+</span>
=======
                    <span class="stat-num stat-num-glow text-5xl font-black group-hover:text-white" data-target="300" style="color:#15803d;">0</span>
                    <span class="text-2xl font-black mt-1 ml-0.5 group-hover:text-white transition-colors duration-300" style="color:#86efac;">+</span>
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                </div>

                <p class="text-sm font-bold text-slate-800 group-hover:text-white transition-colors duration-300">Happy Customers</p>
                <p class="mt-1 text-xs text-slate-400 group-hover:text-white/70 transition-colors duration-300">And growing every day</p>

                <div class="stat-bar-track">
                    <div class="stat-bar-fill green"></div>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
(function () {
    function easeOutExpo(t) { return t === 1 ? 1 : 1 - Math.pow(2, -10 * t); }

    function animateCounter(el) {
        const target = +el.dataset.target;
        const duration = 2000;
        const start = performance.now();
        function tick(now) {
            const elapsed = Math.min((now - start) / duration, 1);
            el.textContent = Math.floor(easeOutExpo(elapsed) * target);
            if (elapsed < 1) requestAnimationFrame(tick);
            else el.textContent = target;
        }
        requestAnimationFrame(tick);
    }

    function popStars(card) {
        card.querySelectorAll('.star-animate').forEach((star, i) => {
            setTimeout(() => star.classList.add('popped'), i * 100 + 200);
        });
    }

    const observed = new Set();
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !observed.has(entry.target)) {
                observed.add(entry.target);
                entry.target.querySelectorAll('.stat-num[data-target]').forEach(animateCounter);
                popStars(entry.target);
            }
        });
    }, { threshold: 0.4 });

    document.querySelectorAll('.stat-card').forEach(el => observer.observe(el));
})();
</script>

{{-- ============================================================
     CUSTOMER REVIEWS
     ============================================================ --}}
<section class="mb-12 reveal">

    {{-- Header --}}
    <div class="mb-8 text-center">
        <span class="inline-block mb-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 uppercase tracking-widest">Reviews</span>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">What Our Customers Say</h2>
        <p class="mt-1.5 text-sm text-slate-500">Real reviews from real customers in Ahmedabad</p>
        {{-- Star summary --}}
        <div class="mt-3 inline-flex items-center gap-2">
            <div class="flex text-amber-400 text-lg">★★★★★</div>
            <span class="text-sm font-bold text-slate-700">5.0</span>
            <span class="text-xs text-slate-400">· 35 Google Reviews</span>
        </div>
    </div>

    {{-- Marquee wrapper --}}
    <div class="relative overflow-hidden">

        {{-- Left fade --}}
        <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-16 z-10"
             style="background: linear-gradient(to right, #f0f7ff, transparent);"></div>
        {{-- Right fade --}}
        <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-16 z-10"
             style="background: linear-gradient(to left, #f0f7ff, transparent);"></div>

        {{-- Track --}}
        <div class="review-track flex gap-4" style="width: max-content;">

            @php
$reviews = [
    ['name'=>'Sabir Shaikh',        'initial'=>'S', 'color'=>'#2563eb', 'stars'=>5, 'text'=>'You will get all the medicines here and the service here is very good and the nature of the owner, Sarfaraz Desai, is even better than that.'],
    ['name'=>'Muttalib Khokhar',    'initial'=>'M', 'color'=>'#4f46e5', 'stars'=>5, 'text'=>'Good service & free and fast delivery is superb. Really best stock medical in Vejalpur area. Specially thanks to Mr. Sarfaraz Desai.'],
    ['name'=>'A K Shaikh Babubhai', 'initial'=>'A', 'color'=>'#059669', 'stars'=>5, 'text'=>'100% recommended. Really good service on monthly medicines order with discounted price. Best medical near Juhapura, Vejalpur, Ahmedabad.'],
    ['name'=>'Tabreaz 786',         'initial'=>'T', 'color'=>'#e11d48', 'stars'=>5, 'text'=>'Good service provider near me. Really fast & free delivery. Good maintained stock.'],
    ['name'=>'Desai Sokat',         'initial'=>'D', 'color'=>'#7c3aed', 'stars'=>5, 'text'=>'Really good service provider in Vejalpur & Juhapura area. Very fast & free service. 100% answers calls & replies very fast.'],
    ['name'=>'SAHIL Rathod',        'initial'=>'S', 'color'=>'#0284c7', 'stars'=>5, 'text'=>'They have good medicine & cosmetics item stock. Really best medical in Vejalpur and Juhapura area.'],
    ['name'=>'Matin Shaikh',        'initial'=>'M', 'color'=>'#1d4ed8', 'stars'=>5, 'text'=>'Super and good medical. Good price. Thank you Medikart and Desai bhai!'],
    ['name'=>'Nazima Kamal',        'initial'=>'N', 'color'=>'#db2777', 'stars'=>5, 'text'=>'Very nice door service with a reasonable price.'],
    ['name'=>'Sadiya Rathod',       'initial'=>'S', 'color'=>'#d97706', 'stars'=>5, 'text'=>'Best medical store near me in Vejalpur, Jivraj & Juhapura area. Well maintained stock, best discount, and free & fast delivery.'],
    ['name'=>'Avinash Kumar',       'initial'=>'A', 'color'=>'#16a34a', 'stars'=>5, 'text'=>'Great for faster delivery and great service 👍'],
    ['name'=>'Sikandar Desai',      'initial'=>'S', 'color'=>'#0d9488', 'stars'=>5, 'text'=>'Best medical store for monthly medicines. Best discount. Well maintained stock for cancer, diabetic, kidney, heart medicines. Special thanks to Mr. Sarfaraz Desai.'],
    ['name'=>'Aakash Parikh',       'initial'=>'A', 'color'=>'#7c3aed', 'stars'=>5, 'text'=>'Medikart pharmacy delivered urgent medicines we needed late at night and delivery was also fast.'],
];
@endphp

            {{-- Render twice for infinite loop --}}
            @foreach([1,2] as $pass)
                @foreach($reviews as $review)
                <div class="review-card flex-shrink-0 rounded-2xl bg-white border border-slate-100 shadow-sm p-5 flex flex-col gap-3" style="width: 300px; min-width: 300px; max-width: 300px;">

                    {{-- Top: avatar + name + stars --}}
                    <div class="flex items-center gap-3">
                       <div class="h-10 w-10 rounded-full flex items-center justify-center text-white text-sm font-black flex-shrink-0 shadow-sm"
     style="background-color: {{ $review['color'] }};">
    {{ $review['initial'] }}
</div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate">{{ $review['name'] }}</p>
                            <div class="flex text-amber-400 text-xs mt-0.5">
                                @for($s = 0; $s < $review['stars']; $s++) ★ @endfor
                            </div>
                        </div>
                        {{-- Google icon --}}
                        <svg class="ml-auto h-5 w-5 flex-shrink-0 text-slate-300" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                    </div>

                    {{-- Review text --}}
                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-4">
                        "{{ $review['text'] }}"
                    </p>

                    {{-- Verified badge --}}
                    <div class="mt-auto flex items-center gap-1.5 text-[10px] font-semibold text-blue-600">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Verified Google Review
                    </div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
</section>

<style>
.review-track {
    animation: reviewScroll 40s linear infinite;
}
.review-track:hover {
    animation-play-state: paused;
}
@keyframes reviewScroll {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
</style>

{{-- ============================================================
     PROMO BANNER
     ============================================================ --}}
<section class="reveal relative overflow-hidden rounded-3xl shadow-xl promo-shine"
         style="background:linear-gradient(135deg,#1e40af 0%,#2563eb 50%,#0891b2 100%);">

    {{-- Delivery boy --}}
    <div class="delboy pointer-events-none absolute bottom-0 right-6 hidden sm:block">
        <img src="{{ asset('Images/MedicalDelhiveryboy.png') }}"
             alt="Fast delivery"
             class="h-40 w-auto object-contain object-bottom drop-shadow-2xl"
             loading="lazy" draggable="false">
    </div>

    {{-- Glowing orb --}}
    <div class="pointer-events-none absolute -top-10 -left-10 h-48 w-48 rounded-full opacity-15"
         style="background:radial-gradient(circle,#fff,transparent 70%);"></div>

    <div class="relative p-8 sm:p-10 sm:pr-52">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-bold text-blue-100 mb-3">
            <span class="h-2.5 w-2.5 rounded-full animate-pulse flex-shrink-0" style="background:#ef4444;"></span>
            Limited Time Offer
        </div>
        <h3 class="text-2xl font-black text-white sm:text-3xl leading-tight">
            Free Delivery<br>on Your First Order!
        </h3>
        <p class="mt-2 text-sm text-blue-100/90">
            Enter pincode <strong class="text-white">380028</strong>to check availability.
        </p>
        <a href="{{ route('medicines.index') }}"
           class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-white px-7 py-3 text-sm font-black text-blue-800 shadow-lg hover:bg-blue-50 transition-all hover:scale-105 active:scale-95">
            Shop Now
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</section>




{{-- ============================================================
     SCROLL-REVEAL + SECTION HEADING ANIMATION JS
     ============================================================ --}}
@push('scripts')
<script>
(function () {
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) e.target.classList.add('visible');
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .section-heading')
            .forEach(el => io.observe(el));
})();
</script>
@endpush

@endsection
