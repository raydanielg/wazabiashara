<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\DebtPayment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        $customers = Customer::where('business_id', $businessId)
            ->withCount(['debts', 'sales'])
            ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        $totalCustomers = Customer::where('business_id', $businessId)->count();
        $activeCustomers = Customer::where('business_id', $businessId)->where('status', 'active')->count();
        $customersWithDebt = Customer::where('business_id', $businessId)->where('current_debt', '>', 0)->count();
        $totalDebt = Customer::where('business_id', $businessId)->sum('current_debt');

        return view('customers.index', compact('customers', 'totalCustomers', 'activeCustomers', 'customersWithDebt', 'totalDebt'));
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
            'business_id' => auth()->user()->business_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'credit_limit' => $request->credit_limit ?? 0,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mteja ameongezwa!', 'customer' => $customer]);
        }
        return redirect()->route('customers.index')->with('success', 'Mteja ameongezwa!');
    }

    public function update(Request $request, Customer $customer)
    {
        if ($customer->business_id !== auth()->user()->business_id) abort(403);

        $request->validate(['name' => 'required|string|max:255']);
        $customer->update($request->only(['name', 'phone', 'email', 'address', 'credit_limit', 'status']));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mteja amesasishwa!', 'customer' => $customer]);
        }
        return redirect()->route('customers.index')->with('success', 'Mteja amesasishwa!');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->business_id !== auth()->user()->business_id) abort(403);
        $customer->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mteja amefutwa.']);
        }
        return redirect()->route('customers.index')->with('success', 'Mteja amefutwa.');
    }

    public function debts(Customer $customer)
    {
        if ($customer->business_id !== auth()->user()->business_id) abort(403);

        $debts = $customer->debts()->with('payments', 'sale')->orderByDesc('id')->get();
        return view('customers.debts', compact('customer', 'debts'));
    }

    public function payDebt(Request $request, CustomerDebt $debt)
    {
        if ($debt->business_id !== auth()->user()->business_id) abort(403);

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
            'user_id' => auth()->id(),
        ]);

        $debt->decrement('balance', $request->amount);
        $debt->customer->decrement('current_debt', $request->amount);

        if ($debt->balance <= 0) {
            $debt->update(['status' => 'paid', 'balance' => 0]);
        } else {
            $debt->update(['status' => 'partial']);
        }

        return redirect()->back()->with('success', 'Malipo ya deni yamerekodiwa!');
    }
}
