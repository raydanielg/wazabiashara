@extends('layouts.dashboard')

@section('title', 'Business Profile')
@section('page_title', 'Business Profile')

@section('content')
<div class="space-y-6">
    {{-- Business Info --}}
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-bold text-gray-900">Business Information</h3>
            <button onclick="editProfile()" class="btn-gold px-3 py-1.5 rounded-lg text-xs font-semibold">Edit</button>
        </div>
        <div class="flex items-start gap-6">
            <div class="w-20 h-20 rounded-xl bg-emerald-50 flex items-center justify-center overflow-hidden shrink-0">
                @if($business->logo)
                <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover">
                @else
                <svg class="w-10 h-10 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                @endif
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Name</p><p class="text-sm font-semibold text-gray-900">{{ $business->name }}</p></div>
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Owner</p><p class="text-sm font-semibold text-gray-900">{{ $business->owner_name ?? '-' }}</p></div>
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Phone</p><p class="text-sm text-gray-700">{{ $business->phone ?? '-' }}</p></div>
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Email</p><p class="text-sm text-gray-700">{{ $business->email ?? '-' }}</p></div>
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Address</p><p class="text-sm text-gray-700">{{ $business->address ?? '-' }}</p></div>
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Tax Number</p><p class="text-sm text-gray-700">{{ $business->tax_number ?? '-' }}</p></div>
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Registration Number</p><p class="text-sm text-gray-700">{{ $business->registration_number ?? '-' }}</p></div>
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Website</p><p class="text-sm text-gray-700">{{ $business->website ?? '-' }}</p></div>
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">VAT Rate</p><p class="text-sm text-gray-700">{{ $business->vat_rate ?? 0 }}%</p></div>
                <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Currency</p><p class="text-sm text-gray-700">{{ $business->currency ?? 'TZS' }}</p></div>
            </div>
        </div>
    </div>

    {{-- Printer Settings --}}
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-bold text-gray-900">Printer Settings</h3>
            <button onclick="editPrinter()" class="btn-gold px-3 py-1.5 rounded-lg text-xs font-semibold">Edit</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Printer Type</p><p class="text-sm text-gray-700">{{ ucfirst($printerSetting->printer_type ?? 'thermal') }}</p></div>
            <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Receipt Size</p><p class="text-sm text-gray-700">{{ $printerSetting->receipt_size ?? '80mm' }}</p></div>
            <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Logo Position</p><p class="text-sm text-gray-700">{{ ucfirst($printerSetting->logo_position ?? 'center') }}</p></div>
            <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Footer Message</p><p class="text-sm text-gray-700">{{ $printerSetting->footer_message ?? '-' }}</p></div>
            <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Show QR</p><p class="text-sm text-gray-700">{{ ($printerSetting->show_qr ?? true) ? 'Yes' : 'No' }}</p></div>
            <div><p class="text-[10px] font-semibold text-gray-500 uppercase">Show Signature</p><p class="text-sm text-gray-700">{{ ($printerSetting->show_signature ?? false) ? 'Yes' : 'No' }}</p></div>
        </div>
    </div>

    {{-- Accounts Summary --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Business Accounts</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @forelse($accounts as $account)
            <div class="border rounded-lg p-3">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $account->type === 'cash' ? 'bg-emerald-50 text-emerald-700' : ($account->type === 'bank' ? 'bg-sky-50 text-sky-700' : 'bg-violet-50 text-violet-700') }}">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</span>
                <p class="text-sm font-semibold text-gray-900 mt-2">{{ $account->name }}</p>
                <p class="text-base font-bold text-emerald-600">TZS {{ number_format($account->current_balance, 0) }}</p>
            </div>
            @empty
            <div class="col-span-full text-center py-6 text-sm text-gray-400">No accounts found. <a href="{{ route('cash-flow.index') }}" class="text-emerald-600">Add here</a></div>
            @endforelse
        </div>
    </div>
</div>

{{-- Profile Edit Modal --}}
<div id="profileModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeProfileModal()"></div>
    <div class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-xl overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-5 py-4 flex items-center justify-between z-10">
            <h3 class="text-sm font-bold text-gray-900">Edit Profile</h3>
            <button onclick="closeProfileModal()" class="p-1 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="profileForm" class="p-5 space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Business Name</label><input type="text" name="name" value="{{ $business->name }}" class="w-full rounded-lg border-gray-200 text-sm" required></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Owner</label><input type="text" name="owner_name" value="{{ $business->owner_name ?? '' }}" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Phone</label><input type="text" name="phone" value="{{ $business->phone ?? '' }}" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Email</label><input type="email" name="email" value="{{ $business->email ?? '' }}" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Address</label><textarea name="address" rows="2" class="w-full rounded-lg border-gray-200 text-sm">{{ $business->address ?? '' }}</textarea></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Tax Number</label><input type="text" name="tax_number" value="{{ $business->tax_number ?? '' }}" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Registration Number</label><input type="text" name="registration_number" value="{{ $business->registration_number ?? '' }}" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Website</label><input type="text" name="website" value="{{ $business->website ?? '' }}" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">VAT Rate (%)</label><input type="number" name="vat_rate" step="0.01" value="{{ $business->vat_rate ?? 0 }}" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Currency</label><input type="text" name="currency" value="{{ $business->currency ?? 'TZS' }}" class="w-full rounded-lg border-gray-200 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Logo</label><input type="file" name="logo" accept="image/*" class="w-full text-sm"></div>
            <div class="flex gap-2 pt-2"><button type="submit" class="btn-gold flex-1 py-2.5 rounded-lg text-sm font-semibold">Save</button><button type="button" onclick="closeProfileModal()" class="px-4 py-2.5 rounded-lg border text-sm font-semibold text-gray-600">Close</button></div>
        </form>
    </div>
</div>

{{-- Printer Edit Modal --}}
<div id="printerModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closePrinterModal()"></div>
    <div class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-xl overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-5 py-4 flex items-center justify-between z-10">
            <h3 class="text-sm font-bold text-gray-900">Printer Settings</h3>
            <button onclick="closePrinterModal()" class="p-1 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="printerForm" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Printer Type</label><select name="printer_type" class="w-full rounded-lg border-gray-200 text-sm"><option value="thermal" {{ ($printerSetting->printer_type ?? 'thermal') === 'thermal' ? 'selected' : '' }}>Thermal</option><option value="bluetooth" {{ ($printerSetting->printer_type ?? '') === 'bluetooth' ? 'selected' : '' }}>Bluetooth</option><option value="network" {{ ($printerSetting->printer_type ?? '') === 'network' ? 'selected' : '' }}>Network</option><option value="a4" {{ ($printerSetting->printer_type ?? '') === 'a4' ? 'selected' : '' }}>A4</option></select></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Receipt Size</label><select name="receipt_size" class="w-full rounded-lg border-gray-200 text-sm"><option value="58mm" {{ ($printerSetting->receipt_size ?? '') === '58mm' ? 'selected' : '' }}>58mm</option><option value="80mm" {{ ($printerSetting->receipt_size ?? '80mm') === '80mm' ? 'selected' : '' }}>80mm</option></select></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Logo Position</label><select name="logo_position" class="w-full rounded-lg border-gray-200 text-sm"><option value="center" {{ ($printerSetting->logo_position ?? 'center') === 'center' ? 'selected' : '' }}>Center</option><option value="left" {{ ($printerSetting->logo_position ?? '') === 'left' ? 'selected' : '' }}>Left</option><option value="right" {{ ($printerSetting->logo_position ?? '') === 'right' ? 'selected' : '' }}>Right</option></select></div>
            <div><label class="text-xs font-semibold text-gray-700 mb-1 block">Footer Message</label><textarea name="footer_message" rows="2" class="w-full rounded-lg border-gray-200 text-sm">{{ $printerSetting->footer_message ?? '' }}</textarea></div>
            <div class="space-y-2">
                <label class="flex items-center gap-2"><input type="checkbox" name="show_qr" {{ ($printerSetting->show_qr ?? true) ? 'checked' : '' }} class="rounded text-emerald-600"><span class="text-xs text-gray-700">Show QR Code</span></label>
                <label class="flex items-center gap-2"><input type="checkbox" name="show_signature" {{ ($printerSetting->show_signature ?? false) ? 'checked' : '' }} class="rounded text-emerald-600"><span class="text-xs text-gray-700">Show Signature</span></label>
                <label class="flex items-center gap-2"><input type="checkbox" name="show_stamp" {{ ($printerSetting->show_stamp ?? false) ? 'checked' : '' }} class="rounded text-emerald-600"><span class="text-xs text-gray-700">Show Stamp</span></label>
            </div>
            <div class="flex gap-2 pt-2"><button type="submit" class="btn-gold flex-1 py-2.5 rounded-lg text-sm font-semibold">Save</button><button type="button" onclick="closePrinterModal()" class="px-4 py-2.5 rounded-lg border text-sm font-semibold text-gray-600">Close</button></div>
        </form>
    </div>
</div>

<script>
function editProfile() { document.getElementById('profileModal').classList.remove('hidden'); }
function closeProfileModal() { document.getElementById('profileModal').classList.add('hidden'); }
function editPrinter() { document.getElementById('printerModal').classList.remove('hidden'); }
function closePrinterModal() { document.getElementById('printerModal').classList.add('hidden'); }

document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('_method', 'PUT');
    try {
        const res = await fetch('/business/profile', { method: 'POST', headers: { 'X-CSRF-TOKEN': formData.get('_token'), 'Accept': 'application/json' }, body: formData });
        const result = await res.json();
        if (result.success) { showToast('success', 'Success', result.message); setTimeout(() => location.reload(), 800); }
        else { showToast('error', 'Error', result.message || 'Error'); }
    } catch (err) { showToast('error', 'Error', 'Error'); }
});

document.getElementById('printerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('_method', 'PUT');
    try {
        const res = await fetch('/business/printer-settings', { method: 'POST', headers: { 'X-CSRF-TOKEN': formData.get('_token'), 'Accept': 'application/json' }, body: formData });
        const result = await res.json();
        if (result.success) { showToast('success', 'Success', result.message); closePrinterModal(); setTimeout(() => location.reload(), 800); }
        else { showToast('error', 'Error', result.message || 'Error'); }
    } catch (err) { showToast('error', 'Error', 'Error'); }
});
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeProfileModal(); closePrinterModal(); } });
</script>
@endsection
