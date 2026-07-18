<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        $branches = Branch::where('business_id', $businessId)
            ->withCount(['users', 'sales'])
            ->orderByDesc('id')
            ->get();

        $totalBranches = $branches->count();
        $activeBranches = $branches->where('status', 'active')->count();
        $totalUsers = $branches->sum('users_count');
        $totalSales = $branches->sum('sales_count');

        return view('branches.index', compact('branches', 'totalBranches', 'activeBranches', 'totalUsers', 'totalSales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $branch = Branch::create([
            'business_id' => auth()->user()->business_id,
            'name' => $request->name,
            'location' => $request->location,
            'phone' => $request->phone,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Tawi limeongezwa kikamilifu!', 'branch' => $branch]);
        }
        return redirect()->route('branches.index')->with('success', 'Tawi limeongezwa kikamilifu!');
    }

    public function update(Request $request, Branch $branch)
    {
        if ($branch->business_id !== auth()->user()->business_id) abort(403);

        $request->validate(['name' => 'required|string|max:255']);
        $branch->update($request->only(['name', 'location', 'phone', 'status']));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Tawi limesasishwa!', 'branch' => $branch]);
        }
        return redirect()->route('branches.index')->with('success', 'Tawi limesasishwa!');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->business_id !== auth()->user()->business_id) abort(403);
        $branch->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Tawi limefutwa.']);
        }
        return redirect()->route('branches.index')->with('success', 'Tawi limefutwa.');
    }

    public function switch(Request $request, Branch $branch)
    {
        if ($branch->business_id !== auth()->user()->business_id) abort(403);
        session(['active_branch_id' => $branch->id]);
        return redirect()->back()->with('success', 'Umebadilisha tawi kwenda: ' . $branch->name);
    }
}
