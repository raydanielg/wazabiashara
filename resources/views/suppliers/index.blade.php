@extends('layouts.dashboard')

@section('title', 'Suppliers')

@section('page_title', 'Suppliers')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 18a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 18h6m-6 0v-6m6 6V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12"/></svg>
                Suppliers
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage suppliers and purchases</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('suppliers.purchases') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Purchases
            </a>
            <button onclick="openSupModal()" class="btn-gold font-bold px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm shadow-sm hover:shadow-md transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Supplier
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Suppliers</p><p class="text-xl font-bold text-gray-800">{{ $totalSuppliers }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Active</p><p class="text-xl font-bold text-emerald-600">{{ $activeSuppliers }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Total Balance</p><p class="text-xl font-bold text-red-600">TZS {{ number_format($totalBalance, 0) }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gold-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Purchases</p><p class="text-xl font-bold text-gold-600">{{ $totalPurchases }}</p></div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wide">
                    <tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left hidden sm:table-cell">Phone</th><th class="px-4 py-3 text-right">Outstanding Balance</th><th class="px-4 py-3 text-center hidden md:table-cell">Purchases</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($suppliers as $s)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 18a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 18h6m-6 0v-6m6 6V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12"/></svg>
                                </div>
                                <span class="font-semibold text-gray-700">{{ $s->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-medium hidden sm:table-cell">{{ $s->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ $s->balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">TZS {{ number_format($s->balance, 0) }}</td>
                        <td class="px-4 py-3 text-center hidden md:table-cell"><span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">{{ $s->purchases_count }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $s->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">{{ $s->status }}</span></td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="editSup({{ $s->id }}, '{{ addslashes($s->name) }}', '{{ $s->phone ?? '' }}', '{{ $s->email ?? '' }}', '{{ addslashes($s->address ?? '') }}', '{{ $s->status }}')" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all flex items-center justify-center" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button onclick="deleteSup({{ $s->id }})" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 18a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 18h6m-6 0v-6m6 6V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12"/></svg>
                        <p class="text-gray-400 font-medium text-sm">No suppliers found.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $suppliers->links() }}</div>
    </div>
</div>

{{-- Supplier Modal Drawer --}}
<div id="supOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeSupModal()"></div>
<div id="supModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="supModalTitle">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            New Supplier
        </h2>
        <button onclick="closeSupModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="supForm" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf <input type="hidden" name="id" id="supId">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Name *</label>
            <input name="name" id="supName" required class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Phone</label>
            <input name="phone" id="supPhone" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</label>
            <input name="email" id="supEmail" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Address</label>
            <textarea name="address" id="supAddress" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all"></textarea>
        </div>
        <div id="supStatusField" class="hidden">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
            <select name="status" id="supStatus" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all"><option value="active">Active</option><option value="inactive">Inactive</option></select>
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button type="button" id="supSubmitBtn" onclick="submitSupForm()" class="w-full btn-gold font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Save
        </button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let supEditMode = false;
let supEditId = null;

function openSupModal() {
    supEditMode = false;
    supEditId = null;
    document.getElementById('supModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></div> New Supplier';
    document.getElementById('supForm').reset();
    document.getElementById('supId').value='';
    document.getElementById('supStatusField').classList.add('hidden');
    showSupModal();
}
function closeSupModal() {
    document.getElementById('supOverlay').classList.add('hidden');
    document.getElementById('supModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}
function showSupModal() {
    document.getElementById('supOverlay').classList.remove('hidden');
    document.getElementById('supModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}
function editSup(id,name,phone,email,address,status) {
    supEditMode = true;
    supEditId = id;
    document.getElementById('supModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div> Edit Supplier';
    document.getElementById('supId').value=id; document.getElementById('supName').value=name; document.getElementById('supPhone').value=phone; document.getElementById('supEmail').value=email; document.getElementById('supAddress').value=address; document.getElementById('supStatus').value=status;
    document.getElementById('supStatusField').classList.remove('hidden');
    showSupModal();
}
function submitSupForm() {
    const btn = document.getElementById('supSubmitBtn');
    const form = document.getElementById('supForm');
    const formData = new FormData(form);
    const data = {};
    formData.forEach((v, k) => { if (k !== '_token') data[k] = v; });

    const url = supEditMode ? '/suppliers/' + supEditId : '/suppliers';
    const method = supEditMode ? 'PUT' : 'POST';

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
            closeSupModal();
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
function deleteSup(id) {
    Swal.fire({
        title: 'Are you sure?', text: 'This supplier will be permanently deleted.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete', cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/suppliers/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('success', 'Success', res.message);
                    setTimeout(() => location.reload(), 800);
                }
            });
        }
    });
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeSupModal(); });
</script>
@endsection
