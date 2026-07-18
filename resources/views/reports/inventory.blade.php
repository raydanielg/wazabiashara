@extends('layouts.dashboard')

@section('title', 'Ripoti ya Stoo')

@section('page_title', 'Ripoti ya Stoo')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('reports.index') }}" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-500 transition-all flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Ripoti ya Stoo
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Thamani na hali ya stoo yako</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-xs font-semibold text-emerald-100 uppercase tracking-wide">Thamani ya Stoo (Cost)</p>
            <p class="font-bold text-2xl mt-2">TZS {{ number_format($stockValue, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-xs font-semibold text-gold-100 uppercase tracking-wide">Thamani ya Uziao (Retail)</p>
            <p class="font-bold text-2xl mt-2">TZS {{ number_format($retailValue, 0) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wide">
                    <tr><th class="px-4 py-3 text-left">Bidhaa</th><th class="px-4 py-3 text-left hidden sm:table-cell">Kategoria</th><th class="px-4 py-3 text-right">Qty</th><th class="px-4 py-3 text-right hidden md:table-cell">Cost</th><th class="px-4 py-3 text-right hidden md:table-cell">Bei ya Uziao</th><th class="px-4 py-3 text-right">Thamani</th><th class="px-4 py-3 text-center">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($stock as $s)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 font-semibold text-gray-700">{{ $s->product->name }}</td>
                        <td class="px-4 py-3 text-gray-500 font-medium hidden sm:table-cell">{{ $s->product->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-700">{{ $s->qty }} {{ $s->product->unit }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-600 hidden md:table-cell">TZS {{ number_format($s->product->cost_price, 0) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-emerald-600 hidden md:table-cell">TZS {{ number_format($s->product->selling_price, 0) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-emerald-600">TZS {{ number_format($s->qty * $s->product->cost_price, 0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $s->qty <= 0 ? 'bg-red-50 text-red-500' : ($s->qty <= $s->reorder_level ? 'bg-gold-50 text-gold-600' : 'bg-emerald-50 text-emerald-600') }}">
                                {{ $s->qty <= 0 ? 'Hakuna' : ($s->qty <= $s->reorder_level ? 'Karibu Kuisha' : 'Nzuri') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-gray-400 font-medium text-sm">Hakuna stoo.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
