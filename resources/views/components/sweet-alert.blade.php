{{-- Sweet Alert Component --}}
<div id="sa-overlay" class="hidden fixed inset-0 z-[70] items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="saClose()"></div>
    <div id="sa-box" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform scale-95 opacity-0 transition-all duration-200">
        <div class="p-6 text-center">
            <div id="sa-icon" class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 id="sa-title" class="text-base font-bold text-gray-900 mb-1">Are you sure?</h3>
            <p id="sa-text" class="text-sm text-gray-500 mb-5">This action cannot be undone.</p>
            <div class="flex gap-2 justify-center">
                <button onclick="saClose()" class="px-4 py-2 border rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</button>
                <button id="sa-confirm" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700 transition-colors">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
let saCallback = null;
function saConfirm({title='Are you sure?', text='This action cannot be undone.', icon='warning', confirmText='Confirm', confirmColor='emerald', onConfirm}) {
    const overlay = document.getElementById('sa-overlay');
    const box = document.getElementById('sa-box');
    const iconBox = document.getElementById('sa-icon');
    const confirmBtn = document.getElementById('sa-confirm');

    document.getElementById('sa-title').textContent = title;
    document.getElementById('sa-text').textContent = text;
    confirmBtn.textContent = confirmText;

    const colors = {
        warning: {bg:'bg-amber-100',icon:'text-amber-600',path:'M12 9v2m0 4h.01m6.938-5.345A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'},
        danger: {bg:'bg-red-100',icon:'text-red-600',path:'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'},
        success: {bg:'bg-emerald-100',icon:'text-emerald-600',path:'M5 13l4 4L19 7'},
        info: {bg:'bg-blue-100',icon:'text-blue-600',path:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}
    };
    const c = colors[icon] || colors.warning;
    iconBox.className = `w-14 h-14 rounded-full ${c.bg} flex items-center justify-center mx-auto mb-4`;
    iconBox.innerHTML = `<svg class="w-7 h-7 ${c.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${c.path}"/></svg>`;

    const btnColors = {emerald:'bg-emerald-600 hover:bg-emerald-700', red:'bg-red-600 hover:bg-red-700', amber:'bg-amber-600 hover:bg-amber-700'};
    confirmBtn.className = `px-4 py-2 ${btnColors[confirmColor] || btnColors.emerald} text-white rounded-xl text-sm font-bold transition-colors`;

    saCallback = onConfirm;
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    setTimeout(() => { box.classList.remove('scale-95','opacity-0'); box.classList.add('scale-100','opacity-100'); }, 10);
}
function saClose() {
    const overlay = document.getElementById('sa-overlay');
    const box = document.getElementById('sa-box');
    box.classList.remove('scale-100','opacity-100');
    box.classList.add('scale-95','opacity-0');
    setTimeout(() => { overlay.classList.add('hidden'); overlay.classList.remove('flex'); saCallback = null; }, 200);
}
document.getElementById('sa-confirm').addEventListener('click', function() {
    if (saCallback) saCallback();
    saClose();
});
</script>
