<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featured = Medicine::query()
            ->with('category')
            ->where('is_active', true)
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $categories = Category::query()
            ->withCount('medicines')
            // Keep the homepage category grid fixed to the first 10 created categories.
            ->orderBy('id')
            ->take(10)
            ->get();

        return view('home', compact('featured', 'categories'));
    }
}
