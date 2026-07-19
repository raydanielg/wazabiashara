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
            'data' => $sales->items(),
            'meta' => [
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'total' => $sales->total(),
            ],
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
     * Chart data for the mobile "Reports" screen — bucketed by the
     * requested period (week / month / year), plus a summary, payment-method
     * split, and top categories by revenue. Kept under generic {label,value}
     * pairs so it maps directly onto the mobile ChartData model.
     */
    public function chartData(Request $request)
    {
        $user = $request->user();
        $businessId = $user->business_id;
        $branchId = $request->query('branch_id') ?? $user->branch_id;
        $period = $request->query('period', 'week');

        $salesQuery = Sale::where('business_id', $businessId)->where('status', 'completed');
        if ($branchId && !$user->isBusinessAdmin()) {
            $salesQuery->where('branch_id', $branchId);
        }

        $series = collect();
        $rangeStart = now();

        if ($period === 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $total = (clone $salesQuery)->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->sum('total');
                $series->push(['label' => $date->format('M'), 'value' => (float) $total]);
            }
            $rangeStart = now()->subMonths(11)->startOfMonth();
        } elseif ($period === 'month') {
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();
                $total = (clone $salesQuery)->whereBetween('created_at', [$weekStart, $weekEnd])->sum('total');
                $series->push(['label' => 'W' . (4 - $i), 'value' => (float) $total]);
            }
            $rangeStart = now()->subWeeks(3)->startOfWeek();
        } else {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $total = (clone $salesQuery)->whereDate('created_at', $date)->sum('total');
                $series->push(['label' => $date->format('D'), 'value' => (float) $total]);
            }
            $rangeStart = now()->subDays(6)->startOfDay();
        }

        $periodQuery = (clone $salesQuery)->where('created_at', '>=', $rangeStart);

        $paymentMethods = (clone $periodQuery)
            ->select('payment_method', \DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get()
            ->map(fn ($r) => ['label' => ucfirst(str_replace('_', ' ', $r->payment_method)), 'value' => (float) $r->total]);

        $topCategories = SaleItem::whereHas('sale', fn ($q) => $q->where('business_id', $businessId)->where('status', 'completed')->where('created_at', '>=', $rangeStart))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(\DB::raw('COALESCE(categories.name, "Uncategorized") as label'), \DB::raw('SUM(sale_items.subtotal) as value'))
            ->groupBy('label')
            ->orderByDesc('value')
            ->limit(6)
            ->get()
            ->map(fn ($r) => ['label' => $r->label, 'value' => (float) $r->value]);

        $values = $series->pluck('value');
        $summary = [
            'total' => (float) $values->sum(),
            'average' => $values->isNotEmpty() ? (float) $values->avg() : 0,
            'peak' => (float) $values->max(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'series' => $series,
                'summary' => $summary,
                'paymentMethods' => $paymentMethods,
                'topCategories' => $topCategories,
            ],
        ]);
    }
}
