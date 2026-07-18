@extends('layouts.dashboard')

@section('title', 'Wateja')

@section('page_title', 'Wateja & Madeni')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Wateja & Madeni
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Simamia wateja na deni lao</p>
        </div>
        <button onclick="openCustModal()" class="btn-gold font-bold px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Mteja Mpya
        </button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Wateja Jumla</p><p class="text-xl font-bold text-gray-800">{{ $totalCustomers }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Wateja Hai</p><p class="text-xl font-bold text-emerald-600">{{ $activeCustomers }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Wenye Deni</p><p class="text-xl font-bold text-red-600">{{ $customersWithDebt }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gold-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Deni Jumla</p><p class="text-xl font-bold text-gold-600">TZS {{ number_format($totalDebt, 0) }}</p></div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2 sm:gap-3 flex-wrap">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tafuta mteja..." class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
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
                    <tr><th class="px-4 py-3 text-left">Jina</th><th class="px-4 py-3 text-left hidden sm:table-cell">Simu</th><th class="px-4 py-3 text-right">Credit Limit</th><th class="px-4 py-3 text-right">Deni Sasa</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Kitendo</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $c)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                                    <span class="text-sm font-bold text-emerald-600">{{ strtoupper(substr($c->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-semibold text-gray-700">{{ $c->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-medium hidden sm:table-cell">{{ $c->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-600">TZS {{ number_format($c->credit_limit, 0) }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ $c->current_debt > 0 ? 'text-red-600' : 'text-emerald-600' }}">TZS {{ number_format($c->current_debt, 0) }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $c->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">{{ $c->status }}</span></td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($c->current_debt > 0)<a href="{{ route('customers.debts', $c) }}" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center" title="Madeni"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg></a>@endif
                                <button onclick="editCust({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ $c->phone ?? '' }}', '{{ $c->email ?? '' }}', '{{ addslashes($c->address ?? '') }}', {{ $c->credit_limit }}, '{{ $c->status }}')" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all flex items-center justify-center" title="Hariri"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button onclick="deleteCust({{ $c->id }})" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-all flex items-center justify-center" title="Futa"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-gray-400 font-medium text-sm">Hakuna wateja.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $customers->links() }}</div>
    </div>
</div>

{{-- Customer Modal Drawer --}}
<div id="custOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeCustModal()"></div>
<div id="custModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="custModalTitle">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            Mteja Mpya
        </h2>
        <button onclick="closeCustModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="custForm" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf <input type="hidden" name="id" id="custId">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Jina *</label>
            <input name="name" id="custName" required class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Simu</label>
            <input name="phone" id="custPhone" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</label>
            <input name="email" id="custEmail" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Mahali</label>
            <textarea name="address" id="custAddress" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all"></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Credit Limit (TZS)</label>
            <input type="number" name="credit_limit" id="custCredit" min="0" value="0" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div id="custStatusField" class="hidden">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
            <select name="status" id="custStatus" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all"><option value="active">Hai</option><option value="inactive">Imezimwa</option></select>
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button type="button" id="custSubmitBtn" onclick="submitCustForm()" class="w-full btn-gold font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Hifadhi
        </button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let custEditMode = false;
let custEditId = null;

function openCustModal() {
    custEditMode = false;
    custEditId = null;
    document.getElementById('custModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></div> Mteja Mpya';
    document.getElementById('custForm').reset();
    document.getElementById('custId').value='';
    document.getElementById('custStatusField').classList.add('hidden');
    showCustModal();
}
function closeCustModal() {
    document.getElementById('custOverlay').classList.add('hidden');
    document.getElementById('custModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}
function showCustModal() {
    document.getElementById('custOverlay').classList.remove('hidden');
    document.getElementById('custModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}
function editCust(id,name,phone,email,address,credit,status) {
    custEditMode = true;
    custEditId = id;
    document.getElementById('custModalTitle').innerHTML = '<div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div> Hariri Mteja';
    document.getElementById('custId').value=id; document.getElementById('custName').value=name; document.getElementById('custPhone').value=phone; document.getElementById('custEmail').value=email; document.getElementById('custAddress').value=address; document.getElementById('custCredit').value=credit; document.getElementById('custStatus').value=status;
    document.getElementById('custStatusField').classList.remove('hidden');
    showCustModal();
}
function submitCustForm() {
    const btn = document.getElementById('custSubmitBtn');
    const form = document.getElementById('custForm');
    const formData = new FormData(form);
    const data = {};
    formData.forEach((v, k) => { if (k !== '_token') data[k] = v; });

    const url = custEditMode ? '/customers/' + custEditId : '/customers';
    const method = custEditMode ? 'PUT' : 'POST';

    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Inahifadhi...';

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hifadhi';
        if (res.success) {
            showToast('success', 'Imefanikiwa', res.message);
            closeCustModal();
            setTimeout(() => location.reload(), 800);
        } else if (res.errors) {
            Object.keys(res.errors).forEach(k => res.errors[k].forEach(m => showToast('error', 'Hitilafu', m)));
        } else {
            showToast('error', 'Hitilafu', res.message || 'Imeshindwa.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hifadhi';
        showToast('error', 'Hitilafu', 'Tatizo la mtandao.');
    });
}
function deleteCust(id) {
    Swal.fire({
        title: 'Una uhakika?', text: 'Mteja huyu atafutwa kabisa.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ndiyo, Futa', cancelButtonText: 'Ghairi',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/customers/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('success', 'Imefanikiwa', res.message);
                    setTimeout(() => location.reload(), 800);
                }
            });
        }
    });
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeCustModal(); });
</script>
@endsection
