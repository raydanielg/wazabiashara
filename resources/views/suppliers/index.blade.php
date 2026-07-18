@extends('layouts.dashboard')

@section('title', 'Wasambazaji')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-emerald-700">🚚 Wasambazaji</h1>
            <p class="text-sm text-gray-500 font-semibold">Simamia wasambazaji na manunuzi</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('suppliers.purchases') }}" class="px-5 py-2.5 rounded-xl border-2 border-emerald-200 text-emerald-600 font-bold text-sm hover:bg-emerald-50">📋 Manunuzi</a>
            <button onclick="document.getElementById('supModal').classList.remove('hidden')" class="btn-gold font-extrabold px-5 py-2.5 rounded-xl">+ Msambazaji</button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                    <tr><th class="px-4 py-3 text-left">Jina</th><th class="px-4 py-3 text-left">Simu</th><th class="px-4 py-3 text-right">Salio la Deni</th><th class="px-4 py-3 text-center">Manunuzi</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Kitendo</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($suppliers as $s)
                    <tr class="hover:bg-emerald-50/30">
                        <td class="px-4 py-3 font-bold text-gray-700">{{ $s->name }}</td>
                        <td class="px-4 py-3 text-gray-500 font-semibold">{{ $s->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-black {{ $s->balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">TZS {{ number_format($s->balance, 0) }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">{{ $s->purchases_count }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $s->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $s->status }}</span></td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="editSup({{ $s->id }}, '{{ addslashes($s->name) }}', '{{ $s->phone ?? '' }}', '{{ $s->email ?? '' }}', '{{ addslashes($s->address ?? '') }}', '{{ $s->status }}')" class="text-emerald-600 font-bold text-xs">Hariri</button>
                            <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="inline" onsubmit="return confirm('Futa?')">@csrf @method('DELETE')<button class="text-red-500 font-bold text-xs ml-2">Futa</button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna wasambazaji.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $suppliers->links() }}
    </div>
</div>

<div id="supModal" class="hidden fixed inset-0 z-50 bg-black/40 grid place-items-center p-4">
    <div class="bg-white rounded-2xl shadow-cardlg p-6 w-full max-w-md">
        <h3 class="font-black text-lg text-emerald-700 mb-4" id="supModalTitle">Msambazaji Mpya</h3>
        <form id="supForm" method="POST" action="{{ route('suppliers.store') }}">
            @csrf <input type="hidden" name="id" id="supId">
            <div class="space-y-3">
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Jina *</label><input name="name" id="supName" required class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Simu</label><input name="phone" id="supPhone" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Email</label><input name="email" id="supEmail" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Mahali</label><textarea name="address" id="supAddress" rows="2" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></textarea></div>
                <div id="supStatusField" class="hidden"><label class="block text-sm font-bold text-gray-600 mb-1">Status</label><select name="status" id="supStatus" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white"><option value="active">Hai</option><option value="inactive">Imezimwa</option></select></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="closeSupModal()" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 font-bold text-gray-600">Funga</button>
                <button type="submit" class="flex-1 btn-gold font-black py-2.5 rounded-xl">Hifadhi</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeSupModal() { document.getElementById('supModal').classList.add('hidden'); document.getElementById('supForm').reset(); document.getElementById('supId').value=''; document.getElementById('supModalTitle').textContent='Msambazaji Mpya'; document.getElementById('supStatusField').classList.add('hidden'); document.getElementById('supForm').action='{{ route("suppliers.store") }}'; document.getElementById('supForm').querySelector('input[name="_method"]')?.remove(); }
function editSup(id,name,phone,email,address,status) {
    document.getElementById('supId').value=id; document.getElementById('supName').value=name; document.getElementById('supPhone').value=phone; document.getElementById('supEmail').value=email; document.getElementById('supAddress').value=address; document.getElementById('supStatus').value=status;
    document.getElementById('supModalTitle').textContent='Hariri Msambazaji'; document.getElementById('supStatusField').classList.remove('hidden');
    document.getElementById('supForm').action='{{ route("suppliers.update", "__ID__") }}'.replace('__ID__', id);
    document.getElementById('supForm').insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
    document.getElementById('supModal').classList.remove('hidden');
}
</script>
@endsection
