@extends('layouts.dashboard')

@section('title', 'Notebook')
@section('page_title', 'Notebook')

@section('content')
@php
$noteColors = [
    'gold' => 'bg-gold-50 border-gold-200',
    'emerald' => 'bg-emerald-50 border-emerald-200',
    'rose' => 'bg-rose-50 border-rose-200',
    'sky' => 'bg-sky-50 border-sky-200',
    'violet' => 'bg-violet-50 border-violet-200',
    'gray' => 'bg-gray-50 border-gray-200',
];
@endphp
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Notebook
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Jot down quick notes and reminders for your business</p>
        </div>
        <button onclick="openNoteModal()" class="btn-gold font-bold px-5 py-3 rounded-2xl inline-flex items-center gap-2 text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Note
        </button>
    </div>

    <div id="notesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($notes as $note)
        <div id="noterow-{{ $note->id }}" class="rounded-2xl border {{ $noteColors[$note->color] ?? $noteColors['gold'] }} p-4 shadow-card flex flex-col gap-2 relative">
            @if($note->pinned)
            <span class="absolute top-3 right-3 text-gold-500" title="Pinned">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5.5l2.7 1.35a1 1 0 01-.4 1.914L12 10.5V17a1 1 0 11-2 0v-6.5l-2.3-.264a1 1 0 01-.4-1.914L10 7.5V2a1 1 0 011.3-.954z"/></svg>
            </span>
            @endif
            <h3 class="font-bold text-gray-800 text-sm pr-6 truncate">{{ $note->title ?: 'Untitled' }}</h3>
            <p class="text-xs text-gray-600 line-clamp-2 flex-1 whitespace-pre-line">{{ $note->content }}</p>
            <div class="flex items-center justify-between pt-2 border-t border-black/5">
                <span class="text-[10px] text-gray-400">{{ $note->updated_at->diffForHumans() }}</span>
                <div class="flex items-center gap-1.5">
                    <button onclick="togglePin({{ $note->id }}, {{ $note->pinned ? 'false' : 'true' }})" class="w-7 h-7 rounded-lg hover:bg-white/70 text-gold-500 transition-all flex items-center justify-center" title="{{ $note->pinned ? 'Unpin' : 'Pin' }}">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5.5l2.7 1.35a1 1 0 01-.4 1.914L12 10.5V17a1 1 0 11-2 0v-6.5l-2.3-.264a1 1 0 01-.4-1.914L10 7.5V2a1 1 0 011.3-.954z"/></svg>
                    </button>
                    <button onclick='editNote({{ $note->id }}, {!! json_encode($note->title) !!}, {!! json_encode($note->content) !!}, {!! json_encode($note->color) !!})' class="w-7 h-7 rounded-lg hover:bg-white/70 text-emerald-600 transition-all flex items-center justify-center" title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="deleteNote({{ $note->id }})" class="w-7 h-7 rounded-lg hover:bg-white/70 text-red-500 transition-all flex items-center justify-center" title="Delete">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-200">
            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <p class="text-gray-400 font-medium text-sm">No notes yet. Add your first note!</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Note Modal --}}
<div id="noteOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeNoteModal()"></div>
<div id="noteModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 id="noteModalTitle" class="text-lg font-bold text-gray-800">Add Note</h2>
        <button onclick="closeNoteModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="noteForm" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf
        <input type="hidden" id="noteMethod" name="_method" value="POST">
        <input type="hidden" id="noteId" value="">

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Title</label>
            <input type="text" name="title" id="noteTitle" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Content</label>
            <textarea name="content" id="noteContent" rows="6" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all"></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Color</label>
            <div class="flex gap-2" id="noteColorPicker">
                @foreach(array_keys($noteColors) as $c)
                <button type="button" data-color="{{ $c }}" onclick="selectColor('{{ $c }}')" class="note-color-swatch w-8 h-8 rounded-full border-2 border-transparent {{ $noteColors[$c] }}"></button>
                @endforeach
            </div>
            <input type="hidden" name="color" id="noteColor" value="gold">
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button id="noteSaveBtn" onclick="submitNote()" class="w-full btn-gold font-bold py-3.5 rounded-2xl text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            Save Note
        </button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function selectColor(color) {
    document.getElementById('noteColor').value = color;
    document.querySelectorAll('.note-color-swatch').forEach(btn => {
        btn.classList.toggle('border-gray-800', btn.dataset.color === color);
    });
}

function openNoteModal() {
    document.getElementById('noteModalTitle').textContent = 'Add Note';
    document.getElementById('noteMethod').value = 'POST';
    document.getElementById('noteId').value = '';
    document.getElementById('noteForm').reset();
    selectColor('gold');
    document.getElementById('noteSaveBtn').textContent = 'Save Note';
    showNoteModal();
}

function editNote(id, title, content, color) {
    document.getElementById('noteModalTitle').textContent = 'Edit Note';
    document.getElementById('noteMethod').value = 'PUT';
    document.getElementById('noteId').value = id;
    document.getElementById('noteTitle').value = title || '';
    document.getElementById('noteContent').value = content || '';
    selectColor(color || 'gold');
    document.getElementById('noteSaveBtn').textContent = 'Save Changes';
    showNoteModal();
}

function showNoteModal() {
    document.getElementById('noteOverlay').classList.remove('hidden');
    document.getElementById('noteModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}

function closeNoteModal() {
    document.getElementById('noteOverlay').classList.add('hidden');
    document.getElementById('noteModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}

async function submitNote() {
    const form = document.getElementById('noteForm');
    const formData = new FormData(form);
    const id = document.getElementById('noteId').value;
    const isEdit = document.getElementById('noteMethod').value === 'PUT';
    const url = isEdit ? '{{ url("/notebook") }}/' + id : '{{ route("notebook.store") }}';

    if (isEdit) formData.append('_method', 'PUT');

    const btn = document.getElementById('noteSaveBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            closeNoteModal();
            showToast('success', 'Success!', data.message);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('error', 'Error!', data.message || 'An error occurred.');
        }
    } catch (e) {
        showToast('error', 'Network Error', 'Please try again.');
    } finally {
        btn.disabled = false;
        btn.textContent = isEdit ? 'Save Changes' : 'Save Note';
    }
}

async function togglePin(id, pinned) {
    try {
        const res = await fetch('{{ url("/notebook") }}/' + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: new URLSearchParams({ '_method': 'PUT', 'pinned': pinned ? '1' : '0' })
        });
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            showToast('error', 'Error!', data.message || 'Failed to update.');
        }
    } catch (e) {
        showToast('error', 'Network Error', 'Please try again.');
    }
}

async function deleteNote(id) {
    saConfirm({
        title: 'Delete Note?',
        text: 'Are you sure you want to delete this note?',
        icon: 'danger',
        confirmText: 'Yes, Delete',
        confirmColor: 'red',
        onConfirm: async () => {
            try {
                const res = await fetch('{{ url("/notebook") }}/' + id, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new URLSearchParams({ '_method': 'DELETE' })
                });
                const data = await res.json();
                if (data.success) {
                    const row = document.getElementById('noterow-' + id);
                    if (row) row.remove();
                    showToast('success', 'Deleted!', data.message);
                } else {
                    showToast('error', 'Error!', data.message || 'Failed to delete.');
                }
            } catch (e) {
                showToast('error', 'Network Error', 'Please try again.');
            }
        }
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeNoteModal();
});
</script>
@endsection
