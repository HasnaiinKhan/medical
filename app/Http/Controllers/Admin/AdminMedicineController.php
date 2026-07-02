<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMedicineImageJob;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminMedicineController extends Controller
{
    public function index(Request $request): View
    {
        $q            = trim((string) $request->get('q', ''));
        $categorySlug = $request->get('category');
        $status       = $request->get('status', 'all'); // all | active | inactive

        $medicines = Medicine::with('category')
            ->when($q, fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('manufacturer', 'like', "%{$q}%");
            }))
            ->when($categorySlug, fn ($query) =>
                $query->whereHas('category', fn ($c) => $c->where('slug', $categorySlug))
            )
            ->when($status === 'active',   fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        // Counts for the status tabs
        $totalCount    = Medicine::count();
        $activeCount   = Medicine::where('is_active', true)->count();
        $inactiveCount = Medicine::where('is_active', false)->count();

        return view('admin.medicines.index', compact(
            'medicines', 'categories', 'q', 'status',
            'totalCount', 'activeCount', 'inactiveCount'
        ));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.medicines.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $exists = Medicine::whereRaw('LOWER(name) = ?', [
            strtolower(trim($request->name))
        ])->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'This medicine already exists.'])->withInput();
        }

        $data       = $this->validated($request);
        $categoryId = $this->resolveCategory($request);
        $primaryImage = $this->resolvePrimaryImage($request);
        $extraImages  = $this->parseExtraImages($request);

        $medicine = Medicine::create([
            'category_id'           => $categoryId,
            'name'                  => $data['name'],
            'slug'                  => Str::slug($data['name']),
            'manufacturer'          => $data['manufacturer'],
            'description'           => $data['description'] ?? '',
            'mrp_paise'             => (int) round($data['mrp'] * 100),
            'price_paise'           => (int) round($data['price'] * 100),
            'prescription_required' => (bool) ($data['prescription_required'] ?? false),
            'stock'                 => (int) $data['stock'],
            'strips_per_pack'       => ($v = (int) ($data['strips_per_pack'] ?? 0)) > 0 ? $v : null,
            'tablets_per_strip'     => ($v = (int) ($data['tablets_per_strip'] ?? 0)) > 0 ? $v : null,
            'image_url'             => $primaryImage,
            'extra_images'          => $extraImages,
        ]);

        $this->queueRemoteImageJobs($medicine, $primaryImage, $extraImages);

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
        $exists = Medicine::whereRaw('LOWER(name) = ?', [strtolower(trim($request->name))])
            ->where('id', '!=', $medicine->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'This medicine already exists.'])->withInput();
        }

        $data       = $this->validated($request, $medicine->id);
        $categoryId = $this->resolveCategory($request);
        $primaryImage = $this->resolvePrimaryImage($request, $medicine->image_url);
        $extraImages  = $this->parseExtraImages($request);

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
            'strips_per_pack'       => ($v = (int) ($data['strips_per_pack'] ?? 0)) > 0 ? $v : null,
            'tablets_per_strip'     => ($v = (int) ($data['tablets_per_strip'] ?? 0)) > 0 ? $v : null,
            'is_active'             => (bool) ($request->input('is_active', false)),
            'image_url'             => $primaryImage,
            'extra_images'          => $extraImages,
        ]);

        $this->queueRemoteImageJobs($medicine, $primaryImage, $extraImages);

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

    public function toggleActive(Medicine $medicine): \Illuminate\Http\JsonResponse
    {
        $medicine->update(['is_active' => ! $medicine->is_active]);

        return response()->json([
            'ok'        => true,
            'is_active' => $medicine->is_active,
            'message'   => $medicine->is_active
                ? "'{$medicine->name}' is now live."
                : "'{$medicine->name}' is now hidden from customers.",
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resolvePrimaryImage(Request $request, ?string $existing = null): ?string
    {
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($file && $file->isValid()) {
                $path = $file->store('medicines', 'public');
                return asset('storage/' . $path);
            }
        }

        $url = trim((string) $request->input('image_url', ''));
        if ($url !== '' && !str_starts_with($url, 'blob:') && filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return $existing;
    }

    private function parseExtraImages(Request $request): array
    {
        $files  = $request->file('extra_image_file', []);
        $urls   = $request->input('extra_image_url', []);
        $count  = max(count((array) $files), count((array) $urls));
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $file = $files[$i] ?? null;
            $url  = trim((string) ($urls[$i] ?? ''));

            if ($file && $file->isValid()) {
                $result[] = asset('storage/' . $file->store('medicines', 'public'));
            } elseif ($url !== '' && !str_starts_with($url, 'blob:') && filter_var($url, FILTER_VALIDATE_URL)) {
                $result[] = $url;
            }
        }

        return $result;
    }

    private function queueRemoteImageJobs(Medicine $medicine, ?string $primaryImageUrl, array $extraImages): void
    {
        $primaryImageUrl = trim((string) ($primaryImageUrl ?? ''));
        if ($primaryImageUrl !== '' && filter_var($primaryImageUrl, FILTER_VALIDATE_URL) && ! str_starts_with($primaryImageUrl, 'blob:')) {
            ProcessMedicineImageJob::dispatch($medicine->id, 'image_url', $primaryImageUrl);
        }

        foreach ($extraImages as $extraImageUrl) {
            $extraImageUrl = trim((string) $extraImageUrl);
            if ($extraImageUrl !== '' && filter_var($extraImageUrl, FILTER_VALIDATE_URL) && ! str_starts_with($extraImageUrl, 'blob:')) {
                ProcessMedicineImageJob::dispatch($medicine->id, 'extra_images', $extraImageUrl);
            }
        }
    }

    private function downloadRemoteImage(string $remoteUrl): ?string
    {
        if ($this->isLocalMedicineImage($remoteUrl)) {
            return $remoteUrl;
        }

        $scheme = strtolower(parse_url($remoteUrl, PHP_URL_SCHEME) ?? '');
        $host   = strtolower(parse_url($remoteUrl, PHP_URL_HOST) ?? '');

        if ($scheme !== 'https' || !str_contains($host, '.')) {
            return null;
        }

        foreach (['localhost', '127.', '192.168.', '10.', '172.16.', '0.0.0.0', '::1'] as $blocked) {
            if (str_starts_with($host, $blocked) || $host === $blocked) {
                return null;
            }
        }

        $referer = '';
        if (str_contains($host, 'pharmeasy')) {
            $referer = 'https://pharmeasy.in/';
        } elseif (str_contains($host, 'netmeds') || str_contains($host, 'pixelbin')) {
            $referer = 'https://www.netmeds.com/';
        } elseif (str_contains($host, 'apollo') || str_contains($host, 'cloudinary')) {
            $referer = 'https://www.apollopharmacy.in/';
        } elseif (str_contains($host, '1mg') || str_contains($host, 'onemg')) {
            $referer = 'https://www.1mg.com/';
        }

        $imageData = $this->httpGetImage($remoteUrl, $referer) ?: $this->httpGetImage($remoteUrl, '');
        if (! $imageData) {
            return null;
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($imageData);
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/avif' => 'avif',
        ];

        if (! isset($mimeToExt[$mime])) {
            Log::warning("Admin medicine image download skipped: unexpected MIME '{$mime}' for {$remoteUrl}");
            return null;
        }

        $dir = public_path('Images/medicines');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'med_' . Str::random(24) . '.' . $mimeToExt[$mime];
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

        if (file_put_contents($fullPath, $imageData) === false) {
            return null;
        }

        return asset('Images/medicines/' . $filename);
    }

    private function httpGetImage(string $url, string $referer): ?string
    {
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124',
            'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
            'Accept-Language: en-IN,en;q=0.9',
        ];
        if ($referer !== '') {
            $headers[] = 'Referer: ' . $referer;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status >= 200 && $status < 300 && is_string($body) && strlen($body) > 100) {
            return $body;
        }

        return null;
    }

    private function isLocalMedicineImage(string $url): bool
    {
        return str_contains($url, '/Images/medicines/')
            || str_contains($url, '/storage/medicines/');
    }

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
            'strips_per_pack'       => ['nullable', 'integer', 'min:1', 'max:9999'],
            'tablets_per_strip'     => ['nullable', 'integer', 'min:1', 'max:9999'],
            'category_id'           => $categoryIdRule,
            'image_url'             => ['nullable', 'string', 'max:2000'],
            'image_file'            => ['nullable', 'image', 'max:4096'],
            'extra_image_url.*'     => ['nullable', 'string', 'max:2000'],
            'extra_image_file.*'    => ['nullable', 'image', 'max:4096'],
        ], [
            'category_id.required' => 'Please select a category, or create a new one.',
            'category_id.exists'   => 'The selected category is invalid.',
        ]);
    }
}
