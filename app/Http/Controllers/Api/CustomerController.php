<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\DebtPayment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $businessId = $request->user()->business_id;

        $customers = Customer::where('business_id', $businessId)
            ->withCount(['debts', 'sales'])
            ->when($request->query('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        $summary = [
            'totalCustomers' => Customer::where('business_id', $businessId)->count(),
            'activeCustomers' => Customer::where('business_id', $businessId)->where('status', 'active')->count(),
            'customersWithDebt' => Customer::where('business_id', $businessId)->where('current_debt', '>', 0)->count(),
            'totalDebt' => Customer::where('business_id', $businessId)->sum('current_debt'),
        ];

        return response()->json([
            'success' => true,
            'customers' => $customers,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $customer = Customer::create([
            'business_id' => $request->user()->business_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'credit_limit' => $request->credit_limit ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mteja ameongezwa!',
            'customer' => $customer,
        ], 201);
    }

    public function show(Request $request, Customer $customer)
    {
        if ($customer->business_id !== $request->user()->business_id) abort(403);

        return response()->json([
            'success' => true,
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        if ($customer->business_id !== $request->user()->business_id) abort(403);

        $request->validate(['name' => 'required|string|max:255']);
        $customer->update($request->only(['name', 'phone', 'email', 'address', 'credit_limit', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'Mteja amesasishwa!',
            'customer' => $customer,
        ]);
    }

    public function destroy(Request $request, Customer $customer)
    {
        if ($customer->business_id !== $request->user()->business_id) abort(403);
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mteja amefutwa.',
        ]);
    }

    public function debts(Request $request, Customer $customer)
    {
        if ($customer->business_id !== $request->user()->business_id) abort(403);

        $debts = $customer->debts()->with('payments', 'sale')->orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'debts' => $debts,
        ]);
    }

    public function payDebt(Request $request, CustomerDebt $debt)
    {
        if ($debt->business_id !== $request->user()->business_id) abort(403);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $debt->balance,
            'method' => 'required|in:cash,mpesa,tigo_pesa,airtel_money,bank',
            'reference' => 'nullable|string',
        ]);

        DebtPayment::create([
            'customer_debt_id' => $debt->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'user_id' => $request->user()->id,
        ]);

        $debt->decrement('balance', $request->amount);
        $debt->customer->decrement('current_debt', $request->amount);

        if ($debt->balance <= 0) {
            $debt->update(['status' => 'paid', 'balance' => 0]);
        } else {
            $debt->update(['status' => 'partial']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Malipo ya deni yamerekodiwa!',
            'debt' => $debt->fresh(),
        ]);
    }
}
