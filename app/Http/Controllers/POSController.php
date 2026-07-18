<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\BranchStock;
use App\Models\StockMovement;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class POSController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessId = $user->business_id;
        $branchId = $user->branch_id ?? session('active_branch_id');

        if (!$branchId) {
            $branchId = $user->business->branches()->first()?->id;
            session(['active_branch_id' => $branchId]);
        }

        $products = Product::where('business_id', $businessId)
            ->where('status', 'active')
            ->with(['category', 'branchStock' => fn($q) => $q->where('branch_id', $branchId)])
            ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('barcode', 'like', "%{$s}%"))
            ->orderBy('name')
            ->get();

        $categories = Category::where('business_id', $businessId)->orderBy('name')->get();
        $customers = Customer::where('business_id', $businessId)->where('status', 'active')->orderBy('name')->get();

        $activeShift = Shift::where('user_id', $user->id)->where('status', 'open')->first();

        return view('pos.index', compact('products', 'categories', 'customers', 'branchId', 'activeShift'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $branchId = session('active_branch_id') ?? auth()->user()->branch_id;

        $products = Product::where('business_id', auth()->user()->business_id)
            ->where('status', 'active')
            ->where(fn($query) => $query->where('name', 'like', "%{$q}%")->orWhere('barcode', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%"))
            ->with(['branchStock' => fn($qq) => $qq->where('branch_id', $branchId)])
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    public function checkout(Request $request)
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
        ]);

        $user = auth()->user();
        $businessId = $user->business_id;
        $branchId = session('active_branch_id') ?? $user->branch_id;

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
                'total' => $total,
                'change' => $change,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function receipt($id)
    {
        $sale = Sale::with(['items.product', 'customer', 'user', 'branch', 'payments'])->findOrFail($id);
        return view('pos.receipt', compact('sale'));
    }

    public function holdSale(Request $request)
    {
        $request->validate(['items' => 'required|array']);
        $user = auth()->user();

        $sale = Sale::create([
            'business_id' => $user->business_id,
            'branch_id' => session('active_branch_id') ?? $user->branch_id,
            'user_id' => $user->id,
            'receipt_no' => 'HOLD-' . Str::random(8),
            'status' => 'held',
            'subtotal' => 0,
            'total' => 0,
            'paid' => 0,
        ]);

        foreach ($request->items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'price' => $item['price'] ?? 0,
                'cost' => 0,
                'subtotal' => ($item['price'] ?? 0) * $item['qty'],
                'profit' => 0,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Muuzo umewekwa kando.']);
    }

    public function heldSales()
    {
        $sales = Sale::where('user_id', auth()->id())->where('status', 'held')->with('items.product')->orderByDesc('id')->get();
        return response()->json($sales);
    }

    public function voidSale(Request $request, $id)
    {
        $sale = Sale::where('id', $id)->where('business_id', auth()->user()->business_id)->firstOrFail();

        if ($sale->status === 'voided') {
            return response()->json(['success' => false, 'message' => 'Muuzo tayari umefutwa.'], 422);
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
                        'user_id' => auth()->id(),
                        'note' => 'Voided sale',
                    ]);
                }
            }
            $sale->update(['status' => 'voided']);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Muuzo umefutwa kikamilifu.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
