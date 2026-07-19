@extends('layouts.dashboard')

@section('title', 'EMI Calculator')
@section('page_title', 'EMI Calculator')

@section('content')
<div class="max-w-4xl">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3v-6m-3 6v-1m12-8V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-1"/></svg>
            EMI Calculator
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Estimate your monthly loan installment (Equated Monthly Installment)</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Inputs --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-card p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Loan Amount</label>
                <input type="number" id="emiAmount" value="1000000" min="0" step="any" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Interest Rate (% per year)</label>
                <input type="number" id="emiRate" value="12" min="0" step="any" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Loan Tenure</label>
                <div class="flex gap-2">
                    <input type="number" id="emiTenure" value="12" min="1" step="1" class="flex-1 px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium">
                    <select id="emiTenureUnit" class="px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white">
                        <option value="months">Months</option>
                        <option value="years">Years</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Results --}}
        <div class="bg-emerald-900 rounded-2xl shadow-cardlg p-6 text-white space-y-5">
            <div class="text-center">
                <p class="text-emerald-200/70 text-xs font-semibold uppercase tracking-wide">Monthly EMI</p>
                <p id="emiMonthly" class="text-3xl font-extrabold text-gold-400 mt-1">TSh 0</p>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-3 border-t border-emerald-800/50">
                <div>
                    <p class="text-emerald-200/70 text-[11px] font-semibold uppercase tracking-wide">Total Interest</p>
                    <p id="emiInterest" class="text-lg font-bold mt-1">TSh 0</p>
                </div>
                <div>
                    <p class="text-emerald-200/70 text-[11px] font-semibold uppercase tracking-wide">Total Payment</p>
                    <p id="emiTotal" class="text-lg font-bold mt-1">TSh 0</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fmt(n) {
    if (!isFinite(n)) n = 0;
    return 'TSh ' + n.toLocaleString('en-US', { maximumFractionDigits: 2, minimumFractionDigits: 0 });
}

function calcEmi() {
    const P = parseFloat(document.getElementById('emiAmount').value) || 0;
    const annualRate = parseFloat(document.getElementById('emiRate').value) || 0;
    let tenure = parseFloat(document.getElementById('emiTenure').value) || 0;
    const unit = document.getElementById('emiTenureUnit').value;
    const n = unit === 'years' ? tenure * 12 : tenure;
    const r = (annualRate / 12) / 100;

    let emi = 0;
    if (n > 0) {
        if (r === 0) {
            emi = P / n;
        } else {
            const factor = Math.pow(1 + r, n);
            emi = (P * r * factor) / (factor - 1);
        }
    }

    const totalPayment = emi * n;
    const totalInterest = totalPayment - P;

    document.getElementById('emiMonthly').textContent = fmt(emi);
    document.getElementById('emiInterest').textContent = fmt(totalInterest);
    document.getElementById('emiTotal').textContent = fmt(totalPayment);
}

['emiAmount', 'emiRate', 'emiTenure', 'emiTenureUnit'].forEach(id => {
    document.getElementById(id).addEventListener('input', calcEmi);
    document.getElementById(id).addEventListener('change', calcEmi);
});

calcEmi();
</script>
@endsection
