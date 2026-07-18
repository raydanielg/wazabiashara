@extends('layouts.dashboard')

@section('title', 'Bidhaa')

@section('page_title', 'Bidhaa & Stoo')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Bidhaa & Stoo
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Simamia bidhaa zako na stoo ya tawi</p>
        </div>
        <button onclick="openProductModal()" class="btn-gold font-bold px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Ongeza Bidhaa
        </button>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-2 sm:gap-3 flex-wrap">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tafuta bidhaa..." class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <select name="category" class="pl-9 pr-8 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">
                <option value="">Kategoria Zote</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2.5 rounded-lg bg-emerald-500 text-white font-bold text-sm hover:bg-emerald-600 transition-all flex items-center gap-1.5">
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
                        <th class="px-4 py-3 text-left">Bidhaa</th>
                        <th class="px-4 py-3 text-left hidden sm:table-cell">Barcode</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">Kategoria</th>
                        <th class="px-4 py-3 text-right hidden sm:table-cell">Bei Kununulia</th>
                        <th class="px-4 py-3 text-right">Bei Uziao</th>
                        <th class="px-4 py-3 text-center">Stoo</th>
                        <th class="px-4 py-3 text-center hidden lg:table-cell">Status</th>
                        <th class="px-4 py-3 text-center">Kitendo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="productsTableBody">
                    @forelse($products as $p)
                    @php $stock = $p->branchStock->first(); $qty = $stock?->qty ?? 0; @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors" id="row-{{ $p->id }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="h-10 w-10 rounded-xl bg-gray-50 grid place-items-center overflow-hidden flex-shrink-0 border border-gray-100">
                                    @if($p->image)<img src="{{ asset('storage/' . $p->image) }}" class="w-full h-full object-cover">@else<svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>@endif
                                </div>
                                <div class="min-w-0"><p class="font-semibold text-gray-700 truncate">{{ $p->name }}</p><p class="text-xs text-gray-400">{{ $p->unit }}</p></div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs hidden sm:table-cell">{{ $p->barcode ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 font-medium hidden md:table-cell">{{ $p->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-600 hidden sm:table-cell">TZS {{ number_format($p->cost_price, 0) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-emerald-600">TZS {{ number_format($p->selling_price, 0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $qty <= 0 ? 'bg-red-50 text-red-500' : ($qty <= $p->reorder_level ? 'bg-gold-50 text-gold-600' : 'bg-emerald-50 text-emerald-600') }}">{{ $qty }} {{ $p->unit }}</span>
                        </td>
                        <td class="px-4 py-3 text-center hidden lg:table-cell">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $p->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">{{ $p->status === 'active' ? 'Hai' : 'Imezimwa' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="editProduct({{ $p->id }})" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all flex items-center justify-center" title="Hariri">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button onclick="deleteProduct({{ $p->id }})" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center" title="Futa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-gray-400 font-medium text-sm">Hakuna bidhaa. Ongeza bidhaa yako ya kwanza!</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $products->links() }}
        </div>
    </div>
</div>

{{-- Product Modal Drawer --}}
<div id="productOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeProductModal()"></div>
<div id="productModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[480px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 id="modalTitle" class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            Ongeza Bidhaa Mpya
        </h2>
        <button onclick="closeProductModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="productForm" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf
        <input type="hidden" id="formMethod" name="_method" value="POST">
        <input type="hidden" id="productId" value="">

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Jina la Bidhaa *</label>
            <input type="text" name="name" id="fName" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Barcode</label>
                <input type="text" name="barcode" id="fBarcode" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">SKU</label>
                <input type="text" name="sku" id="fSku" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kategoria</label>
                <select name="category_id" id="fCategory" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">
                    <option value="">— Chagua —</option>
                    @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kipimo *</label>
                <select name="unit" id="fUnit" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">
                    <option value="piece">Kipande</option><option value="kg">Kilo</option><option value="lita">Lita</option>
                    <option value="katoni">Katoni</option><option value="dazani">Dazani</option><option value="box">Box</option>
                    <option value="m">Mita</option><option value="set">Seti</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Bei ya Kununulia *</label>
                <input type="number" name="cost_price" id="fCost" required min="0" step="0.01" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Bei ya Uziao *</label>
                <input type="number" name="selling_price" id="fSelling" required min="0" step="0.01" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold transition-all">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Reorder Level</label>
                <input type="number" name="reorder_level" id="fReorder" value="5" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tarehe ya Kuisha</label>
                <input type="date" name="expiry_date" id="fExpiry" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
            <select name="status" id="fStatus" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">
                <option value="active">Hai</option>
                <option value="inactive">Imezimwa</option>
            </select>
        </div>

        {{-- Initial stock per branch (only for create) --}}
        <div id="initialStockSection" class="border-t border-gray-100 pt-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Stoo ya Mwanzo
            </h3>
            <div class="grid grid-cols-2 gap-3" id="branchStockInputs">
                @php $branches = auth()->user()->business->branches; @endphp
                @foreach($branches as $branch)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ $branch->name }}</label>
                    <input type="number" name="initial_stock[{{ $branch->id }}]" min="0" step="0.01" placeholder="0" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
                </div>
                @endforeach
            </div>
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button id="saveBtn" onclick="submitProduct()" class="w-full btn-gold font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Hifadhi Bidhaa
        </button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function openProductModal() {
    document.getElementById('modalTitle').innerHTML = '<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Ongeza Bidhaa Mpya';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('productId').value = '';
    document.getElementById('productForm').reset();
    document.getElementById('fReorder').value = '5';
    document.getElementById('fStatus').value = 'active';
    document.getElementById('initialStockSection').style.display = '';
    document.getElementById('saveBtn').innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hifadhi Bidhaa';
    showModal();
}

function editProduct(id) {
    fetch('{{ url("/products") }}/' + id + '/edit', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(data => {
        const p = data.product;
        document.getElementById('modalTitle').innerHTML = '<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Hariri Bidhaa';
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('productId').value = id;
        document.getElementById('fName').value = p.name || '';
        document.getElementById('fBarcode').value = p.barcode || '';
        document.getElementById('fSku').value = p.sku || '';
        document.getElementById('fCategory').value = p.category_id || '';
        document.getElementById('fUnit').value = p.unit || 'piece';
        document.getElementById('fCost').value = p.cost_price || 0;
        document.getElementById('fSelling').value = p.selling_price || 0;
        document.getElementById('fReorder').value = p.reorder_level || 5;
        document.getElementById('fExpiry').value = p.expiry_date || '';
        document.getElementById('fStatus').value = p.status || 'active';
        document.getElementById('initialStockSection').style.display = 'none';
        document.getElementById('saveBtn').innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hifadhi Mabadiliko';
        showModal();
    })
    .catch(() => Swal.fire({icon:'error', title:'Hitilafu!', text:'Imeshindwa kupakia data.', confirmButtonColor:'#024938'}));
}

function showModal() {
    document.getElementById('productOverlay').classList.remove('hidden');
    document.getElementById('productModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}

function closeProductModal() {
    document.getElementById('productOverlay').classList.add('hidden');
    document.getElementById('productModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}

async function submitProduct() {
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    const id = document.getElementById('productId').value;
    const isEdit = document.getElementById('formMethod').value === 'PUT';
    const url = isEdit ? '{{ url("/products") }}/' + id : '{{ route("products.store") }}';
    const method = isEdit ? 'POST' : 'POST';

    if (isEdit) {
        formData.append('_method', 'PUT');
    }

    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Inahifadhi...';

    try {
        const res = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            closeProductModal();
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
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hifadhi Bidhaa';
    }
}

async function deleteProduct(id) {
    const result = await Swal.fire({
        icon: 'warning',
        title: 'Futa Bidhaa?',
        text: 'Una uhakika unataka kufuta bidhaa hii?',
        showCancelButton: true,
        confirmButtonText: 'Ndiyo, Futa',
        cancelButtonText: 'Ghairi',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280'
    });
    if (!result.isConfirmed) return;

    try {
        const res = await fetch('{{ url("/products") }}/' + id, {
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
            const row = document.getElementById('row-' + id);
            if (row) row.remove();
            Swal.fire({icon:'success', title:'Imefutwa!', text:data.message, timer:1500, showConfirmButton:false, toast:true, position:'top-end'});
        } else {
            Swal.fire({icon:'error', title:'Hitilafu!', text:data.message || 'Imeshindwa kufuta.', confirmButtonColor:'#024938'});
        }
    } catch(e) {
        Swal.fire({icon:'error', title:'Tatizo la Mtandao', text:'Jaribu tena.', confirmButtonColor:'#024938'});
    }
}

// Close modal on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeProductModal();
});
</script>
@endsection
