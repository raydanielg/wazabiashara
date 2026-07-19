<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BranchStock;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Sales report — mirrors ReportController::sales in the web app.
     */
    public function sales(Request $request)
    {
        $user = $request->user();
        $businessId = $user->business_id;

        $sales = Sale::where('business_id', $businessId)
            ->where('status', 'completed')
            ->with(['items', 'customer', 'user', 'branch'])
            ->when($request->query('from'), fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->query('to'), fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($request->query('branch'), fn ($q, $b) => $q->where('branch_id', $b))
            ->when($request->query('method'), fn ($q, $m) => $q->where('payment_method', $m))
            ->orderByDesc('id')
            ->paginate(25);

        return response()->json([
            'success' => true,
            'sales' => $sales,
        ]);
    }

    /**
     * Inventory report — mirrors ReportController::inventory in the web app.
     */
    public function inventory(Request $request)
    {
        $user = $request->user();
        $branchId = $request->query('branch_id') ?? $user->branch_id;

        $stock = BranchStock::whereHas('product', fn ($q) => $q->where('business_id', $user->business_id))
            ->where('branch_id', $branchId)
            ->with('product.category', 'branch')
            ->get();

        $stockValue = $stock->sum(fn ($s) => $s->qty * $s->product->cost_price);
        $retailValue = $stock->sum(fn ($s) => $s->qty * $s->product->selling_price);

        return response()->json([
            'success' => true,
            'stock' => $stock,
            'stockValue' => $stockValue,
            'retailValue' => $retailValue,
        ]);
    }

    /**
     * Profit & Loss report — mirrors ReportController::profitLoss in the web app.
     */
    public function profitLoss(Request $request)
    {
        $user = $request->user();
        $businessId = $user->business_id;

        $monthRevenue = Sale::where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month)->sum('total');
        $monthCost = SaleItem::whereHas('sale', fn ($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereMonth('created_at', now()->month))->sum('cost');
        $grossProfit = $monthRevenue - $monthCost;
        $monthExpenses = Expense::where('business_id', $businessId)->whereMonth('expense_date', now()->month)->sum('amount');
        $netProfit = $grossProfit - $monthExpenses;

        $expensesByCategory = Expense::where('business_id', $businessId)->whereMonth('expense_date', now()->month)
            ->select('category', \DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        return response()->json([
            'success' => true,
            'monthRevenue' => $monthRevenue,
            'monthCost' => $monthCost,
            'grossProfit' => $grossProfit,
            'monthExpenses' => $monthExpenses,
            'netProfit' => $netProfit,
            'expensesByCategory' => $expensesByCategory,
        ]);
    }

    /**
     * Chart data (last 7 days sales + sales by method) — mirrors ReportController::chartData.
     */
    public function chartData(Request $request)
    {
        $user = $request->user();
        $businessId = $user->business_id;
        $branchId = $request->query('branch_id') ?? $user->branch_id;

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
            'success' => true,
            'last7Days' => $last7Days,
            'salesByMethod' => $salesByMethod,
        ]);
    }
}
