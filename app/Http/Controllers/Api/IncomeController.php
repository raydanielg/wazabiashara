<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $businessId = $request->user()->business_id;

        $incomes = Income::where('business_id', $businessId)
            ->with(['branch', 'user'])
            ->when($request->query('search'), fn ($q, $s) => $q->where('category', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        $summary = [
            'todayIncome' => Income::where('business_id', $businessId)->whereDate('income_date', today())->sum('amount'),
            'monthIncome' => Income::where('business_id', $businessId)->whereMonth('income_date', now()->month)->sum('amount'),
            'totalIncome' => Income::where('business_id', $businessId)->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'incomes' => $incomes,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'income_date' => 'required|date',
        ]);

        $user = $request->user();

        $income = Income::create([
            'business_id' => $user->business_id,
            'user_id' => $user->id,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'branch_id' => $request->branch_id,
            'income_date' => $request->income_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mapato yamerekodiwa!',
            'income' => $income,
        ], 201);
    }

    public function show(Request $request, Income $income)
    {
        if ($income->business_id !== $request->user()->business_id) abort(403);

        return response()->json([
            'success' => true,
            'income' => $income,
        ]);
    }

    public function update(Request $request, Income $income)
    {
        if ($income->business_id !== $request->user()->business_id) abort(403);

        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'income_date' => 'required|date',
        ]);

        $income->update($request->only(['category', 'description', 'amount', 'payment_method', 'branch_id', 'income_date']));

        return response()->json([
            'success' => true,
            'message' => 'Mapato yamesasishwa!',
            'income' => $income,
        ]);
    }

    public function destroy(Request $request, Income $income)
    {
        if ($income->business_id !== $request->user()->business_id) abort(403);
        $income->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mapato yamefutwa.',
        ]);
    }
}
