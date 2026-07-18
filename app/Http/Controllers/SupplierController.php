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
        $suppliers = Supplier::where('business_id', auth()->user()->business_id)
            ->withCount('purchases')
            ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        return view('suppliers.index', compact('suppliers'));
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

        return redirect()->route('suppliers.index')->with('success', 'Msambazaji ameongezwa!');
    }

    public function update(Request $request, Supplier $supplier)
    {
        if ($supplier->business_id !== auth()->user()->business_id) abort(403);
        $request->validate(['name' => 'required|string|max:255']);
        $supplier->update($request->only(['name', 'phone', 'email', 'address', 'status']));
        return redirect()->route('suppliers.index')->with('success', 'Msambazaji amesasishwa!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->business_id !== auth()->user()->business_id) abort(403);
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Msambazaji amefutwa.');
    }

    public function purchases()
    {
        $purchases = Purchase::where('business_id', auth()->user()->business_id)
            ->with(['supplier', 'branch', 'items.product'])
            ->orderByDesc('id')
            ->paginate(20);

        $suppliers = Supplier::where('business_id', auth()->user()->business_id)->where('status', 'active')->get();
        $branches = auth()->user()->business->branches;
        $products = Product::where('business_id', auth()->user()->business_id)->where('status', 'active')->orderBy('name')->get();

        return view('suppliers.purchases', compact('purchases', 'suppliers', 'branches', 'products'));
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
