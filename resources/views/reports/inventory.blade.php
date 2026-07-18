@extends('layouts.dashboard')

@section('title', 'Ripoti ya Stoo')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('reports.index') }}" class="text-emerald-600 font-bold text-sm">← Rudi</a>
        <h1 class="text-2xl font-black text-emerald-700">📦 Ripoti ya Stoo</h1>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 text-white shadow-card">
            <p class="text-xs font-bold text-emerald-100 uppercase">Thamani ya Stoo (Cost)</p>
            <p class="font-black text-2xl mt-2">TZS {{ number_format($stockValue, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-2xl p-5 text-white shadow-card">
            <p class="text-xs font-bold text-gold-100 uppercase">Thamani ya Uziao (Retail)</p>
            <p class="font-black text-2xl mt-2">TZS {{ number_format($retailValue, 0) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                    <tr><th class="px-4 py-3 text-left">Bidhaa</th><th class="px-4 py-3 text-left">Kategoria</th><th class="px-4 py-3 text-right">Qty</th><th class="px-4 py-3 text-right">Cost</th><th class="px-4 py-3 text-right">Bei ya Uziao</th><th class="px-4 py-3 text-right">Thamani</th><th class="px-4 py-3 text-center">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($stock as $s)
                    <tr class="hover:bg-emerald-50/30">
                        <td class="px-4 py-3 font-bold text-gray-700">{{ $s->product->name }}</td>
                        <td class="px-4 py-3 text-gray-500 font-semibold">{{ $s->product->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-700">{{ $s->qty }} {{ $s->product->unit }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-600">TZS {{ number_format($s->product->cost_price, 0) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-emerald-600">TZS {{ number_format($s->product->selling_price, 0) }}</td>
                        <td class="px-4 py-3 text-right font-black text-emerald-600">TZS {{ number_format($s->qty * $s->product->cost_price, 0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $s->qty <= 0 ? 'bg-red-100 text-red-600' : ($s->qty <= $s->reorder_level ? 'bg-gold-100 text-gold-700' : 'bg-emerald-100 text-emerald-700') }}">
                                {{ $s->qty <= 0 ? 'Hakuna' : ($s->qty <= $s->reorder_level ? 'Karibu Kuisha' : 'Nzuri') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna stoo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
