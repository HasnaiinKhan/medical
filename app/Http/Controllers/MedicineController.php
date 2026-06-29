<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicineController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        $category = $request->get('category');
        $brand = $request->get('brand');

        $brandFilters = array_filter((array) $brand, function ($value) {
            return trim((string) $value) !== '';
        });
        $brandFilters = array_map(fn ($value) => trim((string) $value), $brandFilters);

        $medicineQuery = Medicine::query()
            ->with('category')
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%'.$q.'%')
                        ->orWhere('manufacturer', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%');
                });
            })
            ->when($category, function ($query, $slug) {
                $query->whereHas('category', fn ($c) => $c->where('slug', $slug));
            })
            ->when($brandFilters, function ($query, $brands) {
                $query->whereIn('manufacturer', $brands);
            });

        $medicines = $medicineQuery
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $brands = Medicine::query()
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%'.$q.'%')
                        ->orWhere('manufacturer', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%');
                });
            })
            ->when($category, function ($query, $slug) {
                $query->whereHas('category', fn ($c) => $c->where('slug', $slug));
            })
            ->whereNotNull('manufacturer')
            ->where('manufacturer', '<>', '')
            ->selectRaw('manufacturer as name, count(*) as count')
            ->groupBy('manufacturer')
            ->orderBy('manufacturer')
            ->get();

        $categories = Category::query()->orderBy('name')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'resultsHtml' => view('medicines._results', compact('medicines', 'categories', 'q'))->render(),
                'headingHtml' => view('medicines._summary', compact('medicines', 'categories', 'q'))->render(),
                'filtersHtml' => view('medicines._filters', compact('categories', 'brands', 'brandFilters', 'q'))->render(),
            ]);
        }

        return view('medicines.index', compact('medicines', 'categories', 'q', 'brands', 'brandFilters'));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        $category = $request->get('category');
        $brand = $request->get('brand');

        $brandFilters = array_filter((array) $brand, function ($value) {
            return trim((string) $value) !== '';
        });
        $brandFilters = array_map(fn ($value) => trim((string) $value), $brandFilters);

        if (mb_strlen($q) < 2) {
            return response()->json([
                'suggestions' => [],
            ]);
        }

        $suggestions = Medicine::query()
            ->with('category')
            ->where('is_active', true)
            ->when($category, function ($query, $slug) {
                $query->whereHas('category', fn ($c) => $c->where('slug', $slug));
            })
            ->when($brandFilters, function ($query, $brands) {
                $query->whereIn('manufacturer', $brands);
            })
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%')
                    ->orWhere('manufacturer', 'like', '%'.$q.'%')
                    ->orWhere('description', 'like', '%'.$q.'%');
            })
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', [$q.'%'])
            ->orderBy('name')
            ->limit(6)
            ->get()
            ->map(function (Medicine $medicine) {
                return [
                    'name' => $medicine->name,
                    'manufacturer' => $medicine->manufacturer,
                    'category' => $medicine->category?->name,
                    'image' => $medicine->imageUrl(),
                    'url' => route('medicines.show', $medicine),
                    'price' => number_format($medicine->priceRupees(), 2),
                    'prescription_required' => $medicine->prescription_required,
                ];
            })
            ->values();

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }

    public function show(Medicine $medicine): View
    {
        // Block direct access to inactive products
        abort_if(! $medicine->is_active, 404);

        $medicine->load('category');

        $related = Medicine::query()
            ->where('category_id', $medicine->category_id)
            ->where('is_active', true)
            ->whereKeyNot($medicine->id)
            ->take(4)
            ->get();

        return view('medicines.show', compact('medicine', 'related'));
    }
}
