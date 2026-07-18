<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $today = now()->startOfDay();
        $weekAgo = now()->subDays(7)->startOfDay();

        if ($user->isAdmin()) {
            $stats = [
                'totalUsers'     => User::where('role', 'user')->count(),
                'newUsersWeek'   => User::where('role', 'user')->where('created_at', '>=', $weekAgo)->count(),
                'totalAdmins'    => User::where('role', 'admin')->count(),
                'activeToday'    => User::whereDate('updated_at', $today)->count(),
                'totalRevenue'   => 0,
                'revenueWeek'    => 0,
                'totalOrders'    => 0,
                'ordersWeek'     => 0,
                'successRate'    => 0,
                'pendingCount'   => 0,
            ];
        } else {
            $stats = [
                'totalSales'     => 0,
                'salesWeek'      => 0,
                'totalProducts'  => 0,
                'lowStock'       => 0,
                'totalRevenue'   => 0,
                'revenueWeek'    => 0,
                'totalOrders'    => 0,
                'ordersWeek'     => 0,
                'successRate'    => 0,
                'pendingCount'   => 0,
            ];
        }

        $dailyRevenue = [];
        $dailyLabels = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('d');
            $dailyRevenue[] = 0;
        }

        $recentUsers = User::where('role', 'user')->orderBy('created_at', 'desc')->take(6)->get();

        return view('home', compact('stats', 'dailyRevenue', 'dailyLabels', 'recentUsers'));
    }
}
