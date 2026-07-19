@extends('layouts.dashboard')

@section('title', 'Interest Calculator')
@section('page_title', 'Interest Calculator')

@section('content')
<div class="max-w-4xl">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Interest Calculator
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Calculate simple or compound interest on a principal amount</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Inputs --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Interest Type</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="intType" value="simple" class="peer sr-only" checked>
                        <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-3 text-center transition-all">
                            <span class="text-xs font-semibold text-gray-700">Simple</span>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="intType" value="compound" class="peer sr-only">
                        <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-3 text-center transition-all">
                            <span class="text-xs font-semibold text-gray-700">Compound</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Principal Amount</label>
                <input type="number" id="intPrincipal" value="1000000" min="0" step="any" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Interest Rate (% per year)</label>
                <input type="number" id="intRate" value="10" min="0" step="any" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Time (years)</label>
                <input type="number" id="intTime" value="1" min="0" step="any" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">
            </div>
            <div id="compoundFreqWrap" class="hidden">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Compounded</label>
                <select id="intFreq" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white">
                    <option value="1">Annually</option>
                    <option value="2">Semi-Annually</option>
                    <option value="4">Quarterly</option>
                    <option value="12" selected>Monthly</option>
                </select>
            </div>
        </div>

        {{-- Results --}}
        <div class="bg-emerald-900 rounded-2xl shadow-cardlg p-6 text-white space-y-5">
            <div class="text-center">
                <p class="text-emerald-200/70 text-xs font-semibold uppercase tracking-wide">Interest Amount</p>
                <p id="intInterest" class="text-3xl font-extrabold text-gold-400 mt-1">TSh 0</p>
            </div>
            <div class="pt-3 border-t border-emerald-800/50 text-center">
                <p class="text-emerald-200/70 text-[11px] font-semibold uppercase tracking-wide">Total Amount (Principal + Interest)</p>
                <p id="intTotal" class="text-lg font-bold mt-1">TSh 0</p>
            </div>
        </div>
    </div>
</div>

<script>
function fmt(n) {
    if (!isFinite(n)) n = 0;
    return 'TSh ' + n.toLocaleString('en-US', { maximumFractionDigits: 2, minimumFractionDigits: 0 });
}

function calcInterest() {
    const type = document.querySelector('input[name="intType"]:checked').value;
    const P = parseFloat(document.getElementById('intPrincipal').value) || 0;
    const rate = (parseFloat(document.getElementById('intRate').value) || 0) / 100;
    const t = parseFloat(document.getElementById('intTime').value) || 0;

    document.getElementById('compoundFreqWrap').classList.toggle('hidden', type !== 'compound');

    let interest = 0;
    let total = P;

    if (type === 'simple') {
        interest = P * rate * t;
        total = P + interest;
    } else {
        const n = parseFloat(document.getElementById('intFreq').value) || 1;
        total = P * Math.pow(1 + (rate / n), n * t);
        interest = total - P;
    }

    document.getElementById('intInterest').textContent = fmt(interest);
    document.getElementById('intTotal').textContent = fmt(total);
}

document.querySelectorAll('input[name="intType"]').forEach(el => el.addEventListener('change', calcInterest));
['intPrincipal', 'intRate', 'intTime', 'intFreq'].forEach(id => {
    document.getElementById(id).addEventListener('input', calcInterest);
    document.getElementById(id).addEventListener('change', calcInterest);
});

calcInterest();
</script>
@endsection
