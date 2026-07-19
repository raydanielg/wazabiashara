@extends('layouts.dashboard')

@section('title', 'Bill Gallery')
@section('page_title', 'Bill Gallery')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Bill Gallery
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Keep photos of receipts and bills organized in one place</p>
        </div>
        <button onclick="openBillModal()" class="btn-gold font-bold px-5 py-3 rounded-2xl inline-flex items-center gap-2 text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            Upload Bill
        </button>
    </div>

    <div id="billsGrid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($bills as $bill)
        <div id="billcard-{{ $bill->id }}" class="group relative rounded-2xl border border-gray-200 bg-white shadow-card overflow-hidden">
            <div class="aspect-square bg-gray-50 overflow-hidden">
                <img src="{{ asset('storage/' . $bill->image_path) }}" class="w-full h-full object-cover">
            </div>
            <div class="p-2.5">
                <p class="text-xs font-semibold text-gray-700 truncate">{{ $bill->title ?: 'Bill #' . $bill->id }}</p>
                <p class="text-[10px] text-gray-400">{{ optional($bill->bill_date)->format('d M Y') ?? $bill->created_at->format('d M Y') }}</p>
            </div>
            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                <a href="{{ asset('storage/' . $bill->image_path) }}" target="_blank" class="w-9 h-9 rounded-xl bg-white/90 hover:bg-white text-emerald-700 flex items-center justify-center" title="View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <button onclick="deleteBill({{ $bill->id }})" class="w-9 h-9 rounded-xl bg-white/90 hover:bg-white text-red-500 flex items-center justify-center" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-200">
            <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-gray-400 font-medium text-sm">No bills uploaded yet.</p>
        </div>
        @endforelse
    </div>

    <div>
        {{ $bills->links() }}
    </div>
</div>

{{-- Upload Modal --}}
<div id="billOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeBillModal()"></div>
<div id="billModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800">Upload Bill</h2>
        <button onclick="closeBillModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="billForm" class="flex-1 overflow-y-auto p-5 space-y-4" enctype="multipart/form-data">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Bill Image *</label>
            <input type="file" name="image" id="billImage" accept="image/*" required class="w-full text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Title</label>
            <input type="text" name="title" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Bill Date</label>
            <input type="date" name="bill_date" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all"></textarea>
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button id="billSaveBtn" onclick="submitBill()" class="w-full btn-gold font-bold py-3.5 rounded-2xl text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            Upload
        </button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function openBillModal() {
    document.getElementById('billForm').reset();
    document.getElementById('billOverlay').classList.remove('hidden');
    document.getElementById('billModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}

function closeBillModal() {
    document.getElementById('billOverlay').classList.add('hidden');
    document.getElementById('billModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}

async function submitBill() {
    const form = document.getElementById('billForm');
    if (!document.getElementById('billImage').value) {
        showToast('error', 'Error!', 'Please choose an image to upload.');
        return;
    }
    const formData = new FormData(form);
    const btn = document.getElementById('billSaveBtn');
    btn.disabled = true;
    btn.textContent = 'Uploading...';

    try {
        const res = await fetch('{{ route("bill-gallery.store") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            closeBillModal();
            showToast('success', 'Success!', data.message);
            setTimeout(() => location.reload(), 800);
        } else {
            const errors = data.errors ? Object.values(data.errors).join('\n') : data.message || 'An error occurred.';
            showToast('error', 'Error!', errors);
        }
    } catch (e) {
        showToast('error', 'Network Error', 'Please try again.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Upload';
    }
}

async function deleteBill(id) {
    saConfirm({
        title: 'Delete Bill?',
        text: 'Are you sure you want to delete this bill image?',
        icon: 'danger',
        confirmText: 'Yes, Delete',
        confirmColor: 'red',
        onConfirm: async () => {
            try {
                const res = await fetch('{{ url("/bill-gallery") }}/' + id, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new URLSearchParams({ '_method': 'DELETE' })
                });
                const data = await res.json();
                if (data.success) {
                    const card = document.getElementById('billcard-' + id);
                    if (card) card.remove();
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
    if (e.key === 'Escape') closeBillModal();
});
</script>
@endsection
