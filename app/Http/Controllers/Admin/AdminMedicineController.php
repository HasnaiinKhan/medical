<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class AdminMedicineController extends Controller
{
    public function index(Request $request): View
    {
        $q            = trim((string) $request->get('q', ''));
        $categorySlug = $request->get('category');

        $medicines = Medicine::with('category')
            ->when($q, fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('manufacturer', 'like', "%{$q}%");
            }))
            ->when($categorySlug, fn ($query) =>
                $query->whereHas('category', fn ($c) => $c->where('slug', $categorySlug))
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.medicines.index', compact('medicines', 'categories', 'q'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.medicines.create', compact('categories'));
    }

   public function store(Request $request): RedirectResponse
{
    // Prevent duplicate medicine names (case-insensitive)
    $exists = Medicine::whereRaw('LOWER(name) = ?', [
        strtolower(trim($request->name))
    ])->exists();

    if ($exists) {
        return back()
            ->withErrors([
                'name' => 'This medicine already exists.'
            ])
            ->withInput();
    }

    $data = $this->validated($request);
    $categoryId = $this->resolveCategory($request);

    Medicine::create([
        'category_id'           => $categoryId,
        'name'                  => $data['name'],
        'slug'                  => Str::slug($data['name']),
        'manufacturer'          => $data['manufacturer'],
        'description'           => $data['description'] ?? '',
        'mrp_paise'             => (int) round($data['mrp'] * 100),
        'price_paise'           => (int) round($data['price'] * 100),
        'prescription_required' => (bool) ($data['prescription_required'] ?? false),
        'stock'                 => (int) $data['stock'],
        'image_url'             => $this->resolvePrimaryImage($request),
        'extra_images'          => $this->parseExtraImages($request),
    ]);

    return redirect()->route('admin.medicines.index')
        ->with('status', "Medicine '{$data['name']}' created.");
}

    public function edit(Medicine $medicine): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.medicines.edit', compact('medicine', 'categories'));
    }

    public function update(Request $request, Medicine $medicine): RedirectResponse
    {

        $exists = Medicine::whereRaw('LOWER(name) = ?', [
    strtolower(trim($request->name))
])
->where('id', '!=', $medicine->id)
->exists();

if ($exists) {
    return back()
        ->withErrors([
            'name' => 'This medicine already exists.'
        ])
        ->withInput();
}
        $data = $this->validated($request, $medicine->id);
        $categoryId = $this->resolveCategory($request);

        $medicine->update([
            'category_id'           => $categoryId,
            'name'                  => $data['name'],
            'slug'                  => Str::slug($data['name']),
            'manufacturer'          => $data['manufacturer'],
            'description'           => $data['description'] ?? '',
            'mrp_paise'             => (int) round($data['mrp'] * 100),
            'price_paise'           => (int) round($data['price'] * 100),
            'prescription_required' => (bool) ($data['prescription_required'] ?? false),
            'stock'                 => (int) $data['stock'],
            'image_url'             => $this->resolvePrimaryImage($request, $medicine->image_url),
            'extra_images'          => $this->parseExtraImages($request),
        ]);

        return redirect()->route('admin.medicines.index')
            ->with('status', "Medicine '{$medicine->name}' updated.");
    }

    public function destroy(Medicine $medicine): RedirectResponse
    {
        $name = $medicine->name;
        $medicine->delete();

        return redirect()->route('admin.medicines.index')
            ->with('status', "Medicine '{$name}' deleted.");
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Resolve the primary image: uploaded file takes ABSOLUTE priority over URL input.
     * File uploads are always preferred, even if URL is present.
     */
    private function resolvePrimaryImage(Request $request, ?string $existing = null): ?string
    {
        // File upload takes ABSOLUTE priority - if file exists, ignore any URL
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($file && $file->isValid()) {
                $path = $file->store('medicines', 'public');
                $url = asset('storage/' . $path);
                \Log::info("Resolved primary image from file upload: {$url}");
                return $url;
            }
        }

        // Only use URL if NO file was uploaded
        $url = trim((string) $request->input('image_url', ''));
        if ($url !== '' && !str_starts_with($url, 'blob:')) {
            // Validate that it's actually a real URL before using it
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                \Log::info("Resolved primary image from URL: {$url}");
                return $url;
            }
        }

        // Keep existing on update if neither file nor valid URL provided
        if ($existing) {
            \Log::info("Keeping existing primary image: {$existing}");
        }
        return $existing;
    }

    /**
     * Parse extra images: each slot can be a file upload OR a URL.
     * extra_image_file[] and extra_image_url[] are parallel arrays by index.
     * FILE UPLOADS TAKE ABSOLUTE PRIORITY over URLs.
     */
    private function parseExtraImages(Request $request): array
    {
        $files = $request->file('extra_image_file', []);
        $urls  = $request->input('extra_image_url', []);

        // Normalise to same length
        $count  = max(count((array) $files), count((array) $urls));
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $file = $files[$i] ?? null;
            $url  = trim((string) ($urls[$i] ?? ''));

            // File upload takes ABSOLUTE priority - if file exists, ignore URL completely
            if ($file && $file->isValid()) {
                $path = $file->store('medicines', 'public');
                $imageUrl = asset('storage/' . $path);
                $result[] = $imageUrl;
                \Log::info("Resolved extra image {$i} from file upload: {$imageUrl}");
            } 
            // Only use URL if NO file was uploaded
            elseif ($url !== '' && !str_starts_with($url, 'blob:')) {
                // Validate that it's actually a real URL
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $result[] = $url;
                    \Log::info("Resolved extra image {$i} from URL: {$url}");
                }
            }
        }

        return $result;
    }

    /**
     * If admin typed a new category name, create it on the fly.
     */
    private function resolveCategory(Request $request): int
    {
        $newName = trim((string) $request->input('new_category_name', ''));

        if ($newName !== '') {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($newName)],
                ['name' => $newName]
            );
            return $category->id;
        }

        return (int) $request->input('category_id');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        // Category is required unless the admin is creating a new one inline
        $newCategoryName = trim((string) $request->input('new_category_name', ''));
        $categoryIdRule  = $newCategoryName !== ''
            ? ['nullable', 'integer']
            : ['required', 'integer', 'exists:categories,id'];

        return $request->validate([
            'name'                  => ['required', 'string', 'max:200'],
            'manufacturer'          => ['required', 'string', 'max:200'],
            'description'           => ['nullable', 'string'],
            'mrp'                   => ['required', 'numeric', 'min:0.01'],
            'price'                 => ['required', 'numeric', 'min:0.01'],
            'prescription_required' => ['nullable', 'boolean'],
            'stock'                 => ['required', 'integer', 'min:0'],
            'category_id'           => $categoryIdRule,
            // Primary image: either a URL or a file (both optional)
            'image_url'             => ['nullable', 'string', 'max:500'],
            'image_file'            => ['nullable', 'image', 'max:4096'],
            // Extra images
            'extra_image_url.*'     => ['nullable', 'string', 'max:500'],
            'extra_image_file.*'    => ['nullable', 'image', 'max:4096'],
        ], [
            'category_id.required' => 'Please select a category, or create a new one.',
            'category_id.exists'   => 'The selected category is invalid.',
        ]);
    }
}
