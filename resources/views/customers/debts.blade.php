@extends('layouts.dashboard')

@section('title', 'Madeni ya Mteja')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('customers.index') }}" class="text-emerald-600 font-bold text-sm">← Rudi</a>
        <h1 class="text-2xl font-black text-emerald-700">📒 Madeni — {{ $customer->name }}</h1>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-card p-5"><p class="text-xs font-bold text-gray-400 uppercase">Deni Jumla</p><p class="font-black text-2xl text-red-600 mt-1">TZS {{ number_format($customer->current_debt, 0) }}</p></div>
        <div class="bg-white rounded-2xl shadow-card p-5"><p class="text-xs font-bold text-gray-400 uppercase">Credit Limit</p><p class="font-black text-2xl text-emerald-600 mt-1">TZS {{ number_format($customer->credit_limit, 0) }}</p></div>
        <div class="bg-white rounded-2xl shadow-card p-5"><p class="text-xs font-bold text-gray-400 uppercase">Akiwa Deni</p><p class="font-black text-2xl text-gold-600 mt-1">{{ $debts->where('status', '!=', 'paid')->count() }}</p></div>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                <tr><th class="px-4 py-3 text-left">Risiti</th><th class="px-4 py-3 text-right">Deni</th><th class="px-4 py-3 text-right">Salio</th><th class="px-4 py-3 text-left">Due Date</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Lipa</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($debts as $debt)
                <tr class="hover:bg-emerald-50/30">
                    <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">{{ $debt->sale?->receipt_no ?? '—' }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-700">TZS {{ number_format($debt->amount, 0) }}</td>
                    <td class="px-4 py-3 text-right font-black {{ $debt->balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">TZS {{ number_format($debt->balance, 0) }}</td>
                    <td class="px-4 py-3 text-gray-500 font-semibold">{{ $debt->due_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $debt->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($debt->status === 'overdue' ? 'bg-red-100 text-red-600' : ($debt->status === 'partial' ? 'bg-gold-100 text-gold-700' : 'bg-gray-100 text-gray-600')) }}">{{ $debt->status }}</span></td>
                    <td class="px-4 py-3 text-center">
                        @if($debt->balance > 0)
                        <button onclick="payDebt({{ $debt->id }}, {{ $debt->balance }})" class="text-emerald-600 font-bold text-xs">Lipa</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna madeni.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="payModal" class="hidden fixed inset-0 z-50 bg-black/40 grid place-items-center p-4">
    <div class="bg-white rounded-2xl shadow-cardlg p-6 w-full max-w-sm">
        <h3 class="font-black text-lg text-emerald-700 mb-4">Lipa Deni</h3>
        <form id="payForm" method="POST">
            @csrf
            <input type="hidden" name="debt_id" id="payDebtId">
            <div class="space-y-3">
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Kiasi (TZS) *</label><input type="number" name="amount" id="payAmount" required min="0.01" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-bold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Njia ya Malipo</label><select name="method" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white"><option value="cash">Taslimu</option><option value="mpesa">M-Pesa</option><option value="tigo_pesa">Tigo Pesa</option><option value="airtel_money">Airtel Money</option><option value="bank">Benki</option></select></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Reference</label><input name="reference" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="document.getElementById('payModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 font-bold text-gray-600">Funga</button>
                <button type="submit" class="flex-1 btn-gold font-black py-2.5 rounded-xl">Lipa</button>
            </div>
        </form>
    </div>
</div>

<script>
function payDebt(id, balance) {
    document.getElementById('payDebtId').value = id;
    document.getElementById('payAmount').value = balance;
    document.getElementById('payAmount').max = balance;
    document.getElementById('payForm').action = '{{ route("debts.pay", "__ID__") }}'.replace('__ID__', id);
    document.getElementById('payModal').classList.remove('hidden');
}
</script>
@endsection
