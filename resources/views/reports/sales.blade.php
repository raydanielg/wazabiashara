@extends('layouts.dashboard')

@section('title', 'Ripoti ya Mauzo')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('reports.index') }}" class="text-emerald-600 font-bold text-sm">← Rudi</a>
        <h1 class="text-2xl font-black text-emerald-700">📊 Ripoti ya Mauzo</h1>
    </div>

    <form method="GET" class="flex gap-3 flex-wrap bg-white p-4 rounded-2xl shadow-card">
        <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-semibold">
        <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-semibold">
        <select name="branch" class="px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-semibold bg-white"><option value="">Tawi Zote</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected(request('branch') == $b->id)>{{ $b->name }}</option>@endforeach</select>
        <select name="method" class="px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-semibold bg-white"><option value="">Njia Zote</option><option value="cash">Cash</option><option value="mpesa">M-Pesa</option><option value="tigo_pesa">Tigo Pesa</option><option value="airtel_money">Airtel Money</option><option value="credit">Deni</option></select>
        <button class="px-5 py-2 rounded-lg bg-emerald-500 text-white font-bold text-sm">Chuja</button>
    </form>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                    <tr><th class="px-4 py-3 text-left">Risiti</th><th class="px-4 py-3 text-left">Tawi</th><th class="px-4 py-3 text-left">Muuzaji</th><th class="px-4 py-3 text-left">Mteja</th><th class="px-4 py-3 text-center">Njia</th><th class="px-4 py-3 text-right">Jumla</th><th class="px-4 py-3 text-left">Tarehe</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($sales as $s)
                    <tr class="hover:bg-emerald-50/30">
                        <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600"><a href="{{ route('pos.receipt', $s->id) }}" target="_blank" class="text-emerald-600 hover:underline">{{ $s->receipt_no }}</a></td>
                        <td class="px-4 py-3 font-semibold text-gray-600">{{ $s->branch->name }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-600">{{ $s->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-600">{{ $s->customer?->name ?? 'Kawaida' }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">{{ ucfirst(str_replace('_', ' ', $s->payment_method)) }}</span></td>
                        <td class="px-4 py-3 text-right font-black text-emerald-600">TZS {{ number_format($s->total, 0) }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna mauzo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $sales->links() }}
    </div>
</div>
@endsection
