<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('business_id', $request->user()->business_id)
            ->with('parent', 'products')
            ->when($request->query('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'business_id' => $request->user()->business_id,
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategoria imeongezwa!',
            'category' => $category,
        ], 201);
    }

    public function update(Request $request, Category $category)
    {
        if ($category->business_id !== $request->user()->business_id) abort(403);

        $request->validate(['name' => 'required|string|max:255']);
        $category->update($request->only(['name', 'parent_id', 'icon', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Kategoria imesasishwa!',
            'category' => $category,
        ]);
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->business_id !== $request->user()->business_id) abort(403);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategoria imefutwa.',
        ]);
    }
}
