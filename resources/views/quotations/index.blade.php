@extends('layouts.dashboard')

@section('title', 'Quotations')
@section('page_title', 'Quotations')

@section('content')
@php
$fmt = fn($n) => $n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n));
@endphp

<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border p-4">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-lg font-bold text-gray-900">{{ number_format($totalQuotations) }}</p>
            <p class="text-[10px] text-gray-500 font-medium">Total Quotations</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-lg font-bold text-gray-900">{{ number_format($pendingQuotations) }}</p>
            <p class="text-[10px] text-gray-500 font-medium">Pending</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-lg font-bold text-gray-900">{{ number_format($convertedQuotations) }}</p>
            <p class="text-[10px] text-gray-500 font-medium">Converted to Invoice</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            <p class="text-lg font-bold text-gray-900">TZS {{ $fmt($totalValue) }}</p>
            <p class="text-[10px] text-gray-500 font-medium">Total Value</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Quotations</h2>
            <p class="text-xs text-gray-500">Create and manage customer quotations</p>
        </div>
        <a href="{{ route('pos.index') }}" class="btn-gold px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Quotation
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Quotation No</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Customer</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Total</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">{{ $quotation->quotation_no }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $quotation->customer?->name ?? 'Walk-in Customer' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $quotation->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            @if($quotation->converted_at)
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-semibold">Converted</span>
                            @elseif($quotation->status === 'draft')
                            <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[10px] font-semibold">Draft</span>
                            @else
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-[10px] font-semibold">{{ ucfirst($quotation->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900">TZS {{ number_format($quotation->total, 0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('quotations.show', $quotation) }}" class="p-1.5 rounded-lg hover:bg-sky-50 text-sky-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if(!$quotation->converted_at)
                                <button onclick="convertQuotation({{ $quotation->id }})" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600 transition-colors" title="Convert to Invoice">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                @endif
                                <button onclick="deleteQuotation({{ $quotation->id }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">No quotations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $quotations->links() }}</div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

async function convertQuotation(id) {
    saConfirm({
        title: 'Convert to Invoice?',
        text: 'This quotation will be converted to an invoice and stock will be reduced.',
        icon: 'warning',
        confirmText: 'Yes, Convert',
        confirmColor: 'emerald',
        onConfirm: async () => {
            try {
                const res = await fetch(`/quotations/${id}/convert`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
                const result = await res.json();
                if (result.success) {
                    showToast('success', 'Success', result.message);
                    if (result.sale_id) setTimeout(() => window.location.href = `/pos/receipt/${result.sale_id}`, 1000);
                    else setTimeout(() => location.reload(), 800);
                } else {
                    showToast('error', 'Error', result.message || 'An error occurred');
                }
            } catch (err) {
                showToast('error', 'Error', 'Network error');
            }
        }
    });
}

async function deleteQuotation(id) {
    saConfirm({
        title: 'Delete Quotation?',
        text: 'Are you sure you want to delete this quotation?',
        icon: 'danger',
        confirmText: 'Yes, Delete',
        confirmColor: 'red',
        onConfirm: async () => {
            try {
                const res = await fetch(`/quotations/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
                const result = await res.json();
                if (result.success) {
                    showToast('success', 'Success', result.message);
                    setTimeout(() => location.reload(), 800);
                }
            } catch (err) {
                showToast('error', 'Error', 'Network error');
            }
        }
    });
}
</script>
@endsection
