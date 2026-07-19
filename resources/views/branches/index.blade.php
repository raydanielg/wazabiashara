@extends('layouts.dashboard')

@section('title', 'Branches')

@section('page_title', 'Branches')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Branches
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your business branches</p>
        </div>
        <button onclick="openBranchModal()" class="btn-gold font-bold px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Branch
        </button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Total Branches</p><p class="text-xl font-bold text-gray-800">{{ $totalBranches }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Active Branches</p><p class="text-xl font-bold text-emerald-600">{{ $activeBranches }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gold-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Staff</p><p class="text-xl font-bold text-gray-800">{{ $totalUsers }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gold-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Total Sales</p><p class="text-xl font-bold text-gray-800">{{ $totalSales }}</p></div>
            </div>
        </div>
    </div>

    {{-- Branch Cards Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4" id="branchesGrid">
        @forelse($branches as $branch)
        <div class="bg-white rounded-2xl border p-5 shadow-sm hover:shadow-md transition-all {{ session('active_branch_id') == $branch->id ? 'border-emerald-300' : 'border-gray-200' }}" id="branch-card-{{ $branch->id }}">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $branch->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $branch->location ?? '—' }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $branch->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">{{ $branch->status === 'active' ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="mt-3 flex gap-4 text-xs font-medium text-gray-500">
                <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> {{ $branch->users_count }}</span>
                <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg> {{ $branch->sales_count }}</span>
            </div>
            <div class="mt-4 flex gap-2">
                <form method="POST" action="{{ route('branches.switch', $branch) }}">@csrf<button class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-xs font-bold transition-all">Switch</button></form>
                <button onclick="editBranch({{ $branch->id }}, '{{ addslashes($branch->name) }}', '{{ addslashes($branch->location ?? '') }}', '{{ $branch->phone ?? '' }}', '{{ $branch->status }}')" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-500 transition-all flex items-center justify-center" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                <button onclick="deleteBranch({{ $branch->id }})" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16">
            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <p class="text-gray-400 font-medium text-sm">No branches found. Add your first branch!</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Branch Modal Drawer --}}
<div id="branchOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeBranchModal()"></div>
<div id="branchModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="branchModalTitle">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            New Branch
        </h2>
        <button onclick="closeBranchModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="branchForm" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf
        <input type="hidden" name="id" id="branchId">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Branch Name *</label>
            <input name="name" id="branchName" required class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Location</label>
            <input name="location" id="branchLocation" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Phone</label>
            <input name="phone" id="branchPhone" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div id="statusField" class="hidden">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
            <select name="status" id="branchStatus" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all"><option value="active">Active</option><option value="inactive">Inactive</option></select>
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button type="button" id="branchSubmitBtn" onclick="submitBranchForm()" class="w-full btn-gold font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Save
        </button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let branchEditMode = false;
let branchEditId = null;

function openBranchModal() {
    branchEditMode = false;
    branchEditId = null;
    document.getElementById('branchModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></div> New Branch';
    document.getElementById('branchForm').reset();
    document.getElementById('branchId').value = '';
    document.getElementById('statusField').classList.add('hidden');
    showBranchModal();
}
function closeBranchModal() {
    document.getElementById('branchOverlay').classList.add('hidden');
    document.getElementById('branchModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}
function showBranchModal() {
    document.getElementById('branchOverlay').classList.remove('hidden');
    document.getElementById('branchModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}
function editBranch(id, name, location, phone, status) {
    branchEditMode = true;
    branchEditId = id;
    document.getElementById('branchModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div> Edit Branch';
    document.getElementById('branchId').value = id;
    document.getElementById('branchName').value = name;
    document.getElementById('branchLocation').value = location;
    document.getElementById('branchPhone').value = phone;
    document.getElementById('branchStatus').value = status;
    document.getElementById('statusField').classList.remove('hidden');
    showBranchModal();
}
function submitBranchForm() {
    const btn = document.getElementById('branchSubmitBtn');
    const form = document.getElementById('branchForm');
    const formData = new FormData(form);
    const data = {};
    formData.forEach((v, k) => { if (k !== '_token') data[k] = v; });

    const url = branchEditMode ? '/branches/' + branchEditId : '/branches';
    const method = branchEditMode ? 'PUT' : 'POST';

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
            closeBranchModal();
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
function deleteBranch(id) {
    Swal.fire({
        title: 'Are you sure?', text: 'This branch will be permanently deleted.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete', cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/branches/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('success', 'Success', res.message);
                    const card = document.getElementById('branch-card-' + id);
                    if (card) card.style.display = 'none';
                    setTimeout(() => location.reload(), 800);
                }
            });
        }
    });
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeBranchModal(); });
</script>
@endsection
