<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\BranchStock;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\Branch;
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
        $businessId = $user->business_id;
        $branchId = session('active_branch_id') ?? $user->branch_id;

        if (!$branchId && $user->business) {
            $branchId = $user->business->branches()->first()?->id;
            session(['active_branch_id' => $branchId]);
        }

        $today = today();
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();

        $salesQuery = Sale::where('business_id', $businessId)->where('status', 'completed');
        if ($branchId && !$user->isBusinessAdmin() && !$user->isAdmin()) {
            $salesQuery->where('branch_id', $branchId);
        }

        $todaySales = (clone $salesQuery)->whereDate('created_at', $today)->sum('total');
        $todayCount = (clone $salesQuery)->whereDate('created_at', $today)->count();
        $todayProfit = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereDate('created_at', $today))->sum('profit');
        $weekSales = (clone $salesQuery)->whereBetween('created_at', [$startOfWeek, now()])->sum('total');
        $monthSales = (clone $salesQuery)->whereBetween('created_at', [$startOfMonth, now()])->sum('total');

        $todayExpenses = Expense::where('business_id', $businessId)->whereDate('expense_date', $today)->sum('amount');
        $monthExpenses = Expense::where('business_id', $businessId)->whereBetween('expense_date', [$startOfMonth, now()])->sum('amount');

        $totalProducts = Product::where('business_id', $businessId)->where('status', 'active')->count();
        $lowStockCount = BranchStock::whereHas('product', fn($q) => $q->where('business_id', $businessId))
            ->whereColumn('qty', '<=', 'reorder_level')->count();

        $totalCustomers = Customer::where('business_id', $businessId)->count();
        $totalDebts = CustomerDebt::where('business_id', $businessId)->whereIn('status', ['pending', 'partial', 'overdue'])->sum('balance');

        $branchCount = Branch::where('business_id', $businessId)->count();
        $staffCount = User::where('business_id', $businessId)->count();

        $stats = [
            'todaySales' => $todaySales,
            'todayCount' => $todayCount,
            'todayProfit' => $todayProfit,
            'weekSales' => $weekSales,
            'monthSales' => $monthSales,
            'todayExpenses' => $todayExpenses,
            'monthExpenses' => $monthExpenses,
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockCount,
            'totalCustomers' => $totalCustomers,
            'totalDebts' => $totalDebts,
            'branchCount' => $branchCount,
            'staffCount' => $staffCount,
        ];

        // 14-day revenue chart
        $dailyRevenue = [];
        $dailyLabels = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('d');
            $dailyRevenue[] = (float) (clone $salesQuery)->whereDate('created_at', $date)->sum('total');
        }

        // Recent sales
        $recentSales = (clone $salesQuery)->with(['customer', 'user', 'branch'])->orderByDesc('id')->take(8)->get();

        // Low stock items
        $lowStockItems = BranchStock::whereHas('product', fn($q) => $q->where('business_id', $businessId))
            ->whereColumn('qty', '<=', 'reorder_level')
            ->with('product', 'branch')
            ->limit(5)
            ->get();

        // Top products this month
        $topProducts = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month))
            ->select('product_id', \DB::raw('SUM(qty) as total_qty'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $branches = $user->business?->branches ?? collect();

        return view('home', compact('stats', 'dailyRevenue', 'dailyLabels', 'recentSales', 'lowStockItems', 'topProducts', 'branches', 'branchId'));
    }
}
