@extends('layouts.dashboard')

@section('title', 'Transaction Settings')
@section('page_title', 'Transaction Settings')

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Transaction Settings
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Fine-tune default behavior for sales, purchases and other transactions</p>
    </div>

    <form id="txnSettingsForm" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-3">
            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Set Cash Sale by Default</p>
                    <p class="text-xs text-gray-400">New sales default to a cash payment method</p>
                </div>
                <input type="checkbox" name="cash_sale_default" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['cash_sale_default'] ?? true) ? 'checked' : '' }}>
            </label>

            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Enable Due Date Reminder</p>
                    <p class="text-xs text-gray-400">Get notified before a customer's debt is due</p>
                </div>
                <input type="checkbox" name="due_date_reminder" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['due_date_reminder'] ?? true) ? 'checked' : '' }}>
            </label>

            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Enable Other Income Transactions</p>
                    <p class="text-xs text-gray-400">Allow recording income outside of sales</p>
                </div>
                <input type="checkbox" name="other_income_transactions" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['other_income_transactions'] ?? false) ? 'checked' : '' }}>
            </label>

            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Enable Transaction Prefixes</p>
                    <p class="text-xs text-gray-400">Prefix invoice/receipt numbers, e.g. INV-0001</p>
                </div>
                <input type="checkbox" name="transaction_prefixes" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['transaction_prefixes'] ?? false) ? 'checked' : '' }}>
            </label>

            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Enable Additional Charges</p>
                    <p class="text-xs text-gray-400">Add delivery fees or other charges on sales</p>
                </div>
                <input type="checkbox" name="additional_charges" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['additional_charges'] ?? false) ? 'checked' : '' }}>
            </label>

            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Enable Round Off</p>
                    <p class="text-xs text-gray-400">Round invoice totals to the nearest whole number</p>
                </div>
                <input type="checkbox" name="round_off" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['round_off'] ?? false) ? 'checked' : '' }}>
            </label>

            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Save Images in Gallery</p>
                    <p class="text-xs text-gray-400">Save captured bill/receipt photos to device gallery</p>
                </div>
                <input type="checkbox" name="save_images_gallery" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['save_images_gallery'] ?? true) ? 'checked' : '' }}>
            </label>

            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Enable Image Cropping</p>
                    <p class="text-xs text-gray-400">Crop images before attaching to a transaction</p>
                </div>
                <input type="checkbox" name="image_cropping" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['image_cropping'] ?? true) ? 'checked' : '' }}>
            </label>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-900">Reminder Message Language</h3>
            <select name="reminder_language" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white">
                <option value="en" {{ ($settings['reminder_language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                <option value="sw" {{ ($settings['reminder_language'] ?? 'en') === 'sw' ? 'selected' : '' }}>Swahili</option>
            </select>
        </div>

        <button type="submit" id="txnSettingsBtn" class="btn-gold font-bold px-6 py-3 rounded-2xl text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            Save Settings
        </button>
    </form>
</div>

<script>
document.getElementById('txnSettingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = document.getElementById('txnSettingsBtn');
    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = 'Saving...';
    try {
        const res = await fetch('{{ route("settings.transaction.update") }}', {
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
