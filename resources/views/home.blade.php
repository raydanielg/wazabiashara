@extends('layouts.dashboard')

@section('title', 'Dashboard - Wazabiashara')
@section('page_title', auth()->user()->isAdmin() ? 'Admin Dashboard' : 'My Dashboard')

@section('content')
@php
    $fmt = fn($n) => $n >= 1000000000 ? number_format($n/1000000000,2).'B' : ($n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n)));
@endphp

@if(auth()->user()->isAdmin())
{{-- ADMIN DASHBOARD --}}
<div class="space-y-6">

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
            $cards = [
                ['label'=>'Total Users','value'=>number_format($stats['totalUsers']),'change'=>'+'.$stats['newUsersWeek'].' this week','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','sub'=>'emerald-200'],
                ['label'=>'Total Revenue','value'=>'TZS '.$fmt($stats['totalRevenue']),'change'=>'This week: TZS '.$fmt($stats['revenueWeek']),'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'gold-400','to'=>'gold-500','border'=>'gold-300','text'=>'gold-50','sub'=>'gold-100'],
                ['label'=>'Total Orders','value'=>number_format($stats['totalOrders']),'change'=>'+'.$stats['ordersWeek'].' this week','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','sub'=>'sky-200'],
                ['label'=>'Success Rate','value'=>$stats['successRate'].'%','change'=>$stats['pendingCount'].' pending','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'violet-500','to'=>'violet-600','border'=>'violet-400','text'=>'violet-100','sub'=>'violet-200'],
            ];
        @endphp
        @foreach($cards as $i => $card)
        <div class="bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow {{ ['animate-slide','animate-slide-delay-1','animate-slide-delay-2','animate-slide-delay-3'][$i] }}">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
                    <svg class="w-4 h-4 {{ $card['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white count-up">{{ $card['value'] }}</p>
                <p class="text-[10px] {{ $card['sub'] }} font-medium mt-1">{{ $card['change'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Revenue Chart + Quick Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Revenue Overview</h3>
                    <p class="text-xs text-gray-400">Last 14 days</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="text-xs text-gray-500">Revenue</span>
                </div>
            </div>
            @php $revMax = max($dailyRevenue) ?: 1; @endphp
            <div class="flex items-end gap-[4px] h-44">
                @foreach($dailyRevenue as $i => $rev)
                @php $pct = min(100, ($rev / $revMax) * 100); $isToday = $i === count($dailyRevenue)-1; @endphp
                <div class="flex-1 flex flex-col items-center gap-1 group cursor-pointer" title="Day {{ $dailyLabels[$i] }}: TZS {{ number_format($rev) }}">
                    <div class="w-full bg-gray-50 rounded-t-md relative h-36 overflow-hidden">
                        <div class="absolute bottom-0 left-0 right-0 rounded-t-md transition-all duration-300 {{ $isToday ? 'bg-emerald-500' : 'bg-emerald-300 hover:bg-emerald-400' }}" style="height: {{ max($pct, 3) }}%"></div>
                    </div>
                    <span class="text-[9px] text-gray-400 font-medium">{{ $dailyLabels[$i] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Stats</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-700">Active Today</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($stats['activeToday']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gold-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-700">Total Admins</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($stats['totalAdmins']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-700">Pending</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($stats['pendingCount']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-700">Success Rate</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $stats['successRate'] }}%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Users</h3>
            <span class="text-xs font-medium text-emerald-600">{{ $stats['totalUsers'] }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                        <th class="px-5 py-2.5 font-medium">Name</th>
                        <th class="px-5 py-2.5 font-medium">Email</th>
                        <th class="px-5 py-2.5 font-medium">Phone</th>
                        <th class="px-5 py-2.5 font-medium">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $u)
                    <tr class="border-t border-gray-50 hover:bg-gray-50/30 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-[10px]">{{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}</div>
                                <span class="text-xs font-medium text-gray-900">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $u->email }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $u->phone ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $u->created_at?->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">No users yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@else
{{-- USER DASHBOARD --}}
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 right-0 w-32 h-32 bg-gold-500/10 rounded-full -mr-12 -mb-12"></div>
        <div class="relative z-10">
            <h2 class="text-xl font-bold mb-1">Karibu, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-emerald-100 text-sm">Biashara yako, Mkononi mwako. Here's your overview today.</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
            $cards = [
                ['label'=>'Total Sales','value'=>number_format($stats['totalSales']),'change'=>'+'.$stats['salesWeek'].' this week','icon'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','sub'=>'emerald-200'],
                ['label'=>'Revenue','value'=>'TZS '.$fmt($stats['totalRevenue']),'change'=>'This week: TZS '.$fmt($stats['revenueWeek']),'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'gold-400','to'=>'gold-500','border'=>'gold-300','text'=>'gold-50','sub'=>'gold-100'],
                ['label'=>'Products','value'=>number_format($stats['totalProducts']),'change'=>$stats['lowStock'].' low stock','icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','sub'=>'sky-200'],
                ['label'=>'Orders','value'=>number_format($stats['totalOrders']),'change'=>'+'.$stats['ordersWeek'].' this week','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','from'=>'violet-500','to'=>'violet-600','border'=>'violet-400','text'=>'violet-100','sub'=>'violet-200'],
            ];
        @endphp
        @foreach($cards as $i => $card)
        <div class="bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow {{ ['animate-slide','animate-slide-delay-1','animate-slide-delay-2','animate-slide-delay-3'][$i] }}">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
                    <svg class="w-4 h-4 {{ $card['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white count-up">{{ $card['value'] }}</p>
                <p class="text-[10px] {{ $card['sub'] }} font-medium mt-1">{{ $card['change'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Revenue Chart --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Sales Overview</h3>
                <p class="text-xs text-gray-400">Last 14 days</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-xs text-gray-500">Sales</span>
            </div>
        </div>
        @php $revMax = max($dailyRevenue) ?: 1; @endphp
        <div class="flex items-end gap-[4px] h-44">
            @foreach($dailyRevenue as $i => $rev)
            @php $pct = min(100, ($rev / $revMax) * 100); $isToday = $i === count($dailyRevenue)-1; @endphp
            <div class="flex-1 flex flex-col items-center gap-1 group cursor-pointer" title="Day {{ $dailyLabels[$i] }}: TZS {{ number_format($rev) }}">
                <div class="w-full bg-gray-50 rounded-t-md relative h-36 overflow-hidden">
                    <div class="absolute bottom-0 left-0 right-0 rounded-t-md transition-all duration-300 {{ $isToday ? 'bg-gold-400' : 'bg-emerald-300 hover:bg-emerald-400' }}" style="height: {{ max($pct, 3) }}%"></div>
                </div>
                <span class="text-[9px] text-gray-400 font-medium">{{ $dailyLabels[$i] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="#" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow flex flex-col items-center gap-2 group">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <span class="text-xs font-semibold text-gray-700">New Sale</span>
        </a>
        <a href="#" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow flex flex-col items-center gap-2 group">
            <div class="w-10 h-10 rounded-xl bg-gold-50 group-hover:bg-gold-100 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <span class="text-xs font-semibold text-gray-700">Add Product</span>
        </a>
        <a href="#" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow flex flex-col items-center gap-2 group">
            <div class="w-10 h-10 rounded-xl bg-sky-50 group-hover:bg-sky-100 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <span class="text-xs font-semibold text-gray-700">Reports</span>
        </a>
        <a href="#" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow flex flex-col items-center gap-2 group">
            <div class="w-10 h-10 rounded-xl bg-violet-50 group-hover:bg-violet-100 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-xs font-semibold text-gray-700">Settings</span>
        </a>
    </div>
</div>
@endif
@endsection
