<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalSales = Order::sum('total_price');

        $ordersToday = Order::whereDate('created_at', now()->toDateString())->count();

        $popularItem = Order::select('product_name', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->first();

        $totalCustomers = User::where('role', 'Customer')->count();

        return response()->json([
            'total_sales' => $totalSales,
            'orders_today' => $ordersToday,
            'popular_item' => $popularItem,
            'total_customers' => $totalCustomers,
        ]);
    }
    public function recentOrders()
    {
        $orders = Order::with('user')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return response()->json($orders);
    }
}
