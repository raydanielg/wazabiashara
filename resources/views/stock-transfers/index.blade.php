@extends('layouts.dashboard')

@section('title', 'Stock Transfers')

@section('page_title', 'Stock Transfers')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Stock Transfers
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Transfer stock between branches</p>
        </div>
        <button onclick="openTrfModal()" class="btn-gold font-bold px-5 py-3 rounded-2xl inline-flex items-center gap-2 text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Request Transfer
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">From</th>
                        <th class="px-4 py-3 text-left hidden sm:table-cell">To</th>
                        <th class="px-4 py-3 text-center">Items</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">Requested By</th>
                        <th class="px-4 py-3 text-left hidden sm:table-cell">Date</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transfers as $t)
                    <tr class="hover:bg-gray-50/50 transition-colors" id="trfrow-{{ $t->id }}">
                        <td class="px-4 py-3 font-semibold text-gray-700">{{ $t->fromBranch->name }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-700 hidden sm:table-cell">{{ $t->toBranch->name }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">{{ $t->items->count() }}</span></td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $t->status === 'received' ? 'bg-emerald-50 text-emerald-600' : ($t->status === 'pending' ? 'bg-gold-50 text-gold-600' : ($t->status === 'rejected' ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-600')) }}">{{ $t->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-medium text-xs hidden md:table-cell">{{ $t->requestedBy?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden sm:table-cell">{{ $t->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($t->status === 'pending')
                                <form method="POST" action="{{ route('stock-transfers.approve', $t) }}" class="inline">@csrf<button class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all flex items-center justify-center" title="Approve"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></button></form>
                                <form method="POST" action="{{ route('stock-transfers.reject', $t) }}" class="inline">@csrf<button class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center" title="Reject"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></form>
                                @elseif($t->status === 'approved')
                                <form method="POST" action="{{ route('stock-transfers.ship', $t) }}" class="inline">@csrf<button class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-all flex items-center justify-center" title="Ship"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></button></form>
                                @elseif($t->status === 'shipped')
                                <form method="POST" action="{{ route('stock-transfers.receive', $t) }}" class="inline">@csrf<button class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all flex items-center justify-center" title="Receive"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <p class="text-gray-400 font-medium text-sm">No transfer requests found.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $transfers->links() }}
        </div>
    </div>
</div>

{{-- Transfer Modal Drawer --}}
<div id="trfOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeTrfModal()"></div>
<div id="trfModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[480px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            Request Stock Transfer
        </h2>
        <button onclick="closeTrfModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form method="POST" action="{{ route('stock-transfers.store') }}" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">From Branch *</label>
                <select name="from_branch_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">To Branch *</label>
                <select name="to_branch_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Items to Transfer
            </h3>
            <div id="trfItems" class="space-y-2 mb-3"></div>
            <button type="button" onclick="addTrfRow()" class="text-emerald-600 font-bold text-sm flex items-center gap-1 hover:text-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Item
            </button>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Notes</label>
            <textarea name="note" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all"></textarea>
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button type="submit" formaction="{{ route('stock-transfers.store') }}" class="w-full btn-gold font-bold py-3.5 rounded-2xl text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            Submit Request
        </button>
    </div>
</div>

<script>
function openTrfModal() {
    document.getElementById('trfOverlay').classList.remove('hidden');
    document.getElementById('trfModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}
function closeTrfModal() {
    document.getElementById('trfOverlay').classList.add('hidden');
    document.getElementById('trfModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeTrfModal();
});

let trfIdx = 0;
function addTrfRow() {
    const html = `<div class="flex gap-2 items-center" id="trow${trfIdx}">
        <input type="number" name="items[${trfIdx}][product_id]" placeholder="Product ID" required class="w-32 px-3 py-2 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold">
        <input type="number" name="items[${trfIdx}][qty]" placeholder="Qty" min="0.01" step="0.01" required class="w-24 px-3 py-2 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold">
        <button type="button" onclick="document.getElementById('trow${trfIdx}').remove()" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>`;
    document.getElementById('trfItems').insertAdjacentHTML('beforeend', html);
    trfIdx++;
}
addTrfRow();
</script>
@endsection
