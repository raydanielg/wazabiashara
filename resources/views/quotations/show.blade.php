@extends('layouts.dashboard')

@section('title', 'Quotation')
@section('page_title', 'Quotation Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Quotation {{ $quotation->quotation_no }}</h2>
            <p class="text-xs text-gray-500">Imetengenezwa {{ $quotation->created_at->format('d/m/Y') }}</p>
        </div>
        <div class="flex gap-2">
            @if(!$quotation->converted_at)
            <button onclick="convertQuotation({{ $quotation->id }})" class="btn-gold px-4 py-2 rounded-lg text-sm font-semibold">Geuza Invoice</button>
            @endif
            <button onclick="window.print()" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">Print</button>
            <a href="{{ route('quotations.index') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">Rudi</a>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-start justify-between mb-6 pb-6 border-b">
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $quotation->business->name }}</h3>
                <p class="text-xs text-gray-500">{{ $quotation->business->phone }} • {{ $quotation->business->email }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-gray-900">QUOTATION</p>
                <p class="text-xs text-gray-500">{{ $quotation->quotation_no }}</p>
                <p class="text-xs text-gray-500">Tarehe: {{ $quotation->created_at->format('d/m/Y') }}</p>
                @if($quotation->valid_until)
                <p class="text-xs text-gray-500">Hadi: {{ $quotation->valid_until->format('d/m/Y') }}</p>
                @endif
            </div>
        </div>

        <div class="mb-6">
            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Kwa</p>
            <p class="text-sm font-semibold text-gray-900">{{ $quotation->customer?->name ?? 'Mteja wa Kawaida' }}</p>
            <p class="text-xs text-gray-500">{{ $quotation->customer?->phone ?? '' }}</p>
            <p class="text-xs text-gray-500">{{ $quotation->customer?->address ?? '' }}</p>
        </div>

        <table class="w-full mb-6">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left px-4 py-2 text-[10px] font-semibold text-gray-500 uppercase">Bidhaa</th>
                    <th class="text-center px-4 py-2 text-[10px] font-semibold text-gray-500 uppercase">Qty</th>
                    <th class="text-right px-4 py-2 text-[10px] font-semibold text-gray-500 uppercase">Bei</th>
                    <th class="text-right px-4 py-2 text-[10px] font-semibold text-gray-500 uppercase">Jumla</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                <tr class="border-b">
                    <td class="px-4 py-3 text-xs text-gray-900">{{ $item->name }}</td>
                    <td class="px-4 py-3 text-center text-xs text-gray-600">{{ $item->qty }}</td>
                    <td class="px-4 py-3 text-right text-xs text-gray-600">TZS {{ number_format($item->price, 0) }}</td>
                    <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900">TZS {{ number_format($item->subtotal, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-64 space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Subtotal:</span>
                    <span class="font-semibold text-gray-900">TZS {{ number_format($quotation->subtotal, 0) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Punguzo:</span>
                    <span class="font-semibold text-gray-900">TZS {{ number_format($quotation->discount, 0) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">VAT:</span>
                    <span class="font-semibold text-gray-900">TZS {{ number_format($quotation->vat, 0) }}</span>
                </div>
                <div class="flex justify-between text-sm pt-2 border-t">
                    <span class="font-bold text-gray-900">Jumla:</span>
                    <span class="font-bold text-emerald-600">TZS {{ number_format($quotation->total, 0) }}</span>
                </div>
            </div>
        </div>

        @if($quotation->notes)
        <div class="mt-6 pt-6 border-t">
            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Maelezo</p>
            <p class="text-xs text-gray-600">{{ $quotation->notes }}</p>
        </div>
        @endif
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
async function convertQuotation(id) {
    Swal.fire({
        title: 'Geuza Invoice?', text: 'Quotation itageuzwa kuwa invoice.', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#024938', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ndiyo, Geuza', cancelButtonText: 'Ghairi'
    }).then(async (r) => {
        if (r.isConfirmed) {
            const res = await fetch(`/quotations/${id}/convert`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
            const result = await res.json();
            if (result.success) {
                Toastify({ text: result.message, duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#024938' } }).showToast();
                if (result.sale_id) setTimeout(() => window.location.href = `/pos/receipt/${result.sale_id}`, 1000);
            } else {
                Toastify({ text: result.message || 'Hitilafu', duration: 3000, gravity: 'bottom', position: 'right', style: { background: '#ef4444' } }).showToast();
            }
        }
    });
}
</script>
@endsection
