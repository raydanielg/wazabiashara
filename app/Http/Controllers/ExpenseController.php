<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        $expenses = Expense::where('business_id', $businessId)
            ->with(['branch', 'user'])
            ->when(request('search'), fn($q, $s) => $q->where('category', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"))
            ->when(request('branch'), fn($q, $b) => $q->where('branch_id', $b))
            ->orderByDesc('id')
            ->paginate(20);

        $branches = auth()->user()->business->branches;
        $categories = ['Kodi', 'Umeme', 'Maji', 'Usafiri', 'Chakula', 'Mishahara', 'Upkeep', 'Mafuta', 'Internet', 'Mengineyo'];

        $todayExpenses = Expense::where('business_id', $businessId)->whereDate('expense_date', today())->sum('amount');
        $monthExpenses = Expense::where('business_id', $businessId)->whereMonth('expense_date', now()->month)->sum('amount');
        $totalExpenses = Expense::where('business_id', $businessId)->sum('amount');
        $expensesByCategory = Expense::where('business_id', $businessId)
            ->whereMonth('expense_date', now()->month)
            ->select('category', \DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return view('expenses.index', compact('expenses', 'branches', 'categories', 'todayExpenses', 'monthExpenses', 'totalExpenses', 'expensesByCategory'));
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

        Expense::create([
            'business_id' => auth()->user()->business_id,
            'branch_id' => $request->branch_id ?? session('active_branch_id'),
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'user_id' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Matumizi yamerekodiwa!']);
        }
        return redirect()->route('expenses.index')->with('success', 'Matumizi yamerekodiwa!');
    }

    public function edit(Expense $expense)
    {
        if ($expense->business_id !== auth()->user()->business_id) abort(403);
        $expense->load('branch');
        $expense->expense_date = $expense->expense_date->format('Y-m-d');
        return response()->json(['success' => true, 'expense' => $expense]);
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->business_id !== auth()->user()->business_id) abort(403);

        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'branch_id' => 'nullable|exists:branches,id',
            'expense_date' => 'required|date',
        ]);

        $expense->update($request->only(['category', 'description', 'amount', 'branch_id', 'expense_date']));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Matumizi yamesasishwa!']);
        }
        return redirect()->route('expenses.index')->with('success', 'Matumizi yamesasishwa!');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->business_id !== auth()->user()->business_id) abort(403);
        $expense->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Matumizi yamefutwa.']);
        }
        return redirect()->route('expenses.index')->with('success', 'Matumizi yamefutwa.');
    }
}
