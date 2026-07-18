@extends('layouts.dashboard')

@section('title', 'Madeni ya Mteja')

@section('page_title', 'Madeni — ' . $customer->name)

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('customers.index') }}" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-500 transition-all flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Madeni — {{ $customer->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Simamia deni la mteja</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-xl bg-red-50 grid place-items-center"><svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg></div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Deni Jumla</p>
            </div>
            <p class="font-bold text-xl text-red-600">TZS {{ number_format($customer->current_debt, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Credit Limit</p>
            </div>
            <p class="font-bold text-xl text-emerald-600">TZS {{ number_format($customer->credit_limit, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-xl bg-gold-50 grid place-items-center"><svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Akiwa Deni</p>
            </div>
            <p class="font-bold text-xl text-gold-600">{{ $debts->where('status', '!=', 'paid')->count() }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wide">
                    <tr><th class="px-4 py-3 text-left">Risiti</th><th class="px-4 py-3 text-right">Deni</th><th class="px-4 py-3 text-right">Salio</th><th class="px-4 py-3 text-left hidden sm:table-cell">Due Date</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Lipa</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($debts as $debt)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-600">{{ $debt->sale?->receipt_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-700">TZS {{ number_format($debt->amount, 0) }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ $debt->balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">TZS {{ number_format($debt->balance, 0) }}</td>
                        <td class="px-4 py-3 text-gray-500 font-medium hidden sm:table-cell">{{ $debt->due_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $debt->status === 'paid' ? 'bg-emerald-50 text-emerald-600' : ($debt->status === 'overdue' ? 'bg-red-50 text-red-500' : ($debt->status === 'partial' ? 'bg-gold-50 text-gold-600' : 'bg-gray-100 text-gray-500')) }}">{{ $debt->status }}</span></td>
                        <td class="px-4 py-3 text-center">
                            @if($debt->balance > 0)
                            <button onclick="payDebt({{ $debt->id }}, {{ $debt->balance }})" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-xs font-bold transition-all">Lipa</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-gray-400 font-medium text-sm">Hakuna madeni.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pay Debt Modal Drawer --}}
<div id="payOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closePayModal()"></div>
<div id="payModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[380px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            Lipa Deni
        </h2>
        <button onclick="closePayModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="payForm" method="POST" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf
        <input type="hidden" name="debt_id" id="payDebtId">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kiasi (TZS) *</label>
            <input type="number" name="amount" id="payAmount" required min="0.01" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Njia ya Malipo</label>
            <select name="method" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all"><option value="cash">Taslimu</option><option value="mpesa">M-Pesa</option><option value="tigo_pesa">Tigo Pesa</option><option value="airtel_money">Airtel Money</option><option value="bank">Benki</option></select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Reference</label>
            <input name="reference" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button type="submit" form="payForm" class="w-full btn-gold font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Lipa
        </button>
    </div>
</div>

<script>
function payDebt(id, balance) {
    document.getElementById('payDebtId').value = id;
    document.getElementById('payAmount').value = balance;
    document.getElementById('payAmount').max = balance;
    document.getElementById('payForm').action = '{{ route("debts.pay", "__ID__") }}'.replace('__ID__', id);
    document.getElementById('payOverlay').classList.remove('hidden');
    document.getElementById('payModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}
function closePayModal() {
    document.getElementById('payOverlay').classList.add('hidden');
    document.getElementById('payModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closePayModal(); });
</script>
@endsection
