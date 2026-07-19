@extends('layouts.dashboard')

@section('title', 'Returns')
@section('page_title', 'Returns Management')

@section('content')
@php
$fmt = fn($n) => $n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n));
@endphp

<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl border border-red-400 p-4 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-red-100 uppercase">Sale Returns</span>
                <p class="text-xl font-bold mt-1">TZS {{ $fmt($totalSaleReturns) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-amber-50 uppercase">Purchase Returns</span>
                <p class="text-xl font-bold mt-1">TZS {{ $fmt($totalPurchaseReturns) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-lg font-bold text-gray-900">{{ number_format($pendingReturns) }}</p>
            <p class="text-[10px] text-gray-500 font-medium">Pending</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b">
        <button onclick="switchTab('sale')" id="tab-sale" class="px-4 py-2 text-sm font-semibold border-b-2 border-emerald-600 text-emerald-600">Sale Returns</button>
        <button onclick="switchTab('purchase')" id="tab-purchase" class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-500">Purchase Returns</button>
    </div>

    {{-- Sale Returns Tab --}}
    <div id="tab-sale-content" class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Reference</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Customer</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Item</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Reason</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saleReturns as $return)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">{{ $return->reference ?? 'SR-' . $return->id }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $return->sale?->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $return->product?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $return->reason ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-red-600">TZS {{ number_format($return->total ?? $return->amount, 0) }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-semibold">{{ ucfirst($return->status ?? 'approved') }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">No sale returns found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $saleReturns->links() }}</div>
    </div>

    {{-- Purchase Returns Tab --}}
    <div id="tab-purchase-content" class="bg-white rounded-xl border overflow-hidden hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Reference</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Supplier</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Reason</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseReturns as $return)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">{{ $return->reference }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $return->supplier?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $return->reason ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-amber-600">TZS {{ number_format($return->total, 0) }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-semibold">{{ ucfirst($return->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">No purchase returns found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $purchaseReturns->links() }}</div>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('tab-sale-content').classList.toggle('hidden', tab !== 'sale');
    document.getElementById('tab-purchase-content').classList.toggle('hidden', tab !== 'purchase');
    document.getElementById('tab-sale').classList.toggle('border-emerald-600', tab === 'sale');
    document.getElementById('tab-sale').classList.toggle('text-emerald-600', tab === 'sale');
    document.getElementById('tab-sale').classList.toggle('border-transparent', tab !== 'sale');
    document.getElementById('tab-sale').classList.toggle('text-gray-500', tab !== 'sale');
    document.getElementById('tab-purchase').classList.toggle('border-emerald-600', tab === 'purchase');
    document.getElementById('tab-purchase').classList.toggle('text-emerald-600', tab === 'purchase');
    document.getElementById('tab-purchase').classList.toggle('border-transparent', tab !== 'purchase');
    document.getElementById('tab-purchase').classList.toggle('text-gray-500', tab !== 'purchase');
}
</script>
@endsection
