@extends('layouts.dashboard')

@section('title', 'Greeting Cards')
@section('page_title', 'Greeting Cards')

@section('content')
<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border p-4"><div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center mb-2"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v1a1 1 0 001 1h3a1 1 0 001-1V3m0 0V2a1 1 0 00-1-1H5a1 1 0 00-1 1v1m4 0h.01M7 3h.01M20 3v1a1 1 0 01-1 1h-3a1 1 0 01-1-1V3m0 0V2a1 1 0 011-1h5a1 1 0 011 1v1m-4 0h.01M17 3h.01M7 8h10M7 12h10M7 16h6"/></svg></div><p class="text-lg font-bold text-gray-900">{{ number_format($totalCards) }}</p><p class="text-[10px] text-gray-500">Total Cards</p></div>
        <div class="bg-white rounded-xl border p-4"><div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center mb-2"><svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg></div><p class="text-lg font-bold text-gray-900">{{ number_format($birthdayCards) }}</p><p class="text-[10px] text-gray-500">Birthday</p></div>
        <div class="bg-white rounded-xl border p-4"><div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center mb-2"><svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg></div><p class="text-lg font-bold text-gray-900">{{ number_format($holidayCards) }}</p><p class="text-[10px] text-gray-500">Holiday</p></div>
        <div class="bg-white rounded-xl border p-4"><div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center mb-2"><svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div><p class="text-lg font-bold text-gray-900">{{ number_format($appreciationCards) }}</p><p class="text-[10px] text-gray-500">Appreciation</p></div>
    </div>

    <div class="flex items-center justify-between">
        <div><h2 class="text-lg font-bold text-gray-900">Greeting Cards</h2><p class="text-xs text-gray-500">Send greeting cards to your customers</p></div>
        <button onclick="openGreetingModal()" class="btn-gold px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Create Card</button>
    </div>

    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($cards as $card)
        <div class="bg-white rounded-xl border overflow-hidden hover:shadow-lg transition-shadow">
            @if($card->image)<img src="{{ asset('storage/' . $card->image) }}" class="w-full h-40 object-cover">@else<div class="w-full h-40 bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center"><svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v1a1 1 0 001 1h3a1 1 0 001-1V3m0 0V2a1 1 0 00-1-1H5a1 1 0 00-1 1v1m4 0h.01M7 3h.01M20 3v1a1 1 0 01-1 1h-3a1 1 0 01-1-1V3m0 0V2a1 1 0 011-1h5a1 1 0 011 1v1m-4 0h.01M17 3h.01M7 8h10M7 12h10M7 16h6"/></svg></div>@endif
            <div class="p-4">
                <div class="flex items-center justify-between mb-2"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $card->type === 'birthday' ? 'bg-amber-50 text-amber-700' : ($card->type === 'holiday' ? 'bg-violet-50 text-violet-700' : 'bg-sky-50 text-sky-700') }}">{{ ucfirst($card->type) }}</span><span class="text-[10px] text-gray-400">{{ $card->created_at->format('d/m/Y') }}</span></div>
                <p class="text-sm font-bold text-gray-900">{{ $card->title }}</p>
                @if($card->customer)<p class="text-xs text-gray-500 mt-1">To: {{ $card->customer->name }}</p>@endif
                <p class="text-xs text-gray-600 mt-2 line-clamp-2">{{ $card->message }}</p>
                <div class="flex items-center gap-2 mt-3 pt-3 border-t">
                    <a href="{{ route('cards.greeting.share', $card->share_token) }}" target="_blank" class="flex-1 text-center px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-semibold hover:bg-emerald-100">Share</a>
                    <button onclick="deleteGreeting({{ $card->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </div>
            </div>
        </div>
        @empty<div class="col-span-full text-center py-12 text-sm text-gray-400">No cards yet. Click "Create Card" to get started.</div>@endforelse
    </div>
    <div class="px-4 py-3">{{ $cards->links() }}</div>
</div>

<div id="greetingModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeGreetingModal()"></div>
    <div class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-2xl overflow-y-auto">
        {{-- Modal Header --}}
        <div class="sticky top-0 bg-gradient-to-r from-emerald-600 to-emerald-800 px-6 py-5 flex items-center justify-between z-10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v1a1 1 0 001 1h3a1 1 0 001-1V3m0 0V2a1 1 0 00-1-1H5a1 1 0 00-1 1v1m4 0h.01M7 3h.01M20 3v1a1 1 0 01-1 1h-3a1 1 0 01-1-1V3m0 0V2a1 1 0 011-1h5a1 1 0 011 1v1m-4 0h.01M17 3h.01M7 8h10M7 12h10M7 16h6"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Create Greeting Card</h3>
                    <p class="text-[10px] text-emerald-200">Fill in card details</p>
                </div>
            </div>
            <button onclick="closeGreetingModal()" class="p-1.5 rounded-lg hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        {{-- Modal Body --}}
        <form id="greetingForm" class="p-6 space-y-5" enctype="multipart/form-data">
            @csrf
            {{-- Type --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Card Type</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <select name="type" required class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm appearance-none bg-white">
                        <option value="birthday">🎂 Birthday</option>
                        <option value="holiday">🎉 Holiday</option>
                        <option value="appreciation">💝 Appreciation</option>
                    </select>
                </div>
            </div>
            {{-- Title --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Card Title</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <input type="text" name="title" required placeholder="e.g. Happy Birthday!" class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                </div>
            </div>
            {{-- Message --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message</label>
                <div class="relative">
                    <div class="absolute top-3 left-0 pl-3.5 flex items-start pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <textarea name="message" rows="4" placeholder="Write your message here..." class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm resize-none"></textarea>
                </div>
            </div>
            {{-- Customer --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Customer</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <select name="customer_id" class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm appearance-none bg-white">
                        <option value="">-- Select Customer (Optional) --</option>
                        @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            {{-- Image --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Card Image</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <input type="file" name="image" accept="image/*" class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
            </div>
            {{-- Buttons --}}
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 hover:from-emerald-700 hover:to-emerald-900 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Card
                </button>
                <button type="button" onclick="closeGreetingModal()" class="px-5 py-3 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">Close</button>
            </div>
        </form>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
function openGreetingModal() { document.getElementById('greetingModal').classList.remove('hidden'); }
function closeGreetingModal() { document.getElementById('greetingModal').classList.add('hidden'); }

document.getElementById('greetingForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    try {
        const res = await fetch('/cards/greeting', { method: 'POST', headers: { 'X-CSRF-TOKEN': formData.get('_token'), 'Accept': 'application/json' }, body: formData });
        const result = await res.json();
        if (result.success) { showToast('success', 'Success', result.message); closeGreetingModal(); setTimeout(() => location.reload(), 800); }
    } catch (err) { showToast('error', 'Error', 'An error occurred'); }
});

async function deleteGreeting(id) {
    saConfirm({ title: 'Delete Card?', text: 'Are you sure you want to delete this card?', icon: 'danger', confirmText: 'Yes, Delete', confirmColor: 'red', onConfirm: async () => { const res = await fetch(`/cards/greeting/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } }); const result = await res.json(); if (result.success) { showToast('success', 'Success', result.message); setTimeout(() => location.reload(), 800); } } });
}
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeGreetingModal(); });
</script>
@endsection
