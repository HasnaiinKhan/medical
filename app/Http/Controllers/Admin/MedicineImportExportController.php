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
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Read header row — strip UTF-8 BOM if present
        $rawHeaders = fgetcsv($handle);
        $rawHeaders[0] = ltrim($rawHeaders[0], "\xEF\xBB\xBF");
        $headers = array_map('strtolower', array_map('trim', $rawHeaders));

        $required = ['name', 'manufacturer', 'category', 'mrp', 'price'];
        $missing  = array_diff($required, $headers);

        if ($missing) {
            fclose($handle);
            return back()->withErrors([
                'csv_file' => 'Missing columns: ' . implode(', ', $missing) .
                              '. Required: name, manufacturer, category, mrp, price.',
            ]);
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < count($headers)) {
                $skipped++;
                continue;
            }

            $data = array_combine($headers, array_map('trim', $row));

            if (empty($data['name'])) {
                $skipped++;
                continue;
            }

            try {
                $catName  = $data['category'] ?? 'General';
                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($catName)],
                    ['name' => $catName]
                );

                $mrpPaise   = (int) round((float) ($data['mrp']   ?? 0) * 100);
                $pricePaise = (int) round((float) ($data['price'] ?? 0) * 100);

                if ($mrpPaise <= 0 || $pricePaise <= 0) {
                    $errors[] = "Row skipped (invalid price): {$data['name']}";
                    $skipped++;
                    continue;
                }

                Medicine::updateOrCreate(
                    ['slug' => Str::slug($data['name'])],
                    [
                        'category_id'           => $category->id,
                        'name'                  => $data['name'],
                        'manufacturer'          => $data['manufacturer'] ?? '',
                        'description'           => $data['description']  ?? '',
                        'mrp_paise'             => $mrpPaise,
                        'price_paise'           => $pricePaise,
                        'prescription_required' => filter_var($data['prescription_required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'stock'                 => (int) ($data['stock'] ?? 100),
                        'image_url'             => ($data['image_url'] ?? '') ?: null,
                    ]
                );

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Error on '{$data['name']}': " . $e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);

        $msg = "Import complete: {$imported} medicines imported/updated.";
        if ($skipped) {
            $msg .= " {$skipped} rows skipped.";
        }

        return redirect()->route('admin.medicines.index')
            ->with('status', $msg)
            ->with('import_errors', $errors);
    }

    // ── Export CSV ────────────────────────────────────────────────────────────
    public function export(Request $request): StreamedResponse
    {
        $medicines = Medicine::with('category')
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereHas('category', fn ($c) => $c->where('slug', $request->category));
            })
            ->orderBy('name')
            ->get();

        $filename = 'medicines_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($medicines) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens it correctly
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'name', 'manufacturer', 'category', 'mrp', 'price',
                'prescription_required', 'stock', 'description', 'image_url',
            ]);

            foreach ($medicines as $m) {
                fputcsv($handle, [
                    $m->name,
                    $m->manufacturer,
                    $m->category->name ?? '',
                    number_format($m->mrp_paise   / 100, 2, '.', ''),
                    number_format($m->price_paise / 100, 2, '.', ''),
                    $m->prescription_required ? 'true' : 'false',
                    $m->stock,
                    $m->description,
                    $m->image_url ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Download blank template ───────────────────────────────────────────────
    public function template(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'name', 'manufacturer', 'category', 'mrp', 'price',
                'prescription_required', 'stock', 'description', 'image_url',
            ]);
            fputcsv($handle, [
                'Dolo 650 Tablet', 'Micro Labs', 'Fever & Pain',
                '45.00', '38.00', 'false', '200',
                'Paracetamol 650mg for fever and pain relief.', '',
            ]);
            fclose($handle);
        }, 'medicines_template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
