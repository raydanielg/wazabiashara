<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\CustomerDebt;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $payments = Payment::where('business_id', $businessId)
            ->with(['branch', 'user'])
            ->when(request('type'), fn($q, $t) => $q->where('type', $t))
            ->when(request('search'), fn($q, $s) => $q->where('category', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        $todayIn = Payment::where('business_id', $businessId)->where('type', 'in')->whereDate('payment_date', today())->sum('amount');
        $todayOut = Payment::where('business_id', $businessId)->where('type', 'out')->whereDate('payment_date', today())->sum('amount');
        $monthIn = Payment::where('business_id', $businessId)->where('type', 'in')->whereMonth('payment_date', now()->month)->sum('amount');
        $monthOut = Payment::where('business_id', $businessId)->where('type', 'out')->whereMonth('payment_date', now()->month)->sum('amount');

        $customers = Customer::where('business_id', $businessId)->where('status', 'active')->orderBy('name')->get();
        $suppliers = Supplier::where('business_id', $businessId)->where('status', 'active')->orderBy('name')->get();
        $branches = $user->business->branches;

        $inCategories = ['Malipo ya Mteja', 'Malipo ya Deni', 'Mapeto Mengine', 'Renti', 'Tume'];
        $outCategories = ['Malipo kwa Supplier', 'Mishahara', 'Matumizi', 'Kodi', 'Mengineyo'];

        return view('payments.index', compact('payments', 'todayIn', 'todayOut', 'monthIn', 'monthOut', 'customers', 'suppliers', 'branches', 'inCategories', 'outCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'payment_date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'debt_id' => 'nullable|exists:customer_debts,id',
        ]);

        $payment = Payment::create([
            'business_id' => auth()->user()->business_id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'branch_id' => $request->branch_id,
            'payment_date' => $request->payment_date,
        ]);

        if ($request->type === 'in' && $request->debt_id) {
            $debt = CustomerDebt::find($request->debt_id);
            if ($debt && $debt->balance > 0) {
                $payAmount = min($request->amount, $debt->balance);
                $debt->decrement('balance', $payAmount);
                $debt->customer?->decrement('current_debt', $payAmount);
                if ($debt->balance <= 0) {
                    $debt->update(['status' => 'paid']);
                } elseif ($debt->balance < $debt->amount) {
                    $debt->update(['status' => 'partial']);
                }
            }
        }

        if ($request->type === 'out' && $request->supplier_id) {
            $supplier = Supplier::find($request->supplier_id);
            if ($supplier) {
                $supplier->decrement('balance', $request->amount);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Malipo yamerekodiwa!', 'payment' => $payment]);
        }
        return redirect()->route('payments.index')->with('success', 'Malipo yamerekodiwa!');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->business_id !== auth()->user()->business_id) abort(403);
        $payment->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Malipo yamefutwa.']);
        }
        return redirect()->route('payments.index')->with('success', 'Malipo yamefutwa.');
    }
}
