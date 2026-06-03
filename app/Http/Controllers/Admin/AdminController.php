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

        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        // ── Chart data ──────────────────────────────────────────────────
        // Revenue uses the same source-of-truth as revenueQuery():
        //   payment_status = 'paid'  AND  status != 'cancelled'
        //
        // Strategy: daily bars for the current month (most recent = most detail),
        // monthly bars for the 5 months before that.

        // --- Monthly (5 full months before this month) ---
        $monthlyRaw = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period"),
                DB::raw('SUM(total_paise) / 100 as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->where('created_at', '<', now()->startOfMonth())
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $months = collect();

        // Fill 5 prior months (oldest → newest)
        for ($i = 5; $i >= 1; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $row = $monthlyRaw->get($key);
            $months->push([
                'label'   => now()->subMonths($i)->format('M Y'),
                'revenue' => $row ? round((float) $row->revenue, 2) : 0,
                'orders'  => $row ? (int) $row->orders : 0,
                'type'    => 'month',
            ]);
        }

        // --- Daily (current month so far) ---
        $dailyRaw = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as period"),
                DB::raw('SUM(total_paise) / 100 as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $daysInMonth = now()->daysInMonth;
        for ($d = 1; $d <= now()->day; $d++) {
            $key = now()->format('Y-m-') . str_pad($d, 2, '0', STR_PAD_LEFT);
            $row = $dailyRaw->get($key);
            $months->push([
                'label'   => now()->day($d)->format('d M'),
                'revenue' => $row ? round((float) $row->revenue, 2) : 0,
                'orders'  => $row ? (int) $row->orders : 0,
                'type'    => 'day',
            ]);
        }

        // Order status breakdown (doughnut chart)
        $statusBreakdown = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('admin.dashboard', compact('stats', 'recentOrders', 'months', 'statusBreakdown'));
    }
}
