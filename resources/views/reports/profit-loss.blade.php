@extends('layouts.dashboard')

@section('title', 'Profit & Loss')

@section('page_title', 'Profit & Loss — ' . now()->format('F Y'))

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('reports.index') }}" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-500 transition-all flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                Profit & Loss ({{ now()->format('F Y') }})
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Monthly financial summary</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Revenue</p>
            <p class="font-bold text-2xl mt-1 text-emerald-600">TZS {{ number_format($monthRevenue, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Cost of Goods</p>
            <p class="font-bold text-2xl mt-1 text-red-600">TZS {{ number_format($monthCost, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-xs font-semibold text-emerald-100 uppercase tracking-wide">Gross Profit</p>
            <p class="font-bold text-2xl mt-2">TZS {{ number_format($grossProfit, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-xs font-semibold text-gold-100 uppercase tracking-wide">Net Profit</p>
            <p class="font-bold text-2xl mt-2">TZS {{ number_format($netProfit, 0) }}</p>
        </div>
    </div>

    {{-- Summary & Expenses --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m2 2v-4m2 4v-6m-2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50"><span class="font-medium text-gray-600">Sales Revenue</span><span class="font-bold text-emerald-600">TZS {{ number_format($monthRevenue, 0) }}</span></div>
                <div class="flex justify-between py-2 border-b border-gray-50"><span class="font-medium text-gray-600">(-) Cost of Goods</span><span class="font-bold text-red-600">TZS {{ number_format($monthCost, 0) }}</span></div>
                <div class="flex justify-between py-2 border-b border-gray-100 font-bold"><span class="text-emerald-700">Gross Profit</span><span class="text-emerald-700">TZS {{ number_format($grossProfit, 0) }}</span></div>
                <div class="flex justify-between py-2 border-b border-gray-50"><span class="font-medium text-gray-600">(-) Expenses</span><span class="font-bold text-red-600">TZS {{ number_format($monthExpenses, 0) }}</span></div>
                <div class="flex justify-between py-2 font-bold text-lg"><span class="text-gold-700">Net Profit</span><span class="text-gold-700">TZS {{ number_format($netProfit, 0) }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Expenses by Category
            </h3>
            <div class="space-y-3">
                @forelse($expensesByCategory as $e)
                @php $maxExp = $expensesByCategory->max('total'); $pct = $maxExp > 0 ? ($e->total / $maxExp) * 100 : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm font-bold mb-1"><span class="text-gray-600">{{ $e->category }}</span><span class="text-red-600">TZS {{ number_format($e->total, 0) }}</span></div>
                    <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden"><div class="h-full rounded-full bg-gradient-to-r from-red-400 to-gold-400" style="width: {{ $pct }}%"></div></div>
                </div>
                @empty
                <p class="text-gray-400 font-medium text-center py-8">No expenses found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
