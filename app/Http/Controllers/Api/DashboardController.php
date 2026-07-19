<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BranchStock;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Home-screen data for the mobile app. The response shape here is a
     * flat, snake_case object matching mobile/lib/models/dashboard_data.dart
     * exactly (DashboardData.fromJson) — this is NOT the same shape as the
     * web dashboard's HomeController, which has its own view-model.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return response()->json([
                'success' => false,
                'message' => 'Mtumiaji hana biashara iliyosajiliwa.',
            ], 422);
        }

        $branchId = $request->query('branch_id') ?? $user->branch_id;
        $today = today();
        $startOfMonth = now()->startOfMonth();

        $salesQuery = Sale::where('business_id', $businessId)->where('status', 'completed');
        if ($branchId && !$user->isBusinessAdmin() && !$user->isAdmin()) {
            $salesQuery->where('branch_id', $branchId);
        }

        $todaySales = (float) (clone $salesQuery)->whereDate('created_at', $today)->sum('total');
        $monthSales = (float) (clone $salesQuery)->whereBetween('created_at', [$startOfMonth, now()])->sum('total');
        $monthPurchases = (float) Purchase::where('business_id', $businessId)->whereBetween('created_at', [$startOfMonth, now()])->sum('total');
        $monthExpenses = (float) Expense::where('business_id', $businessId)->whereBetween('expense_date', [$startOfMonth, now()])->sum('amount');

        $accounts = Account::where('business_id', $businessId)->where('is_active', true)->get();
        $cashBalance = (float) $accounts->where('type', 'cash')->sum('current_balance');
        $bankBalance = (float) $accounts->where('type', 'bank')->sum('current_balance');
        $mobileBalance = (float) $accounts->where('type', 'mobile_money')->sum('current_balance');

        $totalReceivables = (float) CustomerDebt::where('business_id', $businessId)->whereIn('status', ['pending', 'partial', 'overdue'])->sum('balance');
        $totalPayables = (float) Supplier::where('business_id', $businessId)->sum('balance');

        $totalProducts = Product::where('business_id', $businessId)->where('status', 'active')->count();
        $lowStockCount = BranchStock::whereHas('product', fn ($q) => $q->where('business_id', $businessId))
            ->whereColumn('qty', '<=', 'reorder_level')->count();
        $totalCustomers = Customer::where('business_id', $businessId)->count();

        // 7-day per-metric trend series, all sharing the same weekday labels
        // so they line up on the mobile Home tab's KPI-detail charts.
        $salesChart = [];
        $purchasesChart = [];
        $expensesChart = [];
        $cashflowIn = [];
        $cashflowOut = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $label = $date->format('D');

            $daySales = (float) (clone $salesQuery)->whereDate('created_at', $date)->sum('total');
            $dayPurchases = (float) Purchase::where('business_id', $businessId)->whereDate('created_at', $date)->sum('total');
            $dayExpenses = (float) Expense::where('business_id', $businessId)->whereDate('expense_date', $date)->sum('amount');
            $dayIncome = (float) Income::where('business_id', $businessId)->whereDate('income_date', $date)->sum('amount');
            $dayPaymentsIn = (float) Payment::where('business_id', $businessId)->where('type', 'in')->whereDate('payment_date', $date)->sum('amount');
            $dayPaymentsOut = (float) Payment::where('business_id', $businessId)->where('type', 'out')->whereDate('payment_date', $date)->sum('amount');

            $salesChart[] = ['label' => $label, 'value' => $daySales];
            $purchasesChart[] = ['label' => $label, 'value' => $dayPurchases];
            $expensesChart[] = ['label' => $label, 'value' => $dayExpenses];
            $cashflowIn[] = ['label' => $label, 'value' => $daySales + $dayIncome + $dayPaymentsIn];
            $cashflowOut[] = ['label' => $label, 'value' => $dayPurchases + $dayExpenses + $dayPaymentsOut];
        }

        $recentSales = (clone $salesQuery)->orderByDesc('id')->take(8)->get()->map(fn ($sale) => [
            'id' => $sale->id,
            'receipt_no' => $sale->receipt_no,
            'total' => (float) $sale->total,
            'payment_method' => $sale->payment_method,
            'date' => optional($sale->created_at)->toIso8601String(),
        ]);

        $topProducts = SaleItem::whereHas('sale', fn ($q) => $q->where('business_id', $businessId)->where('status', 'completed')->whereBetween('created_at', [$startOfMonth, now()]))
            ->select('product_id', \DB::raw('SUM(qty) as total_qty'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->product_id,
                'name' => $row->product?->name ?? 'Unknown',
                'qty_sold' => (int) $row->total_qty,
                'revenue' => (float) $row->total_revenue,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'today_sales' => $todaySales,
                'month_sales' => $monthSales,
                'total_products' => $totalProducts,
                'low_stock_count' => $lowStockCount,
                'total_customers' => $totalCustomers,
                'cash_balance' => $cashBalance,
                'bank_balance' => $bankBalance,
                'mobile_balance' => $mobileBalance,
                'recent_sales' => $recentSales,
                'top_products' => $topProducts,
                'sales_chart' => $salesChart,
                'total_receivables' => $totalReceivables,
                'total_payables' => $totalPayables,
                'month_purchases' => $monthPurchases,
                'month_expenses' => $monthExpenses,
                'cashflow_in' => $cashflowIn,
                'cashflow_out' => $cashflowOut,
                'purchases_chart' => $purchasesChart,
                'expenses_chart' => $expensesChart,
            ],
        ]);
    }
}
