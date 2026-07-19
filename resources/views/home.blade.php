@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
@php
$fmt = fn($n) => $n >= 1000000000 ? number_format($n/1000000000,2).'B' : ($n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n)));
@endphp

<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl p-5 text-white relative overflow-hidden border border-emerald-500">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-0 w-24 h-24 bg-gold-500/10 rounded-full -mr-8 -mb-8"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold mb-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Welcome, {{ auth()->user()->name }}!
                </h2>
                <p class="text-emerald-100 text-xs font-medium">Your business, in your hands — Financial overview.</p>
            </div>
            @if(auth()->user()->isBusinessAdmin() || auth()->user()->isAdmin())
            <div class="flex items-center gap-2">
                <select onchange="window.location='{{ route('branches.switch', '__ID__') }}'.replace('__ID__', this.value)" class="px-3 py-1.5 rounded-lg bg-white/20 text-white text-xs font-semibold outline-none border-0 cursor-pointer">
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" @selected($branchId == $b->id) class="text-gray-800">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    {{-- Financial Overview KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
        $finCards = [
            ['label'=>'Total Sales','value'=>'TZS '.$fmt($stats['totalSales']),'sub'=>'Today: TZS '.$fmt($stats['todaySales']),'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','subc'=>'emerald-200'],
            ['label'=>'Total Purchases','value'=>'TZS '.$fmt($stats['totalPurchases']),'sub'=>'Month: TZS '.$fmt($stats['monthPurchases']),'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','subc'=>'sky-200'],
            ['label'=>'Total Income','value'=>'TZS '.$fmt($stats['totalIncome']),'sub'=>'Month: TZS '.$fmt($stats['monthIncome']),'icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z','from'=>'violet-500','to'=>'violet-600','border'=>'violet-400','text'=>'violet-100','subc'=>'violet-200'],
            ['label'=>'Total Expenses','value'=>'TZS '.$fmt($stats['totalExpenses']),'sub'=>'Month: TZS '.$fmt($stats['monthExpenses']),'icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z','from'=>'red-500','to'=>'red-600','border'=>'red-400','text'=>'red-100','subc'=>'red-200'],
        ];
        @endphp
        @foreach($finCards as $card)
        <div class="bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium {{ $card['text'] }} uppercase tracking-wide">{{ $card['label'] }}</span>
                    <svg class="w-4 h-4 {{ $card['subc'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
                <p class="text-[10px] {{ $card['subc'] }} font-medium mt-1">{{ $card['sub'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Secondary Financial KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
        $secCards = [
            ['label'=>'Total Profit','value'=>'TZS '.$fmt($stats['totalProfit']),'color'=>'emerald','icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['label'=>'Net Profit','value'=>'TZS '.$fmt($stats['netProfit']),'color'=>$stats['netProfit'] >= 0 ? 'emerald' : 'red','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Malipo In','value'=>'TZS '.$fmt($stats['totalPaymentsIn']),'color'=>'sky','icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['label'=>'Malipo Out','value'=>'TZS '.$fmt($stats['totalPaymentsOut']),'color'=>'amber','icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['label'=>'Customer Debts','value'=>'TZS '.$fmt($stats['totalReceivables']),'color'=>$stats['totalReceivables'] > 0 ? 'amber' : 'emerald','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label'=>'Supplier Debts','value'=>'TZS '.$fmt($stats['totalPayables']),'color'=>$stats['totalPayables'] > 0 ? 'red' : 'emerald','icon'=>'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z'],
        ];
        @endphp
        @foreach($secCards as $card)
        <div class="bg-white rounded-xl border p-3 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-1">
                <div class="w-7 h-7 rounded-lg bg-{{ $card['color'] }}-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
            </div>
            <p class="text-base font-bold text-gray-900">{{ $card['value'] }}</p>
            <p class="text-[10px] text-gray-500 font-medium">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Balance Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            <div><p class="text-xs text-gray-500 font-medium">Cash Balance</p><p class="text-lg font-bold text-gray-900">TZS {{ number_format($stats['cashBalance'], 0) }}</p></div>
        </div>
        <div class="bg-white rounded-xl border p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div><p class="text-xs text-gray-500 font-medium">Bank Balance</p><p class="text-lg font-bold text-gray-900">TZS {{ number_format($stats['bankBalance'], 0) }}</p></div>
        </div>
        <div class="bg-white rounded-xl border p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div><p class="text-xs text-gray-500 font-medium">Mobile Money</p><p class="text-lg font-bold text-gray-900">TZS {{ number_format($stats['mobileBalance'], 0) }}</p></div>
        </div>
    </div>

    {{-- Quick Entry System --}}
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Quick Actions
        </h3>
        <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-9 gap-2">
            <a href="{{ route('pos.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-gray-50 hover:bg-emerald-50 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 group-hover:bg-emerald-500 grid place-items-center transition-colors">
                    <svg class="w-4 h-4 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-gray-700 text-center">Add Sale</span>
            </a>
            <a href="{{ route('suppliers.purchases') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-gray-50 hover:bg-sky-50 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-sky-100 group-hover:bg-sky-500 grid place-items-center transition-colors">
                    <svg class="w-4 h-4 text-sky-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-gray-700 text-center">Add Purchase</span>
            </a>
            <a href="{{ route('payments.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-gray-50 hover:bg-emerald-50 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 group-hover:bg-emerald-500 grid place-items-center transition-colors">
                    <svg class="w-4 h-4 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-gray-700 text-center">Receive Payment</span>
            </a>
            <a href="{{ route('payments.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-gray-50 hover:bg-red-50 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-red-100 group-hover:bg-red-500 grid place-items-center transition-colors">
                    <svg class="w-4 h-4 text-red-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-gray-700 text-center">Make Payment</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-gray-50 hover:bg-red-50 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-red-100 group-hover:bg-red-500 grid place-items-center transition-colors">
                    <svg class="w-4 h-4 text-red-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-gray-700 text-center">Add Expense</span>
            </a>
            <a href="{{ route('incomes.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-gray-50 hover:bg-violet-50 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-violet-100 group-hover:bg-violet-500 grid place-items-center transition-colors">
                    <svg class="w-4 h-4 text-violet-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-gray-700 text-center">Add Income</span>
            </a>
            <a href="{{ route('customers.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-gray-50 hover:bg-emerald-50 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 group-hover:bg-emerald-500 grid place-items-center transition-colors">
                    <svg class="w-4 h-4 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-gray-700 text-center">Add Customer</span>
            </a>
            <a href="{{ route('suppliers.index') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-gray-50 hover:bg-sky-50 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-sky-100 group-hover:bg-sky-500 grid place-items-center transition-colors">
                    <svg class="w-4 h-4 text-sky-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-gray-700 text-center">Add Supplier</span>
            </a>
            <a href="{{ route('products.create') }}" class="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-gray-50 hover:bg-amber-50 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-amber-100 group-hover:bg-amber-500 grid place-items-center transition-colors">
                    <svg class="w-4 h-4 text-amber-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-gray-700 text-center">Add Product</span>
            </a>
        </div>
    </div>

    {{-- Charts Row 1: Sales Chart + Income vs Expense --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Sales (Last 14 Days)</h3>
                    <p class="text-xs text-gray-400">Daily revenue trend</p>
                </div>
                <a href="{{ route('reports.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Full Report →</a>
            </div>
            <canvas id="salesChart" height="200"></canvas>
        </div>

        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Income vs Expense (7 Days)</h3>
                    <p class="text-xs text-gray-400">Daily income vs expenses</p>
                </div>
            </div>
            <canvas id="incomeExpenseChart" height="200"></canvas>
        </div>
    </div>

    {{-- Charts Row 2: Monthly Sales vs Purchases + Top Products --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Sales vs Purchases (12 Months)</h3>
                    <p class="text-xs text-gray-400">Monthly trend comparison</p>
                </div>
            </div>
            <canvas id="monthlyChart" height="200"></canvas>
        </div>

        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Top Products (Month)</h3>
            <div class="space-y-2">
                @forelse($topProducts as $i => $p)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                    <span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 font-bold grid place-items-center text-[10px] shrink-0">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0"><p class="text-xs font-semibold text-gray-900 truncate">{{ $p->product->name }}</p><p class="text-[10px] text-gray-400">{{ $p->total_qty }} sold</p></div>
                    <span class="text-[11px] font-bold text-emerald-600 shrink-0">TZS {{ number_format($p->total_revenue, 0) }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent sales & alerts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent sales --}}
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Recent Sales</h3>
                <a href="{{ route('reports.sales') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All →</a>
            </div>
            <div class="p-5 space-y-2">
                @forelse($recentSales as $sale)
                <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 grid place-items-center text-xs font-bold shrink-0">{{ strtoupper(substr($sale->customer?->name ?? 'K', 0, 1)) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $sale->receipt_no }} • {{ $sale->created_at->format('d/m H:i') }}</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 shrink-0">TZS {{ number_format($sale->total, 0) }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-8">No sales yet. <a href="{{ route('pos.index') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">Start selling</a></p>
                @endforelse
            </div>
        </div>

        {{-- Low stock & top customers --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border overflow-hidden">
                <div class="px-5 py-4 border-b">
                    <h3 class="text-sm font-semibold text-gray-900">Low Stock Alerts</h3>
                </div>
                <div class="p-5 space-y-2">
                    @forelse($lowStockItems as $item)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="w-2 h-2 rounded-full shrink-0 {{ $item->qty <= 0 ? 'bg-red-500' : 'bg-amber-500' }}"></span>
                        <div class="flex-1 min-w-0"><p class="text-xs font-semibold text-gray-900 truncate">{{ $item->product->name }}</p><p class="text-[10px] text-gray-400">{{ $item->branch->name }}</p></div>
                        <span class="text-xs font-bold shrink-0 {{ $item->qty <= 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $item->qty }} {{ $item->product->unit }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        All stock is good!
                    </p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border overflow-hidden">
                <div class="px-5 py-4 border-b">
                    <h3 class="text-sm font-semibold text-gray-900">Top Customers (Month)</h3>
                </div>
                <div class="p-5 space-y-2">
                    @forelse($topCustomers as $i => $c)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 font-bold grid place-items-center text-[10px] shrink-0">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0"><p class="text-xs font-semibold text-gray-900 truncate">{{ $c->customer?->name ?? 'Unknown' }}</p><p class="text-[10px] text-gray-400">{{ $c->orders }} orders</p></div>
                        <span class="text-[11px] font-bold text-emerald-600 shrink-0">TZS {{ number_format($c->total_spent, 0) }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">No data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Sales Chart (14 days)
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 200);
salesGradient.addColorStop(0, 'rgba(2,73,56,0.8)');
salesGradient.addColorStop(1, 'rgba(2,73,56,0.1)');

new Chart(salesCtx, {
    type: 'bar',
    data: {
        labels: {{ json_encode($dailyLabels) }},
        datasets: [{
            label: 'Sales (TZS)',
            data: {{ json_encode($dailyRevenue) }},
            backgroundColor: salesGradient,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1200, easing: 'easeOutQuart' },
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'TZS ' + (v/1000) + 'k' } },
            x: { grid: { display: false } }
        }
    }
});

// Income vs Expense Chart (7 days)
const ieCtx = document.getElementById('incomeExpenseChart').getContext('2d');
new Chart(ieCtx, {
    type: 'bar',
    data: {
        labels: {{ json_encode(array_map(fn($i) => now()->subDays(6-$i)->format('D'), range(0,6))) }},
        datasets: [
            { label: 'Income', data: {{ json_encode($dailyIncome) }}, backgroundColor: 'rgba(2,73,56,0.7)', borderRadius: 6, borderSkipped: false },
            { label: 'Expenses', data: {{ json_encode($dailyExpenses) }}, backgroundColor: 'rgba(239,68,68,0.7)', borderRadius: 6, borderSkipped: false },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1200, easing: 'easeOutQuart' },
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11, family: 'Nunito' }, padding: 15 } } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'TZS ' + (v/1000) + 'k' } },
            x: { grid: { display: false } }
        }
    }
});

// Monthly Sales vs Purchases (12 months)
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {{ json_encode($monthlyLabels) }},
        datasets: [
            { label: 'Sales', data: {{ json_encode($monthlySales) }}, borderColor: '#024938', backgroundColor: 'rgba(2,73,56,0.1)', fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#024938' },
            { label: 'Purchases', data: {{ json_encode($monthlyPurchases) }}, borderColor: '#f9ac00', backgroundColor: 'rgba(249,172,0,0.1)', fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#f9ac00' },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1500, easing: 'easeOutQuart' },
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11, family: 'Nunito' }, padding: 15 } } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'TZS ' + (v/1000000) + 'M' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endsection
