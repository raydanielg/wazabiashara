<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Wazabiashara'))</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <meta name="theme-color" content="#001816">
    <meta property="og:title" content="{{ config('app.name', 'Wazabiashara') }}">
    <meta property="og:description" content="Biashara yako, Mkononi mwako">
    <meta property="og:image" content="{{ asset('favicon.png') }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ config('app.name', 'Wazabiashara') }}">
    <meta name="twitter:description" content="Biashara yako, Mkononi mwako">
    <meta name="twitter:image" content="{{ asset('favicon.png') }}">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Toastify-JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
        @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
        @keyframes slideIn { from { opacity:0; transform:translateY(15px); } to { opacity:1; transform:translateY(0); } }
        @keyframes toastIn { from { opacity:0; transform:translateX(100%); } to { opacity:1; transform:translateX(0); } }
        @keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(100%); } }
        @keyframes countUp { from { opacity:0; transform:scale(0.8); } to { opacity:1; transform:scale(1); } }
        .animate-fade { animation: fadeIn 0.3s ease-out both; }
        .animate-slide { animation: slideIn 0.4s ease-out both; }
        .animate-slide-delay-1 { animation: slideIn 0.4s ease-out 0.1s both; }
        .animate-slide-delay-2 { animation: slideIn 0.4s ease-out 0.2s both; }
        .animate-slide-delay-3 { animation: slideIn 0.4s ease-out 0.3s both; }
        .animate-slide-delay-4 { animation: slideIn 0.4s ease-out 0.4s both; }
        .count-up { animation: countUp 0.5s ease-out both; }
        .toast-in { animation: toastIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
        .toast-out { animation: toastOut 0.3s ease-in both; }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { background: rgba(255,255,255,0.06); }
        .sidebar-link.active { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .sidebar-submenu.open { max-height: 500px; }
        .btn-gold { background: linear-gradient(135deg, #f9ac00, #d49700); color: #fff; transition: all 0.2s ease; }
        .btn-gold:hover { background: linear-gradient(135deg, #ffb71a, #f9ac00); box-shadow: 0 4px 12px rgba(249,172,0,0.3); transform: translateY(-1px); }
        .shadow-card { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .shadow-cardlg { box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        .lang-btn { transition: all 0.25s ease; }
        .lang-btn.active { background: rgba(249,172,0,0.15); color: #d49700; border-color: rgba(249,172,0,0.3); }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .dark-mode-transition * { transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #01241f; }
        ::-webkit-scrollbar-thumb { background: #024938; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #f9ac00; }
        /* Dark mode auto-styling for common patterns */
        .dark .bg-white { background-color: #1f2937 !important; }
        .dark .text-gray-900 { color: #f3f4f6 !important; }
        .dark .text-gray-800 { color: #e5e7eb !important; }
        .dark .text-gray-700 { color: #d1d5db !important; }
        .dark .text-gray-600 { color: #9ca3af !important; }
        .dark .border { border-color: #374151 !important; }
        .dark .border-b { border-color: #374151 !important; }
        .dark .border-t { border-color: #374151 !important; }
        .dark .bg-gray-50 { background-color: #111827 !important; }
        .dark .bg-gray-100 { background-color: #1f2937 !important; }
        .dark .hover\:bg-gray-50:hover { background-color: #1f2937 !important; }
        .dark .hover\:bg-gray-100:hover { background-color: #374151 !important; }
        .dark .border-gray-100 { border-color: #374151 !important; }
        .dark .border-gray-200 { border-color: #374151 !important; }
        .dark .placeholder-gray-400::placeholder { color: #6b7280 !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
</head>
<body class="font-['Nunito',sans-serif] antialiased bg-gray-50 text-slate-800 dark:bg-gray-900 dark:text-gray-100">
    <script>if(localStorage.getItem('darkMode')==='true'{{ auth()->user() && auth()->user()->business && auth()->user()->business->dark_mode ? "||true" : "" }}){document.documentElement.classList.add('dark');document.body.classList.add('dark:bg-gray-900','dark:text-gray-100');}</script>

    {{-- Mobile Overlay --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="dashSidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-emerald-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
        {{-- Brand --}}
        <div class="h-16 flex items-center px-6 border-b border-emerald-800/50 flex-shrink-0">
            <img src="{{ asset('logo.png') }}" alt="Wazabiashara" class="h-8 w-auto">
            <div class="ml-2">
                <span class="text-white font-bold text-sm tracking-wide block leading-tight">Wazabiashara</span>
                <span class="text-gold-400 text-[9px] font-medium tracking-wide uppercase" data-lang="sw">{{ auth()->user()->isAdmin() ? 'Admin Panel' : 'Dashboard' }}</span>
                <span class="text-gold-400 text-[9px] font-medium tracking-wide uppercase hidden" data-lang="en">{{ auth()->user()->isAdmin() ? 'Admin Panel' : 'Dashboard' }}</span>
            </div>
        </div>

        {{-- Menu --}}
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            {{-- Dashboard --}}
            <a href="{{ route('home') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('home') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span data-lang="sw">Dashboard</span>
                <span class="hidden" data-lang="en">Dashboard</span>
            </a>

            {{-- POS --}}
            <a href="{{ route('pos.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span data-lang="sw">POS — Mauzo</span>
                <span class="hidden" data-lang="en">POS — Sales</span>
            </a>

            {{-- Quotations --}}
            <a href="{{ route('quotations.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span data-lang="sw">Quotations</span>
                <span class="hidden" data-lang="en">Quotations</span>
            </a>

            {{-- Products --}}
            <a href="{{ route('products.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span data-lang="sw">Bidhaa & Stoo</span>
                <span class="hidden" data-lang="en">Products & Stock</span>
            </a>

            {{-- Categories --}}
            <a href="{{ route('categories.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span data-lang="sw">Kategoria</span>
                <span class="hidden" data-lang="en">Categories</span>
            </a>

            @if(auth()->user()->isBusinessAdmin() || auth()->user()->isAdmin() || auth()->user()->isManager())
            {{-- Branches --}}
            <a href="{{ route('branches.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span data-lang="sw">Matawi</span>
                <span class="hidden" data-lang="en">Branches</span>
            </a>

            {{-- Stock Transfers --}}
            <a href="{{ route('stock-transfers.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span data-lang="sw">Uhamisho wa Stoo</span>
                <span class="hidden" data-lang="en">Stock Transfers</span>
            </a>

            {{-- Suppliers & Purchases --}}
            <a href="{{ route('suppliers.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                <span data-lang="sw">Wasambazaji & Manunuzi</span>
                <span class="hidden" data-lang="en">Suppliers & Purchases</span>
            </a>
            @endif

            {{-- Customers --}}
            <a href="{{ route('customers.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span data-lang="sw">Wateja & Madeni</span>
                <span class="hidden" data-lang="en">Customers & Debts</span>
            </a>

            {{-- Incomes --}}
            <a href="{{ route('incomes.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('incomes.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span data-lang="sw">Mapato</span>
                <span class="hidden" data-lang="en">Income</span>
            </a>

            {{-- Expenses --}}
            <a href="{{ route('expenses.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                <span data-lang="sw">Matumizi</span>
                <span class="hidden" data-lang="en">Expenses</span>
            </a>

            {{-- Payments --}}
            <a href="{{ route('payments.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span data-lang="sw">Malipo</span>
                <span class="hidden" data-lang="en">Payments</span>
            </a>

            {{-- Cash Flow --}}
            <a href="{{ route('cash-flow.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('cash-flow.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span data-lang="sw">Mtiririko wa Fedha</span>
                <span class="hidden" data-lang="en">Cash Flow</span>
            </a>

            {{-- Returns --}}
            <a href="{{ route('returns.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                <span data-lang="sw">Rudisha Bidhaa</span>
                <span class="hidden" data-lang="en">Returns</span>
            </a>

            {{-- Shifts --}}
            <a href="{{ route('shifts.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span data-lang="sw">Zamu (Shifts)</span>
                <span class="hidden" data-lang="en">Shifts</span>
            </a>

            {{-- Reminders --}}
            <a href="{{ route('reminders.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span data-lang="sw">Vikumbusho</span>
                <span class="hidden" data-lang="en">Reminders</span>
            </a>

            {{-- Reports --}}
            <a href="{{ route('reports.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span data-lang="sw">Ripoti & Takwimu</span>
                <span class="hidden" data-lang="en">Reports & Analytics</span>
            </a>

            {{-- Cards --}}
            <button onclick="toggleMenu('menu-cards')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('cards.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v1a1 1 0 001 1h3a1 1 0 001-1V3m0 0V2a1 1 0 00-1-1H5a1 1 0 00-1 1v1m4 0h.01M7 3h.01M20 3v1a1 1 0 01-1 1h-3a1 1 0 01-1-1V3m0 0V2a1 1 0 011-1h5a1 1 0 011 1v1m-4 0h.01M17 3h.01M7 8h10M7 12h10M7 16h6"/></svg>
                <span data-lang="sw">Kadi</span>
                <span class="hidden" data-lang="en">Cards</span>
                <svg id="arrow-cards" class="w-4 h-4 ml-auto transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="menu-cards" class="sidebar-submenu {{ request()->routeIs('cards.*') ? 'open' : '' }}">
                <a href="{{ route('cards.greeting') }}" class="sidebar-link w-full flex items-center gap-3 pl-10 pr-3 py-2 rounded-lg text-emerald-200/70 text-xs font-medium {{ request()->routeIs('cards.greeting*') ? 'active' : '' }}">
                    <span data-lang="sw">Kadi za Salamu</span>
                    <span class="hidden" data-lang="en">Greeting Cards</span>
                </a>
                <a href="{{ route('cards.business') }}" class="sidebar-link w-full flex items-center gap-3 pl-10 pr-3 py-2 rounded-lg text-emerald-200/70 text-xs font-medium {{ request()->routeIs('cards.business') ? 'active' : '' }}">
                    <span data-lang="sw">Kadi za Biashara</span>
                    <span class="hidden" data-lang="en">Business Cards</span>
                </a>
            </div>

            {{-- Business Profile --}}
            <a href="{{ route('business.profile') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('business.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span data-lang="sw">Wasifu wa Biashara</span>
                <span class="hidden" data-lang="en">Business Profile</span>
            </a>
        </div>

        {{-- Bottom User --}}
        <div class="p-4 border-t border-emerald-800/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-emerald-300/60">{{ ucfirst(auth()->user()->role ?? 'User') }}</p>
                </div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('dash-logout').submit();" class="text-emerald-300/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </a>
                <form id="dash-logout" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between px-6 sticky top-0 z-30 transition-colors">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-bold text-gray-800 dark:text-gray-100">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Search --}}
                <div class="hidden md:flex items-center bg-gray-50 dark:bg-gray-700 rounded-xl px-3 py-2 border border-gray-200 dark:border-gray-600 focus-within:border-emerald-300 focus-within:ring-2 focus-within:ring-emerald-100 transition-all">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" placeholder="Search..." class="bg-transparent text-sm outline-none w-48 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500">
                </div>

                {{-- Language Switcher --}}
                <div class="flex items-center gap-0.5 bg-gray-50 dark:bg-gray-700 rounded-xl p-0.5 border border-gray-200 dark:border-gray-600">
                    <button onclick="switchLang('sw')" id="langSW" class="lang-btn active px-2.5 py-1.5 rounded-lg text-[11px] font-bold text-gray-600 dark:text-gray-300 border border-transparent">SW</button>
                    <button onclick="switchLang('en')" id="langEN" class="lang-btn px-2.5 py-1.5 rounded-lg text-[11px] font-bold text-gray-400 dark:text-gray-500 border border-transparent">EN</button>
                </div>

                {{-- Dark Mode Toggle --}}
                <button onclick="toggleDarkMode()" id="darkModeBtn" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition-colors">
                    <svg id="sunIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg id="moonIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>

                {{-- Notifications --}}
                <button class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-gold-400 rounded-full"></span>
                </button>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6 animate-fade bg-gray-50 dark:bg-gray-900 transition-colors min-h-[calc(100vh-4rem)]">
            @yield('content')
        </main>
    </div>

    {{-- Sweet Alert Component --}}
    @include('components.sweet-alert')

    {{-- Toastify-JS Toast System --}}
    <style>
        .toastify { font-family: 'Nunito', sans-serif !important; border-radius: 12px !important; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.2) !important; padding: 14px 18px !important; min-width: 300px !important; max-width: 380px !important; }
        .toastify-title { font-weight: 800 !important; font-size: 14px !important; margin-bottom: 2px !important; }
        .toastify-message { font-weight: 500 !important; font-size: 13px !important; opacity: 0.9 !important; }
        .toastify-icon { width: 22px !important; height: 22px !important; flex-shrink: 0 !important; }
        .toastify-progress { position: absolute !important; bottom: 0 !important; left: 0 !important; height: 3px !important; border-radius: 0 0 0 12px !important; opacity: 0.7 !important; }
        @keyframes toastProgress { from { width: 100%; } to { width: 0%; } }
    </style>
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
            const html = '<div style="display:flex;align-items:flex-start;gap:10px;"><div style="color:' + s.accent + ';">' + icon + '</div><div style="flex:1;min-width:0;"><div class="toastify-title">' + title + '</div>' + (message ? '<div class="toastify-message">' + message + '</div>' : '') + '</div></div>';
            const toast = Toastify({ text: html, escapeMarkup: false, duration: 5000, close: true, gravity: 'top', position: 'right', stopOnFocus: true, style: { background: s.bg, color: '#fff' }, offset: { x: 20, y: 20 } });
            toast.showToast();
            setTimeout(function() { const el = document.querySelector('.toastify:last-child'); if (el) { const bar = document.createElement('div'); bar.className = 'toastify-progress'; bar.style.background = s.accent; bar.style.animation = 'toastProgress 5s linear forwards'; el.style.position = 'relative'; el.style.overflow = 'hidden'; el.appendChild(bar); } }, 50);
        }
        window.showToast = showToast;
        @if(session('status'))
            showToast('success', 'Success', {!! json_encode(session('status')) !!});
        @endif
        @if(session('error'))
            showToast('error', 'Error', {!! json_encode(session('error')) !!});
        @endif
        @if(session('warning'))
            showToast('warning', 'Warning', {!! json_encode(session('warning')) !!});
        @endif
        @if(session('info'))
            showToast('info', 'Info', {!! json_encode(session('info')) !!});
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast('error', 'Hitilafu', {!! json_encode($error) !!});
            @endforeach
        @endif
    })();
    </script>

    {{-- Sidebar + Language Scripts --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('dashSidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            const arrow = document.getElementById('arrow-' + id.replace('menu-', ''));
            menu.classList.toggle('open');
            if (arrow) arrow.classList.toggle('rotate-180');
        }

        function switchLang(lang) {
            document.querySelectorAll('[data-lang]').forEach(function(el) {
                if (el.getAttribute('data-lang') === lang) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
            const sw = document.getElementById('langSW');
            const en = document.getElementById('langEN');
            if (sw) sw.classList.toggle('active', lang === 'sw');
            if (en) en.classList.toggle('active', lang === 'en');
            document.documentElement.lang = lang;
            try { localStorage.setItem('app_lang', lang); } catch(e) {}
        }

        try {
            const saved = localStorage.getItem('app_lang');
            if (saved && saved !== 'sw') switchLang(saved);
        } catch(e) {}

        // Dark Mode
        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
            updateDarkModeIcons(isDark);
            // Persist to server
            fetch('/business/dark-mode', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ dark_mode: isDark })
            }).catch(() => {});
        }
        function updateDarkModeIcons(isDark) {
            const sun = document.getElementById('sunIcon');
            const moon = document.getElementById('moonIcon');
            if (sun) sun.classList.toggle('hidden', !isDark);
            if (moon) moon.classList.toggle('hidden', isDark);
        }
        // Initialize dark mode icons on load
        (function() {
            const isDark = document.documentElement.classList.contains('dark');
            updateDarkModeIcons(isDark);
        })();
    </script>

</body>
</html>
