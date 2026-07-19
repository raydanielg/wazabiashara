<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerDebt;
use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $businessId = $request->user()->business_id;

        $payments = Payment::where('business_id', $businessId)
            ->with(['branch', 'user'])
            ->when($request->query('type'), fn ($q, $t) => $q->where('type', $t))
            ->when($request->query('search'), fn ($q, $s) => $q->where('category', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        $summary = [
            'todayIn' => Payment::where('business_id', $businessId)->where('type', 'in')->whereDate('payment_date', today())->sum('amount'),
            'todayOut' => Payment::where('business_id', $businessId)->where('type', 'out')->whereDate('payment_date', today())->sum('amount'),
            'monthIn' => Payment::where('business_id', $businessId)->where('type', 'in')->whereMonth('payment_date', now()->month)->sum('amount'),
            'monthOut' => Payment::where('business_id', $businessId)->where('type', 'out')->whereMonth('payment_date', now()->month)->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'payments' => $payments,
            'summary' => $summary,
        ]);
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

        $user = $request->user();

        $payment = Payment::create([
            'business_id' => $user->business_id,
            'user_id' => $user->id,
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

        return response()->json([
            'success' => true,
            'message' => 'Malipo yamerekodiwa!',
            'payment' => $payment,
        ], 201);
    }

    public function destroy(Request $request, Payment $payment)
    {
        if ($payment->business_id !== $request->user()->business_id) abort(403);
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Malipo yamefutwa.',
        ]);
    }
}
