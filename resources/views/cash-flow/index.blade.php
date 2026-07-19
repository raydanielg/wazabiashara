@extends('layouts.dashboard')

@section('title', 'Cash Flow')
@section('page_title', 'Cash Flow')

@section('content')
@php
$fmt = fn($n) => $n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n));
@endphp

<div class="space-y-6">
    {{-- Balance Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                <span class="text-[10px] font-medium text-emerald-100 uppercase">Cash Balance</span>
            </div>
            <p class="text-xl font-bold">TZS {{ $fmt($cashBalance) }}</p>
        </div>
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-sky-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span class="text-[10px] font-medium text-sky-100 uppercase">Bank Balance</span>
            </div>
            <p class="text-xl font-bold">TZS {{ $fmt($bankBalance) }}</p>
        </div>
        <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl border border-violet-400 p-4 text-white">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-violet-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="text-[10px] font-medium text-violet-100 uppercase">Mobile Money</span>
            </div>
            <p class="text-xl font-bold">TZS {{ $fmt($mobileBalance) }}</p>
        </div>
    </div>

    {{-- Cash Flow Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Money In (Monthly)</span>
            </div>
            <p class="text-lg font-bold text-emerald-600">TZS {{ $fmt($moneyIn) }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Money Out (Monthly)</span>
            </div>
            <p class="text-lg font-bold text-red-600">TZS {{ $fmt($moneyOut) }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Current Balance</span>
            </div>
            <p class="text-lg font-bold text-gray-900">TZS {{ $fmt($currentBalance) }}</p>
        </div>
    </div>

    {{-- Cash Flow Chart --}}
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Cash Flow (Last 30 Days)</h3>
        <canvas id="cashFlowChart" height="200"></canvas>
    </div>

    {{-- Accounts --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Business Accounts
            </h3>
            <button onclick="openAccountModal()" class="btn-gold px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all hover:shadow-md">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Account
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($accounts as $account)
            <div class="border rounded-xl p-4 hover:border-emerald-200 hover:shadow-sm transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide {{ $account->type === 'cash' ? 'bg-emerald-50 text-emerald-700' : ($account->type === 'bank' ? 'bg-sky-50 text-sky-700' : 'bg-violet-50 text-violet-700') }}">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</span>
                    <button onclick="deleteAccount({{ $account->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
                <p class="text-sm font-bold text-gray-900">{{ $account->name }}</p>
                @if($account->bank_name)<p class="text-[10px] text-gray-500 mt-0.5">{{ $account->bank_name }} • {{ $account->account_number }}</p>@endif
                @if($account->phone_number)<p class="text-[10px] text-gray-500 mt-0.5">{{ $account->phone_number }}</p>@endif
                <p class="text-lg font-bold text-emerald-600 mt-2">TZS {{ number_format($account->current_balance, 0) }}</p>
            </div>
            @empty
            <div class="col-span-full text-center py-10">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <p class="text-gray-400 font-medium text-sm">No accounts registered yet.</p>
                <p class="text-gray-300 text-xs mt-1">Click "Add Account" to create one.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Flows --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-semibold text-gray-900">Recent Flows</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Direction</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Category</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Description</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flows as $flow)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs text-gray-700">{{ $flow->flow_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $flow->direction === 'in' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">{{ $flow->direction === 'in' ? 'IN' : 'OUT' }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $flow->category }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $flow->description ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold {{ $flow->direction === 'in' ? 'text-emerald-600' : 'text-red-600' }}">{{ $flow->direction === 'in' ? '+' : '-' }} TZS {{ number_format($flow->amount, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">No cash flow records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $flows->links() }}</div>
    </div>
</div>

{{-- Account Modal --}}
<div id="accountModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeAccountModal()"></div>
    <div id="accountModalPanel" class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-2xl overflow-y-auto transform translate-x-full transition-transform duration-300">
        <div class="sticky top-0 bg-white border-b px-5 py-4 flex items-center justify-between z-10">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                Add Account
            </h3>
            <button onclick="closeAccountModal()" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="accountForm" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Account Name *</label>
                <input type="text" name="name" placeholder="e.g. Main Cash Box" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Type *</label>
                <select name="type" id="accountType" onchange="toggleAccountFields()" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all" required>
                    <option value="cash">Cash</option>
                    <option value="bank">Bank</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
            </div>
            <div id="bankFields" class="space-y-4 hidden">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Bank Name</label>
                    <input type="text" name="bank_name" placeholder="e.g. CRDB Bank" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Account Number</label>
                    <input type="text" name="account_number" placeholder="e.g. 0123456789012" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
                </div>
            </div>
            <div id="mobileFields" class="space-y-4 hidden">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Phone Number</label>
                    <input type="text" name="phone_number" placeholder="e.g. 0712345678" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Opening Balance</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">TZS</span>
                    <input type="number" name="opening_balance" step="0.01" min="0" value="0" class="w-full pl-14 pr-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
                </div>
            </div>
            <div class="flex gap-2 pt-3 border-t">
                <button type="submit" id="accountSaveBtn" class="btn-gold flex-1 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Account
                </button>
                <button type="button" onclick="closeAccountModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const flowData = {{ json_encode($last30Days) }};

new Chart(document.getElementById('cashFlowChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: flowData.map(d => d.date),
        datasets: [
            { label: 'Money In', data: flowData.map(d => d.in), backgroundColor: 'rgba(2,73,56,0.7)', borderRadius: 4, stack: 'flow' },
            { label: 'Money Out', data: flowData.map(d => -d.out), backgroundColor: 'rgba(239,68,68,0.7)', borderRadius: 4, stack: 'flow' },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        animation: { duration: 1200, easing: 'easeOutQuart' },
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 15 } } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => 'TZS ' + Math.abs(v/1000) + 'k' } }, x: { grid: { display: false } } }
    }
});

function openAccountModal() {
    document.getElementById('accountModal').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('accountModalPanel').classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}
function closeAccountModal() {
    document.getElementById('accountModalPanel').classList.add('translate-x-full');
    setTimeout(() => {
        document.getElementById('accountModal').classList.add('hidden');
    }, 300);
    document.body.style.overflow = '';
}
function toggleAccountFields() {
    const type = document.getElementById('accountType').value;
    document.getElementById('bankFields').classList.toggle('hidden', type !== 'bank');
    document.getElementById('mobileFields').classList.toggle('hidden', type !== 'mobile_money');
}

document.getElementById('accountForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('accountSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Saving...';
    const data = Object.fromEntries(new FormData(e.target));
    delete data._token;
    try {
        const res = await fetch('/cash-flow/accounts', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(data) });
        const result = await res.json();
        if (result.success) {
            showToast('success', 'Success', result.message);
            closeAccountModal();
            setTimeout(() => location.reload(), 800);
        } else {
            const errors = result.errors ? Object.values(result.errors).join('\n') : result.message || 'An error occurred.';
            showToast('error', 'Error', errors);
        }
    } catch (err) {
        showToast('error', 'Error', 'Network error. Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Save Account';
    }
});

async function deleteAccount(id) {
    saConfirm({
        title: 'Delete Account?', text: 'Are you sure you want to delete this account?',
        icon: 'danger', confirmText: 'Yes, Delete', confirmColor: 'red',
        onConfirm: async () => {
            try {
                const res = await fetch(`/cash-flow/accounts/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
                const result = await res.json();
                if (result.success) { showToast('success', 'Success', result.message); setTimeout(() => location.reload(), 800); }
                else { showToast('error', 'Error', result.message || 'Failed to delete.'); }
            } catch (err) { showToast('error', 'Error', 'Network error.'); }
        }
    });
}
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAccountModal(); });
</script>
@endsection
