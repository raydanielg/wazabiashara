<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\Product;
use App\Models\BranchStock;
use App\Models\CustomerDebt;
use App\Models\Income;
use App\Models\Customer;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessId = $user->business_id;
        $branchId = session('active_branch_id') ?? $user->branch_id;

        $today = today();
        $startOfMonth = now()->startOfMonth();
        $startOfWeek = now()->startOfWeek();
        $startOfYear = now()->startOfYear();

        $salesQuery = Sale::where('business_id', $businessId)->where('status', 'completed');
        $branchSalesQuery = clone $salesQuery;
        if ($branchId && !$user->isBusinessAdmin()) {
            $branchSalesQuery->where('branch_id', $branchId);
        }

        // Core KPIs
        $todaySales = (clone $branchSalesQuery)->whereDate('created_at', $today)->sum('total');
        $todayProfit = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereDate('created_at', $today))->sum('profit');
        $weekSales = (clone $branchSalesQuery)->whereBetween('created_at', [$startOfWeek, now()])->sum('total');
        $monthSales = (clone $branchSalesQuery)->whereBetween('created_at', [$startOfMonth, now()])->sum('total');
        $yearSales = (clone $branchSalesQuery)->whereBetween('created_at', [$startOfYear, now()])->sum('total');

        $todayExpenses = Expense::where('business_id', $businessId)->whereDate('expense_date', $today)->sum('amount');
        $monthExpenses = Expense::where('business_id', $businessId)->whereBetween('expense_date', [$startOfMonth, now()])->sum('amount');
        $yearExpenses = Expense::where('business_id', $businessId)->whereBetween('expense_date', [$startOfYear, now()])->sum('amount');

        $monthIncome = Income::where('business_id', $businessId)->whereMonth('income_date', now()->month)->sum('amount');
        $yearIncome = Income::where('business_id', $businessId)->whereBetween('income_date', [$startOfYear, now()])->sum('amount');

        $monthProfit = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month))->sum('profit');
        $yearProfit = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereBetween('created_at', [$startOfYear, now()]))->sum('profit');

        $totalDebts = CustomerDebt::where('business_id', $businessId)->whereIn('status', ['pending', 'partial', 'overdue'])->sum('balance');
        $overdueDebts = CustomerDebt::where('business_id', $businessId)->where('status', 'overdue')->sum('balance');

        $totalCustomers = Customer::where('business_id', $businessId)->count();
        $activeCustomers = Customer::where('business_id', $businessId)->where('status', 'active')->count();

        // Annual goals - dynamic based on last year's data or sensible defaults
        $lastYearSales = (clone $branchSalesQuery)->whereYear('created_at', now()->year - 1)->sum('total');
        $annualSalesGoal = $lastYearSales > 0 ? round($lastYearSales * 1.2) : max($yearSales * 1.5, 10000000);
        $annualProfitGoal = round($annualSalesGoal * 0.3);
        $annualExpenseBudget = round($annualSalesGoal * 0.6);
        $annualCustomerGoal = max($totalCustomers * 1.3, 100);

        $salesProgress = $annualSalesGoal > 0 ? min(($yearSales / $annualSalesGoal) * 100, 100) : 0;
        $profitProgress = $annualProfitGoal > 0 ? min(($yearProfit / $annualProfitGoal) * 100, 100) : 0;
        $expenseProgress = $annualExpenseBudget > 0 ? min(($yearExpenses / $annualExpenseBudget) * 100, 100) : 0;
        $customerProgress = $annualCustomerGoal > 0 ? min(($totalCustomers / $annualCustomerGoal) * 100, 100) : 0;

        // Monthly data for all 12 months
        $monthlySales = [];
        $monthlyExpenses = [];
        $monthlyProfit = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        for ($m = 1; $m <= 12; $m++) {
            $monthlySales[] = (float) (clone $branchSalesQuery)->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('total');
            $monthlyExpenses[] = (float) Expense::where('business_id', $businessId)->whereMonth('expense_date', $m)->whereYear('expense_date', now()->year)->sum('amount');
            $monthlyProfit[] = (float) SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', $m)->whereYear('created_at', now()->year))->sum('profit');
        }

        // Last 7 days with profit
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = (clone $branchSalesQuery)->whereDate('created_at', $date)->sum('total');
            $profit = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereDate('created_at', $date))->sum('profit');
            $last7Days->push(['date' => $date->format('D d'), 'total' => (float) $total, 'profit' => (float) $profit]);
        }

        // Sales by payment method
        $salesByMethod = (clone $branchSalesQuery)->whereMonth('created_at', now()->month)
            ->select('payment_method', \DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        // Top products with progress
        $topProducts = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month))
            ->select('product_id', \DB::raw('SUM(qty) as total_qty'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(8)
            ->get();
        $maxProductQty = $topProducts->max('total_qty') ?: 1;

        // Top customers
        $topCustomers = Sale::where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month)
            ->whereNotNull('customer_id')
            ->select('customer_id', \DB::raw('SUM(total) as total_spent'), \DB::raw('COUNT(*) as orders_count'))
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // Low stock
        $lowStock = BranchStock::whereHas('product', fn($q) => $q->where('business_id', $businessId))
            ->whereColumn('qty', '<=', 'reorder_level')
            ->with('product', 'branch')
            ->limit(8)
            ->get();

        // Category performance (for radar chart)
        $categoryPerformance = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', \DB::raw('SUM(sale_items.subtotal) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return view('reports.index', compact(
            'todaySales', 'todayProfit', 'weekSales', 'monthSales', 'yearSales',
            'todayExpenses', 'monthExpenses', 'yearExpenses', 'monthIncome', 'yearIncome',
            'monthProfit', 'yearProfit', 'totalDebts', 'overdueDebts',
            'totalCustomers', 'activeCustomers',
            'annualSalesGoal', 'annualProfitGoal', 'annualExpenseBudget', 'annualCustomerGoal',
            'salesProgress', 'profitProgress', 'expenseProgress', 'customerProgress',
            'monthlySales', 'monthlyExpenses', 'monthlyProfit', 'monthNames',
            'lowStock', 'topProducts', 'topCustomers', 'last7Days', 'salesByMethod',
            'categoryPerformance', 'maxProductQty'
        ));
    }

    public function sales(Request $request)
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $sales = Sale::where('business_id', $businessId)
            ->where('status', 'completed')
            ->with(['items', 'customer', 'user', 'branch'])
            ->when($request->from, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->to, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($request->branch, fn($q, $b) => $q->where('branch_id', $b))
            ->when($request->method, fn($q, $m) => $q->where('payment_method', $m))
            ->orderByDesc('id')
            ->paginate(25);

        $branches = $user->business->branches;
        return view('reports.sales', compact('sales', 'branches'));
    }

    public function inventory()
    {
        $user = auth()->user();
        $branchId = session('active_branch_id') ?? $user->branch_id;

        $stock = BranchStock::whereHas('product', fn($q) => $q->where('business_id', $user->business_id))
            ->where('branch_id', $branchId)
            ->with('product.category', 'branch')
            ->get();

        $stockValue = $stock->sum(fn($s) => $s->qty * $s->product->cost_price);
        $retailValue = $stock->sum(fn($s) => $s->qty * $s->product->selling_price);

        return view('reports.inventory', compact('stock', 'stockValue', 'retailValue'));
    }

    public function profitLoss()
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $monthRevenue = Sale::where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month)->sum('total');
        $monthCost = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month))->sum('cost');
        $grossProfit = $monthRevenue - $monthCost;
        $monthExpenses = Expense::where('business_id', $businessId)->whereMonth('expense_date', now()->month)->sum('amount');
        $netProfit = $grossProfit - $monthExpenses;

        $expensesByCategory = Expense::where('business_id', $businessId)->whereMonth('expense_date', now()->month)
            ->select('category', \DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        return view('reports.profit-loss', compact('monthRevenue', 'monthCost', 'grossProfit', 'monthExpenses', 'netProfit', 'expensesByCategory'));
    }

    public function chartData()
    {
        $user = auth()->user();
        $businessId = $user->business_id;
        $branchId = session('active_branch_id') ?? $user->branch_id;

        $salesQuery = Sale::where('business_id', $businessId)->where('status', 'completed');
        if ($branchId && !$user->isBusinessAdmin()) {
            $salesQuery->where('branch_id', $branchId);
        }

        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = (clone $salesQuery)->whereDate('created_at', $date)->sum('total');
            $last7Days->push(['date' => $date->format('D d'), 'total' => (float) $total]);
        }

        $salesByMethod = (clone $salesQuery)->whereMonth('created_at', now()->month)
            ->select('payment_method', \DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        return response()->json([
            'last7Days' => $last7Days,
            'salesByMethod' => $salesByMethod,
        ]);
    }
}
