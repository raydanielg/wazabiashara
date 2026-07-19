@extends('layouts.dashboard')

@section('title', 'Receipt')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-2xl shadow-cardlg p-8 mt-6">
    <div class="text-center border-b-2 border-dashed border-gray-200 pb-4">
        @if(auth()->user()->business->logo)
        <img src="{{ asset('storage/' . auth()->user()->business->logo) }}" alt="Logo" class="h-16 w-16 mx-auto rounded-xl object-contain mb-2">
        @else
        <div class="h-16 w-16 mx-auto rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-gold-400 font-black text-3xl grid place-items-center mb-2">W</div>
        @endif
        <h2 class="font-black text-xl text-emerald-700">{{ $sale->branch->business->name ?? config('app.name') }}</h2>
        <p class="text-xs font-bold text-gray-500">{{ $sale->branch->name ?? '' }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
        <p class="text-xs font-bold text-gray-600 mt-1">Receipt: {{ $sale->receipt_no }}</p>
        <p class="text-xs text-gray-400">Salesperson: {{ $sale->user->name ?? 'N/A' }}</p>
    </div>

    <div class="py-4 border-b-2 border-dashed border-gray-200">
        @foreach($sale->items as $item)
        <div class="flex justify-between text-sm py-1">
            <div>
                <span class="font-bold text-gray-700">{{ $item->qty }} × {{ $item->product->name }}</span>
                <span class="text-gray-400 text-xs block">TZS {{ number_format($item->price, 0) }} each</span>
            </div>
            <span class="font-bold text-gray-700">TZS {{ number_format($item->subtotal, 0) }}</span>
        </div>
        @endforeach
    </div>

    <div class="py-3 space-y-1.5 border-b-2 border-dashed border-gray-200">
        <div class="flex justify-between text-sm font-bold text-gray-500"><span>Subtotal:</span><span>TZS {{ number_format($sale->subtotal, 0) }}</span></div>
        @if($sale->discount > 0)
        <div class="flex justify-between text-sm font-bold text-red-500"><span>Discount:</span><span>-TZS {{ number_format($sale->discount, 0) }}</span></div>
        @endif
        @if($sale->vat > 0)
        <div class="flex justify-between text-sm font-bold text-gray-500"><span>VAT:</span><span>TZS {{ number_format($sale->vat, 0) }}</span></div>
        @endif
        <div class="flex justify-between text-lg font-black text-emerald-700"><span>TOTAL:</span><span>TZS {{ number_format($sale->total, 0) }}</span></div>
        <div class="flex justify-between text-sm font-bold text-gray-500"><span>Payment ({{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}):</span><span>TZS {{ number_format($sale->paid, 0) }}</span></div>
        @if($sale->change > 0)
        <div class="flex justify-between text-sm font-bold text-gold-600"><span>Change:</span><span>TZS {{ number_format($sale->change, 0) }}</span></div>
        @endif
    </div>

    @if($sale->customer)
    <div class="py-2 text-center text-xs font-bold text-gray-500">Customer: {{ $sale->customer->name }}</div>
    @endif

    <div class="text-center pt-4">
        <p class="text-xs font-bold text-gray-400">Thank you for using Wazabiashara! 🇹🇿</p>
        <p class="text-[10px] text-gray-300 mt-1">Your Business, In Your Hands</p>
    </div>

    <button onclick="window.print()" class="mt-4 w-full btn-gold font-black py-3 rounded-xl">🖨️ Print</button>
</div>

<style media="print">
body * { visibility: hidden; }
.max-w-md, .max-w-md * { visibility: visible; }
.max-w-md { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; }
button { display: none !important; }
</style>
@endsection
