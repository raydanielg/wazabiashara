<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\BranchStock;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Account;
use App\Models\CashFlow;
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

        if (!$businessId) {
            return redirect()->route('business.register');
        }

        $branchId = session('active_branch_id') ?? $user->branch_id;

        if (!$branchId && $user->business) {
            $branchId = $user->business->branches()->first()?->id;
            session(['active_branch_id' => $branchId]);
        }

        $today = today();
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();
        $startOfYear = now()->startOfYear();

        $salesQuery = Sale::where('business_id', $businessId)->where('status', 'completed');
        if ($branchId && !$user->isBusinessAdmin() && !$user->isAdmin()) {
            $salesQuery->where('branch_id', $branchId);
        }

        // Financial Overview
        $totalSales = (clone $salesQuery)->sum('total');
        $todaySales = (clone $salesQuery)->whereDate('created_at', $today)->sum('total');
        $todayCount = (clone $salesQuery)->whereDate('created_at', $today)->count();
        $todayProfit = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereDate('created_at', $today))->sum('profit');
        $weekSales = (clone $salesQuery)->whereBetween('created_at', [$startOfWeek, now()])->sum('total');
        $monthSales = (clone $salesQuery)->whereBetween('created_at', [$startOfMonth, now()])->sum('total');
        $yearSales = (clone $salesQuery)->whereBetween('created_at', [$startOfYear, now()])->sum('total');

        // Purchases
        $totalPurchases = Purchase::where('business_id', $businessId)->sum('total');
        $monthPurchases = Purchase::where('business_id', $businessId)->whereMonth('created_at', now()->month)->sum('total');

        // Income
        $totalIncome = Income::where('business_id', $businessId)->sum('amount');
        $monthIncome = Income::where('business_id', $businessId)->whereMonth('income_date', now()->month)->sum('amount');

        // Expenses
        $totalExpenses = Expense::where('business_id', $businessId)->sum('amount');
        $todayExpenses = Expense::where('business_id', $businessId)->whereDate('expense_date', $today)->sum('amount');
        $monthExpenses = Expense::where('business_id', $businessId)->whereBetween('expense_date', [$startOfMonth, now()])->sum('amount');

        // Payments
        $totalPaymentsIn = Payment::where('business_id', $businessId)->where('type', 'in')->sum('amount');
        $totalPaymentsOut = Payment::where('business_id', $businessId)->where('type', 'out')->sum('amount');

        // Profit/Loss
        $totalProfit = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed'))->sum('profit');
        $netProfit = $totalProfit + $totalIncome - $totalExpenses;
        $totalLoss = max(0, -$netProfit);

        // Balances
        $accounts = Account::where('business_id', $businessId)->where('is_active', true)->get();
        $cashBalance = $accounts->where('type', 'cash')->sum('current_balance');
        $bankBalance = $accounts->where('type', 'bank')->sum('current_balance');
        $mobileBalance = $accounts->where('type', 'mobile_money')->sum('current_balance');

        // Receivables & Payables
        $totalReceivables = CustomerDebt::where('business_id', $businessId)->whereIn('status', ['pending', 'partial', 'overdue'])->sum('balance');
        $totalPayables = Supplier::where('business_id', $businessId)->sum('balance');

        // Stats
        $totalProducts = Product::where('business_id', $businessId)->where('status', 'active')->count();
        $lowStockCount = BranchStock::whereHas('product', fn($q) => $q->where('business_id', $businessId))
            ->whereColumn('qty', '<=', 'reorder_level')->count();
        $totalCustomers = Customer::where('business_id', $businessId)->count();
        $totalSuppliers = Supplier::where('business_id', $businessId)->count();
        $branchCount = Branch::where('business_id', $businessId)->count();
        $staffCount = User::where('business_id', $businessId)->count();

        $stats = [
            'totalSales' => $totalSales,
            'todaySales' => $todaySales,
            'todayCount' => $todayCount,
            'todayProfit' => $todayProfit,
            'weekSales' => $weekSales,
            'monthSales' => $monthSales,
            'yearSales' => $yearSales,
            'totalPurchases' => $totalPurchases,
            'monthPurchases' => $monthPurchases,
            'totalIncome' => $totalIncome,
            'monthIncome' => $monthIncome,
            'totalExpenses' => $totalExpenses,
            'todayExpenses' => $todayExpenses,
            'monthExpenses' => $monthExpenses,
            'totalPaymentsIn' => $totalPaymentsIn,
            'totalPaymentsOut' => $totalPaymentsOut,
            'totalProfit' => $totalProfit,
            'netProfit' => $netProfit,
            'totalLoss' => $totalLoss,
            'cashBalance' => $cashBalance,
            'bankBalance' => $bankBalance,
            'mobileBalance' => $mobileBalance,
            'totalReceivables' => $totalReceivables,
            'totalPayables' => $totalPayables,
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockCount,
            'totalCustomers' => $totalCustomers,
            'totalSuppliers' => $totalSuppliers,
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

        // 7-day expense vs income chart
        $dailyExpenses = [];
        $dailyIncome = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyExpenses[] = (float) Expense::where('business_id', $businessId)->whereDate('expense_date', $date)->sum('amount');
            $dailyIncome[] = (float) Income::where('business_id', $businessId)->whereDate('income_date', $date)->sum('amount');
        }

        // Monthly sales vs purchases (12 months)
        $monthlySales = [];
        $monthlyPurchases = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            $monthlySales[] = (float) (clone $salesQuery)->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->sum('total');
            $monthlyPurchases[] = (float) Purchase::where('business_id', $businessId)->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->sum('total');
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

        // Top customers
        $topCustomers = Sale::where('business_id', $businessId)->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereNotNull('customer_id')
            ->select('customer_id', \DB::raw('SUM(total) as total_spent'), \DB::raw('COUNT(*) as orders'))
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        $branches = $user->business?->branches ?? collect();

        return view('home', compact(
            'stats', 'dailyRevenue', 'dailyLabels', 'dailyExpenses', 'dailyIncome',
            'monthlySales', 'monthlyPurchases', 'monthlyLabels',
            'recentSales', 'lowStockItems', 'topProducts', 'topCustomers',
            'branches', 'branchId'
        ));
    }
}
