<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\BranchStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $branchId = session('active_branch_id') ?? $user->branch_id;

        $products = Product::where('business_id', $user->business_id)
            ->with(['category', 'branchStock' => fn($q) => $q->where('branch_id', $branchId)])
            ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('barcode', 'like', "%{$s}%"))
            ->when(request('category'), fn($q, $c) => $q->where('category_id', $c))
            ->orderByDesc('id')
            ->paginate(20);

        $categories = Category::where('business_id', $user->business_id)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'branchId'));
    }

    public function create()
    {
        $categories = Category::where('business_id', auth()->user()->business_id)->orderBy('name')->get();
        $branches = auth()->user()->business->branches;
        return view('products.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'initial_stock' => 'nullable|array',
            'initial_stock.*' => 'nullable|numeric|min:0',
        ]);

        $product = Product::create([
            'business_id' => auth()->user()->business_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'barcode' => $request->barcode,
            'sku' => $request->sku,
            'unit' => $request->unit,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'reorder_level' => $request->reorder_level ?? 5,
            'expiry_date' => $request->expiry_date,
            'status' => 'active',
        ]);

        if ($request->initial_stock) {
            foreach ($request->initial_stock as $branchId => $qty) {
                if ($qty > 0) {
                    BranchStock::create([
                        'product_id' => $product->id,
                        'branch_id' => $branchId,
                        'qty' => $qty,
                        'reorder_level' => $request->reorder_level ?? 5,
                    ]);
                    StockMovement::create([
                        'product_id' => $product->id,
                        'branch_id' => $branchId,
                        'type' => 'in',
                        'qty' => $qty,
                        'reference' => 'INITIAL',
                        'user_id' => auth()->id(),
                        'note' => 'Stoo ya mwanzo',
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Bidhaa imeongezwa kikamilifu!');
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);
        $categories = Category::where('business_id', auth()->user()->business_id)->orderBy('name')->get();
        $branches = auth()->user()->business->branches;
        $stock = $product->branchStock()->get()->keyBy('branch_id');
        return view('products.edit', compact('product', 'categories', 'branches', 'stock'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        $product->update($request->only(['name', 'barcode', 'sku', 'category_id', 'unit', 'cost_price', 'selling_price', 'reorder_level', 'expiry_date', 'status']));

        return redirect()->route('products.index')->with('success', 'Bidhaa imesasishwa kikamilifu!');
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Bidhaa imefutwa.');
    }

    public function adjustStock(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'qty' => 'required|numeric',
            'type' => 'required|in:in,out,adjustment',
            'note' => 'nullable|string',
        ]);

        $stock = BranchStock::firstOrCreate(
            ['product_id' => $product->id, 'branch_id' => $request->branch_id],
            ['qty' => 0]
        );

        if ($request->type === 'in') {
            $stock->increment('qty', $request->qty);
        } elseif ($request->type === 'out') {
            $stock->decrement('qty', $request->qty);
        } else {
            $stock->update(['qty' => $request->qty]);
        }

        StockMovement::create([
            'product_id' => $product->id,
            'branch_id' => $request->branch_id,
            'type' => $request->type,
            'qty' => $request->qty,
            'user_id' => auth()->id(),
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'Stoo imesasishwa.');
    }

    private function authorizeProduct($product)
    {
        if ($product->business_id !== auth()->user()->business_id) {
            abort(403);
        }
    }
}
