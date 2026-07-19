<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** Map a category type to its index route name, used for redirects. */
    private function routeForType(?string $type): string
    {
        return match ($type) {
            'party' => 'categories.party',
            'expense' => 'categories.expense',
            'income' => 'categories.income',
            default => 'categories.index',
        };
    }

    private function listByType(string $type, string $view)
    {
        $businessId = auth()->user()->business_id;

        $categories = Category::where('business_id', $businessId)
            ->where('type', $type)
            ->with('parent', 'products')
            ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(20);

        $parents = Category::where('business_id', $businessId)
            ->where('type', $type)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view($view, compact('categories', 'parents'));
    }

    public function index()
    {
        return $this->listByType('item', 'categories.index');
    }

    public function party()
    {
        return $this->listByType('party', 'categories.party');
    }

    public function expense()
    {
        return $this->listByType('expense', 'categories.expense');
    }

    public function income()
    {
        return $this->listByType('income', 'categories.income');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'type' => 'nullable|in:item,party,expense,income',
        ]);

        $type = $request->type ?? 'item';

        Category::create([
            'business_id' => auth()->user()->business_id,
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
            'type' => $type,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kategoria imeongezwa!']);
        }

        return redirect()->route($this->routeForType($type))->with('success', 'Kategoria imeongezwa!');
    }

    public function update(Request $request, Category $category)
    {
        if ($category->business_id !== auth()->user()->business_id) abort(403);

        $request->validate(['name' => 'required|string|max:255']);
        $category->update($request->only(['name', 'parent_id', 'icon', 'description']));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kategoria imesasishwa!']);
        }

        return redirect()->route($this->routeForType($category->type))->with('success', 'Kategoria imesasishwa!');
    }

    public function destroy(Category $category)
    {
        if ($category->business_id !== auth()->user()->business_id) abort(403);

        $type = $category->type;
        $category->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kategoria imefutwa.']);
        }

        return redirect()->route($this->routeForType($type))->with('success', 'Kategoria imefutwa.');
    }
}
