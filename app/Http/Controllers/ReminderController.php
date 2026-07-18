<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\CustomerDebt;
use App\Models\BranchStock;
use App\Models\Product;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $reminders = Reminder::where('business_id', $businessId)
            ->orderBy('remind_at')
            ->paginate(20);

        $overdueDebts = CustomerDebt::where('business_id', $businessId)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->with('customer')
            ->limit(10)
            ->get();

        $lowStockItems = BranchStock::whereHas('product', fn($q) => $q->where('business_id', $businessId))
            ->whereColumn('qty', '<=', 'reorder_level')
            ->with('product', 'branch')
            ->limit(10)
            ->get();

        $expiringProducts = Product::where('business_id', $businessId)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        $pendingCount = Reminder::where('business_id', $businessId)->where('status', 'pending')->count();
        $debtCount = $overdueDebts->count();
        $stockCount = $lowStockItems->count();
        $expiryCount = $expiringProducts->count();

        return view('reminders.index', compact('reminders', 'overdueDebts', 'lowStockItems', 'expiringProducts', 'pendingCount', 'debtCount', 'stockCount', 'expiryCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'nullable|string',
            'channel' => 'required|in:app,sms,whatsapp,email',
            'remind_at' => 'required|date',
        ]);

        $reminder = Reminder::create([
            'business_id' => auth()->user()->business_id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'title' => $request->title,
            'message' => $request->message,
            'channel' => $request->channel,
            'remind_at' => $request->remind_at,
            'status' => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kikumbusho kimeongezwa!', 'reminder' => $reminder]);
        }
        return redirect()->route('reminders.index')->with('success', 'Kikumbusho kimeongezwa!');
    }

    public function destroy(Reminder $reminder)
    {
        if ($reminder->business_id !== auth()->user()->business_id) abort(403);
        $reminder->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kikumbusho kimefutwa.']);
        }
        return redirect()->route('reminders.index')->with('success', 'Kikumbusho kimefutwa.');
    }
}
