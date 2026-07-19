@extends('layouts.dashboard')

@section('title', 'Invoice Print Settings')
@section('page_title', 'Invoice Print Settings')

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m8 0v4H7v-4m8 0H7m8-9V3H7v5"/></svg>
            Invoice Print Settings
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Control how receipts and invoices are printed for your customers</p>
    </div>

    <form id="invoiceSettingsForm" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Default Print Type --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-900">Default Print Type</h3>
            <div class="flex gap-3">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="print_type" value="regular" class="peer sr-only" {{ ($printerSetting->print_type ?? 'thermal') === 'regular' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-4 text-center transition-all">
                        <svg class="w-6 h-6 mx-auto mb-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-xs font-semibold text-gray-700">Regular (A4)</span>
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="print_type" value="thermal" class="peer sr-only" {{ ($printerSetting->print_type ?? 'thermal') === 'thermal' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-4 text-center transition-all">
                        <svg class="w-6 h-6 mx-auto mb-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m8 0v4H7v-4m8 0H7m8-9V3H7v5"/></svg>
                        <span class="text-xs font-semibold text-gray-700">Thermal Receipt</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Printer & Page Setup --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-900">Printer & Page Setup</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Page Size</label>
                    <select name="receipt_size" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white">
                        <option value="58mm" {{ ($printerSetting->receipt_size ?? '80mm') === '58mm' ? 'selected' : '' }}>58mm</option>
                        <option value="80mm" {{ ($printerSetting->receipt_size ?? '80mm') === '80mm' ? 'selected' : '' }}>80mm</option>
                        <option value="A4" {{ ($printerSetting->receipt_size ?? '80mm') === 'A4' ? 'selected' : '' }}>A4</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Printer Settings</label>
                    <a href="{{ route('business.profile') }}" class="w-full inline-flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-gray-200 hover:border-emerald-300 text-sm font-medium text-gray-600 transition-all">
                        Manage connected printer
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Signature</label>
                @if($printerSetting->signature_image ?? null)
                    <div class="mb-2 flex items-center gap-3">
                        <img src="{{ asset('storage/' . $printerSetting->signature_image) }}" class="h-14 rounded-lg border border-gray-200 object-contain bg-white p-1">
                        <span class="text-xs text-gray-400">Current signature</span>
                    </div>
                @endif
                <input type="file" name="signature_image" accept="image/*" class="w-full text-sm">
                <p class="text-[11px] text-gray-400 mt-1">Upload a scanned or photographed signature to print on invoices.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Terms & Conditions</label>
                <textarea name="terms_conditions" rows="3" placeholder="e.g. Goods once sold cannot be returned." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">{{ $printerSetting->terms_conditions ?? '' }}</textarea>
            </div>
        </div>

        {{-- Invoice Customization --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-900 mb-1">Invoice Customization</h3>

            <label class="flex items-center justify-between gap-4 py-1">
                <p class="text-sm font-semibold text-gray-700">Show Phone No.</p>
                <input type="checkbox" name="show_phone" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($printerSetting->show_phone ?? true) ? 'checked' : '' }}>
            </label>
            <label class="flex items-center justify-between gap-4 py-1">
                <p class="text-sm font-semibold text-gray-700">Show Address</p>
                <input type="checkbox" name="show_address" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($printerSetting->show_address ?? true) ? 'checked' : '' }}>
            </label>
            <label class="flex items-center justify-between gap-4 py-1">
                <p class="text-sm font-semibold text-gray-700">Show Email</p>
                <input type="checkbox" name="show_email" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($printerSetting->show_email ?? false) ? 'checked' : '' }}>
            </label>
            <label class="flex items-center justify-between gap-4 py-1">
                <p class="text-sm font-semibold text-gray-700">Show Signature</p>
                <input type="checkbox" name="show_signature" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($printerSetting->show_signature ?? false) ? 'checked' : '' }}>
            </label>
            <label class="flex items-center justify-between gap-4 py-1">
                <p class="text-sm font-semibold text-gray-700">Show Party Balance</p>
                <input type="checkbox" name="show_party_balance" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($printerSetting->show_party_balance ?? true) ? 'checked' : '' }}>
            </label>
        </div>

        <button type="submit" id="invoiceSettingsBtn" class="btn-gold font-bold px-6 py-3 rounded-2xl text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            Save Settings
        </button>
    </form>
</div>

<script>
document.getElementById('invoiceSettingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = document.getElementById('invoiceSettingsBtn');
    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = 'Saving...';
    try {
        const res = await fetch('{{ route("settings.invoice.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            showToast('success', 'Saved!', data.message);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('error', 'Error', data.message || 'Could not save settings.');
        }
    } catch (err) {
        showToast('error', 'Network Error', 'Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = original;
    }
});
</script>
@endsection
