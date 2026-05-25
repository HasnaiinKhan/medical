<form id="medicine-search-form"
      action="{{ route('medicines.index') }}"
      method="get"
      class="relative z-[120] mb-5 flex gap-2 overflow-visible"
      data-medicine-results-form
      data-medicine-suggest-form>
    @if (request('category'))
        <input type="hidden" name="category" value="{{ request('category') }}">
    @endif
    @php
        $selectedBrands = [];
        foreach ((array) request('brand') as $brandItem) {
            $brandItem = trim((string) $brandItem);
            if ($brandItem !== '') {
                $selectedBrands[] = $brandItem;
            }
        }
    @endphp
    @foreach ($selectedBrands as $brandItem)
        <input type="hidden" name="brand[]" value="{{ $brandItem }}">
    @endforeach
    <div class="flex-1">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="search" name="q" value="{{ $q }}"
               data-medicine-suggest-input
               autocomplete="off"
               aria-autocomplete="list"
               aria-expanded="false"
               placeholder="Search by name, brand, or description..."
               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
    </div>
    <button type="submit"
            class="btn-primary rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-sm">
        Search
    </button>
    @if($q)
        <a href="{{ route('medicines.index', array_filter(['category' => request('category'), 'brand' => request('brand')])) }}"
           class="js-medicine-results-link rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 shadow-sm transition-colors">
            Clear
        </a>
    @endif
    <div data-medicine-suggestions
         class="absolute left-0 right-0 top-full mt-2 hidden w-full min-w-full overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl max-h-96"
         style="z-index:999999;">
    </div>
</form>

<p class="mb-4 text-xs text-slate-500">
    Showing {{ $medicines->firstItem() }}-{{ $medicines->lastItem() }} of {{ $medicines->total() }} results
</p>

@php
$colors = ['from-blue-50 to-blue-100', 'from-blue-50 to-indigo-100', 'from-purple-50 to-violet-100', 'from-amber-50 to-orange-100', 'from-rose-50 to-pink-100', 'from-sky-50 to-cyan-100', 'from-lime-50 to-green-100', 'from-fuchsia-50 to-pink-100'];
$cartItems = app(\App\Services\CartService::class)->items();
@endphp

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @forelse ($medicines as $m)
        @php $color = $colors[$loop->index % count($colors)]; @endphp
        <article data-product-url="{{ route('medicines.show', $m) }}" data-product-id="{{ $m->id }}" class="medicine-card cursor-pointer flex flex-col rounded-2xl bg-white shadow-sm overflow-hidden transition-shadow hover:shadow-md">
            <div class="relative h-24 overflow-hidden bg-gradient-to-br {{ $color }}">
                <img src="{{ $m->imageUrl() }}"
                     alt="{{ $m->name }}"
                     class="h-full w-full object-contain object-center p-2 transition-transform duration-300 hover:scale-105"
                     loading="lazy"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="absolute inset-0 hidden items-center justify-center">
                    <span class="text-4xl font-black text-slate-300/60 select-none">{{ strtoupper(substr($m->name, 0, 1)) }}</span>
                </div>
                @if ($m->prescription_required)
                    <span class="absolute top-2 left-2 rounded-md bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">Rx</span>
                @endif
                @if ($m->mrp_paise > $m->price_paise)
                    <span class="absolute top-2 right-2 rounded-md bg-blue-600 px-2 py-0.5 text-xs font-bold text-white">
                        {{ $m->discountPercent() }}% OFF
                    </span>
                @endif
            </div>

            <div class="flex flex-1 flex-col p-4">
                <p class="mb-0.5 text-xs font-medium text-blue-700">{{ $m->category->name }}</p>
                <h2 class="line-clamp-2 text-sm font-semibold leading-snug text-slate-900">{{ $m->name }}</h2>
                <p class="mt-0.5 text-xs text-slate-500">{{ $m->manufacturer }}</p>

                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-base font-bold text-slate-900">&#8377;{{ number_format($m->priceRupees(), 2) }}</span>
                    @if ($m->mrp_paise > $m->price_paise)
                        <span class="text-xs text-slate-400 line-through">&#8377;{{ number_format($m->mrpRupees(), 2) }}</span>
                        <span class="text-xs font-semibold text-blue-700">Save &#8377;{{ number_format($m->mrpRupees() - $m->priceRupees(), 2) }}</span>
                    @endif
                </div>

                <div class="mt-auto pt-3">
                    <form method="post" action="{{ route('cart.add') }}" class="js-add-to-cart-form w-full {{ isset($cartItems[$m->id]) && $cartItems[$m->id] > 0 ? 'hidden' : '' }}">
                        @csrf
                        <input type="hidden" name="medicine_id" value="{{ $m->id }}">
                        <button type="submit"
                                class="btn-primary w-full rounded-2xl py-3 text-sm font-black text-white shadow-sm">
                            Add to Cart
                        </button>
                    </form>

                    <form method="post" action="{{ route('cart.update', $m) }}"
                          class="js-cart-update-form mt-3 flex w-full items-center justify-between overflow-hidden rounded-2xl border border-slate-200 {{ isset($cartItems[$m->id]) && $cartItems[$m->id] > 0 ? '' : 'hidden' }}"
                          data-cart-medicine-id="{{ $m->id }}">
                        @csrf
                        @method('PATCH')
                        <button type="button"
                                class="js-card-qty-minus w-14 bg-slate-50 text-lg font-bold leading-none text-slate-700 transition-colors hover:bg-slate-100"
                                aria-label="Decrease quantity">-</button>
                        <input type="number" name="quantity" value="{{ $cartItems[$m->id] ?? 1 }}" min="0" max="99" readonly
                               class="flex-1 border-x border-slate-200 bg-white py-3 text-center text-sm font-semibold focus:outline-none" />
                        <button type="button"
                                class="js-card-qty-plus w-14 bg-slate-50 text-lg font-bold leading-none text-slate-700 transition-colors hover:bg-slate-100"
                                aria-label="Increase quantity">+</button>
                    </form>
                </div>
            </div>
        </article>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white py-16 text-center">
            <div class="notfound" style="width: 60px; height: auto; margin-bottom: 1rem;">
                <img src="{{ asset('images/sad.png') }}" alt="Application Logo">
            </div>
            <p class="text-lg font-semibold text-slate-700">No medicines found</p>
            <p class="mt-1 text-sm text-slate-500">Try a different search term or browse all categories.</p>
            <a href="{{ route('medicines.index') }}" class="js-medicine-results-link mt-4 rounded-xl bg-blue-700 px-5 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-800">
                Browse All
            </a>
        </div>
    @endforelse
</div>

@if ($medicines->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $medicines->links() }}
    </div>
@endif
