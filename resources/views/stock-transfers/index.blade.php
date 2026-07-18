@extends('layouts.dashboard')

@section('title', 'Uhamisho wa Stoo')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-emerald-700">🔄 Uhamisho wa Stoo</h1>
        <button onclick="document.getElementById('trfModal').classList.remove('hidden')" class="btn-gold font-extrabold px-5 py-2.5 rounded-xl">+ Omba Uhamisho</button>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                    <tr><th class="px-4 py-3 text-left">Kutoka</th><th class="px-4 py-3 text-left">Kwenda</th><th class="px-4 py-3 text-center">Bidhaa</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-left">Ombi la</th><th class="px-4 py-3 text-left">Tarehe</th><th class="px-4 py-3 text-center">Kitendo</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transfers as $t)
                    <tr class="hover:bg-emerald-50/30">
                        <td class="px-4 py-3 font-bold text-gray-700">{{ $t->fromBranch->name }}</td>
                        <td class="px-4 py-3 font-bold text-gray-700">{{ $t->toBranch->name }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">{{ $t->items->count() }}</span></td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $t->status === 'received' ? 'bg-emerald-100 text-emerald-700' : ($t->status === 'pending' ? 'bg-gold-100 text-gold-700' : ($t->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600')) }}">{{ $t->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-semibold text-xs">{{ $t->requestedBy?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $t->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($t->status === 'pending')
                            <form method="POST" action="{{ route('stock-transfers.approve', $t) }}" class="inline">@csrf<button class="text-emerald-600 font-bold text-xs">Idhinisha</button></form>
                            <form method="POST" action="{{ route('stock-transfers.reject', $t) }}" class="inline">@csrf<button class="text-red-500 font-bold text-xs ml-2">Kataa</button></form>
                            @elseif($t->status === 'approved')
                            <form method="POST" action="{{ route('stock-transfers.ship', $t) }}" class="inline">@csrf<button class="text-blue-600 font-bold text-xs">Tuma</button></form>
                            @elseif($t->status === 'shipped')
                            <form method="POST" action="{{ route('stock-transfers.receive', $t) }}" class="inline">@csrf<button class="text-emerald-600 font-bold text-xs">Pokoa</button></form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna maombi ya uhamisho.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $transfers->links() }}
    </div>
</div>

<div id="trfModal" class="hidden fixed inset-0 z-50 bg-black/40 grid place-items-center p-4">
    <div class="bg-white rounded-2xl shadow-cardlg p-6 w-full max-w-lg">
        <h3 class="font-black text-lg text-emerald-700 mb-4">Omba Uhamisho wa Stoo</h3>
        <form method="POST" action="{{ route('stock-transfers.store') }}">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="block text-xs font-bold text-gray-500 mb-1">Kutoka Tawi *</label><select name="from_branch_id" required class="w-full px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm bg-white">@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-bold text-gray-500 mb-1">Kwenda Tawi *</label><select name="to_branch_id" required class="w-full px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm bg-white">@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
            </div>
            <div id="trfItems" class="space-y-2 mb-3"></div>
            <button type="button" onclick="addTrfRow()" class="text-emerald-600 font-bold text-sm">+ Ongeza Bidhaa</button>
            <div><label class="block text-xs font-bold text-gray-500 mb-1 mt-3">Maoni</label><textarea name="note" rows="2" class="w-full px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm"></textarea></div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="document.getElementById('trfModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 font-bold text-gray-600">Funga</button>
                <button type="submit" class="flex-1 btn-gold font-black py-2.5 rounded-xl">Tuma Ombi</button>
            </div>
        </form>
    </div>
</div>

<script>
let trfIdx = 0;
function addTrfRow() {
    const html = `<div class="flex gap-2 items-center" id="trow${trfIdx}">
        <input type="number" name="items[${trfIdx}][product_id]" placeholder="Product ID" required class="w-32 px-2 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-bold">
        <input type="number" name="items[${trfIdx}][qty]" placeholder="Qty" min="0.01" step="0.01" required class="w-24 px-2 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-bold">
        <button type="button" onclick="document.getElementById('trow${trfIdx}').remove()" class="text-red-400">✕</button>
    </div>`;
    document.getElementById('trfItems').insertAdjacentHTML('beforeend', html);
    trfIdx++;
}
addTrfRow();
</script>
@endsection
