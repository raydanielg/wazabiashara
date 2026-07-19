@extends('layouts.dashboard')

@section('title', 'Mtiririko wa Fedha')
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
                <span class="text-xs font-semibold text-gray-700">Money In (Mwezi)</span>
            </div>
            <p class="text-lg font-bold text-emerald-600">TZS {{ $fmt($moneyIn) }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Money Out (Mwezi)</span>
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
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Mtiririko wa Fedha (Siku 30)</h3>
        <canvas id="cashFlowChart" height="200"></canvas>
    </div>

    {{-- Accounts --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Akaunti za Biashara</h3>
            <button onclick="openAccountModal()" class="btn-gold px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ongeza Akaunti
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($accounts as $account)
            <div class="border rounded-lg p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $account->type === 'cash' ? 'bg-emerald-50 text-emerald-700' : ($account->type === 'bank' ? 'bg-sky-50 text-sky-700' : 'bg-violet-50 text-violet-700') }}">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</span>
                    <button onclick="deleteAccount({{ $account->id }})" class="p-1 rounded hover:bg-red-50 text-red-500">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
                <p class="text-sm font-semibold text-gray-900">{{ $account->name }}</p>
                @if($account->bank_name)<p class="text-[10px] text-gray-500">{{ $account->bank_name }} • {{ $account->account_number }}</p>@endif
                @if($account->phone_number)<p class="text-[10px] text-gray-500">{{ $account->phone_number }}</p>@endif
                <p class="text-base font-bold text-emerald-600 mt-1">TZS {{ number_format($account->current_balance, 0) }}</p>
            </div>
            @empty
            <div class="col-span-full text-center py-8 text-sm text-gray-400">Hakuna akaunti zilizosajiliwa.</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Flows --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-semibold text-gray-900">Mtiririko wa Karibuni</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Tarehe</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Mwelekeo</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Kategoria</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Maelezo</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Kiasi</th>
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
                    <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">Hakuna mtiririko wa fedha.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $flows->links() }}</div>
    </div>
</div>

{{-- Account Modal --}}
<div id="accountModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeAccountModal()"></div>
    <div class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-xl overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-5 py-4 flex items-center justify-between z-10">
            <h3 class="text-sm font-bold text-gray-900">Ongeza Akaunti</h3>
            <button onclick="closeAccountModal()" class="p-1 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="accountForm" class="p-5 space-y-4">
            @csrf
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Jina la Akaunti</label><input type="text" name="name" class="w-full rounded-lg border-gray-200 text-sm" required></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Aina</label><select name="type" class="w-full rounded-lg border-gray-200 text-sm" required><option value="cash">Cash</option><option value="bank">Bank</option><option value="mobile_money">Mobile Money</option></select></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Jina la Benki</label><input type="text" name="bank_name" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Namba ya Akaunti</label><input type="text" name="account_number" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Namba ya Simu</label><input type="text" name="phone_number" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Salio la Awali</label><input type="number" name="opening_balance" step="0.01" min="0" value="0" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div class="flex gap-2 pt-2"><button type="submit" class="btn-gold flex-1 py-2.5 rounded-lg text-sm font-semibold">Hifadhi</button><button type="button" onclick="closeAccountModal()" class="px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600">Funga</button></div>
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

function openAccountModal() { document.getElementById('accountModal').classList.remove('hidden'); }
function closeAccountModal() { document.getElementById('accountModal').classList.add('hidden'); }

document.getElementById('accountForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    delete data._token;
    try {
        const res = await fetch('/cash-flow/accounts', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(data) });
        const result = await res.json();
        if (result.success) { Toastify({ text: result.message, duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#024938' } }).showToast(); closeAccountModal(); setTimeout(() => location.reload(), 800); }
    } catch (err) { Toastify({ text: 'Hitilafu', duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#ef4444' } }).showToast(); }
});

async function deleteAccount(id) {
    Swal.fire({ title: 'Futa Akaunti?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ndiyo, Futa', cancelButtonText: 'Ghairi' }).then(async (r) => {
        if (r.isConfirmed) { const res = await fetch(`/cash-flow/accounts/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } }); const result = await res.json(); if (result.success) { Toastify({ text: result.message, duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#024938' } }).showToast(); setTimeout(() => location.reload(), 800); } }
    });
}
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAccountModal(); });
</script>
@endsection
