<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicineImportExportController extends Controller
{
    // ── Import form ───────────────────────────────────────────────────────────
    public function importForm(): View
    {
        return view('admin.medicines.import');
    }

    // ── Handle CSV upload ─────────────────────────────────────────────────────
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        // Give large imports breathing room — won't freeze the browser because
        // the response is sent after all DB work is done (images are queued).
        set_time_limit(300);
        ini_set('memory_limit', '256M');

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // ── Parse header row, strip BOM ───────────────────────────────────────
        $rawHeaders    = fgetcsv($handle);
        $rawHeaders[0] = ltrim($rawHeaders[0], "\xEF\xBB\xBF");
        $headers       = array_map('strtolower', array_map('trim', $rawHeaders));

        $required = ['name', 'manufacturer', 'category', 'mrp', 'price'];
        $missing  = array_diff($required, $headers);
        if ($missing) {
            fclose($handle);
            return back()->withErrors([
                'csv_file' => 'Missing columns: ' . implode(', ', $missing) .
                              '. Required: name, manufacturer, category, mrp, price.',
            ]);
        }

        // ── Pre-load all existing category slugs into memory ──────────────────
        // Avoids one SELECT per row for the common case where categories repeat.
        $categoryCache = \App\Models\Category::pluck('id', 'slug')->toArray();

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $chunk    = [];          // rows to upsert in bulk
        $imageJobs = [];         // [medicineSlug => [field, url], ...]

        $CHUNK_SIZE = 50;        // upsert 50 rows per query

        $flushChunk = function () use (&$chunk, &$imported, &$imageJobs) {
            if (empty($chunk)) return;
            // Bulk upsert — one query for up to 50 medicines
            Medicine::upsert($chunk, ['slug'], [
                'category_id', 'name', 'manufacturer', 'description',
                'mrp_paise', 'price_paise', 'prescription_required',
                'stock', 'strips_per_pack', 'tablets_per_strip',
                'image_url', 'extra_images',
            ]);
            $imported += count($chunk);
            $chunk = [];
        };

        // ── Stream-parse CSV row by row ───────────────────────────────────────
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < count($headers)) { $skipped++; continue; }

            $data = array_combine($headers, array_map('trim', $row));
            if (empty($data['name'])) { $skipped++; continue; }

            // ── Prices ───────────────────────────────────────────────────────
            $mrpPaise   = (int) round((float) ($data['mrp']   ?? 0) * 100);
            $pricePaise = (int) round((float) ($data['price'] ?? 0) * 100);
            if ($mrpPaise <= 0 || $pricePaise <= 0) {
                $errors[] = "Skipped (invalid price): {$data['name']}";
                $skipped++;
                continue;
            }

            // ── Category — use cache, create only if genuinely new ────────────
            try {
                $catName = trim($data['category'] ?? 'General') ?: 'General';
                $catSlug = Str::slug($catName);
                if (!isset($categoryCache[$catSlug])) {
                    $cat = \App\Models\Category::firstOrCreate(
                        ['slug' => $catSlug],
                        ['name' => $catName]
                    );
                    $categoryCache[$catSlug] = $cat->id;
                }
                $categoryId = $categoryCache[$catSlug];
            } catch (\Throwable $e) {
                $errors[] = "Category error for '{$data['name']}': " . $e->getMessage();
                $skipped++;
                continue;
            }

            // ── Images ───────────────────────────────────────────────────────
            $primaryImageUrl = trim((string) ($data['image_url'] ?? ''));

            $extraImages = [];
            foreach (['image_url_2', 'image_url_3', 'image_url_4'] as $col) {
                $u = trim((string) ($data[$col] ?? ''));
                if ($u !== '') $extraImages[] = $u;
            }
            if (empty($extraImages)) {
                $extraImages = $this->parseExtraImages(
                    $data['extra_images'] ?? $data['gallery_images'] ?? ''
                ) ?? [];
            }

            $slug = Str::slug($data['name']);

            // ── Collect image jobs (dispatched after DB work, not during) ─────
            if ($primaryImageUrl !== '' && filter_var($primaryImageUrl, FILTER_VALIDATE_URL)
                && !str_starts_with($primaryImageUrl, 'blob:')
                && !$this->isLocalImage($primaryImageUrl)) {
                $imageJobs[] = ['slug' => $slug, 'field' => 'image_url', 'url' => $primaryImageUrl];
            }
            foreach ($extraImages as $extraUrl) {
                $extraUrl = trim((string) $extraUrl);
                if ($extraUrl !== '' && filter_var($extraUrl, FILTER_VALIDATE_URL)
                    && !str_starts_with($extraUrl, 'blob:')
                    && !$this->isLocalImage($extraUrl)) {
                    $imageJobs[] = ['slug' => $slug, 'field' => 'extra_images', 'url' => $extraUrl];
                }
            }

            // ── Add to bulk chunk ─────────────────────────────────────────────
            $chunk[] = [
                'slug'                  => $slug,
                'category_id'           => $categoryId,
                'name'                  => $data['name'],
                'manufacturer'          => $data['manufacturer'] ?? '',
                'description'           => $data['description']  ?? '',
                'mrp_paise'             => $mrpPaise,
                'price_paise'           => $pricePaise,
                'prescription_required' => (int) filter_var(
                    $data['prescription_required'] ?? false, FILTER_VALIDATE_BOOLEAN
                ),
                'stock'                 => (int) ($data['stock'] ?? 100),
                'strips_per_pack'       => ($v = (int) ($data['strips_per_pack'] ?? 0)) > 0 ? $v : null,
                'tablets_per_strip'     => ($v = (int) ($data['tablets_per_strip'] ?? 0)) > 0 ? $v : null,
                'image_url'             => $primaryImageUrl ?: null,
                'extra_images'          => $extraImages ? json_encode($extraImages) : null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ];

            if (count($chunk) >= $CHUNK_SIZE) {
                try {
                    $flushChunk();
                } catch (\Throwable $e) {
                    $errors[] = 'Bulk insert error: ' . $e->getMessage();
                }
            }
        }

        // Flush remaining rows
        try {
            $flushChunk();
        } catch (\Throwable $e) {
            $errors[] = 'Final flush error: ' . $e->getMessage();
        }

        fclose($handle);

        // ── Dispatch image-download jobs AFTER all DB writes are done ─────────
        // Because QUEUE_CONNECTION=database, these are inserted into the jobs
        // table instantly and processed by the queue worker in the background.
        // The HTTP response returns immediately — no blocking cURL calls here.
        $medicineIdsBySlug = Medicine::whereIn('slug', array_unique(array_column($imageJobs, 'slug')))
            ->pluck('id', 'slug')
            ->toArray();

        foreach ($imageJobs as $job) {
            $medicineId = $medicineIdsBySlug[$job['slug']] ?? null;
            if ($medicineId) {
                \App\Jobs\ProcessMedicineImageJob::dispatch($medicineId, $job['field'], $job['url']);
            }
        }

        $msg = "Import complete: {$imported} medicines imported/updated.";
        if ($skipped)         $msg .= " {$skipped} rows skipped.";
        if (!empty($imageJobs)) $msg .= " " . count($imageJobs) . " images queued for background download.";

        return redirect()->route('admin.medicines.index')
            ->with('status', $msg)
            ->with('import_errors', $errors);
    }

    private function isLocalImage(string $url): bool
    {
        return str_contains($url, '/Images/medicines/')
            || str_contains($url, '/storage/medicines/');
    }

    // ── Export form with filters ─────────────────────────────────────────────────
    public function exportForm(): View
    {
        $categories    = Category::orderBy('name')->get();
        $manufacturers = Medicine::distinct()
            ->whereNotNull('manufacturer')
            ->where('manufacturer', '!=', '')
            ->orderBy('manufacturer')
            ->pluck('manufacturer');

        return view('admin.medicines.export', compact('categories', 'manufacturers'));
    }

    // ── Export CSV with filters ───────────────────────────────────────────────
    // Accepts GET so the download URL can be triggered directly (no CSRF needed
    // for a read-only / streaming export operation).
    public function export(Request $request): StreamedResponse
    {
        $query = Medicine::with('category');

        // Filter by categories array (multiple checkboxes)
        $categorySlugs = array_filter((array) $request->input('categories', []));
        if (!empty($categorySlugs)) {
            $query->whereHas('category', fn ($c) => $c->whereIn('slug', $categorySlugs));
        }

        // Filter by manufacturers array (multiple checkboxes)
        $manufacturers = array_filter((array) $request->input('manufacturer', []));
        if (!empty($manufacturers)) {
            $query->whereIn('manufacturer', $manufacturers);
        }

        // Keyword search
        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('manufacturer', 'like', "%{$q}%");
            });
        }

        // Prescription filter
        if ($request->filled('prescription') && $request->prescription !== '') {
            $query->where('prescription_required', (bool)(int) $request->prescription);
        }

        // Stock status
        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'out_of_stock' => $query->where('stock', '<=', 0),
                'low_stock'    => $query->where('stock', '>', 0)->where('stock', '<=', 5),
                'in_stock'     => $query->where('stock', '>', 5),
                default        => null,
            };
        }

        // Price range
        if ($request->filled('price_min')) {
            $query->where('price_paise', '>=', (int) round((float) $request->price_min * 100));
        }
        if ($request->filled('price_max')) {
            $query->where('price_paise', '<=', (int) round((float) $request->price_max * 100));
        }

        $medicines = $query->orderBy('name')->get();
        $filename  = 'medicines_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($medicines) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            fputcsv($handle, [
                'name', 'manufacturer', 'category', 'mrp', 'price',
                'prescription_required', 'stock', 'description',
                'strips_per_pack', 'tablets_per_strip',
                'image_url', 'image_url_2', 'image_url_3', 'image_url_4',
            ]);

            foreach ($medicines as $m) {
                $extras = array_values(array_filter((array) ($m->extra_images ?? [])));
                fputcsv($handle, [
                    $m->name,
                    $m->manufacturer ?? '',
                    $m->category->name ?? '',
                    number_format($m->mrp_paise   / 100, 2, '.', ''),
                    number_format($m->price_paise / 100, 2, '.', ''),
                    $m->prescription_required ? 'true' : 'false',
                    $m->stock,
                    $m->description ?? '',
                    $m->strips_per_pack   ?? '',
                    $m->tablets_per_strip ?? '',
                    $m->image_url ?? '',
                    $extras[0] ?? '',
                    $extras[1] ?? '',
                    $extras[2] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ── Download blank template ───────────────────────────────────────────────
    public function template(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'name', 'manufacturer', 'category', 'mrp', 'price',
                'prescription_required', 'stock', 'description',
                'strips_per_pack', 'tablets_per_strip',
                'image_url', 'image_url_2', 'image_url_3', 'image_url_4',
            ]);
            fputcsv($handle, [
                'Dolo 650 Tablet', 'Micro Labs', 'Fever & Pain',
                '45.00', '38.00', 'false', '200',
                'Paracetamol 650mg for fever and pain relief.',
                '3', '10',
                '', '', '', '',
            ]);
            fclose($handle);
        }, 'medicines_template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function parseExtraImages(string $value): ?array
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $images = $decoded;
        } else {
            $images = preg_split('/\s*[|;]\s*/', $value) ?: [];
        }

        $images = array_values(array_filter(array_map(
            fn ($url) => trim((string) $url),
            $images
        )));

        return $images ?: null;
    }
}
