<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BranchStock;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Shift;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * List sales for the authenticated user's business (this is the sales/POS resource).
     */
    public function index(Request $request)
    {
        $businessId = $request->user()->business_id;

        $sales = Sale::where('business_id', $businessId)
            ->with(['items.product', 'customer', 'user', 'branch'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s), fn ($q) => $q->where('status', 'completed'))
            ->when($request->query('branch_id'), fn ($q, $b) => $q->where('branch_id', $b))
            ->when($request->query('from'), fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->query('to'), fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'sales' => $sales,
        ]);
    }

    public function show(Request $request, $id)
    {
        $sale = Sale::where('business_id', $request->user()->business_id)
            ->with(['items.product', 'customer', 'user', 'branch', 'payments'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'sale' => $sale,
        ]);
    }

    /**
     * Checkout / create a new completed sale (mirrors POSController::checkout).
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,mpesa,tigo_pesa,airtel_money,halopesa,bank,credit,split',
            'customer_id' => 'nullable|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'payment_ref' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user = $request->user();
        $businessId = $user->business_id;
        $branchId = $request->branch_id ?? $user->branch_id;

        if (!$branchId) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna tawi lililochaguliwa (branch_id required).',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $vatRate = $user->business->vat_rate ?? 0;

            $receiptNo = 'WZ-' . date('ymd') . '-' . str_pad(Sale::where('business_id', $businessId)->whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'business_id' => $businessId,
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'customer_id' => $request->customer_id,
                'shift_id' => Shift::where('user_id', $user->id)->where('status', 'open')->value('id'),
                'receipt_no' => $receiptNo,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $stock = BranchStock::where('product_id', $item['product_id'])->where('branch_id', $branchId)->first();

                if (!$stock || $stock->qty < $item['qty']) {
                    throw new \Exception("Bidhaa '{$product->name}' haitoshi kwenye stoo.");
                }

                $itemSubtotal = $product->selling_price * $item['qty'];
                $itemCost = $product->cost_price * $item['qty'];
                $itemProfit = $itemSubtotal - $itemCost;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $product->selling_price,
                    'cost' => $product->cost_price,
                    'subtotal' => $itemSubtotal,
                    'profit' => $itemProfit,
                ]);

                $stock->decrement('qty', $item['qty']);

                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'branch_id' => $branchId,
                    'type' => 'out',
                    'qty' => $item['qty'],
                    'reference' => $receiptNo,
                    'user_id' => $user->id,
                ]);

                $subtotal += $itemSubtotal;
            }

            $discount = $request->discount ?? 0;
            $vat = $vatRate > 0 ? ($subtotal - $discount) * ($vatRate / 100) : 0;
            $total = $subtotal - $discount + $vat;
            $change = max(0, $request->paid - $total);

            $sale->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'vat' => $vat,
                'total' => $total,
                'paid' => $request->paid,
                'change' => $change,
            ]);

            SalePayment::create([
                'sale_id' => $sale->id,
                'method' => $request->payment_method,
                'amount' => $request->paid,
                'reference' => $request->payment_ref,
            ]);

            if ($request->payment_method === 'credit' && $request->customer_id) {
                $customer = Customer::find($request->customer_id);
                CustomerDebt::create([
                    'customer_id' => $request->customer_id,
                    'sale_id' => $sale->id,
                    'business_id' => $businessId,
                    'amount' => $total,
                    'balance' => $total,
                    'due_date' => now()->addDays(30),
                    'status' => 'pending',
                ]);
                $customer->increment('current_debt', $total);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Muuzo umekamilika!',
                'receipt_no' => $receiptNo,
                'sale_id' => $sale->id,
                'sale' => $sale->fresh(['items.product', 'customer', 'payments']),
                'total' => $total,
                'change' => $change,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Void a completed sale and restock its items.
     */
    public function void(Request $request, $id)
    {
        $sale = Sale::where('id', $id)->where('business_id', $request->user()->business_id)->firstOrFail();

        if ($sale->status === 'voided') {
            return response()->json([
                'success' => false,
                'message' => 'Muuzo tayari umefutwa.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($sale->items as $item) {
                $stock = BranchStock::where('product_id', $item->product_id)->where('branch_id', $sale->branch_id)->first();
                if ($stock) {
                    $stock->increment('qty', $item->qty);
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'branch_id' => $sale->branch_id,
                        'type' => 'adjustment',
                        'qty' => $item->qty,
                        'reference' => 'VOID-' . $sale->receipt_no,
                        'user_id' => $request->user()->id,
                        'note' => 'Voided sale',
                    ]);
                }
            }
            $sale->update(['status' => 'voided']);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Muuzo umefutwa kikamilifu.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
