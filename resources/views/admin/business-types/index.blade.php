@extends('layouts.dashboard')

@section('title', 'Business Types')
@section('page_title', 'Business Types')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Business Types
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage available business types for registration</p>
        </div>
        <button onclick="openCreateModal()" class="btn-gold font-bold px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Type
        </button>
    </div>

    {{-- Types Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="typesGrid">
        @foreach($types as $type)
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:border-emerald-200 transition-all" id="type-card-{{ $type->id }}">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm text-gray-700">{{ $type->name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $type->slug }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $type->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                    {{ $type->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            @if($type->description)
            <p class="text-xs text-gray-500 mb-3">{{ $type->description }}</p>
            @endif
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-400 font-medium">Order: {{ $type->sort_order }}</span>
                <div class="flex items-center gap-2">
                    <button onclick="toggleType({{ $type->id }})" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-500 transition-all flex items-center justify-center" title="Toggle">
                        @if($type->is_active)
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L18.364 5.636"/></svg>
                        @endif
                    </button>
                    <button onclick="openEditModal({{ $type->id }}, '{{ $type->name }}', '{{ $type->slug }}', '{{ $type->icon }}', '{{ $type->description }}', {{ $type->sort_order }}, {{ $type->is_active ? 1 : 0 }})" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all flex items-center justify-center" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="deleteType({{ $type->id }})" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Create/Edit Modal Drawer --}}
<div id="typeOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeModal()"></div>
<div id="typeModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="modalTitle">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            Add Business Type
        </h2>
        <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="typeForm" class="flex-1 overflow-y-auto p-5 space-y-4">
        <input type="hidden" id="typeId" value="">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Name</label>
            <input id="typeName" type="text" required class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all" placeholder="e.g. Wholesale Store">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Slug (Optional)</label>
            <input id="typeSlug" type="text" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-mono transition-all" placeholder="retail">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Icon (Optional)</label>
                <input id="typeIcon" type="text" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all" placeholder="shopping-bag">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Sort Order</label>
                <input id="typeSort" type="number" min="0" value="0" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Description (Optional)</label>
            <textarea id="typeDesc" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all" placeholder="Description for this type..."></textarea>
        </div>
        <div id="activeWrap" class="hidden">
            <label class="flex items-center gap-2 cursor-pointer">
                <input id="typeActive" type="checkbox" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                <span class="text-sm font-medium text-gray-600">Active</span>
            </label>
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button type="submit" form="typeForm" id="typeSubmitBtn" class="w-full btn-gold font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Save
        </button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function openCreateModal() {
    document.getElementById('modalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></div> Add Business Type';
    document.getElementById('typeId').value = '';
    document.getElementById('typeName').value = '';
    document.getElementById('typeSlug').value = '';
    document.getElementById('typeIcon').value = '';
    document.getElementById('typeDesc').value = '';
    document.getElementById('typeSort').value = '0';
    document.getElementById('activeWrap').classList.add('hidden');
    document.getElementById('typeForm').dataset.mode = 'create';
    showModal();
}

function openEditModal(id, name, slug, icon, desc, sort, active) {
    document.getElementById('modalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div> Edit Business Type';
    document.getElementById('typeId').value = id;
    document.getElementById('typeName').value = name;
    document.getElementById('typeSlug').value = slug;
    document.getElementById('typeIcon').value = icon;
    document.getElementById('typeDesc').value = desc;
    document.getElementById('typeSort').value = sort;
    document.getElementById('typeActive').checked = active == 1;
    document.getElementById('activeWrap').classList.remove('hidden');
    document.getElementById('typeForm').dataset.mode = 'edit';
    showModal();
}

function showModal() {
    document.getElementById('typeOverlay').classList.remove('hidden');
    document.getElementById('typeModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('typeOverlay').classList.add('hidden');
    document.getElementById('typeModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}

document.getElementById('typeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const mode = this.dataset.mode;
    const id = document.getElementById('typeId').value;
    const btn = document.getElementById('typeSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const data = {
        name: document.getElementById('typeName').value,
        slug: document.getElementById('typeSlug').value || null,
        icon: document.getElementById('typeIcon').value || null,
        description: document.getElementById('typeDesc').value || null,
        sort_order: parseInt(document.getElementById('typeSort').value) || 0,
    };

    if (mode === 'edit') {
        data.is_active = document.getElementById('typeActive').checked;
    }

    const url = mode === 'edit'
        ? '/admin/business-types/' + id
        : '/admin/business-types';
    const method = mode === 'edit' ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.textContent = 'Save';
        if (res.success) {
            showToast('success', 'Success', res.message);
            setTimeout(() => location.reload(), 800);
        } else if (res.errors) {
            Object.keys(res.errors).forEach(k => res.errors[k].forEach(m => showToast('error', 'Error', m)));
        } else {
            showToast('error', 'Error', res.message || 'Failed.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Save';
        showToast('error', 'Error', 'Network error.');
    });
});

function toggleType(id) {
    fetch('/admin/business-types/' + id + '/toggle', {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showToast('success', 'Success', res.message);
            setTimeout(() => location.reload(), 600);
        }
    });
}

function deleteType(id) {
    saConfirm({
        title: 'Are you sure?',
        text: 'This type will be permanently deleted.',
        icon: 'danger',
        confirmText: 'Yes, Delete',
        confirmColor: 'red',
        onConfirm: () => {
            fetch('/admin/business-types/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('success', 'Success', res.message);
                    setTimeout(() => location.reload(), 600);
                }
            });
        }
    });
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
</script>
@endsection
