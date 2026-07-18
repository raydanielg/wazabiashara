<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sajili Biashara — Wazabiashara</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes simpleFadeIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes floatText { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-10px); } }
        @keyframes pulseGlow { 0%,100% { opacity:0.4; } 50% { opacity:0.7; } }
        @keyframes ajaxProgress { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        @keyframes shimmer { 0% { transform: translateX(-150%) rotate(25deg); } 100% { transform: translateX(250%) rotate(25deg); } }
        @keyframes glowPulse { 0%,100% { box-shadow: 0 0 20px rgba(2,73,56,0.3), 0 0 40px rgba(249,172,0,0.1); } 50% { box-shadow: 0 0 30px rgba(2,73,56,0.4), 0 0 60px rgba(249,172,0,0.15); } }
        .page-transition { animation: simpleFadeIn 0.35s ease-out both; }
        .float-text { animation: floatText 4s ease-in-out infinite; }
        .pulse-glow { animation: pulseGlow 3s ease-in-out infinite; }
        .ajax-loader { position:fixed; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #024938, #f9ac00, #024938); background-size: 200% 100%; animation: ajaxProgress 1s linear infinite; z-index:9999; display:none; }
        .glass-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 25px 60px -15px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1) inset; }
        .glass-shine { position: relative; overflow: hidden; }
        .glass-shine::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent); transform: translateX(-150%) rotate(25deg); animation: shimmer 4s infinite; pointer-events: none; z-index: 1; }
        .glow-pulse { animation: glowPulse 3s ease-in-out infinite; }
        .lang-btn { transition: all 0.25s ease; }
        .lang-btn.active { background: rgba(249,172,0,0.2); border-color: rgba(249,172,0,0.4); color: #f9ac00; }
        .toastify { font-family: 'Nunito', sans-serif !important; border-radius: 12px !important; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.2) !important; padding: 14px 18px !important; min-width: 300px !important; max-width: 380px !important; }
        .toastify-title { font-weight: 800 !important; font-size: 14px !important; margin-bottom: 2px !important; }
        .toastify-message { font-weight: 500 !important; font-size: 13px !important; opacity: 0.9 !important; }
        .toastify-icon { width: 22px !important; height: 22px !important; flex-shrink: 0 !important; }
        .toastify-progress { position: absolute !important; bottom: 0 !important; left: 0 !important; height: 3px !important; border-radius: 0 0 0 12px !important; opacity: 0.7 !important; }
        @keyframes toastProgress { from { width: 100%; } to { width: 0%; } }
    </style>
</head>
<body class="font-['Nunito',sans-serif] antialiased bg-gray-50 min-h-screen">

    {{-- AJAX Progress Bar --}}
    <div id="ajaxLoader" class="ajax-loader"></div>

    {{-- Background --}}
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('flat-abstract-background-pattern-vector_822782-866.jpg') }}" alt="" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/90 via-emerald-800/85 to-emerald-700/80"></div>
        <div class="absolute top-0 left-0 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-400/10 rounded-full blur-3xl"></div>
    </div>

    {{-- Language Switcher (top right) --}}
    <div class="fixed top-5 right-5 z-50">
        <div class="flex items-center gap-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full p-1">
            <button onclick="switchLang('sw')" id="langSW" class="lang-btn active px-3 py-1.5 rounded-full text-xs font-bold text-white border border-transparent">SW</button>
            <button onclick="switchLang('en')" id="langEN" class="lang-btn px-3 py-1.5 rounded-full text-xs font-bold text-white/70 border border-transparent">EN</button>
        </div>
    </div>

    {{-- Content --}}
    <main class="relative z-10 min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-2xl page-transition">

            {{-- Logo + Title --}}
            <div class="text-center mb-6">
                <div class="inline-block glow-pulse rounded-2xl">
                    <img src="{{ asset('logo.png') }}" alt="Wazabiashara" class="w-16 h-16 mx-auto object-contain rounded-2xl shadow-lg">
                </div>
                <h1 class="text-3xl font-black text-white mt-3" data-lang="sw">Sajili Biashara Yako</h1>
                <h1 class="text-3xl font-black text-white mt-3 hidden" data-lang="en">Register Your Business</h1>
                <p class="text-emerald-200 text-sm font-semibold mt-1" data-lang="sw">Hatua ya mwisho kabla ya kuanza kutumia Wazabiashara</p>
                <p class="text-emerald-200 text-sm font-semibold mt-1 hidden" data-lang="en">Final step before using Wazabiashara</p>
            </div>

            {{-- Form Card --}}
            <div class="glass-card glass-shine rounded-2xl overflow-hidden">

                {{-- Step indicator --}}
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-50/80 to-gray-50/80 backdrop-blur-sm border-b border-gray-100/50">
                    <ol class="flex items-center w-full space-x-2 text-sm font-medium text-center bg-transparent sm:space-x-4">
                        <li class="flex items-center text-emerald-700">
                            <span class="flex items-center justify-center w-6 h-6 me-2 text-xs border-2 border-emerald-600 bg-emerald-600 text-white rounded-full shrink-0 font-black">1</span>
                            <span class="font-bold" data-lang="sw">Akaunti</span>
                            <span class="font-bold hidden" data-lang="en">Account</span>
                            <svg class="w-5 h-5 ms-2 text-gray-300" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 16 4-4-4-4m6 8 4-4-4-4"/></svg>
                        </li>
                        <li class="flex items-center text-gold-600">
                            <span class="flex items-center justify-center w-6 h-6 me-2 text-xs border-2 border-gold-500 bg-gold-500 text-white rounded-full shrink-0 font-black">2</span>
                            <span class="font-bold" data-lang="sw">Biashara</span>
                            <span class="font-bold hidden" data-lang="en">Business</span>
                            <svg class="w-5 h-5 ms-2 text-gray-300" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 16 4-4-4-4m6 8 4-4-4-4"/></svg>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <span class="flex items-center justify-center w-6 h-6 me-2 text-xs border-2 border-gray-300 text-gray-400 rounded-full shrink-0 font-black">3</span>
                            <span class="font-bold" data-lang="sw">Anza</span>
                            <span class="font-bold hidden" data-lang="en">Start</span>
                        </li>
                    </ol>
                </div>

                {{-- Form --}}
                <div class="p-8">
                    <form id="businessForm" class="space-y-5">
                        @csrf

                        {{-- Business Name --}}
                        <div>
                            <label for="business_name" class="block text-sm font-semibold text-gray-700 mb-1.5" data-lang="sw">Jina la Biashara</label>
                            <label for="business_name" class="block text-sm font-semibold text-gray-700 mb-1.5 hidden" data-lang="en">Business Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <input id="business_name" type="text" name="business_name" required autofocus
                                    class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:bg-white outline-none transition-all text-sm"
                                    placeholder="Mfano: Duka la Juma General" data-placeholder-sw="Mfano: Duka la Juma General" data-placeholder-en="e.g. Juma General Store">
                            </div>
                        </div>

                        {{-- Business Type --}}
                        <div>
                            <label for="business_type" class="block text-sm font-semibold text-gray-700 mb-1.5" data-lang="sw">Aina ya Biashara</label>
                            <label for="business_type" class="block text-sm font-semibold text-gray-700 mb-1.5 hidden" data-lang="en">Business Type</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <select id="business_type" name="business_type" required
                                    class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:bg-white outline-none transition-all text-sm appearance-none">
                                    @foreach($businessTypes as $type)
                                        <option value="{{ $type->slug }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Region + Phone --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="region" class="block text-sm font-semibold text-gray-700 mb-1.5" data-lang="sw">Mkoa</label>
                                <label for="region" class="block text-sm font-semibold text-gray-700 mb-1.5 hidden" data-lang="en">Region</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    @php $regions = json_decode(file_get_contents(base_path('regions.json')), true)['regions'] ?? []; @endphp
                                    <select id="region" name="region" required
                                        class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:bg-white outline-none transition-all text-sm appearance-none">
                                        <option value="" disabled selected data-lang="sw">— Chagua Mkoa —</option>
                                        <option value="" disabled selected class="hidden" data-lang="en">— Select Region —</option>
                                        @foreach($regions as $r)
                                        <option value="{{ $r['name'] }}">{{ $r['name'] }}{{ isset($r['capital']) ? ' (' . $r['capital'] . ')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="business_phone" class="block text-sm font-semibold text-gray-700 mb-1.5" data-lang="sw">Namba ya Simu</label>
                                <label for="business_phone" class="block text-sm font-semibold text-gray-700 mb-1.5 hidden" data-lang="en">Phone Number</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <input id="business_phone" type="tel" name="business_phone" required
                                        value="{{ auth()->user()->phone ?? '' }}"
                                        readonly
                                        class="w-full pl-11 pr-10 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50/50 text-emerald-800 font-semibold outline-none cursor-not-allowed text-sm">
                                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                </div>
                                <p class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span data-lang="sw">Namba yako kutoka akaunti</span>
                                    <span class="hidden" data-lang="en">From your account</span>
                                </p>
                            </div>
                        </div>

                        {{-- Business Email --}}
                        <div>
                            <label for="business_email" class="block text-sm font-semibold text-gray-700 mb-1.5" data-lang="sw">Barua Pepe (Hiari)</label>
                            <label for="business_email" class="block text-sm font-semibold text-gray-700 mb-1.5 hidden" data-lang="en">Email (Optional)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input id="business_email" type="email" name="business_email"
                                    value="{{ auth()->user()->email ?? '' }}"
                                    class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:bg-white outline-none transition-all text-sm"
                                    placeholder="biashara@email.com">
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="relative pt-2">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                            <div class="relative flex justify-center"><span class="px-3 bg-white text-xs font-bold text-emerald-600 uppercase tracking-wide" data-lang="sw">Tawi Kuu</span></div>
                            <div class="absolute inset-0 flex items-center hidden"><div class="w-full border-t border-gray-200"></div></div>
                            <div class="relative flex justify-center hidden"><span class="px-3 bg-white text-xs font-bold text-emerald-600 uppercase tracking-wide" data-lang="en">Main Branch</span></div>
                        </div>

                        {{-- Branch Name + Location --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="branch_name" class="block text-sm font-semibold text-gray-700 mb-1.5" data-lang="sw">Jina la Tawi</label>
                                <label for="branch_name" class="block text-sm font-semibold text-gray-700 mb-1.5 hidden" data-lang="en">Branch Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <input id="branch_name" type="text" name="branch_name" required
                                        class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:bg-white outline-none transition-all text-sm"
                                        placeholder="Tawi Kuu">
                                </div>
                            </div>
                            <div>
                                <label for="branch_location" class="block text-sm font-semibold text-gray-700 mb-1.5" data-lang="sw">Eneo</label>
                                <label for="branch_location" class="block text-sm font-semibold text-gray-700 mb-1.5 hidden" data-lang="en">Location</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <input id="branch_location" type="text" name="branch_location" required
                                        class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:bg-white outline-none transition-all text-sm"
                                        placeholder="Mfano: Kariakoo, Dar">
                                </div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" id="businessBtn" class="w-full py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 hover:from-emerald-700 hover:to-emerald-900 rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed relative overflow-hidden group">
                            <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                            <svg id="businessBtnIcon" class="w-5 h-5 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span id="businessBtnText" class="relative" data-lang="sw">Sajili Biashara</span>
                            <span id="businessBtnTextEn" class="relative hidden" data-lang="en">Register Business</span>
                        </button>
                    </form>
                </div>
            </div>

            <p class="mt-4 text-center text-xs text-emerald-200/60">&copy; {{ date('Y') }} Wazabiashara. Haki zote zimehifadhiwa.</p>
        </div>
    </main>

    {{-- Toastify Script --}}
    <script>
    (function() {
        const icons = {
            success: '<svg class="toastify-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error: '<svg class="toastify-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            warning: '<svg class="toastify-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
            info: '<svg class="toastify-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };

        const styles = {
            success: { bg: 'linear-gradient(135deg, #024938, #013028)', accent: '#f9ac00' },
            error:   { bg: 'linear-gradient(135deg, #dc2626, #991b1b)', accent: '#fca5a5' },
            warning: { bg: 'linear-gradient(135deg, #d97706, #92400e)', accent: '#fde68a' },
            info:    { bg: 'linear-gradient(135deg, #2563eb, #1e3a8a)', accent: '#93c5fd' }
        };

        function showToast(type, title, message) {
            const s = styles[type] || styles.info;
            const icon = icons[type] || icons.info;
            const html = '<div style="display:flex;align-items:flex-start;gap:10px;">' +
                '<div style="color:' + s.accent + ';">' + icon + '</div>' +
                '<div style="flex:1;min-width:0;">' +
                    '<div class="toastify-title">' + title + '</div>' +
                    (message ? '<div class="toastify-message">' + message + '</div>' : '') +
                '</div></div>';

            const toast = Toastify({
                text: html,
                escapeMarkup: false,
                duration: 5000,
                close: true,
                gravity: 'top',
                position: 'right',
                stopOnFocus: true,
                style: { background: s.bg, color: '#fff' },
                offset: { x: 20, y: 20 },
            });
            toast.showToast();

            setTimeout(function() {
                const el = document.querySelector('.toastify:last-child');
                if (el) {
                    const bar = document.createElement('div');
                    bar.className = 'toastify-progress';
                    bar.style.background = s.accent;
                    bar.style.animation = 'toastProgress 5s linear forwards';
                    el.style.position = 'relative';
                    el.style.overflow = 'hidden';
                    el.appendChild(bar);
                }
            }, 50);
        }

        window.showToast = showToast;

        @if(session('status'))
            showToast('success', 'Success', {!! json_encode(session('status')) !!});
        @endif
        @if(session('error'))
            showToast('error', 'Error', {!! json_encode(session('error')) !!});
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast('error', 'Hitilafu', {!! json_encode($error) !!});
            @endforeach
        @endif
    })();
    </script>

    {{-- Language Switcher Script --}}
    <script>
    function switchLang(lang) {
        document.querySelectorAll('[data-lang]').forEach(function(el) {
            if (el.getAttribute('data-lang') === lang) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });
        document.querySelectorAll('input[data-placeholder-' + lang + ']').forEach(function(el) {
            el.placeholder = el.getAttribute('data-placeholder-' + lang);
        });
        document.getElementById('langSW').classList.toggle('active', lang === 'sw');
        document.getElementById('langEN').classList.toggle('active', lang === 'en');
        document.documentElement.lang = lang;
    }
    </script>

    {{-- AJAX Submit Script --}}
    <script>
    (function() {
        const form = document.getElementById('businessForm');
        const btn = document.getElementById('businessBtn');
        const btnText = document.getElementById('businessBtnText');
        const btnIcon = document.getElementById('businessBtnIcon');
        const loader = document.getElementById('ajaxLoader');

        if (!form) return;

        const spinnerSvg = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';
        const checkSvg = btnIcon.innerHTML;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const data = {};
            formData.forEach((v, k) => data[k] = v);

            btn.disabled = true;
            btnText.textContent = 'Inasajili...';
            btnIcon.innerHTML = spinnerSvg;
            if (loader) loader.style.display = 'block';

            fetch('{{ route("business.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(r => r.json())
            .then(res => {
                if (loader) loader.style.display = 'none';
                if (res.success) {
                    showToast('success', 'Imefanikiwa!', res.message);
                    btnText.textContent = 'Inaelekeza...';
                    btnIcon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    setTimeout(() => {
                        window.location.href = res.redirect;
                    }, 1500);
                } else if (res.errors) {
                    Object.keys(res.errors).forEach(function(field) {
                        res.errors[field].forEach(function(msg) {
                            showToast('error', 'Hitilafu', msg);
                        });
                    });
                    btn.disabled = false;
                    btnText.textContent = 'Sajili Biashara';
                    btnIcon.innerHTML = checkSvg;
                } else {
                    showToast('error', 'Hitilafu', res.message || 'Imeshindwa kusajili.');
                    btn.disabled = false;
                    btnText.textContent = 'Sajili Biashara';
                    btnIcon.innerHTML = checkSvg;
                }
            })
            .catch(err => {
                if (loader) loader.style.display = 'none';
                showToast('error', 'Hitilafu', 'Tatizo la mtandao. Jaribu tena.');
                btn.disabled = false;
                btnText.textContent = 'Sajili Biashara';
                btnIcon.innerHTML = checkSvg;
            });
        });
    })();
    </script>
</body>
</html>
