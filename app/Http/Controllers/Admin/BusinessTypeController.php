<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $types = BusinessType::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.business-types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:business_types,slug'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        BusinessType::create([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ?? null,
            'icon'        => $validated['icon'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'is_active'   => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aina ya biashara imeongezwa.',
        ]);
    }

    public function update(Request $request, BusinessType $businessType)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:business_types,slug,' . $businessType->id],
            'icon'        => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['boolean'],
        ]);

        $businessType->update([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ?? $businessType->slug,
            'icon'        => $validated['icon'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'is_active'   => $validated['is_active'] ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aina ya biashara imerekebishwa.',
        ]);
    }

    public function destroy(BusinessType $businessType)
    {
        $businessType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aina ya biashara imefutwa.',
        ]);
    }

    public function toggle(BusinessType $businessType)
    {
        $businessType->update(['is_active' => !$businessType->is_active]);

        return response()->json([
            'success' => true,
            'message' => $businessType->is_active ? 'Imewashwa.' : 'Imezimwa.',
            'is_active' => $businessType->is_active,
        ]);
    }
}
