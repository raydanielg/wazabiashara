@extends('layouts.dashboard')

@section('title', 'Zamu')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-emerald-700">⏰ Zamu (Shifts)</h1>
        @if(!$activeShift)
        <button onclick="document.getElementById('shiftModal').classList.remove('hidden')" class="btn-gold font-extrabold px-5 py-2.5 rounded-xl">Fungua Zamu</button>
        @endif
    </div>

    @if($activeShift)
    <div class="bg-emerald-50 border-2 border-emerald-200 rounded-2xl p-6 flex items-center justify-between">
        <div>
            <p class="font-black text-lg text-emerald-700">✅ Zamu Wazi</p>
            <p class="text-sm text-gray-500 font-semibold mt-1">Fungu la kuanzia: TZS {{ number_format($activeShift->opening_float, 0) }} • Iliyofunguliwa: {{ $activeShift->opened_at->format('d/m/Y H:i') }}</p>
        </div>
        <button onclick="document.getElementById('closeModal').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-red-500 text-white font-bold text-sm hover:bg-red-600">Funga Zamu</button>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                <tr><th class="px-4 py-3 text-left">Muuzaji</th><th class="px-4 py-3 text-left">Tawi</th><th class="px-4 py-3 text-right">Fungu</th><th class="px-4 py-3 text-right">Hela ya Kufunga</th><th class="px-4 py-3 text-right">Tarajiliwa</th><th class="px-4 py-3 text-right">Tofauti</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-left">Muda</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($shifts as $s)
                <tr class="hover:bg-emerald-50/30">
                    <td class="px-4 py-3 font-bold text-gray-700">{{ $s->user->name }}</td>
                    <td class="px-4 py-3 text-gray-500 font-semibold">{{ $s->branch->name }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-600">TZS {{ number_format($s->opening_float, 0) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-600">{{ $s->closing_cash ? 'TZS ' . number_format($s->closing_cash, 0) : '—' }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-600">{{ $s->expected_cash ? 'TZS ' . number_format($s->expected_cash, 0) : '—' }}</td>
                    <td class="px-4 py-3 text-right font-black {{ ($s->variance ?? 0) == 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $s->variance !== null ? 'TZS ' . number_format($s->variance, 0) : '—' }}</td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $s->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $s->status }}</span></td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $s->opened_at->format('d/m H:i') }}{{ $s->closed_at ? ' → ' . $s->closed_at->format('H:i') : '' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna zamu.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $shifts->links() }}
    </div>
</div>

<div id="shiftModal" class="hidden fixed inset-0 z-50 bg-black/40 grid place-items-center p-4">
    <div class="bg-white rounded-2xl shadow-cardlg p-6 w-full max-w-sm">
        <h3 class="font-black text-lg text-emerald-700 mb-4">Fungua Zamu</h3>
        <form method="POST" action="{{ route('shifts.open') }}">
            @csrf
            <div><label class="block text-sm font-bold text-gray-600 mb-1">Fungu la Kuanzia (TZS) *</label><input type="number" name="opening_float" required min="0" value="0" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-bold"></div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="document.getElementById('shiftModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 font-bold text-gray-600">Funga</button>
                <button type="submit" class="flex-1 btn-gold font-black py-2.5 rounded-xl">Fungua</button>
            </div>
        </form>
    </div>
</div>

@if($activeShift)
<div id="closeModal" class="hidden fixed inset-0 z-50 bg-black/40 grid place-items-center p-4">
    <div class="bg-white rounded-2xl shadow-cardlg p-6 w-full max-w-sm">
        <h3 class="font-black text-lg text-emerald-700 mb-4">Funga Zamu</h3>
        <form method="POST" action="{{ route('shifts.close', $activeShift) }}">
            @csrf
            <div class="space-y-3">
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Hela Halisi Mkononi (TZS) *</label><input type="number" name="closing_cash" required min="0" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-bold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Maoni</label><textarea name="note" rows="2" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></textarea></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="document.getElementById('closeModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 font-bold text-gray-600">Funga</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 text-white font-black hover:bg-red-600">Funga Zamu</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
