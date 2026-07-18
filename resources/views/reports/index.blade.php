@extends('layouts.dashboard')

@section('title', 'Ripoti')

@section('page_title', 'Ripoti & Takwimu')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Ripoti & Takwimu
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Muhtasari wa biashara yako</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.sales') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-all">Mauzo</a>
            <a href="{{ route('reports.inventory') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-all">Stoo</a>
            <a href="{{ route('reports.profit-loss') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-all">Faida & Hasara</a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-xs font-semibold text-emerald-100 uppercase tracking-wide">Mauzo ya Leo</p>
            <p class="font-bold text-2xl mt-2">TZS {{ number_format($todaySales, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-xs font-semibold text-gold-100 uppercase tracking-wide">Faida ya Leo</p>
            <p class="font-bold text-2xl mt-2">TZS {{ number_format($todayProfit, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Mauzo ya Wiki</p>
            <p class="font-bold text-2xl mt-2 text-emerald-600">TZS {{ number_format($weekSales, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Mauzo ya Mwezi</p>
            <p class="font-bold text-2xl mt-2 text-emerald-600">TZS {{ number_format($monthSales, 0) }}</p>
        </div>
    </div>

    {{-- Secondary KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200"><p class="text-xs font-semibold text-gray-400 uppercase">Matumizi ya Leo</p><p class="font-bold text-lg text-red-600 mt-1">TZS {{ number_format($todayExpenses, 0) }}</p></div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200"><p class="text-xs font-semibold text-gray-400 uppercase">Matumizi ya Mwezi</p><p class="font-bold text-lg text-red-600 mt-1">TZS {{ number_format($monthExpenses, 0) }}</p></div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200"><p class="text-xs font-semibold text-gray-400 uppercase">Madeni Jumla</p><p class="font-bold text-lg text-gold-600 mt-1">TZS {{ number_format($totalDebts, 0) }}</p></div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200"><p class="text-xs font-semibold text-gray-400 uppercase">Madeni Yaliyochelewa</p><p class="font-bold text-lg text-red-600 mt-1">TZS {{ number_format($overdueDebts, 0) }}</p></div>
    </div>

    {{-- Charts --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Sales chart --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Mauzo ya Siku 7 Zilizopita
            </h3>
            <canvas id="salesChart" height="200"></canvas>
        </div>

        {{-- Payment methods --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Mauzo kwa Njia ya Malipo (Mwezi)
            </h3>
            <canvas id="paymentChart" height="200"></canvas>
        </div>
    </div>

    {{-- Top products & low stock --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Bidhaa Zinazouzwa Zaidi (Mwezi)
            </h3>
            <div class="space-y-2">
                @forelse($topProducts as $i => $p)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                    <span class="h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600 font-bold grid place-items-center text-sm">{{ $i + 1 }}</span>
                    <div class="flex-1"><p class="font-semibold text-sm text-gray-700">{{ $p->product->name }}</p><p class="text-xs text-gray-400">{{ $p->total_qty }} vimeuzwa</p></div>
                    <span class="font-bold text-sm text-emerald-600">TZS {{ number_format($p->total_revenue, 0) }}</span>
                </div>
                @empty
                <p class="text-gray-400 font-medium text-center py-8">Hakuna data.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Tahadhari za Stoo
            </h3>
            <div class="space-y-2">
                @forelse($lowStock as $s)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-red-50/50 transition-colors">
                    <span class="h-2 w-2 rounded-full {{ $s->qty <= 0 ? 'bg-red-500' : 'bg-gold-500' }}"></span>
                    <div class="flex-1"><p class="font-semibold text-sm text-gray-700">{{ $s->product->name }}</p><p class="text-xs text-gray-400">{{ $s->branch->name }}</p></div>
                    <span class="font-bold text-sm {{ $s->qty <= 0 ? 'text-red-600' : 'text-gold-600' }}">{{ $s->qty }} {{ $s->product->unit }}</span>
                </div>
                @empty
                <p class="text-gray-400 font-medium text-center py-8">Stoo yote ni nzuri!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const salesData = {{ $last7Days->pluck('total') }};
const salesLabels = {{ $last7Days->pluck('date') }};
const paymentLabels = {{ $salesByMethod->map(fn($m) => ucfirst(str_replace('_', ' ', $m->payment_method))) }};
const paymentTotals = {{ $salesByMethod->pluck('total') }};

const ctx1 = document.getElementById('salesChart').getContext('2d');
const gradient1 = ctx1.createLinearGradient(0, 0, 0, 200);
gradient1.addColorStop(0, 'rgba(2,73,56,0.8)');
gradient1.addColorStop(1, 'rgba(2,73,56,0.1)');

new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: salesLabels,
        datasets: [{
            label: 'Mauzo (TZS)',
            data: salesData,
            backgroundColor: gradient1,
            borderRadius: 8,
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

const ctx2 = document.getElementById('paymentChart').getContext('2d');
const colors = ['#024938', '#f9ac00', '#1a9f8e', '#d49700', '#4db5a8'];
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: paymentLabels.length ? paymentLabels : ['Hakuna data'],
        datasets: [{
            data: paymentTotals.length ? paymentTotals : [1],
            backgroundColor: colors,
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 1200, easing: 'easeOutQuart', animateRotate: true },
        plugins: { legend: { position: 'bottom', labels: { font: { size: 12, family: 'Nunito' }, padding: 15 } } }
    }
});
</script>
@endsection
