<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\BranchStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $saleReturns = SaleReturn::whereHas('sale', fn($q) => $q->where('business_id', $businessId))
            ->with(['sale.customer', 'product', 'user'])
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'sr_page');

        $purchaseReturns = PurchaseReturn::where('business_id', $businessId)
            ->with(['purchase.supplier', 'supplier', 'items.product'])
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'pr_page');

        $totalSaleReturns = SaleReturn::whereHas('sale', fn($q) => $q->where('business_id', $businessId))->sum('total');
        $totalPurchaseReturns = PurchaseReturn::where('business_id', $businessId)->sum('total');
        $pendingReturns = SaleReturn::whereHas('sale', fn($q) => $q->where('business_id', $businessId))->where('status', 'pending')->count() + PurchaseReturn::where('business_id', $businessId)->where('status', 'pending')->count();

        return view('returns.index', compact('saleReturns', 'purchaseReturns', 'totalSaleReturns', 'totalPurchaseReturns', 'pendingReturns'));
    }

    public function storeSaleReturn(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();
        $sale = Sale::where('business_id', $user->business_id)->findOrFail($request->sale_id);

        DB::beginTransaction();
        try {
            $reference = 'SR-' . date('ymd') . '-' . str_pad(SaleReturn::whereHas('sale', fn($q) => $q->where('business_id', $user->business_id))->whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            $total = 0;

            $return = SaleReturn::create([
                'sale_id' => $sale->id,
                'product_id' => $request->items[0]['product_id'],
                'qty' => $request->items[0]['qty'],
                'amount' => 0,
                'reason' => $request->reason,
                'approved_by' => $user->id,
                'reference' => $reference,
                'status' => 'approved',
                'total' => 0,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['qty'] * $item['price'];
                $total += $subtotal;

                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                $stock = BranchStock::where('product_id', $item['product_id'])->where('branch_id', $sale->branch_id)->first();
                if ($stock) {
                    $stock->increment('qty', $item['qty']);
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'branch_id' => $sale->branch_id,
                        'type' => 'in',
                        'qty' => $item['qty'],
                        'reference' => $reference,
                        'user_id' => $user->id,
                        'note' => 'Sale return: ' . $sale->receipt_no,
                    ]);
                }
            }

            $return->update(['total' => $total, 'amount' => $total]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Rudisha mauzo imerekodiwa!']);
            }
            return redirect()->route('returns.index')->with('success', 'Rudisha mauzo imerekodiwa!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function storePurchaseReturn(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();
        $purchase = Purchase::where('business_id', $user->business_id)->findOrFail($request->purchase_id);

        DB::beginTransaction();
        try {
            $reference = 'PR-' . date('ymd') . '-' . str_pad(PurchaseReturn::where('business_id', $user->business_id)->whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            $total = 0;

            $return = PurchaseReturn::create([
                'business_id' => $user->business_id,
                'branch_id' => $purchase->branch_id,
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'user_id' => $user->id,
                'reference' => $reference,
                'reason' => $request->reason,
                'status' => 'approved',
                'total' => 0,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['qty'] * $item['price'];
                $total += $subtotal;

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                $stock = BranchStock::where('product_id', $item['product_id'])->where('branch_id', $purchase->branch_id)->first();
                if ($stock) {
                    $stock->decrement('qty', $item['qty']);
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'branch_id' => $purchase->branch_id,
                        'type' => 'out',
                        'qty' => $item['qty'],
                        'reference' => $reference,
                        'user_id' => $user->id,
                        'note' => 'Purchase return: ' . $purchase->reference,
                    ]);
                }
            }

            $return->update(['total' => $total]);

            if ($purchase->supplier) {
                $purchase->supplier->increment('balance', $total);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Rudisha manunuzi imerekodiwa!']);
            }
            return redirect()->route('returns.index')->with('success', 'Rudisha manunuzi imerekodiwa!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
