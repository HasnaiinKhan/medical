<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'medicines'  => Medicine::count(),
            'categories' => Category::count(),
            'orders'     => Order::count(),
            'users'      => User::where('is_admin', false)->count(),
            'revenue'    => AdminOrderController::revenueQuery()->sum('total_paise') / 100,
            'pending'    => Order::where('status', 'placed')->count(),
        ];

        // Out-of-stock medicines (stock <= 0)
        $outOfStockMedicines = Medicine::where('stock', '<=', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'stock']);

        // Low-stock medicines (stock between 1 and 5, not zero)
        $lowStockMedicines = Medicine::where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get(['id', 'name', 'slug', 'stock']);

        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        // Base query for paid orders only
        $baseQ = fn () => Order::where('payment_status', 'paid')->where('status', '!=', 'cancelled');

        // ── TODAY — hourly (0–23) ────────────────────────────────────────
        $todayRaw = (clone $baseQ())
            ->select(DB::raw("HOUR(created_at) as period"),
                     DB::raw('SUM(total_paise)/100 as revenue'),
                     DB::raw('COUNT(*) as orders'))
            ->whereDate('created_at', today())
            ->groupBy('period')->orderBy('period')
            ->get()->keyBy('period');

        $todayData = collect();
        for ($h = 0; $h <= 23; $h++) {
            $row = $todayRaw->get($h);
            $todayData->push([
                'label'   => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00',
                'revenue' => $row ? round((float)$row->revenue, 2) : 0,
                'orders'  => $row ? (int)$row->orders : 0,
            ]);
        }

        // ── THIS WEEK — daily (Mon–today) ───────────────────────────────
        $weekRaw = (clone $baseQ())
            ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as period"),
                     DB::raw('SUM(total_paise)/100 as revenue'),
                     DB::raw('COUNT(*) as orders'))
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfDay()])
            ->groupBy('period')->orderBy('period')
            ->get()->keyBy('period');

        $weekData = collect();
        $day = now()->startOfWeek()->copy();
        while ($day->lte(now())) {
            $key = $day->format('Y-m-d');
            $row = $weekRaw->get($key);
            $weekData->push([
                'label'   => $day->format('D d'),
                'revenue' => $row ? round((float)$row->revenue, 2) : 0,
                'orders'  => $row ? (int)$row->orders : 0,
            ]);
            $day->addDay();
        }

        // ── THIS MONTH — daily ──────────────────────────────────────────
        $monthRaw = (clone $baseQ())
            ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as period"),
                     DB::raw('SUM(total_paise)/100 as revenue'),
                     DB::raw('COUNT(*) as orders'))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('period')->orderBy('period')
            ->get()->keyBy('period');

        $monthData = collect();
        for ($d = 1; $d <= now()->day; $d++) {
            $key = now()->format('Y-m-') . str_pad($d, 2, '0', STR_PAD_LEFT);
            $row = $monthRaw->get($key);
            $monthData->push([
                'label'   => now()->day($d)->format('d M'),
                'revenue' => $row ? round((float)$row->revenue, 2) : 0,
                'orders'  => $row ? (int)$row->orders : 0,
            ]);
        }

        // ── THIS YEAR — monthly ─────────────────────────────────────────
        $yearRaw = (clone $baseQ())
            ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m') as period"),
                     DB::raw('SUM(total_paise)/100 as revenue'),
                     DB::raw('COUNT(*) as orders'))
            ->whereYear('created_at', now()->year)
            ->groupBy('period')->orderBy('period')
            ->get()->keyBy('period');

        $yearData = collect();
        for ($m = 1; $m <= now()->month; $m++) {
            $key = now()->year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            $row = $yearRaw->get($key);
            $yearData->push([
                'label'   => now()->month($m)->format('M'),
                'revenue' => $row ? round((float)$row->revenue, 2) : 0,
                'orders'  => $row ? (int)$row->orders : 0,
            ]);
        }

        // Order status breakdown by time period
        $statusBreakdownToday = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereDate('created_at', today())
            ->groupBy('status')->get()->pluck('count', 'status');

        $statusBreakdownWeek = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfDay()])
            ->groupBy('status')->get()->pluck('count', 'status');

        $statusBreakdownMonth = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('status')->get()->pluck('count', 'status');

        $statusBreakdownYear = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', now()->year)
            ->groupBy('status')->get()->pluck('count', 'status');

        return view('admin.dashboard', compact(
            'stats', 'recentOrders',
            'todayData', 'weekData', 'monthData', 'yearData',
            'statusBreakdownToday', 'statusBreakdownWeek', 'statusBreakdownMonth', 'statusBreakdownYear',
            'outOfStockMedicines', 'lowStockMedicines'
        ));
    }
}
