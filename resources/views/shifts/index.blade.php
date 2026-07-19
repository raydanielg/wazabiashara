@extends('layouts.dashboard')

@section('title', 'Shifts')

@section('page_title', 'Shifts')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Shifts
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage staff shifts</p>
        </div>
        @if(!$activeShift)
        <button onclick="openShiftModal()" class="btn-gold font-bold px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Open Shift
        </button>
        @endif
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Total Shifts</p><p class="text-xl font-bold text-gray-800">{{ $totalShifts }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Open Shifts</p><p class="text-xl font-bold text-emerald-600">{{ $openShifts }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gold-50 grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Today's Shifts</p><p class="text-xl font-bold text-gold-600">{{ $todayShifts }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $totalVariance >= 0 ? 'bg-emerald-50' : 'bg-red-50' }} grid place-items-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $totalVariance >= 0 ? 'text-emerald-600' : 'text-red-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14v-3m6 3v-3M9 7v3m6-3v3"/></svg>
                </div>
                <div><p class="text-xs text-gray-500 font-semibold">Total Variance</p><p class="text-xl font-bold {{ $totalVariance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">TZS {{ number_format($totalVariance, 0) }}</p></div>
            </div>
        </div>
    </div>

    @if($activeShift)
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 grid place-items-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="font-bold text-emerald-700">Shift Open</p>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Float: TZS {{ number_format($activeShift->opening_float, 0) }} • Opened: {{ $activeShift->opened_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <button onclick="openCloseModal()" class="px-4 py-2 rounded-lg bg-red-500 text-white font-bold text-sm hover:bg-red-600 transition-all">Close Shift</button>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wide">
                    <tr><th class="px-4 py-3 text-left">Cashier</th><th class="px-4 py-3 text-left hidden sm:table-cell">Branch</th><th class="px-4 py-3 text-right">Float</th><th class="px-4 py-3 text-right hidden md:table-cell">Closing Cash</th><th class="px-4 py-3 text-right hidden md:table-cell">Expected</th><th class="px-4 py-3 text-right">Variance</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-left hidden sm:table-cell">Time</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($shifts as $s)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 font-semibold text-gray-700">{{ $s->user->name }}</td>
                        <td class="px-4 py-3 text-gray-500 font-medium hidden sm:table-cell">{{ $s->branch->name }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-600">TZS {{ number_format($s->opening_float, 0) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-600 hidden md:table-cell">{{ $s->closing_cash ? 'TZS ' . number_format($s->closing_cash, 0) : '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-600 hidden md:table-cell">{{ $s->expected_cash ? 'TZS ' . number_format($s->expected_cash, 0) : '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ ($s->variance ?? 0) == 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $s->variance !== null ? 'TZS ' . number_format($s->variance, 0) : '—' }}</td>
                        <td class="px-4 py-3 text-center"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $s->status === 'open' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">{{ $s->status }}</span></td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden sm:table-cell">{{ $s->opened_at->format('d/m H:i') }}{{ $s->closed_at ? ' → ' . $s->closed_at->format('H:i') : '' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-16 text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-gray-400 font-medium text-sm">No shifts found.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $shifts->links() }}</div>
    </div>
</div>

{{-- Open Shift Modal Drawer --}}
<div id="shiftOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeShiftModal()"></div>
<div id="shiftModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[380px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 grid place-items-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            Open Shift
        </h2>
        <button onclick="closeShiftModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="shiftForm" method="POST" action="{{ route('shifts.open') }}" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Opening Float (TZS) *</label>
            <input type="number" name="opening_float" required min="0" value="0" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold transition-all">
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button type="submit" form="shiftForm" class="w-full btn-gold font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Open
        </button>
    </div>
</div>

@if($activeShift)
{{-- Close Shift Modal Drawer --}}
<div id="closeOverlay" class="fixed inset-0 bg-black/40 z-50 hidden" onclick="closeCloseModal()"></div>
<div id="closeModal" class="fixed top-0 right-0 bottom-0 w-full sm:w-[380px] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-red-50 grid place-items-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            Close Shift
        </h2>
        <button onclick="closeCloseModal()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="closeForm" method="POST" action="{{ route('shifts.close', $activeShift) }}" class="flex-1 overflow-y-auto p-5 space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Closing Cash (TZS) *</label>
            <input type="number" name="closing_cash" required min="0" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Notes</label>
            <textarea name="note" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all"></textarea>
        </div>
    </form>

    <div class="p-5 border-t border-gray-100 flex-shrink-0">
        <button type="submit" form="closeForm" class="w-full bg-red-500 text-white font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 shadow-sm hover:shadow-md hover:bg-red-600 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Close Shift
        </button>
    </div>
</div>
@endif

<script>
function openShiftModal() {
    document.getElementById('shiftOverlay').classList.remove('hidden');
    document.getElementById('shiftModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}
function closeShiftModal() {
    document.getElementById('shiftOverlay').classList.add('hidden');
    document.getElementById('shiftModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}
function openCloseModal() {
    document.getElementById('closeOverlay').classList.remove('hidden');
    document.getElementById('closeModal').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}
function closeCloseModal() {
    document.getElementById('closeOverlay').classList.add('hidden');
    document.getElementById('closeModal').classList.add('translate-x-full');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeShiftModal(); closeCloseModal(); } });
</script>
@endsection
