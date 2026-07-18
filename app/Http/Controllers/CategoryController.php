<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('business_id', auth()->user()->business_id)
            ->with('parent', 'products')
            ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(20);

        $parents = Category::where('business_id', auth()->user()->business_id)->whereNull('parent_id')->orderBy('name')->get();

        return view('categories.index', compact('categories', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'business_id' => auth()->user()->business_id,
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategoria imeongezwa!');
    }

    public function update(Request $request, Category $category)
    {
        if ($category->business_id !== auth()->user()->business_id) abort(403);

        $request->validate(['name' => 'required|string|max:255']);
        $category->update($request->only(['name', 'parent_id', 'icon', 'description']));

        return redirect()->route('categories.index')->with('success', 'Kategoria imesasishwa!');
    }

    public function destroy(Category $category)
    {
        if ($category->business_id !== auth()->user()->business_id) abort(403);
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategoria imefutwa.');
    }
}
