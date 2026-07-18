@extends('layouts.dashboard')

@section('title', 'Hariri Bidhaa')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('products.index') }}" class="text-emerald-600 font-bold text-sm">← Rudi</a>
        <h1 class="text-2xl font-black text-emerald-700">📦 Hariri Bidhaa</h1>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" class="bg-white rounded-2xl shadow-card p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-600 mb-1">Jina *</label>
                <input type="text" name="name" value="{{ $product->name }}" required class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold">
            </div>
            <div><label class="block text-sm font-bold text-gray-600 mb-1">Barcode</label><input type="text" name="barcode" value="{{ $product->barcode }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
            <div><label class="block text-sm font-bold text-gray-600 mb-1">SKU</label><input type="text" name="sku" value="{{ $product->sku }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Kategoria</label>
                <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white">
                    <option value="">—</option>
                    @foreach($categories as $cat)<option value="{{ $cat->id }}" @selected($product->category_id == $cat->id)>{{ $cat->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Kipimo</label>
                <select name="unit" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white">
                    @foreach(['piece'=>'Kipande','kg'=>'Kilo','lita'=>'Lita','katoni'=>'Katoni','dazani'=>'Dazani','box'=>'Box','m'=>'Mita','set'=>'Seti'] as $v=>$l)<option value="{{ $v }}" @selected($product->unit === $v)>{{ $l }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-sm font-bold text-gray-600 mb-1">Bei ya Kununulia</label><input type="number" name="cost_price" value="{{ $product->cost_price }}" required min="0" step="0.01" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
            <div><label class="block text-sm font-bold text-gray-600 mb-1">Bei ya Uziao</label><input type="number" name="selling_price" value="{{ $product->selling_price }}" required min="0" step="0.01" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
            <div><label class="block text-sm font-bold text-gray-600 mb-1">Reorder Level</label><input type="number" name="reorder_level" value="{{ $product->reorder_level }}" min="0" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
            <div><label class="block text-sm font-bold text-gray-600 mb-1">Tarehe ya Kuisha</label><input type="date" name="expiry_date" value="{{ $product->expiry_date?->format('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white">
                    <option value="active" @selected($product->status === 'active')>Hai</option>
                    <option value="inactive" @selected($product->status === 'inactive')>Imezimwa</option>
                </select>
            </div>
        </div>
        <button type="submit" class="w-full btn-gold font-black py-3 rounded-xl">Hifadhi Mabadiliko</button>
    </form>

    <!-- Stock per branch -->
    <div class="bg-white rounded-2xl shadow-card p-6">
        <h3 class="font-black text-sm text-emerald-700 mb-3">Stoo kwa Tawi</h3>
        <div class="space-y-2">
            @foreach($branches as $branch)
            @php $s = $stock[$branch->id] ?? null; @endphp
            <div class="flex items-center justify-between p-3 rounded-lg bg-emerald-50/50 border border-emerald-100">
                <span class="font-bold text-sm text-gray-700">{{ $branch->name }}</span>
                <span class="font-black text-emerald-600">{{ $s?->qty ?? 0 }} {{ $product->unit }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
