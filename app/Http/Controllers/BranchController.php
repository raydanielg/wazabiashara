<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::where('business_id', auth()->user()->business_id)
            ->withCount(['users', 'sales'])
            ->orderByDesc('id')
            ->get();

        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Branch::create([
            'business_id' => auth()->user()->business_id,
            'name' => $request->name,
            'location' => $request->location,
            'phone' => $request->phone,
        ]);

        return redirect()->route('branches.index')->with('success', 'Tawi limeongezwa kikamilifu!');
    }

    public function update(Request $request, Branch $branch)
    {
        if ($branch->business_id !== auth()->user()->business_id) abort(403);

        $request->validate(['name' => 'required|string|max:255']);
        $branch->update($request->only(['name', 'location', 'phone', 'status']));

        return redirect()->route('branches.index')->with('success', 'Tawi limesasishwa!');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->business_id !== auth()->user()->business_id) abort(403);
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Tawi limefutwa.');
    }

    public function switch(Request $request, Branch $branch)
    {
        if ($branch->business_id !== auth()->user()->business_id) abort(403);
        session(['active_branch_id' => $branch->id]);
        return redirect()->back()->with('success', 'Umebadilisha tawi kwenda: ' . $branch->name);
    }
}
