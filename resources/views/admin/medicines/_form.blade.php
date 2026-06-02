{{-- Shared form partial for create & edit --}}
@php
    $existingImages = isset($medicine)
        ? array_values(array_filter(array_merge(
            [$medicine->image_url ?? ''],
            $medicine->extra_images ?? []
          )))
        : [];
    $initialImages = json_encode($existingImages ?: ['']);
@endphp

<div class="grid gap-6 lg:grid-cols-3">

    {{-- ===== LEFT: main fields ===== --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Basic info --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-5">Basic Information</h3>
            <div class="grid gap-4 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Medicine Name *</label>
                    <input name="name" value="{{ old('name', $medicine->name ?? '') }}" required
                           placeholder="e.g. Dolo 650 Tablet"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Manufacturer *</label>
                    <input name="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer ?? '') }}" required
                           placeholder="e.g. Micro Labs"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    @error('manufacturer')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Category: select existing OR create new --}}
                <div x-data="{ creating: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Category *</label>
                        <button type="button" @click="creating = !creating"
                                class="text-xs font-semibold text-blue-700 hover:underline"
                                x-text="creating ? '← Pick existing' : '+ Create new'"></button>
                    </div>
                    <div x-show="!creating">
                        <select name="category_id"
<<<<<<< HEAD
                                :required="!creating"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                            <option value="">— Select a category —</option>
=======
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                            <option value="">Select category…</option>
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $medicine->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
<<<<<<< HEAD
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
=======
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                    </div>
                    <div x-show="creating" x-cloak>
                        <input name="new_category_name"
                               placeholder="New category name…"
                               class="w-full rounded-xl border border-blue-300 bg-blue-50 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        <p class="mt-1 text-xs text-blue-700">Category will be created automatically on save.</p>
                    </div>
<<<<<<< HEAD
=======
                    @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Description</label>
                    <textarea name="description" rows="3"
                              placeholder="Brief description of the medicine…"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 resize-none">{{ old('description', $medicine->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Pricing & Stock --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-5">Pricing & Stock</h3>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">MRP (₹) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">₹</span>
                        <input name="mrp" type="number" step="0.01" min="0.01"
                               value="{{ old('mrp', isset($medicine) ? number_format($medicine->mrp_paise/100,2,'.','') : '') }}"
                               required placeholder="0.00"
                               class="w-full rounded-xl border border-slate-200 pl-7 pr-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    </div>
                    @error('mrp')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Selling Price (₹) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">₹</span>
                        <input name="price" type="number" step="0.01" min="0.01"
                               value="{{ old('price', isset($medicine) ? number_format($medicine->price_paise/100,2,'.','') : '') }}"
                               required placeholder="0.00"
                               class="w-full rounded-xl border border-slate-200 pl-7 pr-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    </div>
                    @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Stock *</label>
                    <input name="stock" type="number" min="0"
                           value="{{ old('stock', $medicine->stock ?? 100) }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    @error('stock')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ===== PRODUCT IMAGES ===== --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
             x-data="{
                images: {{ $initialImages }},
                addSlot() { if (this.images.length < 8) this.images.push(''); },
                removeSlot(idx) { if (this.images.length > 1) this.images.splice(idx, 1); }
             }">

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Product Images</h3>
                    <p class="text-xs text-slate-500 mt-0.5">First image is the primary. Add up to 8 images.</p>
                </div>
                <button type="button" @click="addSlot()"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-blue-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-800 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Image
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(img, idx) in images" :key="idx">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                        {{-- Slot label --}}
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold uppercase tracking-wider"
                                  :class="idx === 0 ? 'text-blue-700' : 'text-slate-400'"
                                  x-text="idx === 0 ? '★ Primary Image' : 'Extra Image ' + idx"></span>
                            <button type="button" @click="removeSlot(idx)"
                                    x-show="images.length > 1"
                                    class="rounded-lg p-1 text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                                    title="Remove slot">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            {{-- Live preview --}}
                            <div class="h-20 w-20 flex-shrink-0 rounded-xl overflow-hidden bg-white border border-slate-200 flex items-center justify-center">
                                <img :src="img" :alt="'Image ' + (idx + 1)"
                                     class="h-full w-full object-contain p-1"
                                     x-show="img !== ''"
                                     onerror="this.style.opacity='.15'">
                                <svg x-show="img === ''" class="h-7 w-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>

                            {{-- Inputs --}}
                            <div class="flex-1 space-y-2">

                                {{-- URL input --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1">
                                        🔗 Image URL
                                    </label>
                                    <input type="url"
                                           :name="idx === 0 ? 'image_url' : 'extra_image_url[]'"
                                           x-model="images[idx]"
                                           placeholder="https://example.com/image.jpg"
                                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                                </div>

                                {{-- Divider --}}
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-px bg-slate-200"></div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">or</span>
                                    <div class="flex-1 h-px bg-slate-200"></div>
                                </div>

                                {{-- File upload --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1">
                                        📁 Upload from Device
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer rounded-lg border-2 border-dashed border-slate-300 bg-white px-3 py-2 hover:border-blue-400 hover:bg-blue-50 transition-colors">
                                        <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        <span class="text-xs text-slate-500 file-label" x-ref="fileLabel">
                                            Click to choose image (JPG, PNG, WEBP — max 4MB)
                                        </span>
                                        <input type="file"
                                               :name="idx === 0 ? 'image_file' : 'extra_image_file[]'"
                                               accept="image/*"
                                               class="sr-only"
                                               @change="
                                                   const f = $event.target.files[0];
                                                   if (f) {
                                                       images[idx] = URL.createObjectURL(f);
                                                       $event.target.closest('label').querySelector('.file-label').textContent = f.name;
                                                   }
                                               ">
                                    </label>
                                    <p class="mt-1 text-[10px] text-slate-400">Uploaded file takes priority over URL if both are provided.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <button type="button" @click="addSlot()"
                    class="mt-4 w-full rounded-xl border-2 border-dashed border-slate-300 py-2.5 text-xs font-semibold text-slate-500 hover:border-blue-400 hover:text-blue-600 transition-colors">
                + Add another image slot
            </button>
        </div>
    </div>

    {{-- ===== RIGHT: options + submit ===== --}}
    <div class="space-y-5">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Options</h3>
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="prescription_required" value="1"
                           {{ old('prescription_required', $medicine->prescription_required ?? false) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-10 h-5 bg-slate-200 rounded-full peer-checked:bg-blue-700 transition-colors"></div>
                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Prescription Required</p>
                    <p class="text-xs text-slate-500">Show Rx badge on product</p>
                </div>
            </label>
        </div>

        

        <div class="flex flex-col sm:flex-row gap-2">
            <button type="submit"
                    class="p-4 ml-1 w-full sm:flex-1 rounded-xl bg-blue-700 py-2.5 text-sm font-bold text-white hover:bg-blue-800 transition-colors shadow-md">
                {{ isset($medicine) ? 'Update Medicine' : 'Create Medicine' }}
            </button>
            <a href="{{ route('admin.medicines.index') }}"
               class="p-4 sm:w-auto rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>
