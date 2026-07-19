<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BranchStock;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $branchId = $request->query('branch_id') ?? $user->branch_id;

        $products = Product::where('business_id', $user->business_id)
            ->with(['category', 'branchStock' => fn ($q) => $q->where('branch_id', $branchId)])
            ->when($request->query('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('barcode', 'like', "%{$s}%"))
            ->when($request->query('category'), fn ($q, $c) => $q->where('category_id', $c))
            ->orderByDesc('id')
            ->get()
            ->map(function ($product) use ($branchId) {
                $product->stock = (int) ($product->branchStock->first()?->qty ?? 0);
                // Mobile's Product model expects a plain category name string,
                // not the full relation object.
                $product->category = $product->category?->name;
                return $product;
            });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'category' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'stock' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
            'initial_stock' => 'nullable|array',
            'initial_stock.*' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();
        $categoryId = $request->category_id ?? $this->resolveCategoryId($request->category, $user->business_id);

        $product = Product::create([
            'business_id' => $user->business_id,
            'category_id' => $categoryId,
            'name' => $request->name,
            'barcode' => $request->barcode,
            'sku' => $request->sku,
            'unit' => $request->unit ?? 'piece',
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'reorder_level' => $request->reorder_level ?? 5,
            'expiry_date' => $request->expiry_date,
            'status' => 'active',
        ]);

        // Mobile's Add Item screen (single-branch, no branch picker yet) sends
        // a flat "stock" number for the user's own branch.
        $branchId = $request->branch_id ?? $user->branch_id;
        if ($branchId && $request->filled('stock') && $request->stock > 0) {
            $this->applyInitialStock($product, $branchId, (float) $request->stock, $user->id, $request->reorder_level ?? 5);
        }

        if ($request->initial_stock) {
            foreach ($request->initial_stock as $bId => $qty) {
                if ($qty > 0) {
                    $this->applyInitialStock($product, $bId, (float) $qty, $user->id, $request->reorder_level ?? 5);
                }
            }
        }

        $product->load('category');
        $product->stock = (int) $product->branchStock()->where('branch_id', $branchId)->value('qty') ?? 0;
        $product->category = $product->category?->name;

        return response()->json([
            'success' => true,
            'message' => 'Bidhaa imeongezwa kikamilifu!',
            'product' => $product,
        ], 201);
    }

    private function applyInitialStock(Product $product, $branchId, float $qty, int $userId, int $reorderLevel): void
    {
        BranchStock::create([
            'product_id' => $product->id,
            'branch_id' => $branchId,
            'qty' => $qty,
            'reorder_level' => $reorderLevel,
        ]);
        StockMovement::create([
            'product_id' => $product->id,
            'branch_id' => $branchId,
            'type' => 'in',
            'qty' => $qty,
            'reference' => 'INITIAL',
            'user_id' => $userId,
            'note' => 'Stoo ya mwanzo',
        ]);
    }

    /**
     * Resolve a free-text category name (as sent by the mobile Add Item
     * screen) into a category_id, creating an "item"-type category on the
     * fly if it doesn't exist yet for this business.
     */
    private function resolveCategoryId(?string $name, int $businessId): ?int
    {
        $name = trim((string) $name);
        if ($name === '' || strtolower($name) === 'general') return null;

        return Category::firstOrCreate(
            ['business_id' => $businessId, 'type' => 'item', 'name' => $name],
        )->id;
    }

    public function show(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $product->load('category');
        $product->stock = $product->branchStock()->get()->keyBy('branch_id');

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'category' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'stock' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'nullable|in:active,inactive',
        ]);

        $user = $request->user();
        $categoryId = $request->category_id ?? ($request->filled('category') ? $this->resolveCategoryId($request->category, $user->business_id) : $product->category_id);

        $product->update([
            ...$request->only(['name', 'barcode', 'sku', 'unit', 'cost_price', 'selling_price', 'reorder_level', 'expiry_date']),
            'category_id' => $categoryId,
            'status' => $request->status ?? $product->status,
        ]);

        $branchId = $request->branch_id ?? $user->branch_id;
        if ($branchId && $request->filled('stock')) {
            $stock = BranchStock::firstOrCreate(
                ['product_id' => $product->id, 'branch_id' => $branchId],
                ['qty' => 0, 'reorder_level' => $request->reorder_level ?? 5]
            );
            $stock->update(['qty' => (float) $request->stock]);
        }

        $product->load('category');
        $product->stock = (int) ($product->branchStock()->where('branch_id', $branchId)->value('qty') ?? 0);
        $product->category = $product->category?->name;

        return response()->json([
            'success' => true,
            'message' => 'Bidhaa imesasishwa kikamilifu!',
            'product' => $product,
        ]);
    }

    public function destroy(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bidhaa imefutwa.',
        ]);
    }

    public function adjustStock(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

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
            'user_id' => $request->user()->id,
            'note' => $request->note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stoo imesasishwa.',
            'stock' => $stock->fresh(),
        ]);
    }

    private function authorizeProduct(Request $request, Product $product): void
    {
        if ($product->business_id !== $request->user()->business_id) {
            abort(403);
        }
    }
}
