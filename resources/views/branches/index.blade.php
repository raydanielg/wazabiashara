@extends('layouts.dashboard')

@section('title', 'Matawi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-emerald-700">🏪 Matawi</h1>
        <button onclick="document.getElementById('branchModal').classList.remove('hidden')" class="btn-gold font-extrabold px-5 py-2.5 rounded-xl">+ Ongeza Tawi</button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($branches as $branch)
        <div class="bg-white rounded-2xl shadow-card p-5 border-2 {{ session('active_branch_id') == $branch->id ? 'border-emerald-400' : 'border-gray-100' }}">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-black text-lg text-emerald-700">{{ $branch->name }}</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-0.5">{{ $branch->location ?? '—' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $branch->phone ?? '—' }}</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $branch->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $branch->status === 'active' ? 'Hai' : 'Imezimwa' }}</span>
            </div>
            <div class="mt-3 flex gap-4 text-xs font-bold text-gray-500">
                <span>👥 {{ $branch->users_count }} Wafanyakazi</span>
                <span>🛒 {{ $branch->sales_count }} Mauzo</span>
            </div>
            <div class="mt-4 flex gap-2">
                <form method="POST" action="{{ route('branches.switch', $branch) }}">@csrf<button class="text-xs font-bold text-emerald-600 hover:text-emerald-800">Badilisha Tawi</button></form>
                <button onclick="editBranch({{ $branch->id }}, '{{ addslashes($branch->name) }}', '{{ addslashes($branch->location ?? '') }}', '{{ $branch->phone ?? '' }}', '{{ $branch->status }}')" class="text-xs font-bold text-gold-600 hover:text-gold-800">Hariri</button>
                <form method="POST" action="{{ route('branches.destroy', $branch) }}" class="inline" onsubmit="return confirm('Futa tawi?')">@csrf @method('DELETE')<button class="text-xs font-bold text-red-500 hover:text-red-700">Futa</button></form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-400 font-bold">Hakuna matawi. Ongeza tawi la kwanza!</div>
        @endforelse
    </div>
</div>

<div id="branchModal" class="hidden fixed inset-0 z-50 bg-black/40 grid place-items-center p-4">
    <div class="bg-white rounded-2xl shadow-cardlg p-6 w-full max-w-md">
        <h3 class="font-black text-lg text-emerald-700 mb-4" id="branchModalTitle">Tawi Jipya</h3>
        <form id="branchForm" method="POST" action="{{ route('branches.store') }}">
            @csrf
            <input type="hidden" name="id" id="branchId">
            <div class="space-y-3">
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Jina la Tawi *</label><input name="name" id="branchName" required class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Mahali</label><input name="location" id="branchLocation" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Simu</label><input name="phone" id="branchPhone" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div id="statusField" class="hidden"><label class="block text-sm font-bold text-gray-600 mb-1">Status</label><select name="status" id="branchStatus" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white"><option value="active">Hai</option><option value="inactive">Imezimwa</option></select></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="closeBranchModal()" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 font-bold text-gray-600">Funga</button>
                <button type="submit" class="flex-1 btn-gold font-black py-2.5 rounded-xl">Hifadhi</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeBranchModal() { document.getElementById('branchModal').classList.add('hidden'); document.getElementById('branchForm').reset(); document.getElementById('branchId').value = ''; document.getElementById('branchModalTitle').textContent = 'Tawi Jipya'; document.getElementById('statusField').classList.add('hidden'); document.getElementById('branchForm').action = '{{ route("branches.store") }}'; document.getElementById('branchForm').querySelector('input[name="_method"]')?.remove(); }
function editBranch(id, name, location, phone, status) {
    document.getElementById('branchId').value = id;
    document.getElementById('branchName').value = name;
    document.getElementById('branchLocation').value = location;
    document.getElementById('branchPhone').value = phone;
    document.getElementById('branchStatus').value = status;
    document.getElementById('branchModalTitle').textContent = 'Hariri Tawi';
    document.getElementById('statusField').classList.remove('hidden');
    document.getElementById('branchForm').action = '{{ route("branches.update", "__ID__") }}'.replace('__ID__', id);
    document.getElementById('branchForm').insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
    document.getElementById('branchModal').classList.remove('hidden');
}
</script>
@endsection
