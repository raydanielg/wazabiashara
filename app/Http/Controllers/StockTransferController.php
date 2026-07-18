<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\BranchStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::where('business_id', auth()->user()->business_id)
            ->with(['fromBranch', 'toBranch', 'items.product', 'requestedBy', 'approvedBy'])
            ->orderByDesc('id')
            ->paginate(20);

        $branches = auth()->user()->business->branches;

        return view('stock-transfers.index', compact('transfers', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_branch_id' => 'required|exists:branches,id|different:to_branch_id',
            'to_branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $user = auth()->user();

        DB::beginTransaction();
        try {
            $transfer = StockTransfer::create([
                'business_id' => $user->business_id,
                'from_branch_id' => $request->from_branch_id,
                'to_branch_id' => $request->to_branch_id,
                'status' => $user->isBusinessAdmin() ? 'approved' : 'pending',
                'requested_by' => $user->id,
                'approved_by' => $user->isBusinessAdmin() ? $user->id : null,
                'note' => $request->note,
            ]);

            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'qty_requested' => $item['qty'],
                ]);
            }

            DB::commit();
            return redirect()->route('stock-transfers.index')->with('success', 'Ombi la uhamisho limewekwa!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve(StockTransfer $transfer)
    {
        if ($transfer->business_id !== auth()->user()->business_id) abort(403);
        if ($transfer->status !== 'pending') return redirect()->back()->with('error', 'Ombi haliwezi kuidhinishwa.');

        $transfer->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        return redirect()->back()->with('success', 'Ombi limeidhinishwa!');
    }

    public function ship(StockTransfer $transfer)
    {
        if ($transfer->business_id !== auth()->user()->business_id) abort(403);
        if ($transfer->status !== 'approved') return redirect()->back()->with('error', 'Ombi halijaidhinishwa.');

        DB::beginTransaction();
        try {
            foreach ($transfer->items as $item) {
                $stock = BranchStock::where('product_id', $item->product_id)->where('branch_id', $transfer->from_branch_id)->first();
                if (!$stock || $stock->qty < $item->qty_requested) {
                    throw new \Exception('Stoo haiitosi kwa ' . $item->product->name);
                }
                $stock->decrement('qty', $item->qty_requested);
                $item->update(['qty_sent' => $item->qty_requested]);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'branch_id' => $transfer->from_branch_id,
                    'type' => 'transfer',
                    'qty' => -$item->qty_requested,
                    'reference' => 'TRF-' . $transfer->id,
                    'user_id' => auth()->id(),
                    'note' => 'Uhamisho kwaka ' . $transfer->toBranch->name,
                ]);
            }
            $transfer->update(['status' => 'shipped']);
            DB::commit();
            return redirect()->back()->with('success', 'Bidhaa zimetumwa!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function receive(StockTransfer $transfer)
    {
        if ($transfer->business_id !== auth()->user()->business_id) abort(403);
        if ($transfer->status !== 'shipped') return redirect()->back()->with('error', 'Bidhaa hazijatumwa bado.');

        DB::beginTransaction();
        try {
            foreach ($transfer->items as $item) {
                $stock = BranchStock::firstOrCreate(
                    ['product_id' => $item->product_id, 'branch_id' => $transfer->to_branch_id],
                    ['qty' => 0]
                );
                $stock->increment('qty', $item->qty_sent);
                $item->update(['qty_received' => $item->qty_sent]);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'branch_id' => $transfer->to_branch_id,
                    'type' => 'transfer',
                    'qty' => $item->qty_sent,
                    'reference' => 'TRF-' . $transfer->id,
                    'user_id' => auth()->id(),
                    'note' => 'Uhamisho kutoka ' . $transfer->fromBranch->name,
                ]);
            }
            $transfer->update(['status' => 'received']);
            DB::commit();
            return redirect()->back()->with('success', 'Bidhaa zimepokelewa!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(StockTransfer $transfer)
    {
        if ($transfer->business_id !== auth()->user()->business_id) abort(403);
        $transfer->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Ombi limekataliwa.');
    }
}
