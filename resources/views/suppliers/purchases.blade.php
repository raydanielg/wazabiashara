@extends('layouts.dashboard')

@section('title', 'Purchases')

@section('page_title', 'Purchases')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Purchases
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Record product purchases from suppliers</p>
        </div>
        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Suppliers
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Total Purchases</p><p class="text-xl font-bold text-gray-800">{{ $totalPurchases }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gold-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">This Month</p><p class="text-xl font-bold text-gold-600">{{ $monthPurchases }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Monthly Value</p><p class="text-xl font-bold text-emerald-600">TZS {{ number_format($monthTotal, 0) }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Credit</p><p class="text-xl font-bold text-red-600">{{ $creditPurchases }}</p></div>
            </div>
        </div>
    </div>

    <!-- New purchase form -->
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            Manunuzi Mapya
        </h3>
        <form method="POST" action="{{ route('suppliers.purchases.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Branch *</label><select name="branch_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Supplier</label><select name="supplier_id" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all"><option value="">— None —</option>@foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Payment</label><select name="payment_status" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all"><option value="paid">Paid</option><option value="credit">Credit</option><option value="partial">Partial</option></select></div>
            </div>

            <div id="purchaseItems" class="space-y-2 mb-3"></div>
            <button type="button" onclick="addPurchaseRow()" class="text-emerald-600 font-bold text-sm flex items-center gap-1 hover:text-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Product
            </button>

            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
                <span class="font-bold text-lg text-emerald-600">Total: <span id="purchaseTotal">TZS 0</span></span>
                <button type="submit" class="btn-gold font-bold px-5 py-2.5 rounded-lg text-sm flex items-center gap-2 shadow-sm hover:shadow-md transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save
                </button>
            </div>
        </form>
    </div>

    <!-- Purchases list -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wide">
                    <tr><th class="px-4 py-3 text-left">Ref</th><th class="px-4 py-3 text-left hidden sm:table-cell">Branch</th><th class="px-4 py-3 text-left hidden md:table-cell">Supplier</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3 text-center">Payment</th><th class="px-4 py-3 text-center hidden sm:table-cell">Status</th><th class="px-4 py-3 text-left hidden sm:table-cell">Date</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($purchases as $p)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-600">{{ $p->reference ?? 'PUR-' . $p->id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">{{ $p->branch->name }}</td>
                        <td class="px-4 py-3 font-medium text-gray-600 hidden md:table-cell">{{ $p->supplier?->name ?? 'None' }}</td>
                        <td class="px-4 py-3 text-right font-bold text-emerald-600">TZS {{ number_format($p->total, 0) }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $p->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-600' : ($p->payment_status === 'credit' ? 'bg-red-50 text-red-500' : 'bg-gold-50 text-gold-600') }}">{{ $p->payment_status }}</span></td>
                        <td class="px-4 py-3 text-center hidden sm:table-cell"><span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">{{ $p->status }}</span></td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden sm:table-cell">{{ $p->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-gray-400 font-medium text-sm">No purchases found.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $purchases->links() }}</div>
    </div>
</div>

<script>
const productsData = @json($products->map(fn($p) => ['id'=>$p->id, 'name'=>$p->name, 'cost_price'=>$p->cost_price]));
let rowIdx = 0;
function addPurchaseRow() {
    const opts = productsData.map(p => `<option value="${p.id}" data-price="${p.cost_price}">${p.name}</option>`).join('');
    const html = `<div class="flex gap-2 items-center" id="prow${rowIdx}">
        <select name="items[${rowIdx}][product_id]" required class="flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white transition-all" onchange="updateRowTotal(this)"><option value="">— Select —</option>${opts}</select>
        <input type="number" name="items[${rowIdx}][qty]" placeholder="Qty" min="0.01" step="0.01" required class="w-20 px-2 py-2 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold" oninput="updateRowTotal(this)">
        <input type="number" name="items[${rowIdx}][cost_price]" placeholder="Price" min="0" step="0.01" required class="w-24 px-2 py-2 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold" oninput="updateRowTotal(this)">
        <span class="text-sm font-bold text-emerald-600 w-24 text-right" data-subtotal>0</span>
        <button type="button" onclick="document.getElementById('prow${rowIdx}').remove(); updatePurchaseTotal()" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
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
