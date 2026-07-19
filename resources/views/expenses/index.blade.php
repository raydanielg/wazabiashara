@extends('layouts.dashboard')

@section('title', 'Expenses')

@section('page_title', 'Expenses')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Expenses
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Record business expenses</p>
        </div>
        <button onclick="openExpModal()" class="btn-gold font-bold px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Expense
        </button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Today</p><p class="text-xl font-bold text-gray-800">TZS {{ number_format($todayExpenses, 0) }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gold-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">This Month</p><p class="text-xl font-bold text-gold-600">TZS {{ number_format($monthExpenses, 0) }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Total</p><p class="text-xl font-bold text-red-600">TZS {{ number_format($totalExpenses, 0) }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Categories (Month)</p><p class="text-xl font-bold text-gray-800">{{ $expensesByCategory->count() }}</p></div>
            </div>
        </div>
    </div>

    {{-- Expense by Category Chart --}}
    @if($expensesByCategory->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Expenses by Category (This Month)
        </h3>
        <div class="space-y-2.5">
            @foreach($expensesByCategory as $cat)
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-gray-600 w-20 flex-shrink-0">{{ $cat->category }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 flex items-center justify-end pr-2" style="width: {{ min(100, ($cat->total / max(1, $expensesByCategory->first()->total)) * 100) }}%">
                        <span class="text-[10px] font-bold text-white">TZS {{ number_format($cat->total, 0) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wide">
                    <tr><th class="px-4 py-3 text-left">Category</th><th class="px-4 py-3 text-left hidden sm:table-cell">Description</th><th class="px-4 py-3 text-left hidden md:table-cell">Branch</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3 text-left hidden sm:table-cell">Date</th><th class="px-4 py-3 text-center">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($expenses as $e)
                    <tr class="hover:bg-gray-50/50 transition-colors" id="exp-row-{{ $e->id }}">
                        <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gold-50 text-gold-600">{{ $e->category }}</span></td>
                        <td class="px-4 py-3 text-gray-600 font-medium hidden sm:table-cell">{{ $e->description ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 font-medium hidden md:table-cell">{{ $e->branch?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold text-red-600">TZS {{ number_format($e->amount, 0) }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden sm:table-cell">{{ $e->expense_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="editExp({{ $e->id }})" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all flex items-center justify-center" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button onclick="deleteExp({{ $e->id }})" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="text-gray-400 font-medium text-sm">No expenses found.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $expenses->links() }}</div>
    </div>
</div>

{{-- Expense Modal Drawer --}}
<div id="expOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeExpModal()"></div>
<div id="expModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="expModalTitle">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            New Expense
        </h2>
        <button onclick="closeExpModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="expForm" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf <input type="hidden" name="id" id="expId">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Category *</label>
            <select name="category" id="expCategory" required class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">@foreach($categories as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach</select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Description</label>
            <input name="description" id="expDesc" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Branch</label>
            <select name="branch_id" id="expBranch" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all"><option value="">—</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Amount (TZS) *</label>
            <input type="number" name="amount" id="expAmount" required min="0.01" step="0.01" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date *</label>
            <input type="date" name="expense_date" id="expDate" required value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button type="button" id="expSubmitBtn" onclick="submitExpForm()" class="w-full btn-gold font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Save
        </button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let expEditMode = false;
let expEditId = null;

function openExpModal() {
    expEditMode = false;
    expEditId = null;
    document.getElementById('expModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></div> New Expense';
    document.getElementById('expForm').reset();
    document.getElementById('expId').value = '';
    document.getElementById('expDate').value = '{{ date('Y-m-d') }}';
    showExpModal();
}
function closeExpModal() {
    document.getElementById('expOverlay').classList.add('hidden');
    document.getElementById('expModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}
function showExpModal() {
    document.getElementById('expOverlay').classList.remove('hidden');
    document.getElementById('expModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}
function editExp(id) {
    expEditMode = true;
    expEditId = id;
    fetch('/expenses/' + id + '/edit', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const e = res.expense;
            document.getElementById('expModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div> Edit Expense';
            document.getElementById('expId').value = id;
            document.getElementById('expCategory').value = e.category;
            document.getElementById('expDesc').value = e.description || '';
            document.getElementById('expBranch').value = e.branch_id || '';
            document.getElementById('expAmount').value = e.amount;
            document.getElementById('expDate').value = e.expense_date;
            showExpModal();
        }
    });
}
function submitExpForm() {
    const btn = document.getElementById('expSubmitBtn');
    const form = document.getElementById('expForm');
    const formData = new FormData(form);
    const data = {};
    formData.forEach((v, k) => { if (k !== '_token') data[k] = v; });

    const url = expEditMode ? '/expenses/' + expEditId : '/expenses';
    const method = expEditMode ? 'PUT' : 'POST';

    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Saving...';

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Save';
        if (res.success) {
            showToast('success', 'Success', res.message);
            closeExpModal();
            setTimeout(() => location.reload(), 800);
        } else if (res.errors) {
            Object.keys(res.errors).forEach(k => res.errors[k].forEach(m => showToast('error', 'Error', m)));
        } else {
            showToast('error', 'Error', res.message || 'Failed.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Save';
        showToast('error', 'Error', 'Network error.');
    });
}
function deleteExp(id) {
    Swal.fire({
        title: 'Are you sure?', text: 'This expense will be permanently deleted.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete', cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/expenses/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('success', 'Success', res.message);
                    const row = document.getElementById('exp-row-' + id);
                    if (row) row.style.display = 'none';
                    setTimeout(() => location.reload(), 800);
                }
            });
        }
    });
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeExpModal(); });
</script>
@endsection
