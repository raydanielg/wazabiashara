@extends('layouts.dashboard')

@section('title', 'Kategoria')

@section('page_title', 'Kategoria')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kategoria
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Simamia kategoria za bidhaa zako</p>
        </div>
        <button onclick="openCatModal()" class="btn-gold font-bold px-5 py-3 rounded-2xl inline-flex items-center gap-2 text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Ongeza Kategoria
        </button>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2 sm:gap-3 flex-wrap">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tafuta kategoria..." class="w-full pl-9 pr-3 py-2.5 rounded-2xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-500 text-white font-bold text-sm hover:bg-emerald-600 hover:shadow-md transition-all flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Tafuta
        </button>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Jina</th>
                        <th class="px-4 py-3 text-left hidden sm:table-cell">Parent</th>
                        <th class="px-4 py-3 text-center">Bidhaa</th>
                        <th class="px-4 py-3 text-center">Kitendo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="catTableBody">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-gray-50/50 transition-colors" id="catrow-{{ $cat->id }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                                    @if($cat->icon)<span class="text-lg">{{ $cat->icon }}</span>@else<svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>@endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-700 truncate">{{ $cat->name }}</p>
                                    @if($cat->description)<p class="text-xs text-gray-400 truncate">{{ $cat->description }}</p>@endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-medium hidden sm:table-cell">{{ $cat->parent?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">{{ $cat->products_count }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="editCat({{ $cat->id }}, '{{ addslashes($cat->name) }}', {{ $cat->parent_id ?? 'null' }}, '{{ addslashes($cat->icon ?? '') }}', '{{ addslashes($cat->description ?? '') }}')" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all flex items-center justify-center" title="Hariri">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button onclick="deleteCat({{ $cat->id }})" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center" title="Futa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <p class="text-gray-400 font-medium text-sm">Hakuna kategoria. Ongeza kategoria yako ya kwanza!</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $categories->links() }}
        </div>
    </div>
</div>

{{-- Category Modal Drawer --}}
<div id="catOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeCatModal()"></div>
<div id="catModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 id="catModalTitle" class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            Ongeza Kategoria Mpya
        </h2>
        <button onclick="closeCatModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="catForm" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf
        <input type="hidden" id="catMethod" name="_method" value="POST">
        <input type="hidden" id="catId" value="">

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Jina la Kategoria *</label>
            <input type="text" name="name" id="catName" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Parent (chagua kama ni sub-kategoria)</label>
            <select name="parent_id" id="catParent" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">
                <option value="">— Hakuna —</option>
                @foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Icon (emoji)</label>
            <input type="text" name="icon" id="catIcon" placeholder="📦" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Maelezo</label>
            <textarea name="description" id="catDesc" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all"></textarea>
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button id="catSaveBtn" onclick="submitCat()" class="w-full btn-gold font-bold py-3.5 rounded-2xl text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Hifadhi Kategoria
        </button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function openCatModal() {
    document.getElementById('catModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></div> Ongeza Kategoria Mpya';
    document.getElementById('catMethod').value = 'POST';
    document.getElementById('catId').value = '';
    document.getElementById('catForm').reset();
    document.getElementById('catSaveBtn').innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hifadhi Kategoria';
    showCatModal();
}

function editCat(id, name, parentId, icon, desc) {
    document.getElementById('catModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div> Hariri Kategoria';
    document.getElementById('catMethod').value = 'PUT';
    document.getElementById('catId').value = id;
    document.getElementById('catName').value = name;
    document.getElementById('catParent').value = parentId || '';
    document.getElementById('catIcon').value = icon || '';
    document.getElementById('catDesc').value = desc || '';
    document.getElementById('catSaveBtn').innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hifadhi Mabadiliko';
    showCatModal();
}

function showCatModal() {
    document.getElementById('catOverlay').classList.remove('hidden');
    document.getElementById('catModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}

function closeCatModal() {
    document.getElementById('catOverlay').classList.add('hidden');
    document.getElementById('catModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}

async function submitCat() {
    const form = document.getElementById('catForm');
    const formData = new FormData(form);
    const id = document.getElementById('catId').value;
    const isEdit = document.getElementById('catMethod').value === 'PUT';
    const url = isEdit ? '{{ url("/categories") }}/' + id : '{{ route("categories.store") }}';

    if (isEdit) {
        formData.append('_method', 'PUT');
    }

    const btn = document.getElementById('catSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Inahifadhi...';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            closeCatModal();
            Swal.fire({
                icon: 'success',
                title: 'Imefanikiwa!',
                text: data.message,
                timer: 1800,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            }).then(() => location.reload());
        } else {
            const errors = data.errors ? Object.values(data.errors).join('\n') : data.message || 'Hitilafu imetokea.';
            Swal.fire({icon:'error', title:'Hitilafu!', text:errors, confirmButtonColor:'#024938'});
        }
    } catch(e) {
        Swal.fire({icon:'error', title:'Tatizo la Mtandao', text:'Jaribu tena.', confirmButtonColor:'#024938'});
    } finally {
        btn.disabled = false;
        btn.innerHTML = isEdit
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hifadhi Mabadiliko'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hifadhi Kategoria';
    }
}

async function deleteCat(id) {
    const result = await Swal.fire({
        icon: 'warning',
        title: 'Futa Kategoria?',
        text: 'Una uhakika unataka kufuta kategoria hii?',
        showCancelButton: true,
        confirmButtonText: 'Ndiyo, Futa',
        cancelButtonText: 'Ghairi',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280'
    });
    if (!result.isConfirmed) return;

    try {
        const res = await fetch('{{ url("/categories") }}/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new URLSearchParams({ '_method': 'DELETE' })
        });
        const data = await res.json();
        if (data.success) {
            const row = document.getElementById('catrow-' + id);
            if (row) row.remove();
            Swal.fire({icon:'success', title:'Imefutwa!', text:data.message, timer:1500, showConfirmButton:false, toast:true, position:'top-end'});
        } else {
            Swal.fire({icon:'error', title:'Hitilafu!', text:data.message || 'Imeshindwa kufuta.', confirmButtonColor:'#024938'});
        }
    } catch(e) {
        Swal.fire({icon:'error', title:'Tatizo la Mtandao', text:'Jaribu tena.', confirmButtonColor:'#024938'});
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCatModal();
});
</script>
@endsection
