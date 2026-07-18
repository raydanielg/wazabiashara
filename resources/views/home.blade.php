@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 right-0 w-32 h-32 bg-gold-500/10 rounded-full -mr-12 -mb-12"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black mb-1">Karibu, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-emerald-100 text-sm font-semibold">Biashara yako, Mkononi mwako — Muhtasari wa leo.</p>
            </div>
            @if(auth()->user()->isBusinessAdmin() || auth()->user()->isAdmin())
            <div class="flex items-center gap-2">
                <select onchange="window.location='{{ route('branches.switch', '__ID__') }}'.replace('__ID__', this.value)" class="px-3 py-1.5 rounded-lg bg-white/20 text-white text-sm font-bold outline-none border-0">
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" @selected($branchId == $b->id) class="text-gray-800">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 text-white shadow-card animate-slide">
            <div class="flex items-center justify-between">
                <div><p class="text-xs font-bold text-emerald-100 uppercase">Mauzo ya Leo</p><p class="font-black text-2xl mt-2 count-up">TZS {{ number_format($stats['todaySales'], 0) }}</p></div>
                <div class="h-12 w-12 rounded-xl bg-white/20 grid place-items-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg></div>
            </div>
            <p class="text-xs text-emerald-100 mt-2 font-semibold">{{ $stats['todayCount'] }} mauzo leo</p>
        </div>

        <div class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-2xl p-5 text-white shadow-card animate-slide-delay-1">
            <div class="flex items-center justify-between">
                <div><p class="text-xs font-bold text-gold-100 uppercase">Faida ya Leo</p><p class="font-black text-2xl mt-2 count-up">TZS {{ number_format($stats['todayProfit'], 0) }}</p></div>
                <div class="h-12 w-12 rounded-xl bg-white/20 grid place-items-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
            </div>
            <p class="text-xs text-gold-100 mt-2 font-semibold">Ghafi (Gross Profit)</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-card border-2 border-gray-100 animate-slide-delay-2">
            <div class="flex items-center justify-between">
                <div><p class="text-xs font-bold text-gray-400 uppercase">Mauzo Wiki</p><p class="font-black text-2xl mt-2 text-emerald-600 count-up">TZS {{ number_format($stats['weekSales'], 0) }}</p></div>
                <div class="h-12 w-12 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            </div>
            <p class="text-xs text-gray-400 mt-2 font-semibold">Siku 7 zilizopita</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-card border-2 border-gray-100 animate-slide-delay-3">
            <div class="flex items-center justify-between">
                <div><p class="text-xs font-bold text-gray-400 uppercase">Mauzo Mwezi</p><p class="font-black text-2xl mt-2 text-emerald-600 count-up">TZS {{ number_format($stats['monthSales'], 0) }}</p></div>
                <div class="h-12 w-12 rounded-xl bg-gold-50 grid place-items-center"><svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
            </div>
            <p class="text-xs text-gray-400 mt-2 font-semibold">{{ now()->format('F Y') }}</p>
        </div>
    </div>

    {{-- Secondary KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-card border-2 border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase">Bidhaa</p>
            <p class="font-black text-xl mt-1 text-emerald-600">{{ $stats['totalProducts'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-card border-2 border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase">Stoo Iliyochache</p>
            <p class="font-black text-xl mt-1 {{ $stats['lowStockCount'] > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $stats['lowStockCount'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-card border-2 border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase">Wateja</p>
            <p class="font-black text-xl mt-1 text-emerald-600">{{ $stats['totalCustomers'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-card border-2 border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase">Madeni</p>
            <p class="font-black text-xl mt-1 {{ $stats['totalDebts'] > 0 ? 'text-gold-600' : 'text-emerald-600' }}">TZS {{ number_format($stats['totalDebts'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-card border-2 border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase">Matumizi Leo</p>
            <p class="font-black text-xl mt-1 text-red-600">TZS {{ number_format($stats['todayExpenses'], 0) }}</p>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Revenue chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-black text-lg text-emerald-700">📈 Mauzo ya Siku 14 Zilizopita</h3>
                <a href="{{ route('reports.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800">Ripoti Kamili →</a>
            </div>
            @php $maxRev = max($dailyRevenue) ?: 1; @endphp
            <div class="flex items-end gap-1.5 h-48">
                @foreach($dailyRevenue as $i => $rev)
                <div class="flex-1 flex flex-col items-center gap-1 group">
                    <div class="relative w-full flex flex-col justify-end h-full">
                        <div class="w-full rounded-t-md bg-gradient-to-t from-emerald-600 to-emerald-400 group-hover:from-gold-500 group-hover:to-gold-300 transition-all" style="height: {{ ($rev / $maxRev) * 100 }}%"></div>
                        <div class="absolute -top-6 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-emerald-900 text-white text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">TZS {{ number_format($rev, 0) }}</div>
                    </div>
                    <span class="text-[9px] font-bold text-gray-400">{{ $dailyLabels[$i] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <h3 class="font-black text-lg text-emerald-700 mb-4">⚡ Vitendo vya Haraka</h3>
            <div class="space-y-2">
                <a href="{{ route('pos.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 transition-colors">
                    <span class="h-10 w-10 rounded-xl bg-emerald-500 text-white grid place-items-center font-black">🛒</span>
                    <div><p class="font-bold text-sm text-gray-700">Fungua POS</p><p class="text-xs text-gray-400">Anza kuuza sasa</p></div>
                </a>
                <a href="{{ route('products.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-gold-50 hover:bg-gold-100 transition-colors">
                    <span class="h-10 w-10 rounded-xl bg-gold-500 text-white grid place-items-center font-black">📦</span>
                    <div><p class="font-bold text-sm text-gray-700">Ongeza Bidhaa</p><p class="text-xs text-gray-400">Sajili bidhaa mpya</p></div>
                </a>
                <a href="{{ route('suppliers.purchases') }}" class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 hover:bg-blue-100 transition-colors">
                    <span class="h-10 w-10 rounded-xl bg-blue-500 text-white grid place-items-center font-black">📋</span>
                    <div><p class="font-bold text-sm text-gray-700">Manunuzi</p><p class="text-xs text-gray-400">Rekodi manunuzi</p></div>
                </a>
                <a href="{{ route('shifts.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors">
                    <span class="h-10 w-10 rounded-xl bg-purple-500 text-white grid place-items-center font-black">⏰</span>
                    <div><p class="font-bold text-sm text-gray-700">Fungua Zamu</p><p class="text-xs text-gray-400">Anza zamu yako</p></div>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent sales & alerts --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Recent sales --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-black text-lg text-emerald-700">🛒 Mauzo ya Karibuni</h3>
                <a href="{{ route('reports.sales') }}" class="text-xs font-bold text-emerald-600">Yote →</a>
            </div>
            <div class="space-y-2">
                @forelse($recentSales as $sale)
                <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-emerald-50/50 transition-colors">
                    <div class="h-9 w-9 rounded-lg bg-emerald-100 text-emerald-700 grid place-items-center font-black text-xs">{{ strtoupper(substr($sale->customer?->name ?? 'K', 0, 1)) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-gray-700 truncate">{{ $sale->customer?->name ?? 'Mteja wa Kawaida' }}</p>
                        <p class="text-xs text-gray-400">{{ $sale->receipt_no }} • {{ $sale->created_at->format('d/m H:i') }}</p>
                    </div>
                    <span class="font-black text-sm text-emerald-600">TZS {{ number_format($sale->total, 0) }}</span>
                </div>
                @empty
                <p class="text-gray-400 font-bold text-center py-8 text-sm">Hakuna mauzo bado. <a href="{{ route('pos.index') }}" class="text-emerald-600 underline">Anza kuuza</a></p>
                @endforelse
            </div>
        </div>

        {{-- Low stock & top products --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h3 class="font-black text-lg text-emerald-700 mb-4">⚠️ Tahadhari za Stoo</h3>
                <div class="space-y-2">
                    @forelse($lowStockItems as $item)
                    <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-red-50/50">
                        <span class="h-2 w-2 rounded-full {{ $item->qty <= 0 ? 'bg-red-500' : 'bg-gold-500' }}"></span>
                        <div class="flex-1"><p class="font-bold text-sm text-gray-700">{{ $item->product->name }}</p><p class="text-xs text-gray-400">{{ $item->branch->name }}</p></div>
                        <span class="font-black text-sm {{ $item->qty <= 0 ? 'text-red-600' : 'text-gold-600' }}">{{ $item->qty }} {{ $item->product->unit }}</span>
                    </div>
                    @empty
                    <p class="text-gray-400 font-bold text-center py-4 text-sm">Stoo yote nzuri! ✅</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-card p-6">
                <h3 class="font-black text-lg text-emerald-700 mb-4">🏆 Bidhaa Bora (Mwezi)</h3>
                <div class="space-y-2">
                    @forelse($topProducts as $i => $p)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-emerald-50/50">
                        <span class="h-7 w-7 rounded-lg bg-emerald-100 text-emerald-700 font-black grid place-items-center text-xs">{{ $i + 1 }}</span>
                        <div class="flex-1"><p class="font-bold text-sm text-gray-700">{{ $p->product->name }}</p><p class="text-xs text-gray-400">{{ $p->total_qty }} vimeuzwa</p></div>
                        <span class="font-black text-xs text-emerald-600">TZS {{ number_format($p->total_revenue, 0) }}</span>
                    </div>
                    @empty
                    <p class="text-gray-400 font-bold text-center py-4 text-sm">Hakuna data bado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
