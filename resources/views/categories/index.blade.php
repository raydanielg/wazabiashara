@extends('layouts.dashboard')

@section('title', 'Kategoria')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-emerald-700">🏷️ Kategoria</h1>
        <button onclick="document.getElementById('catModal').classList.remove('hidden')" class="btn-gold font-extrabold px-5 py-2.5 rounded-xl">+ Ongeza Kategoria</button>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-emerald-50 text-emerald-700 font-black text-xs uppercase">
                <tr><th class="px-4 py-3 text-left">Jina</th><th class="px-4 py-3 text-left">Parent</th><th class="px-4 py-3 text-center">Bidhaa</th><th class="px-4 py-3 text-center">Kitendo</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($categories as $cat)
                <tr class="hover:bg-emerald-50/30">
                    <td class="px-4 py-3 font-bold text-gray-700">{{ $cat->icon ?? '' }} {{ $cat->name }}</td>
                    <td class="px-4 py-3 text-gray-500 font-semibold">{{ $cat->parent?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">{{ $cat->products_count }}</span></td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="editCat({{ $cat->id }}, '{{ addslashes($cat->name) }}', {{ $cat->parent_id ?? 'null' }})" class="text-emerald-600 font-bold text-xs">Hariri</button>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Futa?')">@csrf @method('DELETE')<button class="text-red-500 font-bold text-xs ml-2">Futa</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400 font-bold">Hakuna kategoria.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $categories->links() }}
    </div>
</div>

<!-- Modal -->
<div id="catModal" class="hidden fixed inset-0 z-50 bg-black/40 grid place-items-center p-4">
    <div class="bg-white rounded-2xl shadow-cardlg p-6 w-full max-w-md">
        <h3 class="font-black text-lg text-emerald-700 mb-4" id="modalTitle">Kategoria Mpya</h3>
        <form id="catForm" method="POST" action="{{ route('categories.store') }}">
            @csrf
            <input type="hidden" name="id" id="catId">
            <div class="space-y-3">
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Jina *</label><input name="name" id="catName" required class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Parent (chagua kama ni sub-kategoria)</label><select name="parent_id" id="catParent" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold bg-white"><option value="">— Hakuna —</option>@foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Icon (emoji)</label><input name="icon" id="catIcon" placeholder="📦" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></div>
                <div><label class="block text-sm font-bold text-gray-600 mb-1">Maelezo</label><textarea name="description" id="catDesc" rows="2" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold"></textarea></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 font-bold text-gray-600">Funga</button>
                <button type="submit" class="flex-1 btn-gold font-black py-2.5 rounded-xl">Hifadhi</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeModal() { document.getElementById('catModal').classList.add('hidden'); document.getElementById('catForm').reset(); document.getElementById('catId').value = ''; document.getElementById('modalTitle').textContent = 'Kategoria Mpya'; document.getElementById('catForm').action = '{{ route("categories.store") }}'; }
function editCat(id, name, parentId) {
    document.getElementById('catId').value = id;
    document.getElementById('catName').value = name;
    document.getElementById('catParent').value = parentId || '';
    document.getElementById('modalTitle').textContent = 'Hariri Kategoria';
    document.getElementById('catForm').action = '{{ route("categories.update", "__ID__") }}'.replace('__ID__', id);
    document.getElementById('catForm').insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
    document.getElementById('catModal').classList.remove('hidden');
}
</script>
@endsection
