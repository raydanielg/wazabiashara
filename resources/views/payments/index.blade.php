@extends('layouts.dashboard')

@section('title', 'Payments')
@section('page_title', 'Payments')

@section('content')
@php
$fmt = fn($n) => $n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n));
@endphp

<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-emerald-100 uppercase">Payments In Today</span>
                <p class="text-xl font-bold mt-1">TZS {{ $fmt($todayIn) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl border border-red-400 p-4 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-red-100 uppercase">Payments Out Today</span>
                <p class="text-xl font-bold mt-1">TZS {{ $fmt($todayOut) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-sky-100 uppercase">Payments In (Month)</span>
                <p class="text-xl font-bold mt-1">TZS {{ $fmt($monthIn) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-amber-50 uppercase">Payments Out (Month)</span>
                <p class="text-xl font-bold mt-1">TZS {{ $fmt($monthOut) }}</p>
            </div>
        </div>
    </div>

    {{-- Header + Add Button --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Payments</h2>
            <p class="text-xs text-gray-500">Track all incoming and outgoing payments</p>
        </div>
        <button onclick="openPaymentModal()" class="btn-gold px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Payment
        </button>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2">
        <a href="{{ route('payments.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('type') ? 'bg-emerald-600 text-white' : 'bg-white border text-gray-600' }}">All</a>
        <a href="{{ route('payments.index', ['type' => 'in']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('type') === 'in' ? 'bg-emerald-600 text-white' : 'bg-white border text-gray-600' }}">Payments In</a>
        <a href="{{ route('payments.index', ['type' => 'out']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('type') === 'out' ? 'bg-emerald-600 text-white' : 'bg-white border text-gray-600' }}">Payments Out</a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Type</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Category</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Description</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Method</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-xs text-gray-700">{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $payment->type === 'in' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $payment->type === 'in' ? 'IN' : 'OUT' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $payment->category }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $payment->description ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold {{ $payment->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $payment->type === 'in' ? '+' : '-' }} TZS {{ number_format($payment->amount, 0) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="deletePayment({{ $payment->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $payments->links() }}</div>
    </div>
</div>

{{-- Drawer Modal --}}
<div id="paymentModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closePaymentModal()"></div>
    <div class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-xl overflow-y-auto transition-transform">
        <div class="sticky top-0 bg-white border-b px-5 py-4 flex items-center justify-between z-10">
            <h3 class="text-sm font-bold text-gray-900">Add Payment</h3>
            <button onclick="closePaymentModal()" class="p-1 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="paymentForm" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Payment Type</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer hover:bg-emerald-50">
                        <input type="radio" name="type" value="in" checked onchange="updateCategories()" class="text-emerald-600">
                        <span class="text-xs font-semibold text-gray-700">Payment In (Received)</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer hover:bg-red-50">
                        <input type="radio" name="type" value="out" onchange="updateCategories()" class="text-red-600">
                        <span class="text-xs font-semibold text-gray-700">Payment Out (Paid)</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Category</label>
                <select id="payment_category" name="category" class="w-full rounded-lg border-gray-200 text-sm" required></select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Description</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border-gray-200 text-sm"></textarea>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Amount (TZS)</label>
                <input type="number" name="amount" step="0.01" min="0.01" class="w-full rounded-lg border-gray-200 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Payment Method</label>
                <select name="payment_method" class="w-full rounded-lg border-gray-200 text-sm" required>
                    <option value="cash">Cash</option>
                    <option value="mpesa">M-Pesa</option>
                    <option value="tigo_pesa">Tigo Pesa</option>
                    <option value="airtel_money">Airtel Money</option>
                    <option value="halopesa">HaloPesa</option>
                    <option value="bank">Bank</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Customer (for Payments In)</label>
                <select name="customer_id" class="w-full rounded-lg border-gray-200 text-sm">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} {{ $c->current_debt > 0 ? '(Debt: TZS ' . number_format($c->current_debt, 0) . ')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Supplier (for Payments Out)</label>
                <select name="supplier_id" class="w-full rounded-lg border-gray-200 text-sm">
                    <option value="">-- Select Supplier --</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->name }} {{ $s->balance > 0 ? '(Debt: TZS ' . number_format($s->balance, 0) . ')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Branch</label>
                <select name="branch_id" class="w-full rounded-lg border-gray-200 text-sm">
                    <option value="">-- Select Branch --</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Date</label>
                <input type="date" name="payment_date" class="w-full rounded-lg border-gray-200 text-sm" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-gold flex-1 py-2.5 rounded-lg text-sm font-semibold">Save</button>
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">Close</button>
            </div>
        </form>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const inCategories = {{ json_encode($inCategories) }};
const outCategories = {{ json_encode($outCategories) }};

function updateCategories() {
    const type = document.querySelector('input[name="type"]:checked').value;
    const select = document.getElementById('payment_category');
    const cats = type === 'in' ? inCategories : outCategories;
    select.innerHTML = cats.map(c => `<option value="${c}">${c}</option>`).join('');
}

function openPaymentModal() {
    document.getElementById('paymentForm').reset();
    updateCategories();
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

document.getElementById('paymentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    delete data._token;

    try {
        const res = await fetch('/payments', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            showToast('success', 'Success', result.message);
            closePaymentModal();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('error', 'Error', result.message || 'An error occurred');
        }
    } catch (err) {
        showToast('error', 'Error', 'Network error');
    }
});

async function deletePayment(id) {
    saConfirm({
        title: 'Delete Payment?',
        text: 'Are you sure you want to delete this payment?',
        icon: 'danger',
        confirmText: 'Yes, Delete',
        confirmColor: 'red',
        onConfirm: async () => {
            try {
                const res = await fetch(`/payments/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
                const result = await res.json();
                if (result.success) {
                    showToast('success', 'Success', result.message);
                    setTimeout(() => location.reload(), 800);
                }
            } catch (err) {
                showToast('error', 'Error', 'Network error');
            }
        }
    });
}

document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closePaymentModal(); });
</script>
@endsection
