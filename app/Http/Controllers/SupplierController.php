<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\BranchStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        $suppliers = Supplier::where('business_id', $businessId)
            ->withCount('purchases')
            ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        $totalSuppliers = Supplier::where('business_id', $businessId)->count();
        $activeSuppliers = Supplier::where('business_id', $businessId)->where('status', 'active')->count();
        $totalBalance = Supplier::where('business_id', $businessId)->sum('balance');
        $totalPurchases = Purchase::where('business_id', $businessId)->count();

        return view('suppliers.index', compact('suppliers', 'totalSuppliers', 'activeSuppliers', 'totalBalance', 'totalPurchases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        Supplier::create([
            'business_id' => auth()->user()->business_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Msambazaji ameongezwa!']);
        }
        return redirect()->route('suppliers.index')->with('success', 'Msambazaji ameongezwa!');
    }

    public function update(Request $request, Supplier $supplier)
    {
        if ($supplier->business_id !== auth()->user()->business_id) abort(403);
        $request->validate(['name' => 'required|string|max:255']);
        $supplier->update($request->only(['name', 'phone', 'email', 'address', 'status']));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Msambazaji amesasishwa!']);
        }
        return redirect()->route('suppliers.index')->with('success', 'Msambazaji amesasishwa!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->business_id !== auth()->user()->business_id) abort(403);
        $supplier->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Msambazaji amefutwa.']);
        }
        return redirect()->route('suppliers.index')->with('success', 'Msambazaji amefutwa.');
    }

    public function purchases()
    {
        $businessId = auth()->user()->business_id;
        $purchases = Purchase::where('business_id', $businessId)
            ->with(['supplier', 'branch', 'items.product'])
            ->orderByDesc('id')
            ->paginate(20);

        $suppliers = Supplier::where('business_id', $businessId)->where('status', 'active')->get();
        $branches = auth()->user()->business->branches;
        $products = Product::where('business_id', $businessId)->where('status', 'active')->orderBy('name')->get();

        $totalPurchases = Purchase::where('business_id', $businessId)->count();
        $monthPurchases = Purchase::where('business_id', $businessId)->whereMonth('created_at', now()->month)->count();
        $monthTotal = Purchase::where('business_id', $businessId)->whereMonth('created_at', now()->month)->sum('total');
        $creditPurchases = Purchase::where('business_id', $businessId)->where('payment_status', 'credit')->count();

        return view('suppliers.purchases', compact('purchases', 'suppliers', 'branches', 'products', 'totalPurchases', 'monthPurchases', 'monthTotal', 'creditPurchases'));
    }

    public function storePurchase(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'payment_status' => 'required|in:paid,credit,partial',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            $purchase = Purchase::create([
                'business_id' => auth()->user()->business_id,
                'branch_id' => $request->branch_id,
                'supplier_id' => $request->supplier_id,
                'payment_status' => $request->payment_status,
                'status' => 'received',
                'user_id' => auth()->id(),
                'total' => 0,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['cost_price'] * $item['qty'];
                $total += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'cost_price' => $item['cost_price'],
                    'subtotal' => $subtotal,
                ]);

                $stock = BranchStock::firstOrCreate(
                    ['product_id' => $item['product_id'], 'branch_id' => $request->branch_id],
                    ['qty' => 0]
                );
                $stock->increment('qty', $item['qty']);

                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'branch_id' => $request->branch_id,
                    'type' => 'in',
                    'qty' => $item['qty'],
                    'reference' => 'PUR-' . $purchase->id,
                    'user_id' => auth()->id(),
                    'note' => 'Manunuzi kutoka ' . ($purchase->supplier?->name ?? 'Nje'),
                ]);

                Product::where('id', $item['product_id'])->update(['cost_price' => $item['cost_price']]);
            }

            $purchase->update(['total' => $total]);

            if ($request->supplier_id && $request->payment_status === 'credit') {
                Supplier::find($request->supplier_id)->increment('balance', $total);
            }

            DB::commit();
            return redirect()->route('suppliers.purchases')->with('success', 'Manunuzi yamerekodiwa kikamilifu!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
