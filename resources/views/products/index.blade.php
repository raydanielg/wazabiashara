@extends('layouts.dashboard')

@section('title', 'Bidhaa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-emerald-700">📦 Bidhaa & Stoo</h1>
            <p class="text-sm text-gray-500 font-semibold">Simamia bidhaa zako na stoo ya tawi</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn-gold font-extrabold px-5 py-2.5 rounded-xl inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Ongeza Bidhaa
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex gap-3 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tafuta bidhaa..." class="flex-1 min-w-[200px] px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm">
        <select name="category" class="px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm bg-white">
            <option value="">Kategoria Zote</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 text-white font-bold text-sm hover:bg-emerald-600">Tafuta</button>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Bidhaa</th>
                        <th class="px-4 py-3 text-left">Barcode</th>
                        <th class="px-4 py-3 text-left">Kategoria</th>
                        <th class="px-4 py-3 text-right">Bei ya Kununulia</th>
                        <th class="px-4 py-3 text-right">Bei ya Uziao</th>
                        <th class="px-4 py-3 text-center">Stoo</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Kitendo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $p)
                    @php $stock = $p->branchStock->first(); $qty = $stock?->qty ?? 0; @endphp
                    <tr class="hover:bg-emerald-50/30 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-lg bg-emerald-50 grid place-items-center overflow-hidden">
                                    @if($p->image)<img src="{{ asset('storage/' . $p->image) }}" class="w-full h-full object-cover">@else<svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>@endif
                                </div>
                                <div><p class="font-bold text-gray-700">{{ $p->name }}</p><p class="text-xs text-gray-400">{{ $p->unit }}</p></div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $p->barcode ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 font-semibold">{{ $p->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-600">TZS {{ number_format($p->cost_price, 0) }}</td>
                        <td class="px-4 py-3 text-right font-black text-emerald-600">TZS {{ number_format($p->selling_price, 0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $qty <= 0 ? 'bg-red-100 text-red-600' : ($qty <= $p->reorder_level ? 'bg-gold-100 text-gold-700' : 'bg-emerald-100 text-emerald-700') }}">{{ $qty }} {{ $p->unit }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $p->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $p->status === 'active' ? 'Hai' : 'Imezimwa' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('products.edit', $p) }}" class="text-emerald-600 font-bold hover:text-emerald-800 text-xs">Hariri</a>
                            <form method="POST" action="{{ route('products.destroy', $p) }}" class="inline" onsubmit="return confirm('Futa bidhaa hii?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 font-bold hover:text-red-700 text-xs ml-2">Futa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna bidhaa. Ongeza bidhaa yako ya kwanza!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    </div>
</div>
@endsection
