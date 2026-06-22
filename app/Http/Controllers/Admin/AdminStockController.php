<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class AdminStockController extends Controller
{
    public function alerts()
    {
        // Out-of-stock medicines (stock <= 0)
        $outOfStockMedicines = Medicine::where('stock', '<=', 0)
            ->orderBy('name')
            ->paginate(50, ['*'], 'out');

        // Low-stock medicines (stock between 1 and 5, not zero)
        $lowStockMedicines = Medicine::where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->paginate(50, ['*'], 'low');

        return view('admin.stock.alerts', compact('outOfStockMedicines', 'lowStockMedicines'));
    }
}
