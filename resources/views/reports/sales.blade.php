@extends('layouts.dashboard')

@section('title', 'Ripoti ya Mauzo')

@section('page_title', 'Ripoti ya Mauzo')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('reports.index') }}" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-500 transition-all flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Ripoti ya Mauzo
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Chuja na tazama mauzo yote</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-2 sm:gap-3 flex-wrap bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <input type="date" name="from" value="{{ request('from') }}" class="px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        <input type="date" name="to" value="{{ request('to') }}" class="px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        <select name="branch" class="px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all"><option value="">Tawi Zote</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected(request('branch') == $b->id)>{{ $b->name }}</option>@endforeach</select>
        <select name="method" class="px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all"><option value="">Njia Zote</option><option value="cash">Cash</option><option value="mpesa">M-Pesa</option><option value="tigo_pesa">Tigo Pesa</option><option value="airtel_money">Airtel Money</option><option value="credit">Deni</option></select>
        <button class="px-4 py-2.5 rounded-lg bg-emerald-500 text-white font-bold text-sm hover:bg-emerald-600 transition-all flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>Chuja</button>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wide">
                    <tr><th class="px-4 py-3 text-left">Risiti</th><th class="px-4 py-3 text-left hidden sm:table-cell">Tawi</th><th class="px-4 py-3 text-left hidden md:table-cell">Muuzaji</th><th class="px-4 py-3 text-left hidden md:table-cell">Mteja</th><th class="px-4 py-3 text-center">Njia</th><th class="px-4 py-3 text-right">Jumla</th><th class="px-4 py-3 text-left hidden sm:table-cell">Tarehe</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($sales as $s)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-600"><a href="{{ route('pos.receipt', $s->id) }}" target="_blank" class="text-emerald-600 hover:underline">{{ $s->receipt_no }}</a></td>
                        <td class="px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">{{ $s->branch->name }}</td>
                        <td class="px-4 py-3 font-medium text-gray-600 hidden md:table-cell">{{ $s->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-600 hidden md:table-cell">{{ $s->customer?->name ?? 'Kawaida' }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold">{{ ucfirst(str_replace('_', ' ', $s->payment_method)) }}</span></td>
                        <td class="px-4 py-3 text-right font-bold text-emerald-600">TZS {{ number_format($s->total, 0) }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden sm:table-cell">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <p class="text-gray-400 font-medium text-sm">Hakuna mauzo.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $sales->links() }}</div>
    </div>
</div>
@endsection
