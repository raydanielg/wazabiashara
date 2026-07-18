@extends('layouts.dashboard')

@section('title', 'Manunuzi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-emerald-700">📋 Manunuzi</h1>
            <p class="text-sm text-gray-500 font-semibold">Rekodi manunuzi ya bidhaa kutoka kwa wasambazaji</p>
        </div>
        <a href="{{ route('suppliers.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-emerald-200 text-emerald-600 font-bold text-sm hover:bg-emerald-50">← Wasambazaji</a>
    </div>

    <!-- New purchase form -->
    <div class="bg-white rounded-2xl shadow-card p-6">
        <h3 class="font-black text-lg text-emerald-700 mb-4">➕ Manunuzi Mapya</h3>
        <form method="POST" action="{{ route('suppliers.purchases.store') }}">
            @csrf
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div><label class="block text-xs font-bold text-gray-500 mb-1">Tawi *</label><select name="branch_id" required class="w-full px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm bg-white">@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-bold text-gray-500 mb-1">Msambazaji</label><select name="supplier_id" class="w-full px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm bg-white"><option value="">— Nje —</option>@foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-bold text-gray-500 mb-1">Malipo</label><select name="payment_status" class="w-full px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm bg-white"><option value="paid">Lipa</option><option value="credit">Deni</option><option value="partial">Sehemu</option></select></div>
            </div>

            <div id="purchaseItems" class="space-y-2 mb-4"></div>
            <button type="button" onclick="addPurchaseRow()" class="text-emerald-600 font-bold text-sm">+ Ongeza Bidhaa</button>

            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
                <span class="font-black text-lg text-emerald-700">Jumla: <span id="purchaseTotal">TZS 0</span></span>
                <button type="submit" class="btn-gold font-black px-6 py-2.5 rounded-xl">Hifadhi Manunuzi</button>
            </div>
        </form>
    </div>

    <!-- Purchases list -->
    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                <tr><th class="px-4 py-3 text-left">Ref</th><th class="px-4 py-3 text-left">Tawi</th><th class="px-4 py-3 text-left">Msambazaji</th><th class="px-4 py-3 text-right">Jumla</th><th class="px-4 py-3 text-center">Malipo</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-left">Tarehe</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($purchases as $p)
                <tr class="hover:bg-emerald-50/30">
                    <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">{{ $p->reference ?? 'PUR-' . $p->id }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-600">{{ $p->branch->name }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-600">{{ $p->supplier?->name ?? 'Nje' }}</td>
                    <td class="px-4 py-3 text-right font-black text-emerald-600">TZS {{ number_format($p->total, 0) }}</td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $p->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($p->payment_status === 'credit' ? 'bg-red-100 text-red-600' : 'bg-gold-100 text-gold-700') }}">{{ $p->payment_status }}</span></td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">{{ $p->status }}</span></td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $p->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna manunuzi.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $purchases->links() }}
    </div>
</div>

<script>
const productsData = @json($products->map(fn($p) => ['id'=>$p->id, 'name'=>$p->name, 'cost_price'=>$p->cost_price]));
let rowIdx = 0;
function addPurchaseRow() {
    const opts = productsData.map(p => `<option value="${p.id}" data-price="${p.cost_price}">${p.name}</option>`).join('');
    const html = `<div class="flex gap-2 items-center" id="prow${rowIdx}">
        <select name="items[${rowIdx}][product_id]" required class="flex-1 px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-semibold bg-white" onchange="updateRowTotal(this)"><option value="">— Chagua —</option>${opts}</select>
        <input type="number" name="items[${rowIdx}][qty]" placeholder="Qty" min="0.01" step="0.01" required class="w-20 px-2 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-bold" oninput="updateRowTotal(this)">
        <input type="number" name="items[${rowIdx}][cost_price]" placeholder="Bei" min="0" step="0.01" required class="w-24 px-2 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-bold" oninput="updateRowTotal(this)">
        <span class="text-sm font-black text-emerald-600 w-24 text-right" data-subtotal>0</span>
        <button type="button" onclick="document.getElementById('prow${rowIdx}').remove(); updatePurchaseTotal()" class="text-red-400 hover:text-red-600">✕</button>
    </div>`;
    document.getElementById('purchaseItems').insertAdjacentHTML('beforeend', html);
    rowIdx++;
}
function updateRowTotal(el) {
    const row = el.closest('[id^="prow"]');
    const select = row.querySelector('select');
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const price = parseFloat(row.querySelector('input[name*="[cost_price]"]').value) || 0;
    const subtotal = qty * price;
    row.querySelector('[data-subtotal]').textContent = 'TZS ' + subtotal.toLocaleString('sw-TZ');
    updatePurchaseTotal();
}
function updatePurchaseTotal() {
    let total = 0;
    document.querySelectorAll('[data-subtotal]').forEach(el => {
        total += parseFloat(el.textContent.replace(/[^0-9]/g, '')) || 0;
    });
    document.getElementById('purchaseTotal').textContent = 'TZS ' + total.toLocaleString('sw-TZ');
}
addPurchaseRow();
</script>
@endsection
