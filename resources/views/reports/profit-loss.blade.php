@extends('layouts.dashboard')

@section('title', 'Faida & Hasara')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('reports.index') }}" class="text-emerald-600 font-bold text-sm">← Rudi</a>
        <h1 class="text-2xl font-black text-emerald-700">💰 Ripoti ya Faida & Hasara ({{ now()->format('F Y') }})</h1>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-card border-2 border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase">Mapato</p>
            <p class="font-black text-2xl mt-1 text-emerald-600">TZS {{ number_format($monthRevenue, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-card border-2 border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase">Gharama ya Bidhaa</p>
            <p class="font-black text-2xl mt-1 text-red-600">TZS {{ number_format($monthCost, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 text-white shadow-card">
            <p class="text-xs font-bold text-emerald-100 uppercase">Faida Ghafi</p>
            <p class="font-black text-2xl mt-2">TZS {{ number_format($grossProfit, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-2xl p-5 text-white shadow-card">
            <p class="text-xs font-bold text-gold-100 uppercase">Faida Safi</p>
            <p class="font-black text-2xl mt-2">TZS {{ number_format($netProfit, 0) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-card p-6">
            <h3 class="font-black text-lg text-emerald-700 mb-4">📋 Muhtasari</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50"><span class="font-bold text-gray-600">Mapato ya Mauzo</span><span class="font-black text-emerald-600">TZS {{ number_format($monthRevenue, 0) }}</span></div>
                <div class="flex justify-between py-2 border-b border-gray-50"><span class="font-bold text-gray-600">(-) Gharama ya Bidhaa</span><span class="font-black text-red-600">TZS {{ number_format($monthCost, 0) }}</span></div>
                <div class="flex justify-between py-2 border-b border-gray-100 font-black"><span class="text-emerald-700">Faida Ghafi (Gross Profit)</span><span class="text-emerald-700">TZS {{ number_format($grossProfit, 0) }}</span></div>
                <div class="flex justify-between py-2 border-b border-gray-50"><span class="font-bold text-gray-600">(-) Matumizi</span><span class="font-black text-red-600">TZS {{ number_format($monthExpenses, 0) }}</span></div>
                <div class="flex justify-between py-2 font-black text-lg"><span class="text-gold-700">Faida Safi (Net Profit)</span><span class="text-gold-700">TZS {{ number_format($netProfit, 0) }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-card p-6">
            <h3 class="font-black text-lg text-emerald-700 mb-4">💸 Matumizi kwa Kategoria</h3>
            <div class="space-y-3">
                @forelse($expensesByCategory as $e)
                @php $maxExp = $expensesByCategory->max('total'); $pct = $maxExp > 0 ? ($e->total / $maxExp) * 100 : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm font-bold mb-1"><span class="text-gray-600">{{ $e->category }}</span><span class="text-red-600">TZS {{ number_format($e->total, 0) }}</span></div>
                    <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden"><div class="h-full rounded-full bg-gradient-to-r from-red-400 to-gold-400" style="width: {{ $pct }}%"></div></div>
                </div>
                @empty
                <p class="text-gray-400 font-bold text-center py-8">Hakuna matumizi.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
