@extends('layouts.dashboard')

@section('title', 'Add Product')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('products.index') }}" class="text-emerald-600 font-bold text-sm">← Back</a>
        <h1 class="text-2xl font-black text-emerald-700">📦 Add New Product</h1>
    </div>

    <form method="POST" action="{{ route('products.store') }}" class="bg-white rounded-2xl shadow-card p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-600 mb-1">Product Name *</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Barcode</label>
                <input type="text" name="barcode" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">SKU</label>
                <input type="text" name="sku" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Category</label>
                <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white">
                    <option value="">— Select —</option>
                    @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Unit *</label>
                <select name="unit" required class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white">
                    <option value="piece">Piece</option><option value="kg">Kilogram</option><option value="lita">Liter</option>
                    <option value="katoni">Carton</option><option value="dazani">Dozen</option><option value="box">Box</option>
                    <option value="m">Meter</option><option value="set">Set</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Cost Price *</label>
                <input type="number" name="cost_price" required min="0" step="0.01" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Selling Price *</label>
                <input type="number" name="selling_price" required min="0" step="0.01" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Reorder Level</label>
                <input type="number" name="reorder_level" value="5" min="0" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold">
            </div>
        </div>

        <!-- Initial stock per branch -->
        <div class="border-t border-gray-100 pt-4">
            <h3 class="font-black text-sm text-emerald-700 mb-3">Initial Stock (per branch)</h3>
            <div class="grid grid-cols-2 gap-3">
                @foreach($branches as $branch)
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">{{ $branch->name }}</label>
                    <input type="number" name="initial_stock[{{ $branch->id }}]" min="0" step="0.01" placeholder="0" class="w-full px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm">
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full btn-gold font-black py-3 rounded-xl">Save Product</button>
    </form>
</div>
@endsection
