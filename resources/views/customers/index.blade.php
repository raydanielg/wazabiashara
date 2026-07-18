@extends('layouts.dashboard')

@section('title', 'Wateja')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-emerald-700">👥 Wateja & Madeni</h1>
        <button onclick="document.getElementById('custModal').classList.remove('hidden')" class="btn-gold font-extrabold px-5 py-2.5 rounded-xl">+ Mteja Mpya</button>
    </div>

    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tafuta mteja..." class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm">
        <button class="px-5 py-2.5 rounded-xl bg-emerald-500 text-white font-bold text-sm">Tafuta</button>
    </form>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                    <tr><th class="px-4 py-3 text-left">Jina</th><th class="px-4 py-3 text-left">Simu</th><th class="px-4 py-3 text-right">Credit Limit</th><th class="px-4 py-3 text-right">Deni Sasa</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Kitendo</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $c)
                    <tr class="hover:bg-emerald-50/30">
                        <td class="px-4 py-3 font-bold text-gray-700">{{ $c->name }}</td>
                        <td class="px-4 py-3 text-gray-500 font-semibold">{{ $c->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-600">TZS {{ number_format($c->credit_limit, 0) }}</td>
                        <td class="px-4 py-3 text-right font-black {{ $c->current_debt > 0 ? 'text-red-600' : 'text-emerald-600' }}">TZS {{ number_format($c->current_debt, 0) }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $c->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $c->status }}</span></td>
                        <td class="px-4 py-3 text-center">
                            @if($c->current_debt > 0)<a href="{{ route('customers.debts', $c) }}" class="text-red-600 font-bold text-xs">Madeni</a>@endif
                            <button onclick="editCust({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ $c->phone ?? '' }}', '{{ $c->email ?? '' }}', '{{ addslashes($c->address ?? '') }}', {{ $c->credit_limit }}, '{{ $c->status }}')" class="text-emerald-600 font-bold text-xs {{ $c->current_debt > 0 ? 'ml-2' : '' }}">Hariri</button>
                            <form method="POST" action="{{ route('customers.destroy', $c) }}" class="inline" onsubmit="return confirm('Futa?')">@csrf @method('DELETE')<button class="text-red-500 font-bold text-xs ml-2">Futa</button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna wateja.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $customers->links() }}
    </div>
</div>

<div id="custModal" class="hidden fixed inset-0 z-50 bg-black/40 grid place-items-center p-4">
    <div class="bg-white rounded-2xl shadow-cardlg p-6 w-full max-w-md">
        <h3 class="font-black text-lg text-emerald-700 mb-4" id="custModalTitle">Mteja Mpya</h3>
        <form id="custForm" method="POST" action="{{ route('customers.store') }}">
            @csrf <input type="hidden" name="id" id="custId">
            <div class="space-y-3">
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Jina *</label><input name="name" id="custName" required class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Simu</label><input name="phone" id="custPhone" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Email</label><input name="email" id="custEmail" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Mahali</label><textarea name="address" id="custAddress" rows="2" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></textarea></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Credit Limit (TZS)</label><input type="number" name="credit_limit" id="custCredit" min="0" value="0" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div id="custStatusField" class="hidden"><label class="block text-sm font-bold text-gray-600 mb-1">Status</label><select name="status" id="custStatus" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white"><option value="active">Hai</option><option value="inactive">Imezimwa</option></select></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="closeCustModal()" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 font-bold text-gray-600">Funga</button>
                <button type="submit" class="flex-1 btn-gold font-black py-2.5 rounded-xl">Hifadhi</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeCustModal() { document.getElementById('custModal').classList.add('hidden'); document.getElementById('custForm').reset(); document.getElementById('custId').value=''; document.getElementById('custModalTitle').textContent='Mteja Mpya'; document.getElementById('custStatusField').classList.add('hidden'); document.getElementById('custForm').action='{{ route("customers.store") }}'; document.getElementById('custForm').querySelector('input[name="_method"]')?.remove(); }
function editCust(id,name,phone,email,address,credit,status) {
    document.getElementById('custId').value=id; document.getElementById('custName').value=name; document.getElementById('custPhone').value=phone; document.getElementById('custEmail').value=email; document.getElementById('custAddress').value=address; document.getElementById('custCredit').value=credit; document.getElementById('custStatus').value=status;
    document.getElementById('custModalTitle').textContent='Hariri Mteja'; document.getElementById('custStatusField').classList.remove('hidden');
    document.getElementById('custForm').action='{{ route("customers.update", "__ID__") }}'.replace('__ID__', id);
    document.getElementById('custForm').insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
    document.getElementById('custModal').classList.remove('hidden');
}
</script>
@endsection
