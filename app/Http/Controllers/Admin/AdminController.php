<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'medicines'  => Medicine::count(),
            'categories' => Category::count(),
            'orders'     => Order::count(),
            'users'      => User::where('is_admin', false)->count(),
            'revenue'    => Order::where('payment_status', 'paid')->sum('total_paise') / 100,
            'pending'    => Order::where('status', 'placed')->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
