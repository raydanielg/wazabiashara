<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\BranchStock;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $quotations = Quotation::where('business_id', $businessId)
            ->with(['customer', 'user', 'branch'])
            ->when(request('search'), fn($q, $s) => $q->where('quotation_no', 'like', "%{$s}%")->orWhereHas('customer', fn($qq) => $qq->where('name', 'like', "%{$s}%")))
            ->orderByDesc('id')
            ->paginate(20);

        $totalQuotations = Quotation::where('business_id', $businessId)->count();
        $pendingQuotations = Quotation::where('business_id', $businessId)->where('status', 'draft')->count();
        $convertedQuotations = Quotation::where('business_id', $businessId)->whereNotNull('converted_at')->count();
        $totalValue = Quotation::where('business_id', $businessId)->sum('total');

        $customers = Customer::where('business_id', $businessId)->where('status', 'active')->orderBy('name')->get();
        $products = Product::where('business_id', $businessId)->where('status', 'active')->orderBy('name')->get();

        return view('quotations.index', compact('quotations', 'totalQuotations', 'pendingQuotations', 'convertedQuotations', 'totalValue', 'customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'valid_until' => 'nullable|date',
        ]);

        $user = auth()->user();
        $businessId = $user->business_id;

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $quotationNo = 'QT-' . date('ymd') . '-' . str_pad(Quotation::where('business_id', $businessId)->whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'business_id' => $businessId,
                'branch_id' => session('active_branch_id') ?? $user->branch_id,
                'user_id' => $user->id,
                'customer_id' => $request->customer_id,
                'quotation_no' => $quotationNo,
                'status' => 'draft',
                'notes' => $request->notes,
                'valid_until' => $request->valid_until,
            ]);

            foreach ($request->items as $item) {
                $itemSubtotal = $item['qty'] * $item['price'] - ($item['discount'] ?? 0);
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'] ?? null,
                    'name' => $item['name'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                ]);
                $subtotal += $itemSubtotal;
            }

            $discount = $request->discount ?? 0;
            $vatRate = $user->business->vat_rate ?? 0;
            $vat = $vatRate > 0 ? ($subtotal - $discount) * ($vatRate / 100) : 0;
            $total = $subtotal - $discount + $vat;

            $quotation->update(['subtotal' => $subtotal, 'discount' => $discount, 'vat' => $vat, 'total' => $total]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Quotation imetengenezwa!', 'quotation_id' => $quotation->id]);
            }
            return redirect()->route('quotations.index')->with('success', 'Quotation imetengenezwa!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Quotation $quotation)
    {
        if ($quotation->business_id !== auth()->user()->business_id) abort(403);
        $quotation->load(['items.product', 'customer', 'user', 'branch']);
        return view('quotations.show', compact('quotation'));
    }

    public function convert(Quotation $quotation)
    {
        if ($quotation->business_id !== auth()->user()->business_id) abort(403);
        if ($quotation->converted_at) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Quotation tayari imegeuzwa invoice.'], 422);
            }
            return back()->with('error', 'Quotation tayari imegeuzwa invoice.');
        }

        $user = auth()->user();
        $branchId = session('active_branch_id') ?? $user->branch_id;

        DB::beginTransaction();
        try {
            $receiptNo = 'WZ-' . date('ymd') . '-' . str_pad(Sale::where('business_id', $quotation->business_id)->whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'business_id' => $quotation->business_id,
                'branch_id' => $quotation->branch_id ?? $branchId,
                'user_id' => $user->id,
                'customer_id' => $quotation->customer_id,
                'receipt_no' => $receiptNo,
                'subtotal' => $quotation->subtotal,
                'discount' => $quotation->discount,
                'vat' => $quotation->vat,
                'total' => $quotation->total,
                'paid' => 0,
                'change' => 0,
                'payment_method' => 'credit',
                'status' => 'completed',
            ]);

            foreach ($quotation->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'cost' => $item->product?->cost_price ?? 0,
                    'subtotal' => $item->subtotal,
                    'profit' => $item->subtotal - ($item->product?->cost_price ?? 0) * $item->qty,
                ]);

                if ($item->product_id) {
                    $stock = BranchStock::where('product_id', $item->product_id)->where('branch_id', $sale->branch_id)->first();
                    if ($stock) {
                        $stock->decrement('qty', $item->qty);
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'branch_id' => $sale->branch_id,
                            'type' => 'out',
                            'qty' => $item->qty,
                            'reference' => $receiptNo,
                            'user_id' => $user->id,
                        ]);
                    }
                }
            }

            $quotation->update(['status' => 'converted', 'converted_at' => now()]);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Quotation imegeuzwa invoice!', 'sale_id' => $sale->id]);
            }
            return redirect()->route('pos.receipt', $sale->id)->with('success', 'Quotation imegeuzwa invoice!');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Quotation $quotation)
    {
        if ($quotation->business_id !== auth()->user()->business_id) abort(403);
        $quotation->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Quotation imefutwa.']);
        }
        return redirect()->route('quotations.index')->with('success', 'Quotation imefutwa.');
    }
}
