<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $incomes = Income::where('business_id', $businessId)
            ->with(['branch', 'user'])
            ->when(request('search'), fn($q, $s) => $q->where('category', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        $todayIncome = Income::where('business_id', $businessId)->whereDate('income_date', today())->sum('amount');
        $monthIncome = Income::where('business_id', $businessId)->whereMonth('income_date', now()->month)->sum('amount');
        $totalIncome = Income::where('business_id', $businessId)->sum('amount');
        $incomeByCategory = Income::where('business_id', $businessId)->whereMonth('income_date', now()->month)
            ->select('category', \DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $branches = $user->business->branches;
        $categories = ['Mauzo', 'Huduma', 'Kodi', 'Renti', 'Tume', 'Mengineyo'];

        if (request()->expectsJson()) {
            return response()->json(compact('incomes', 'todayIncome', 'monthIncome', 'totalIncome', 'incomeByCategory'));
        }

        return view('incomes.index', compact('incomes', 'todayIncome', 'monthIncome', 'totalIncome', 'incomeByCategory', 'branches', 'categories'));
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

        $income = Income::create([
            'business_id' => auth()->user()->business_id,
            'user_id' => auth()->id(),
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'branch_id' => $request->branch_id,
            'income_date' => $request->income_date,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mapato yamerekodiwa!', 'income' => $income]);
        }
        return redirect()->route('incomes.index')->with('success', 'Mapato yamerekodiwa!');
    }

    public function edit(Income $income)
    {
        if ($income->business_id !== auth()->user()->business_id) abort(403);
        $income->income_date = $income->income_date->format('Y-m-d');
        return response()->json(['success' => true, 'income' => $income]);
    }

    public function update(Request $request, Income $income)
    {
        if ($income->business_id !== auth()->user()->business_id) abort(403);
        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'income_date' => 'required|date',
        ]);

        $income->update($request->only(['category', 'description', 'amount', 'payment_method', 'branch_id', 'income_date']));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mapato yamesasishwa!']);
        }
        return redirect()->route('incomes.index')->with('success', 'Mapato yamesasishwa!');
    }

    public function destroy(Income $income)
    {
        if ($income->business_id !== auth()->user()->business_id) abort(403);
        $income->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mapato yamefutwa.']);
        }
        return redirect()->route('incomes.index')->with('success', 'Mapato yamefutwa.');
    }
}
