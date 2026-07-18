@extends('layouts.dashboard')

@section('title', 'Matumizi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-emerald-700">💰 Matumizi</h1>
        <button onclick="document.getElementById('expModal').classList.remove('hidden')" class="btn-gold font-extrabold px-5 py-2.5 rounded-xl">+ Ongeza Matumizi</button>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                    <tr><th class="px-4 py-3 text-left">Kategoria</th><th class="px-4 py-3 text-left">Maelezo</th><th class="px-4 py-3 text-left">Tawi</th><th class="px-4 py-3 text-right">Kiasi</th><th class="px-4 py-3 text-left">Tarehe</th><th class="px-4 py-3 text-center">Kitendo</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($expenses as $e)
                    <tr class="hover:bg-emerald-50/30">
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full bg-gold-50 text-gold-700 text-xs font-bold">{{ $e->category }}</span></td>
                        <td class="px-4 py-3 text-gray-600 font-semibold">{{ $e->description ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 font-semibold">{{ $e->branch?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-black text-red-600">TZS {{ number_format($e->amount, 0) }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $e->expense_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center"><form method="POST" action="{{ route('expenses.destroy', $e) }}" class="inline" onsubmit="return confirm('Futa?')">@csrf @method('DELETE')<button class="text-red-500 font-bold text-xs">Futa</button></form></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna matumizi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $expenses->links() }}
    </div>
</div>

<div id="expModal" class="hidden fixed inset-0 z-50 bg-black/40 grid place-items-center p-4">
    <div class="bg-white rounded-2xl shadow-cardlg p-6 w-full max-w-md">
        <h3 class="font-black text-lg text-emerald-700 mb-4">Matumizi Mapya</h3>
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            <div class="space-y-3">
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Kategoria *</label><select name="category" required class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white">@foreach($categories as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Maelezo</label><input name="description" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Tawi</label><select name="branch_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white"><option value="">—</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Kiasi (TZS) *</label><input type="number" name="amount" required min="0.01" step="0.01" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-bold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Tarehe *</label><input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 font-bold text-gray-600">Funga</button>
                <button type="submit" class="flex-1 btn-gold font-black py-2.5 rounded-xl">Hifadhi</button>
            </div>
        </form>
    </div>
</div>
@endsection
