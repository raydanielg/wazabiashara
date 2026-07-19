@extends('layouts.dashboard')

@section('title', 'App Settings')
@section('page_title', 'App Settings')

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            App Settings
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Personalize how Wazabiashara looks and behaves for your business</p>
    </div>

    <form id="appSettingsForm" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Appearance --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-5">
            <h3 class="text-sm font-bold text-gray-900">Appearance</h3>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Theme</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="appearance" value="light" class="peer sr-only" {{ ($settings['appearance'] ?? 'light') === 'light' ? 'checked' : '' }}>
                        <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-4 text-center transition-all">
                            <svg class="w-6 h-6 mx-auto mb-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span class="text-xs font-semibold text-gray-700">Light</span>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="appearance" value="dark" class="peer sr-only" {{ ($settings['appearance'] ?? 'light') === 'dark' ? 'checked' : '' }}>
                        <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-4 text-center transition-all">
                            <svg class="w-6 h-6 mx-auto mb-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <span class="text-xs font-semibold text-gray-700">Dark</span>
                        </div>
                    </label>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5">You can also toggle dark mode any time from the header.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Font Size</label>
                <select name="font_size" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white">
                    <option value="small" {{ ($settings['font_size'] ?? 'normal') === 'small' ? 'selected' : '' }}>Small</option>
                    <option value="normal" {{ ($settings['font_size'] ?? 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="large" {{ ($settings['font_size'] ?? 'normal') === 'large' ? 'selected' : '' }}>Large</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Language</label>
                <div class="flex gap-2">
                    <button type="button" onclick="switchLang('sw'); document.getElementById('langInput').value='sw'; document.getElementById('langSwBtn').classList.add('border-emerald-500','bg-emerald-50'); document.getElementById('langEnBtn').classList.remove('border-emerald-500','bg-emerald-50');" id="langSwBtn" class="px-4 py-2 rounded-xl border-2 border-gray-200 text-sm font-semibold {{ ($settings['language'] ?? 'en') === 'sw' ? 'border-emerald-500 bg-emerald-50' : '' }}">Swahili</button>
                    <button type="button" onclick="switchLang('en'); document.getElementById('langInput').value='en'; document.getElementById('langEnBtn').classList.add('border-emerald-500','bg-emerald-50'); document.getElementById('langSwBtn').classList.remove('border-emerald-500','bg-emerald-50');" id="langEnBtn" class="px-4 py-2 rounded-xl border-2 border-gray-200 text-sm font-semibold {{ ($settings['language'] ?? 'en') === 'en' ? 'border-emerald-500 bg-emerald-50' : '' }}">English</button>
                </div>
                <input type="hidden" name="language" id="langInput" value="{{ $settings['language'] ?? 'en' }}">
            </div>
        </div>

        {{-- Currency & Formats --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-5">
            <h3 class="text-sm font-bold text-gray-900">Currency & Formats</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Currency</label>
                    <input type="text" name="currency" value="{{ $settings['currency'] ?? 'TSh' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Currency Position</label>
                    <select name="currency_position" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white">
                        <option value="start" {{ ($settings['currency_position'] ?? 'start') === 'start' ? 'selected' : '' }}>Start (TSh 1,000)</option>
                        <option value="end" {{ ($settings['currency_position'] ?? 'start') === 'end' ? 'selected' : '' }}>End (1,000 TSh)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date Format</label>
                    <select name="date_format" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white">
                        <option value="d/m/Y" {{ ($settings['date_format'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                        <option value="m/d/Y" {{ ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        <option value="Y-m-d" {{ ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Time Format</label>
                    <select name="time_format" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white">
                        <option value="24h" {{ ($settings['time_format'] ?? '24h') === '24h' ? 'selected' : '' }}>24 Hour</option>
                        <option value="12h" {{ ($settings['time_format'] ?? '') === '12h' ? 'selected' : '' }}>12 Hour (AM/PM)</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Number Format</label>
                    <select name="number_format" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white">
                        <option value="1,234.56" {{ ($settings['number_format'] ?? '1,234.56') === '1,234.56' ? 'selected' : '' }}>1,234.56</option>
                        <option value="1.234,56" {{ ($settings['number_format'] ?? '') === '1.234,56' ? 'selected' : '' }}>1.234,56</option>
                        <option value="1 234.56" {{ ($settings['number_format'] ?? '') === '1 234.56' ? 'selected' : '' }}>1 234.56</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Security --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-900">Security</h3>

            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Privacy Mode</p>
                    <p class="text-xs text-gray-400">Hide sensitive amounts on the dashboard</p>
                </div>
                <input type="checkbox" name="privacy_mode" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['privacy_mode'] ?? false) ? 'checked' : '' }}>
            </label>

            <label class="flex items-center justify-between gap-4 py-1">
                <div>
                    <p class="text-sm font-semibold text-gray-700">App Lock</p>
                    <p class="text-xs text-gray-400">Require PIN/password when reopening the app</p>
                </div>
                <input type="checkbox" name="app_lock" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-300" {{ ($settings['app_lock'] ?? false) ? 'checked' : '' }}>
            </label>
        </div>

        <button type="submit" id="appSettingsBtn" class="btn-gold font-bold px-6 py-3 rounded-2xl text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
            Save Settings
        </button>
    </form>
</div>

<script>
document.getElementById('appSettingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = document.getElementById('appSettingsBtn');
    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = 'Saving...';
    try {
        const res = await fetch('{{ route("settings.app.update") }}', {
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
