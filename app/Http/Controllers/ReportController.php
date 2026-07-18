<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\Product;
use App\Models\BranchStock;
use App\Models\CustomerDebt;
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

        $salesQuery = Sale::where('business_id', $businessId)->where('status', 'completed');
        $branchSalesQuery = clone $salesQuery;
        if ($branchId && !$user->isBusinessAdmin()) {
            $branchSalesQuery->where('branch_id', $branchId);
        }

        $todaySales = (clone $branchSalesQuery)->whereDate('created_at', $today)->sum('total');
        $todayProfit = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereDate('created_at', $today))->sum('profit');
        $weekSales = (clone $branchSalesQuery)->whereBetween('created_at', [$startOfWeek, now()])->sum('total');
        $monthSales = (clone $branchSalesQuery)->whereBetween('created_at', [$startOfMonth, now()])->sum('total');

        $todayExpenses = Expense::where('business_id', $businessId)->whereDate('expense_date', $today)->sum('amount');
        $monthExpenses = Expense::where('business_id', $businessId)->whereBetween('expense_date', [$startOfMonth, now()])->sum('amount');

        $totalDebts = CustomerDebt::where('business_id', $businessId)->whereIn('status', ['pending', 'partial', 'overdue'])->sum('balance');
        $overdueDebts = CustomerDebt::where('business_id', $businessId)->where('status', 'overdue')->sum('balance');

        $lowStock = BranchStock::whereHas('product', fn($q) => $q->where('business_id', $businessId))
            ->whereColumn('qty', '<=', 'reorder_level')
            ->with('product', 'branch')
            ->limit(10)
            ->get();

        $topProducts = SaleItem::whereHas('sale', fn($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month))
            ->select('product_id', \DB::raw('SUM(qty) as total_qty'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = (clone $branchSalesQuery)->whereDate('created_at', $date)->sum('total');
            $last7Days->push(['date' => $date->format('D d'), 'total' => (float) $total]);
        }

        $salesByMethod = (clone $branchSalesQuery)->whereMonth('created_at', now()->month)
            ->select('payment_method', \DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('reports.index', compact(
            'todaySales', 'todayProfit', 'weekSales', 'monthSales',
            'todayExpenses', 'monthExpenses', 'totalDebts', 'overdueDebts',
            'lowStock', 'topProducts', 'last7Days', 'salesByMethod'
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
