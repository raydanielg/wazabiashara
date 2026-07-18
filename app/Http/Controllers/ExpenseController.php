<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('business_id', auth()->user()->business_id)
            ->with(['branch', 'user'])
            ->when(request('search'), fn($q, $s) => $q->where('category', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"))
            ->when(request('branch'), fn($q, $b) => $q->where('branch_id', $b))
            ->orderByDesc('id')
            ->paginate(20);

        $branches = auth()->user()->business->branches;
        $categories = ['Kodi', 'Umeme', 'Maji', 'Usafiri', 'Chakula', 'Mishahara', 'Upkeep', 'Mafuta', 'Internet', 'Mengineyo'];

        return view('expenses.index', compact('expenses', 'branches', 'categories'));
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

        return redirect()->route('expenses.index')->with('success', 'Matumizi yamerekodiwa!');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->business_id !== auth()->user()->business_id) abort(403);
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Matumizi yamefutwa.');
    }
}
