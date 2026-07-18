@extends('layouts.dashboard')

@section('title', 'Ripoti')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-emerald-700">📊 Ripoti & Takwimu</h1>
        <div class="flex gap-2">
            <a href="{{ route('reports.sales') }}" class="px-4 py-2 rounded-xl border-2 border-emerald-200 text-emerald-600 font-bold text-sm hover:bg-emerald-50">Mauzo</a>
            <a href="{{ route('reports.inventory') }}" class="px-4 py-2 rounded-xl border-2 border-emerald-200 text-emerald-600 font-bold text-sm hover:bg-emerald-50">Stoo</a>
            <a href="{{ route('reports.profit-loss') }}" class="px-4 py-2 rounded-xl border-2 border-emerald-200 text-emerald-600 font-bold text-sm hover:bg-emerald-50">Faida & Hasara</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 text-white shadow-card">
            <p class="text-xs font-bold text-emerald-100 uppercase">Mauzo ya Leo</p>
            <p class="font-black text-2xl mt-2">TZS {{ number_format($todaySales, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-2xl p-5 text-white shadow-card">
            <p class="text-xs font-bold text-gold-100 uppercase">Faida ya Leo</p>
            <p class="font-black text-2xl mt-2">TZS {{ number_format($todayProfit, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-card border-2 border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase">Mauzo ya Wiki</p>
            <p class="font-black text-2xl mt-2 text-emerald-600">TZS {{ number_format($weekSales, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-card border-2 border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase">Mauzo ya Mwezi</p>
            <p class="font-black text-2xl mt-2 text-emerald-600">TZS {{ number_format($monthSales, 0) }}</p>
        </div>
    </div>

    <!-- Secondary KPIs -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-card border-2 border-gray-100"><p class="text-xs font-bold text-gray-400">Matumizi ya Leo</p><p class="font-black text-lg text-red-600 mt-1">TZS {{ number_format($todayExpenses, 0) }}</p></div>
        <div class="bg-white rounded-2xl p-4 shadow-card border-2 border-gray-100"><p class="text-xs font-bold text-gray-400">Matumizi ya Mwezi</p><p class="font-black text-lg text-red-600 mt-1">TZS {{ number_format($monthExpenses, 0) }}</p></div>
        <div class="bg-white rounded-2xl p-4 shadow-card border-2 border-gray-100"><p class="text-xs font-bold text-gray-400">Madeni Jumla</p><p class="font-black text-lg text-gold-600 mt-1">TZS {{ number_format($totalDebts, 0) }}</p></div>
        <div class="bg-white rounded-2xl p-4 shadow-card border-2 border-gray-100"><p class="text-xs font-bold text-gray-400">Madeni Yaliyochelewa</p><p class="font-black text-lg text-red-600 mt-1">TZS {{ number_format($overdueDebts, 0) }}</p></div>
    </div>

    <!-- Charts -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Sales chart -->
        <div class="bg-white rounded-2xl shadow-card p-6">
            <h3 class="font-black text-lg text-emerald-700 mb-4">📈 Mauzo ya Siku 7 Zilizopita</h3>
            <div class="flex items-end gap-2 h-48">
                @foreach($last7Days as $day)
                @php $maxVal = max($last7Days->pluck('total')->max(), 1); $height = ($day['total'] / $maxVal) * 100; @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-[10px] font-bold text-gray-400">{{ number_format($day['total'] / 1000, 0) }}k</span>
                    <div class="w-full rounded-t-lg bg-gradient-to-t from-emerald-600 to-emerald-400 transition-all hover:from-gold-500 hover:to-gold-300" style="height: {{ $height }}%"></div>
                    <span class="text-[10px] font-bold text-gray-500">{{ $day['date'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Payment methods -->
        <div class="bg-white rounded-2xl shadow-card p-6">
            <h3 class="font-black text-lg text-emerald-700 mb-4">💳 Mauzo kwa Njia ya Malipo (Mwezi)</h3>
            <div class="space-y-3">
                @forelse($salesByMethod as $m)
                @php $totalMethod = $salesByMethod->sum('total'); $pct = $totalMethod > 0 ? ($m->total / $totalMethod) * 100 : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm font-bold mb-1"><span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $m->payment_method)) }}</span><span class="text-emerald-600">TZS {{ number_format($m->total, 0) }}</span></div>
                    <div class="h-3 rounded-full bg-gray-100 overflow-hidden"><div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-gold-400" style="width: {{ $pct }}%"></div></div>
                </div>
                @empty
                <p class="text-gray-400 font-bold text-center py-8">Hakuna data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top products & low stock -->
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-card p-6">
            <h3 class="font-black text-lg text-emerald-700 mb-4">🏆 Bidhaa Zinazouzwa Zaidi (Mwezi)</h3>
            <div class="space-y-2">
                @forelse($topProducts as $i => $p)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-emerald-50/50">
                    <span class="h-8 w-8 rounded-lg bg-emerald-100 text-emerald-700 font-black grid place-items-center text-sm">{{ $i + 1 }}</span>
                    <div class="flex-1"><p class="font-bold text-sm text-gray-700">{{ $p->product->name }}</p><p class="text-xs text-gray-400">{{ $p->total_qty }} vimeuzwa</p></div>
                    <span class="font-black text-sm text-emerald-600">TZS {{ number_format($p->total_revenue, 0) }}</span>
                </div>
                @empty
                <p class="text-gray-400 font-bold text-center py-8">Hakuna data.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-card p-6">
            <h3 class="font-black text-lg text-emerald-700 mb-4">⚠️ Tahadhari za Stoo</h3>
            <div class="space-y-2">
                @forelse($lowStock as $s)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-red-50/50">
                    <span class="h-2 w-2 rounded-full {{ $s->qty <= 0 ? 'bg-red-500' : 'bg-gold-500' }}"></span>
                    <div class="flex-1"><p class="font-bold text-sm text-gray-700">{{ $s->product->name }}</p><p class="text-xs text-gray-400">{{ $s->branch->name }}</p></div>
                    <span class="font-black text-sm {{ $s->qty <= 0 ? 'text-red-600' : 'text-gold-600' }}">{{ $s->qty }} {{ $s->product->unit }}</span>
                </div>
                @empty
                <p class="text-gray-400 font-bold text-center py-8">Stoo yote ni nzuri! ✅</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
