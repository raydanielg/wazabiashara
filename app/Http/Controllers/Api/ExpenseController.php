<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $businessId = $request->user()->business_id;

        $expenses = Expense::where('business_id', $businessId)
            ->with(['branch', 'user'])
            ->when($request->query('search'), fn ($q, $s) => $q->where('category', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"))
            ->when($request->query('branch_id'), fn ($q, $b) => $q->where('branch_id', $b))
            ->orderByDesc('id')
            ->paginate(20);

        $summary = [
            'todayExpenses' => Expense::where('business_id', $businessId)->whereDate('expense_date', today())->sum('amount'),
            'monthExpenses' => Expense::where('business_id', $businessId)->whereMonth('expense_date', now()->month)->sum('amount'),
            'totalExpenses' => Expense::where('business_id', $businessId)->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'expenses' => $expenses,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'branch_id' => 'nullable|exists:branches,id',
            'expense_date' => 'required|date',
        ]);

        $user = $request->user();

        $expense = Expense::create([
            'business_id' => $user->business_id,
            'branch_id' => $request->branch_id,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Matumizi yamerekodiwa!',
            'expense' => $expense,
        ], 201);
    }

    public function show(Request $request, Expense $expense)
    {
        if ($expense->business_id !== $request->user()->business_id) abort(403);

        return response()->json([
            'success' => true,
            'expense' => $expense,
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->business_id !== $request->user()->business_id) abort(403);

        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'branch_id' => 'nullable|exists:branches,id',
            'expense_date' => 'required|date',
        ]);

        $expense->update($request->only(['category', 'description', 'amount', 'branch_id', 'expense_date']));

        return response()->json([
            'success' => true,
            'message' => 'Matumizi yamesasishwa!',
            'expense' => $expense,
        ]);
    }

    public function destroy(Request $request, Expense $expense)
    {
        if ($expense->business_id !== $request->user()->business_id) abort(403);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Matumizi yamefutwa.',
        ]);
    }
}
