@extends('layouts.dashboard')

@section('title', 'Mapato')
@section('page_title', 'Mapato (Income)')

@section('content')
@php
$fmt = fn($n) => $n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n));
@endphp

<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl border border-violet-400 p-4 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-violet-100 uppercase">Mapato ya Leo</span>
                <p class="text-xl font-bold mt-1">TZS {{ $fmt($todayIncome) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-emerald-100 uppercase">Mapato ya Mwezi</span>
                <p class="text-xl font-bold mt-1">TZS {{ $fmt($monthIncome) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-sky-100 uppercase">Mapato Jumla</span>
                <p class="text-xl font-bold mt-1">TZS {{ $fmt($totalIncome) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <span class="text-[10px] font-medium text-gray-500 uppercase">Aina za Mapato</span>
            <div class="mt-2 space-y-1">
                @foreach($incomeByCategory->take(3) as $cat)
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-gray-600">{{ $cat->category }}</span>
                    <span class="text-[10px] font-bold text-violet-600">TZS {{ $fmt($cat->total) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Header + Add Button --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Mapato (Income Records)</h2>
            <p class="text-xs text-gray-500">Rekodi na fuatilia mapato yote ya biashara</p>
        </div>
        <button onclick="openIncomeModal()" class="btn-gold px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ongeza Mapato
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Tarehe</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Kategoria</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Maelezo</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Njia</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Kiasi</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomes as $income)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-xs text-gray-700">{{ $income->income_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md bg-violet-50 text-violet-700 text-[10px] font-semibold">{{ $income->category }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $income->description ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ ucfirst(str_replace('_', ' ', $income->payment_method)) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-emerald-600">TZS {{ number_format($income->amount, 0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editIncome({{ $income->id }})" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button onclick="deleteIncome({{ $income->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">Hakuna mapato yaliyorekodiwa bado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $incomes->links() }}</div>
    </div>
</div>

{{-- Drawer Modal --}}
<div id="incomeModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeIncomeModal()"></div>
    <div class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-xl overflow-y-auto transition-transform">
        <div class="sticky top-0 bg-white border-b px-5 py-4 flex items-center justify-between z-10">
            <h3 id="incomeModalTitle" class="text-sm font-bold text-gray-900">Ongeza Mapato</h3>
            <button onclick="closeIncomeModal()" class="p-1 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="incomeForm" class="p-5 space-y-4">
            @csrf
            <input type="hidden" id="income_id" name="id">
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Kategoria</label>
                <select id="income_category" name="category" class="w-full rounded-lg border-gray-200 text-sm" required>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Maelezo</label>
                <textarea id="income_description" name="description" rows="2" class="w-full rounded-lg border-gray-200 text-sm"></textarea>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Kiasi (TZS)</label>
                <input type="number" id="income_amount" name="amount" step="0.01" min="0.01" class="w-full rounded-lg border-gray-200 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Njia ya Malipo</label>
                <select id="income_payment_method" name="payment_method" class="w-full rounded-lg border-gray-200 text-sm" required>
                    <option value="cash">Cash</option>
                    <option value="mpesa">M-Pesa</option>
                    <option value="tigo_pesa">Tigo Pesa</option>
                    <option value="airtel_money">Airtel Money</option>
                    <option value="halopesa">HaloPesa</option>
                    <option value="bank">Bank</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Tawi</label>
                <select id="income_branch_id" name="branch_id" class="w-full rounded-lg border-gray-200 text-sm">
                    <option value="">-- Chagua Tawi --</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Tarehe</label>
                <input type="date" id="income_income_date" name="income_date" class="w-full rounded-lg border-gray-200 text-sm" required>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-gold flex-1 py-2.5 rounded-lg text-sm font-semibold">Hifadhi</button>
                <button type="button" onclick="closeIncomeModal()" class="px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">Funga</button>
            </div>
        </form>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

function openIncomeModal() {
    document.getElementById('incomeForm').reset();
    document.getElementById('income_id').value = '';
    document.getElementById('income_income_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('incomeModalTitle').textContent = 'Ongeza Mapato';
    document.getElementById('incomeModal').classList.remove('hidden');
}

function closeIncomeModal() {
    document.getElementById('incomeModal').classList.add('hidden');
}

document.getElementById('incomeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('income_id').value;
    const url = id ? `/incomes/${id}` : '/incomes';
    const method = id ? 'PUT' : 'POST';
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    delete data._token;

    try {
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            Toastify({ text: result.message, duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#024938' } }).showToast();
            closeIncomeModal();
            setTimeout(() => location.reload(), 800);
        } else {
            Toastify({ text: result.message || 'Hitilafu imetokea', duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#ef4444' } }).showToast();
        }
    } catch (err) {
        Toastify({ text: 'Hitilafu ya mtandao', duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#ef4444' } }).showToast();
    }
});

async function editIncome(id) {
    try {
        const res = await fetch(`/incomes/${id}/edit`, { headers: { 'Accept': 'application/json' } });
        const result = await res.json();
        if (result.success) {
            const inc = result.income;
            document.getElementById('income_id').value = inc.id;
            document.getElementById('income_category').value = inc.category;
            document.getElementById('income_description').value = inc.description || '';
            document.getElementById('income_amount').value = inc.amount;
            document.getElementById('income_payment_method').value = inc.payment_method;
            document.getElementById('income_branch_id').value = inc.branch_id || '';
            document.getElementById('income_income_date').value = inc.income_date;
            document.getElementById('incomeModalTitle').textContent = 'Hariri Mapato';
            document.getElementById('incomeModal').classList.remove('hidden');
        }
    } catch (err) {
        Toastify({ text: 'Hitilafu ya mtandao', duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#ef4444' } }).showToast();
    }
}

async function deleteIncome(id) {
    Swal.fire({
        title: 'Futa Mapato?',
        text: 'Una uhakika unataka kufuta mapato haya?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ndiyo, Futa',
        cancelButtonText: 'Ghairi'
    }).then(async (r) => {
        if (r.isConfirmed) {
            try {
                const res = await fetch(`/incomes/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
                const result = await res.json();
                if (result.success) {
                    Toastify({ text: result.message, duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#024938' } }).showToast();
                    setTimeout(() => location.reload(), 800);
                }
            } catch (err) {
                Toastify({ text: 'Hitilafu ya mtandao', duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#ef4444' } }).showToast();
            }
        }
    });
}

document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeIncomeModal(); });
</script>
@endsection
