@extends('layouts.dashboard')

@section('title', 'Vikumbusho')
@section('page_title', 'Reminders')

@section('content')
<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border p-4">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center mb-2"><svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></div>
            <p class="text-lg font-bold text-gray-900">{{ number_format($pendingCount) }}</p><p class="text-[10px] text-gray-500">Vikumbusho Visivyofanyika</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center mb-2"><svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg></div>
            <p class="text-lg font-bold text-gray-900">{{ number_format($debtCount) }}</p><p class="text-[10px] text-gray-500">Madeni Yaliyosalia</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center mb-2"><svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
            <p class="text-lg font-bold text-gray-900">{{ number_format($stockCount) }}</p><p class="text-[10px] text-gray-500">Stoo Iliyochache</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center mb-2"><svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
            <p class="text-lg font-bold text-gray-900">{{ number_format($expiryCount) }}</p><p class="text-[10px] text-gray-500">Bidhaa Zinazokwisha</p>
        </div>
    </div>

    {{-- Alerts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Overdue Debts --}}
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Madeni Yaliyosalia</h3></div>
            <div class="p-5 space-y-2">
                @forelse($overdueDebts as $debt)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                    <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                    <div class="flex-1 min-w-0"><p class="text-xs font-semibold text-gray-900 truncate">{{ $debt->customer?->name }}</p><p class="text-[10px] text-gray-400">Hadi: {{ $debt->due_date?->format('d/m/Y') }}</p></div>
                    <span class="text-xs font-bold text-red-600 shrink-0">TZS {{ number_format($debt->balance, 0) }}</span>
                </div>
                @empty<p class="text-sm text-gray-400 text-center py-4">Hakuna madeni.</p>@endforelse
            </div>
        </div>

        {{-- Low Stock --}}
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Stoo Iliyochache</h3></div>
            <div class="p-5 space-y-2">
                @forelse($lowStockItems as $item)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <div class="flex-1 min-w-0"><p class="text-xs font-semibold text-gray-900 truncate">{{ $item->product->name }}</p><p class="text-[10px] text-gray-400">{{ $item->branch->name }}</p></div>
                    <span class="text-xs font-bold text-amber-600 shrink-0">{{ $item->qty }} {{ $item->product->unit }}</span>
                </div>
                @empty<p class="text-sm text-gray-400 text-center py-4">Stoo yote nzuri!</p>@endforelse
            </div>
        </div>

        {{-- Expiring Products --}}
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Bidhaa Zinazokwisha</h3></div>
            <div class="p-5 space-y-2">
                @forelse($expiringProducts as $product)
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                    <span class="w-2 h-2 rounded-full bg-violet-500 shrink-0"></span>
                    <div class="flex-1 min-w-0"><p class="text-xs font-semibold text-gray-900 truncate">{{ $product->name }}</p><p class="text-[10px] text-gray-400">Inakwisha: {{ $product->expiry_date->format('d/m/Y') }}</p></div>
                </div>
                @empty<p class="text-sm text-gray-400 text-center py-4">Hakuna bidhaa zinazokwisha.</p>@endforelse
            </div>
        </div>
    </div>

    {{-- Add Reminder + List --}}
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-900">Vikumbusho</h2>
        <button onclick="openReminderModal()" class="btn-gold px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Ongeza Kikumbusho</button>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b bg-gray-50">
                    <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Kikumbusho</th>
                    <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Aina</th>
                    <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Njia</th>
                    <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Muda</th>
                    <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Hali</th>
                    <th class="text-center px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Vitendo</th>
                </tr></thead>
                <tbody>
                    @forelse($reminders as $reminder)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">{{ $reminder->title }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ ucfirst($reminder->type) }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-semibold">{{ strtoupper($reminder->channel) }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $reminder->remind_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $reminder->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ ucfirst($reminder->status) }}</span></td>
                        <td class="px-4 py-3 text-center"><button onclick="deleteReminder({{ $reminder->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
                    </tr>
                    @empty<tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">Hakuna vikumbusho.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $reminders->links() }}</div>
    </div>
</div>

<div id="reminderModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeReminderModal()"></div>
    <div class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-xl overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-5 py-4 flex items-center justify-between z-10"><h3 class="text-sm font-bold text-gray-900">Ongeza Kikumbusho</h3><button onclick="closeReminderModal()" class="p-1 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        <form id="reminderForm" class="p-5 space-y-4">
            @csrf
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Aina</label><select name="type" class="w-full rounded-lg border-gray-200 text-sm" required><option value="debt">Deni</option><option value="stock">Stoo</option><option value="expiry">Muda wa Bidhaa</option><option value="invoice">Invoice</option><option value="payment">Malipo</option><option value="other">Mengineyo</option></select></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Kichwa</label><input type="text" name="title" class="w-full rounded-lg border-gray-200 text-sm" required></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Ujumbe</label><textarea name="message" rows="3" class="w-full rounded-lg border-gray-200 text-sm"></textarea></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Njia ya Kutuma</label><select name="channel" class="w-full rounded-lg border-gray-200 text-sm" required><option value="app">App</option><option value="sms">SMS</option><option value="whatsapp">WhatsApp</option><option value="email">Email</option></select></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Muda</label><input type="datetime-local" name="remind_at" class="w-full rounded-lg border-gray-200 text-sm" required></div>
            <div class="flex gap-2 pt-2"><button type="submit" class="btn-gold flex-1 py-2.5 rounded-lg text-sm font-semibold">Hifadhi</button><button type="button" onclick="closeReminderModal()" class="px-4 py-2.5 rounded-lg border text-sm font-semibold text-gray-600">Funga</button></div>
        </form>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
function openReminderModal() { document.getElementById('reminderModal').classList.remove('hidden'); }
function closeReminderModal() { document.getElementById('reminderModal').classList.add('hidden'); }

document.getElementById('reminderForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target)); delete data._token;
    try {
        const res = await fetch('/reminders', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(data) });
        const result = await res.json();
        if (result.success) { Toastify({ text: result.message, duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#024938' } }).showToast(); closeReminderModal(); setTimeout(() => location.reload(), 800); }
    } catch (err) { Toastify({ text: 'Hitilafu', duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#ef4444' } }).showToast(); }
});

async function deleteReminder(id) {
    Swal.fire({ title: 'Futa Kikumbusho?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ndiyo, Futa', cancelButtonText: 'Ghairi' }).then(async (r) => {
        if (r.isConfirmed) { const res = await fetch(`/reminders/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } }); const result = await res.json(); if (result.success) { Toastify({ text: result.message, duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#024938' } }).showToast(); setTimeout(() => location.reload(), 800); } }
    });
}
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeReminderModal(); });
</script>
@endsection
