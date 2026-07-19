@extends('layouts.dashboard')

@section('title', 'Ripoti')
@section('page_title', 'Ripoti & Takwimu')

@section('content')
@php
$fmt = fn($n) => $n >= 1000000000 ? number_format($n/1000000000,2).'B' : ($n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n,0)));
$fmtFull = fn($n) => number_format($n, 0);
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Ripoti & Takwimu
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Muhtasari wa kifedha wa biashara yako — Mwaka {{ now()->year }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.sales') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-all">Mauzo</a>
            <a href="{{ route('reports.inventory') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-all">Stoo</a>
            <a href="{{ route('reports.profit-loss') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-all">Faida & Hasara</a>
        </div>
    </div>

    {{-- ====== ANNUAL GOALS - CIRCULAR PROGRESS ====== --}}
    <div class="bg-gradient-to-br from-emerald-700 via-emerald-800 to-emerald-900 rounded-2xl p-6 text-white relative overflow-hidden shadow-xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gold-500/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full -ml-20 -mb-20"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Malengo ya Mwaka {{ now()->year }}
                    </h2>
                    <p class="text-emerald-200 text-xs mt-0.5">Fuatilia maendeleo ya malengo yako ya kifedha</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-white/10 text-[10px] font-bold text-gold-300 border border-gold-400/20">{{ now()->format('M Y') }}</span>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Sales Goal --}}
                <div class="bg-white/5 rounded-xl p-4 border border-white/10 backdrop-blur-sm text-center">
                    <div class="relative inline-flex items-center justify-center mb-3">
                        <svg class="w-24 h-24 -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="6"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#f9ac00" stroke-width="6" stroke-linecap="round"
                                stroke-dasharray="{{ 2 * pi() * 42 }}" stroke-dashoffset="{{ 2 * pi() * 42 * (1 - $salesProgress / 100) }}"
                                style="transition: stroke-dashoffset 1.5s ease-out;"/>
                        </svg>
                        <span class="absolute text-lg font-bold text-gold-400">{{ round($salesProgress) }}%</span>
                    </div>
                    <p class="text-[10px] font-semibold text-emerald-200 uppercase">Mauzo</p>
                    <p class="text-sm font-bold mt-0.5">TZS {{ $fmt($yearSales) }}</p>
                    <p class="text-[10px] text-emerald-300/60">Lengo: TZS {{ $fmt($annualSalesGoal) }}</p>
                </div>
                {{-- Profit Goal --}}
                <div class="bg-white/5 rounded-xl p-4 border border-white/10 backdrop-blur-sm text-center">
                    <div class="relative inline-flex items-center justify-center mb-3">
                        <svg class="w-24 h-24 -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="6"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#4db5a8" stroke-width="6" stroke-linecap="round"
                                stroke-dasharray="{{ 2 * pi() * 42 }}" stroke-dashoffset="{{ 2 * pi() * 42 * (1 - $profitProgress / 100) }}"
                                style="transition: stroke-dashoffset 1.5s ease-out;"/>
                        </svg>
                        <span class="absolute text-lg font-bold text-emerald-300">{{ round($profitProgress) }}%</span>
                    </div>
                    <p class="text-[10px] font-semibold text-emerald-200 uppercase">Faida</p>
                    <p class="text-sm font-bold mt-0.5">TZS {{ $fmt($yearProfit) }}</p>
                    <p class="text-[10px] text-emerald-300/60">Lengo: TZS {{ $fmt($annualProfitGoal) }}</p>
                </div>
                {{-- Expense Budget --}}
                <div class="bg-white/5 rounded-xl p-4 border border-white/10 backdrop-blur-sm text-center">
                    <div class="relative inline-flex items-center justify-center mb-3">
                        <svg class="w-24 h-24 -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="6"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke="{{ $expenseProgress > 80 ? '#ef4444' : '#80cbc0' }}" stroke-width="6" stroke-linecap="round"
                                stroke-dasharray="{{ 2 * pi() * 42 }}" stroke-dashoffset="{{ 2 * pi() * 42 * (1 - $expenseProgress / 100) }}"
                                style="transition: stroke-dashoffset 1.5s ease-out;"/>
                        </svg>
                        <span class="absolute text-lg font-bold {{ $expenseProgress > 80 ? 'text-red-400' : 'text-teal-300' }}">{{ round($expenseProgress) }}%</span>
                    </div>
                    <p class="text-[10px] font-semibold text-emerald-200 uppercase">Matumizi</p>
                    <p class="text-sm font-bold mt-0.5">TZS {{ $fmt($yearExpenses) }}</p>
                    <p class="text-[10px] text-emerald-300/60">Bajeti: TZS {{ $fmt($annualExpenseBudget) }}</p>
                </div>
                {{-- Customer Goal --}}
                <div class="bg-white/5 rounded-xl p-4 border border-white/10 backdrop-blur-sm text-center">
                    <div class="relative inline-flex items-center justify-center mb-3">
                        <svg class="w-24 h-24 -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="6"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#1a9f8e" stroke-width="6" stroke-linecap="round"
                                stroke-dasharray="{{ 2 * pi() * 42 }}" stroke-dashoffset="{{ 2 * pi() * 42 * (1 - $customerProgress / 100) }}"
                                style="transition: stroke-dashoffset 1.5s ease-out;"/>
                        </svg>
                        <span class="absolute text-lg font-bold text-teal-300">{{ round($customerProgress) }}%</span>
                    </div>
                    <p class="text-[10px] font-semibold text-emerald-200 uppercase">Wateja</p>
                    <p class="text-sm font-bold mt-0.5">{{ $totalCustomers }}</p>
                    <p class="text-[10px] text-emerald-300/60">Lengo: {{ round($annualCustomerGoal) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== KPI CARDS ====== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 text-white shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 12l3-3 3 3 5-5"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-emerald-100 uppercase">Mauzo Leo</span>
                </div>
                <p class="font-bold text-2xl">TZS {{ $fmt($todaySales) }}</p>
                <p class="text-[10px] text-emerald-200 mt-1">Faida: TZS {{ $fmt($todayProfit) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-2xl p-5 text-white shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gold-100 uppercase">Mauzo Wiki</span>
                </div>
                <p class="font-bold text-2xl">TZS {{ $fmt($weekSales) }}</p>
                <p class="text-[10px] text-gold-200 mt-1">Mwezi: TZS {{ $fmt($monthSales) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-50 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase">Mauzo Mwaka</span>
                </div>
                <p class="font-bold text-2xl text-emerald-600">TZS {{ $fmt($yearSales) }}</p>
                <p class="text-[10px] text-gray-400 mt-1">Faida: TZS {{ $fmt($yearProfit) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-red-50 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase">Matumizi Mwaka</span>
                </div>
                <p class="font-bold text-2xl text-red-500">TZS {{ $fmt($yearExpenses) }}</p>
                <p class="text-[10px] text-gray-400 mt-1">Mapato: TZS {{ $fmt($yearIncome) }}</p>
            </div>
        </div>
    </div>

    {{-- ====== SECONDARY KPIs ====== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border p-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
            <div><p class="text-[10px] font-semibold text-gray-400 uppercase">Faida Mwezi</p><p class="font-bold text-sm text-emerald-600">TZS {{ $fmt($monthProfit) }}</p></div>
        </div>
        <div class="bg-white rounded-xl border p-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg></div>
            <div><p class="text-[10px] font-semibold text-gray-400 uppercase">Matumizi Mwezi</p><p class="font-bold text-sm text-red-500">TZS {{ $fmt($monthExpenses) }}</p></div>
        </div>
        <div class="bg-white rounded-xl border p-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-gold-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
            <div><p class="text-[10px] font-semibold text-gray-400 uppercase">Madeni Jumla</p><p class="font-bold text-sm text-gold-600">TZS {{ $fmt($totalDebts) }}</p></div>
        </div>
        <div class="bg-white rounded-xl border p-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
            <div><p class="text-[10px] font-semibold text-gray-400 uppercase">Madeni Yaliyochelewa</p><p class="font-bold text-sm text-red-500">TZS {{ $fmt($overdueDebts) }}</p></div>
        </div>
    </div>

    {{-- ====== CHARTS ROW 1: Sales Trend + Payment Methods ====== --}}
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Sales 7 days area chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Mauzo & Faida ya Siku 7
                </h3>
                <div class="flex gap-2 text-[10px]">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-600"></span>Mauzo</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gold-500"></span>Faida</span>
                </div>
            </div>
            <canvas id="salesChart" height="220"></canvas>
        </div>

        {{-- Payment methods doughnut --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Njia za Malipo
            </h3>
            <canvas id="paymentChart" height="220"></canvas>
        </div>
    </div>

    {{-- ====== CHARTS ROW 2: Monthly Overview + Category Radar ====== --}}
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Monthly Sales vs Expenses vs Profit --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Mauzo vs Matumizi vs Faida (Miezi 12)
                </h3>
                <div class="flex gap-2 text-[10px]">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-600"></span>Mauzo</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400"></span>Matumizi</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gold-500"></span>Faida</span>
                </div>
            </div>
            <canvas id="monthlyChart" height="220"></canvas>
        </div>

        {{-- Category Performance Radar --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Aina za Bidhaa
            </h3>
            <canvas id="categoryChart" height="220"></canvas>
        </div>
    </div>

    {{-- ====== TOP PRODUCTS WITH PROGRESS BARS ====== --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Bidhaa Zinazouzwa Zaidi
            </h3>
            <div class="space-y-3">
                @forelse($topProducts as $i => $p)
                @php $pct = round(($p->total_qty / $maxProductQty) * 100); @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 font-bold grid place-items-center text-[10px]">{{ $i + 1 }}</span>
                            <span class="text-xs font-semibold text-gray-700">{{ $p->product->name }}</span>
                        </div>
                        <span class="text-xs font-bold text-emerald-600">{{ $p->total_qty }} vimeuzwa</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600 transition-all duration-1000 ease-out" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">TZS {{ number_format($p->total_revenue, 0) }}</p>
                </div>
                @empty
                <p class="text-gray-400 font-medium text-center py-8">Hakuna data.</p>
                @endforelse
            </div>
        </div>

        {{-- Top Customers --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Wateja Wakuu (Mwezi)
            </h3>
            <div class="space-y-2">
                @forelse($topCustomers as $i => $c)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                    <span class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 text-white font-bold grid place-items-center text-xs">{{ $i + 1 }}</span>
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-700">{{ $c->customer?->name ?? 'Mteja' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $c->orders_count }} maagizo</p>
                    </div>
                    <span class="font-bold text-sm text-emerald-600">TZS {{ $fmt($c->total_spent) }}</span>
                </div>
                @empty
                <p class="text-gray-400 font-medium text-center py-8">Hakuna data.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ====== LOW STOCK ALERTS ====== --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Tahadhari za Stoo
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @forelse($lowStock as $s)
            <div class="border rounded-xl p-3 {{ $s->qty <= 0 ? 'border-red-200 bg-red-50/30' : 'border-amber-200 bg-amber-50/30' }}">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full {{ $s->qty <= 0 ? 'bg-red-500' : 'bg-amber-500' }}"></span>
                    <span class="text-xs font-semibold text-gray-700 truncate">{{ $s->product->name }}</span>
                </div>
                <p class="text-[10px] text-gray-400">{{ $s->branch->name }}</p>
                <p class="font-bold text-sm {{ $s->qty <= 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $s->qty }} {{ $s->product->unit }}</p>
            </div>
            @empty
            <div class="col-span-full text-center py-8 text-sm text-gray-400">Stoo yote ni nzuri!</div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Data
const salesData = {{ $last7Days->pluck('total') }};
const profitData = {{ $last7Days->pluck('profit') }};
const salesLabels = {{ $last7Days->pluck('date') }};
const paymentLabels = {{ $salesByMethod->map(fn($m) => ucfirst(str_replace('_', ' ', $m->payment_method))) }};
const paymentTotals = {{ $salesByMethod->pluck('total') }};
const monthNames = {{ json_encode($monthNames) }};
const monthlySales = {{ json_encode($monthlySales) }};
const monthlyExpenses = {{ json_encode($monthlyExpenses) }};
const monthlyProfit = {{ json_encode($monthlyProfit) }};
const categoryLabels = {{ $categoryPerformance->map(fn($c) => $c->name) }};
const categoryTotals = {{ $categoryPerformance->map(fn($c) => (float)$c->total) }};

// Chart defaults
Chart.defaults.font.family = 'Nunito, sans-serif';
Chart.defaults.font.size = 11;
Chart.defaults.color = '#6b7280';

// === 1. Sales & Profit Area Chart ===
const ctx1 = document.getElementById('salesChart').getContext('2d');
const grad1 = ctx1.createLinearGradient(0, 0, 0, 250);
grad1.addColorStop(0, 'rgba(2,73,56,0.7)');
grad1.addColorStop(1, 'rgba(2,73,56,0.02)');
const grad2 = ctx1.createLinearGradient(0, 0, 0, 250);
grad2.addColorStop(0, 'rgba(249,172,0,0.5)');
grad2.addColorStop(1, 'rgba(249,172,0,0.02)');

new Chart(ctx1, {
    type: 'line',
    data: {
        labels: salesLabels,
        datasets: [
            {
                label: 'Mauzo',
                data: salesData,
                borderColor: '#024938',
                backgroundColor: grad1,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#024938',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            },
            {
                label: 'Faida',
                data: profitData,
                borderColor: '#f9ac00',
                backgroundColor: grad2,
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#f9ac00',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1500, easing: 'easeOutQuart' },
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(2,73,56,0.95)',
                titleFont: { weight: 'bold', size: 12 },
                bodyFont: { size: 11 },
                padding: 12,
                cornerRadius: 8,
                callbacks: { label: ctx => ctx.dataset.label + ': TZS ' + (ctx.parsed.y/1000).toFixed(1) + 'k' }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'TZS ' + (v/1000) + 'k' }, grid: { color: 'rgba(0,0,0,0.04)' } },
            x: { grid: { display: false } }
        }
    }
});

// === 2. Payment Methods Doughnut ===
const ctx2 = document.getElementById('paymentChart').getContext('2d');
const colors = ['#024938', '#f9ac00', '#1a9f8e', '#d49700', '#4db5a8', '#80cbc0'];
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: paymentLabels.length ? paymentLabels : ['Hakuna data'],
        datasets: [{
            data: paymentTotals.length ? paymentTotals : [1],
            backgroundColor: colors,
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1500, easing: 'easeOutQuart', animateRotate: true },
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 10, family: 'Nunito' }, padding: 12, usePointStyle: true, pointStyle: 'circle' } },
            tooltip: {
                backgroundColor: 'rgba(2,73,56,0.95)',
                padding: 12,
                cornerRadius: 8,
                callbacks: { label: ctx => ctx.label + ': TZS ' + (ctx.parsed/1000).toFixed(1) + 'k' }
            }
        }
    }
});

// === 3. Monthly Sales vs Expenses vs Profit ===
const ctx3 = document.getElementById('monthlyChart').getContext('2d');
const gradSales = ctx3.createLinearGradient(0, 0, 0, 250);
gradSales.addColorStop(0, 'rgba(2,73,56,0.8)');
gradSales.addColorStop(1, 'rgba(2,73,56,0.2)');
const gradExp = ctx3.createLinearGradient(0, 0, 0, 250);
gradExp.addColorStop(0, 'rgba(239,68,68,0.6)');
gradExp.addColorStop(1, 'rgba(239,68,68,0.1)');

new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: monthNames,
        datasets: [
            {
                label: 'Mauzo',
                data: monthlySales,
                backgroundColor: gradSales,
                borderRadius: 6,
                borderSkipped: false,
                barPercentage: 0.6,
            },
            {
                label: 'Matumizi',
                data: monthlyExpenses,
                backgroundColor: gradExp,
                borderRadius: 6,
                borderSkipped: false,
                barPercentage: 0.6,
            },
            {
                label: 'Faida',
                data: monthlyProfit,
                type: 'line',
                borderColor: '#f9ac00',
                backgroundColor: 'rgba(249,172,0,0.1)',
                borderWidth: 3,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#f9ac00',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                fill: false,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1500, easing: 'easeOutQuart' },
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(2,73,56,0.95)',
                padding: 12,
                cornerRadius: 8,
                callbacks: { label: ctx => ctx.dataset.label + ': TZS ' + (ctx.parsed.y/1000).toFixed(1) + 'k' }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'TZS ' + (v/1000) + 'k' }, grid: { color: 'rgba(0,0,0,0.04)' } },
            x: { grid: { display: false } }
        }
    }
});

// === 4. Category Performance Radar ===
const ctx4 = document.getElementById('categoryChart').getContext('2d');
new Chart(ctx4, {
    type: 'radar',
    data: {
        labels: categoryLabels.length ? categoryLabels : ['Hakuna data'],
        datasets: [{
            label: 'Mauzo',
            data: categoryTotals.length ? categoryTotals : [0],
            backgroundColor: 'rgba(2,73,56,0.15)',
            borderColor: '#024938',
            borderWidth: 2,
            pointBackgroundColor: '#f9ac00',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1500, easing: 'easeOutQuart' },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(2,73,56,0.95)',
                padding: 12,
                cornerRadius: 8,
                callbacks: { label: ctx => ctx.label + ': TZS ' + (ctx.parsed.r/1000).toFixed(1) + 'k' }
            }
        },
        scales: {
            r: {
                beginAtZero: true,
                ticks: { display: false },
                grid: { color: 'rgba(0,0,0,0.06)' },
                angleLines: { color: 'rgba(0,0,0,0.06)' },
                pointLabels: { font: { size: 10, family: 'Nunito', weight: '600' }, color: '#374151' }
            }
        }
    }
});
</script>
@endsection
