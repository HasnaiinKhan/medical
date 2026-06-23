@extends('layouts.shop')

@section('title', $medicine->name)

@section('content')

@php
    $cartItems   = app(\App\Services\CartService::class)->items();
    $cartQty     = $cartItems[$medicine->id] ?? 0;
    $allImages   = $medicine->allImages();
@endphp

{{-- Breadcrumb --}}
<nav class="mb-5 flex items-center gap-2 text-xs text-slate-500">
    <a href="{{ route('home') }}" class="hover:text-blue-700 transition-colors">Home</a>
    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('medicines.index') }}" class="hover:text-blue-700 transition-colors">Medicines</a>
    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('medicines.index', ['category' => $medicine->category->slug]) }}" class="hover:text-blue-700 transition-colors">{{ $medicine->category->name }}</a>
    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-slate-700 font-medium truncate max-w-xs">{{ $medicine->name }}</span>
</nav>

<div class="grid gap-8 lg:grid-cols-2">

    {{-- ===== IMAGE PANEL ===== --}}
    <div class="space-y-3" x-data="{ active: 0 }">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50 via-blue-50 to-cyan-100 shadow-inner" style="height: 320px;">
            <template x-for="(src, i) in {{ json_encode($allImages) }}" :key="i">
                <img :src="src" :alt="'{{ addslashes($medicine->name) }}'"
                     class="absolute inset-0 h-full w-full object-contain object-center p-4 transition-opacity duration-300"
                     :class="active === i ? 'opacity-100' : 'opacity-0'"
                     loading="eager"
                     onerror="this.style.display='none'">
            </template>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none" style="z-index:-1">
                <span class="text-9xl font-black text-blue-800/10 select-none">{{ strtoupper(substr($medicine->name, 0, 1)) }}</span>
            </div>
            @if($medicine->prescription_required)
                <div class="absolute top-4 left-4 flex items-center gap-1.5 rounded-xl bg-amber-100 px-3 py-1.5 text-sm font-semibold text-amber-800 ring-1 ring-amber-200 shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Prescription Required
                </div>
            @endif
            @if($medicine->mrp_paise > $medicine->price_paise)
                <div class="absolute top-4 right-4 flex h-14 w-14 flex-col items-center justify-center rounded-full bg-blue-600 text-white shadow-lg">
                    <span class="text-xs font-bold leading-none">{{ $medicine->discountPercent() }}%</span>
                    <span class="text-xs font-semibold leading-none">OFF</span>
                </div>
            @endif
        </div>

        @if(count($allImages) > 1)
            <div class="flex gap-2 overflow-x-auto pb-1">
                @foreach($allImages as $i => $src)
                    <button type="button"
                            @click="active = {{ $i }}"
                            :class="active === {{ $i }} ? 'ring-2 ring-blue-600 border-blue-400' : 'border-slate-200 hover:border-blue-300'"
                            class="flex-shrink-0 h-16 w-16 rounded-xl border-2 overflow-hidden bg-white transition-all">
                        <img src="{{ $src }}" alt="Thumbnail {{ $i + 1 }}"
                             class="h-full w-full object-contain p-1"
                             onerror="this.parentElement.style.display='none'">
                    </button>
                @endforeach
            </div>
        @endif

        <div class="flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3">
            <span class="h-2 w-2 rounded-full {{ $medicine->stock > 0 ? 'bg-blue-600 animate-pulse' : 'bg-red-500' }}"></span>
            <span class="text-sm font-medium text-slate-800">
                {{ $medicine->stock > 0 ? 'In Stock - ' . $medicine->stock . ' units available' : 'Out of Stock' }}
            </span>
        </div>
    </div>

    {{-- ===== PRODUCT INFO ===== --}}
    <div class="flex flex-col">
        <div class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-slate-700 w-fit mb-2">
            <a href="{{ route('medicines.index', ['category' => $medicine->category->slug]) }}" class="hover:underline">
                {{ $medicine->category->name }}
            </a>
        </div>

        <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl leading-tight">{{ $medicine->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">by <span class="font-medium text-slate-700">{{ $medicine->manufacturer }}</span></p>

        {{-- Price block --}}
        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="flex items-baseline gap-3">
                <span class="text-3xl font-extrabold text-slate-900">₹{{ number_format($medicine->priceRupees(), 2) }}</span>
                @if($medicine->mrp_paise > $medicine->price_paise)
                    <span class="text-lg text-slate-400 line-through">₹{{ number_format($medicine->mrpRupees(), 2) }}</span>
                    <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-sm font-bold text-slate-800">{{ $medicine->discountPercent() }}% OFF</span>
                @endif
            </div>
            @if($medicine->mrp_paise > $medicine->price_paise)
                <p class="mt-1 text-sm text-slate-700 font-medium">
                    You save ₹{{ number_format(($medicine->mrp_paise - $medicine->price_paise) / 100, 2) }} on this item
                </p>
            @endif
            <p class="mt-2 text-xs text-slate-500">Inclusive of all taxes. MRP ₹{{ number_format($medicine->mrpRupees(), 2) }}</p>
        </div>

        {{-- Description --}}
        <div class="mt-5">
            <h3 class="text-sm font-semibold text-slate-900 mb-2">About this medicine</h3>
            <p class="text-sm leading-relaxed text-slate-600">{{ $medicine->description }}</p>
        </div>

        {{-- Key info --}}
        <div class="mt-5 grid grid-cols-2 gap-3">
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <p class="text-xs text-slate-500 mb-0.5">Manufacturer</p>
                <p class="text-sm font-semibold text-slate-800">{{ $medicine->manufacturer }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <p class="text-xs text-slate-500 mb-0.5">Category</p>
                <p class="text-sm font-semibold text-slate-800">{{ $medicine->category->name }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <p class="text-xs text-slate-500 mb-0.5">Prescription</p>
                <p class="text-sm font-semibold {{ $medicine->prescription_required ? 'text-amber-700' : 'text-slate-800' }}">
                    {{ $medicine->prescription_required ? 'Required' : 'Not Required' }}
                </p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <p class="text-xs text-slate-500 mb-1">Payment Options</p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-blue-200">
                        <img src="{{ asset('Images/credit-card.png') }}" class="w-4 h-4" alt=""> Online
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">
                        <img src="{{ asset('Images/dollars.png') }}" class="w-4 h-4" alt=""> COD
                    </span>
                </div>
            </div>
        </div>

        {{-- Add to cart --}}
        <form method="post" action="{{ route('cart.add') }}"
              class="js-add-to-cart-form w-full mt-6 {{ $cartQty > 0 ? 'hidden' : '' }}"
              data-product-id="{{ $medicine->id }}">
            @csrf
            <input type="hidden" name="medicine_id" value="{{ $medicine->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
                    class="btn-primary w-full rounded-2xl py-4 text-sm font-black text-white shadow-sm {{ $medicine->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ $medicine->stock <= 0 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-cart-arrow-down fa-l" style="color: rgb(266, 266, 266);"></i>
                {{ $medicine->stock <= 0 ? 'Out of Stock' : ' Add to Cart' }}
            </button>
        </form>

        <form method="post" action="{{ route('cart.update', $medicine) }}"
              class="js-cart-update-form w-full mt-6 {{ $cartQty > 0 ? '' : 'hidden' }}"
              data-cart-medicine-id="{{ $medicine->id }}"
              data-product-id="{{ $medicine->id }}"
              data-stock="{{ $medicine->stock }}">
            @csrf
            @method('PATCH')
            <div class="flex w-full items-center justify-between rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <button type="button"
                        class="js-card-qty-minus w-14 py-4 bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors font-bold text-xl leading-none"
                        aria-label="Decrease quantity">−</button>
                <input type="number" name="quantity"
                       value="{{ $cartQty > 0 ? $cartQty : 1 }}"
                       min="0" max="99" readonly
                       class="flex-1 border-x border-slate-200 bg-white py-4 text-center text-base font-bold focus:outline-none"/>
                <button type="button"
                        class="js-card-qty-plus w-14 py-4 bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors font-bold text-xl leading-none"
                        aria-label="Increase quantity">+</button>
            </div>
        </form>

        <a href="{{ route('medicines.index') }}"
           class="mt-3 inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            ← Back to Medicines
        </a>

        <div class="mt-4 flex items-center gap-2 rounded-xl bg-blue-50 border border-blue-100 px-4 py-3">
            <img src="{{ asset('Images/free-delivery.png') }}" class="w-9 h-9 flex-shrink-0" alt="">
            <p class="text-xs text-slate-700">
                <strong>Free delivery</strong> on orders above ₹500. Enter your pincode to check availability.
            </p>
        </div>
    </div>
</div>

{{-- Related --}}
@if($related->isNotEmpty())
    <section class="mt-12 border-t border-slate-200 pt-10">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">You May Also Like</h2>
            <a href="{{ route('medicines.index', ['category' => $medicine->category->slug]) }}"
               class="text-sm font-medium text-blue-700 hover:underline">
                View all in {{ $medicine->category->name }} →
            </a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php $colors = ['from-blue-50 to-blue-100','from-blue-50 to-indigo-100','from-purple-50 to-violet-100','from-amber-50 to-orange-100']; @endphp
            @foreach($related as $r)
                <article data-product-url="{{ route('medicines.show', $r) }}"
                         data-product-id="{{ $r->id }}"
                         class="medicine-card cursor-pointer flex flex-col rounded-2xl bg-white shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="relative h-28 overflow-hidden bg-gradient-to-br {{ $colors[$loop->index % 4] }}">
                        <img src="{{ $r->imageUrl() }}" alt="{{ $r->name }}"
                             class="h-full w-full object-contain object-center p-2 hover:scale-105 transition-transform duration-300"
                             loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="absolute inset-0 hidden items-center justify-center">
                            <span class="text-3xl font-black text-slate-300/60">{{ strtoupper(substr($r->name,0,1)) }}</span>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col p-3">
                        <h3 class="text-sm font-semibold text-slate-900 line-clamp-2 leading-snug">{{ $r->name }}</h3>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $r->manufacturer }}</p>
                        <div class="mt-1.5 flex items-baseline gap-1.5">
                            <span class="text-sm font-bold text-slate-900">₹{{ number_format($r->priceRupees(), 2) }}</span>
                            @if($r->mrp_paise > $r->price_paise)
                                <span class="text-xs text-slate-400 line-through">₹{{ number_format($r->mrpRupees(), 2) }}</span>
                            @endif
                        </div>
                        <div class="mt-auto pt-3">
                            <form method="post" action="{{ route('cart.add') }}"
                                  class="js-add-to-cart-form w-full {{ isset($cartItems[$r->id]) && $cartItems[$r->id] > 0 ? 'hidden' : '' }}">
                                @csrf
                                <input type="hidden" name="medicine_id" value="{{ $r->id }}">
                                <button type="submit" class="btn-primary w-full rounded-2xl py-3 text-sm font-black text-white">Add to Cart</button>
                            </form>
                            <form method="post" action="{{ route('cart.update', $r) }}"
                                  class="js-cart-update-form w-full {{ isset($cartItems[$r->id]) && $cartItems[$r->id] > 0 ? '' : 'hidden' }}"
                                  data-cart-medicine-id="{{ $r->id }}" data-stock="{{ $r->stock }}">
                                @csrf @method('PATCH')
                                <div class="flex items-center justify-between rounded-2xl border border-slate-200 overflow-hidden">
                                    <button type="button" class="js-card-qty-minus w-14 bg-slate-50 text-slate-700 hover:bg-slate-100 font-bold text-lg py-3">−</button>
                                    <input type="number" name="quantity" value="{{ $cartItems[$r->id] ?? 1 }}" min="0" max="99" readonly
                                           class="flex-1 border-x border-slate-200 bg-white py-3 text-center text-sm font-semibold focus:outline-none"/>
                                    <button type="button" class="js-card-qty-plus w-14 bg-slate-50 text-slate-700 hover:bg-slate-100 font-bold text-lg py-3">+</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif

@endsection
