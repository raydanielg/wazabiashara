@extends('layouts.dashboard')

@section('title', 'Tax Calculator')
@section('page_title', 'Tax Calculator')

@section('content')
<div class="max-w-4xl">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3v-6m-3 6v-1m12-8V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-1"/></svg>
            Tax (VAT) Calculator
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Quickly work out VAT-inclusive or exclusive amounts, default 18% Tanzania VAT</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Inputs --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Amount Type</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="taxType" value="exclusive" class="peer sr-only" checked>
                        <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-3 text-center transition-all">
                            <span class="text-xs font-semibold text-gray-700">Tax Exclusive</span>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="taxType" value="inclusive" class="peer sr-only">
                        <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-3 text-center transition-all">
                            <span class="text-xs font-semibold text-gray-700">Tax Inclusive</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Amount</label>
                <input type="number" id="taxAmount" value="100000" min="0" step="any" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tax Rate (%)</label>
                <input type="number" id="taxRate" value="18" min="0" step="any" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">
                <p class="text-[11px] text-gray-400 mt-1">Default 18% is the standard Tanzania VAT rate.</p>
            </div>
        </div>

        {{-- Results --}}
        <div class="bg-emerald-900 rounded-2xl shadow-cardlg p-6 text-white space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-emerald-200/70 text-[11px] font-semibold uppercase tracking-wide">Net Amount</p>
                    <p id="taxNet" class="text-lg font-bold mt-1">TSh 0</p>
                </div>
                <div class="text-center">
                    <p class="text-emerald-200/70 text-[11px] font-semibold uppercase tracking-wide">Tax Amount</p>
                    <p id="taxAmountOut" class="text-lg font-bold text-gold-400 mt-1">TSh 0</p>
                </div>
            </div>
            <div class="pt-3 border-t border-emerald-800/50 text-center">
                <p class="text-emerald-200/70 text-xs font-semibold uppercase tracking-wide">Total (Gross) Amount</p>
                <p id="taxTotal" class="text-3xl font-extrabold text-gold-400 mt-1">TSh 0</p>
            </div>
        </div>
    </div>
</div>

<script>
function fmt(n) {
    if (!isFinite(n)) n = 0;
    return 'TSh ' + n.toLocaleString('en-US', { maximumFractionDigits: 2, minimumFractionDigits: 0 });
}

function calcTax() {
    const type = document.querySelector('input[name="taxType"]:checked').value;
    const amount = parseFloat(document.getElementById('taxAmount').value) || 0;
    const rate = (parseFloat(document.getElementById('taxRate').value) || 0) / 100;

    let net, tax, gross;

    if (type === 'exclusive') {
        net = amount;
        tax = net * rate;
        gross = net + tax;
    } else {
        gross = amount;
        net = rate > -1 ? gross / (1 + rate) : gross;
        tax = gross - net;
    }

    document.getElementById('taxNet').textContent = fmt(net);
    document.getElementById('taxAmountOut').textContent = fmt(tax);
    document.getElementById('taxTotal').textContent = fmt(gross);
}

document.querySelectorAll('input[name="taxType"]').forEach(el => el.addEventListener('change', calcTax));
['taxAmount', 'taxRate'].forEach(id => {
    document.getElementById(id).addEventListener('input', calcTax);
    document.getElementById(id).addEventListener('change', calcTax);
});

calcTax();
</script>
@endsection
