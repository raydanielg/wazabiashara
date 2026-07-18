@extends('layouts.dashboard')

@section('title', 'POS — Mauzo')

@section('content')
<div class="flex h-[calc(100vh-4rem)] overflow-hidden">
    <!-- Left: Products -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Search & filters -->
        <div class="p-4 bg-white border-b border-gray-100">
            <div class="flex gap-3 items-center">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="posSearch" type="text" placeholder="Tafuta bidhaa kwa jina au barcode..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm">
                </div>
                <select id="posCategory" class="px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-emerald-400 outline-none font-semibold text-sm bg-white">
                    <option value="">Kategoria Zote</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            @if($activeShift)
            <div class="mt-2 flex items-center gap-2 text-xs font-bold text-emerald-600">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Zamu wazi — Fungu la kuanzia: TZS {{ number_format($activeShift->opening_float, 0) }}
            </div>
            @else
            <div class="mt-2 flex items-center gap-2 text-xs font-bold text-red-500">
                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                Huna zamu wazi. Fungua zamu kuanza kuuza.
                <a href="{{ route('shifts.index') }}" class="text-emerald-600 underline">Fungua hapa</a>
            </div>
            @endif
        </div>

        <!-- Products grid -->
        <div id="productsGrid" class="flex-1 overflow-y-auto p-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            @foreach($products as $product)
            @php $stock = $product->branchStock->first(); $qty = $stock?->qty ?? 0; @endphp
            <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $qty }})"
                    class="product-card group relative bg-white rounded-xl border-2 border-gray-100 p-3 text-left hover:border-emerald-400 hover:shadow-card transition-all {{ $qty <= 0 ? 'opacity-50' : '' }}"
                    data-category="{{ $product->category_id }}"
                    data-name="{{ strtolower($product->name) }}"
                    data-barcode="{{ strtolower($product->barcode ?? '') }}">
                <div class="aspect-square rounded-lg bg-emerald-50 mb-2 grid place-items-center overflow-hidden">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                    <svg class="w-10 h-10 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @endif
                </div>
                <p class="font-bold text-xs text-gray-700 line-clamp-2 leading-tight">{{ $product->name }}</p>
                <p class="mt-1 font-black text-sm text-emerald-600">TZS {{ number_format($product->selling_price, 0) }}</p>
                <span class="absolute top-2 right-2 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $qty > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">{{ $qty > 0 ? $qty . ' ' . $product->unit : 'Hakuna' }}</span>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="w-[380px] bg-white border-l border-gray-100 flex flex-col overflow-hidden">
        <!-- Cart header -->
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-black text-lg text-emerald-700">🛒 Kikapu</h3>
            <button onclick="clearCart()" class="text-xs font-bold text-red-500 hover:text-red-700">Futa Vyote</button>
        </div>

        <!-- Customer -->
        <div class="p-3 border-b border-gray-100">
            <select id="customerId" class="w-full px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-semibold">
                <option value="">Mteja wa Kawaida</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->phone ?? 'N/A' }}</option>
                @endforeach
            </select>
        </div>

        <!-- Cart items -->
        <div id="cartItems" class="flex-1 overflow-y-auto p-3 space-y-2">
            <div id="emptyCart" class="text-center py-12 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <p class="font-bold text-sm">Bofya bidhaa kuanza</p>
            </div>
        </div>

        <!-- Totals -->
        <div class="p-4 border-t border-gray-100 space-y-2">
            <div class="flex justify-between text-sm font-bold text-gray-500">
                <span>Jumla:</span>
                <span id="cartSubtotal">TZS 0</span>
            </div>
            <div class="flex justify-between text-sm font-bold text-gray-500">
                <span>Punguzo:</span>
                <span><input id="discountInput" type="number" value="0" min="0" onchange="updateTotals()" class="w-20 text-right px-2 py-1 rounded border border-gray-200 text-sm"></span>
            </div>
            <div class="flex justify-between text-lg font-black text-emerald-700 border-t border-gray-100 pt-2">
                <span>Jumla ya Kulipa:</span>
                <span id="cartTotal">TZS 0</span>
            </div>

            <!-- Payment -->
            <div>
                <label class="text-xs font-bold text-gray-500">Njia ya Malipo</label>
                <select id="paymentMethod" class="w-full mt-1 px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-semibold">
                    <option value="cash">💵 Taslimu</option>
                    <option value="mpesa">📱 M-Pesa</option>
                    <option value="tigo_pesa">📲 Tigo Pesa</option>
                    <option value="airtel_money">📶 Airtel Money</option>
                    <option value="halopesa">💳 Halopesa</option>
                    <option value="bank">🏦 Benki/Kadi</option>
                    <option value="credit">📒 Deni (Credit)</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500">Amount Paid</label>
                <input id="paidInput" type="number" value="0" min="0" oninput="updateChange()" class="w-full mt-1 px-3 py-2 rounded-lg border-2 border-gray-100 focus:border-emerald-400 outline-none text-sm font-bold">
            </div>
            <div class="flex justify-between text-sm font-bold text-gold-600">
                <span>Change:</span>
                <span id="changeDisplay">TZS 0</span>
            </div>

            <button id="checkoutBtn" onclick="checkout()"
                    class="w-full btn-gold font-black py-3.5 rounded-xl text-base disabled:opacity-50 disabled:cursor-not-allowed">
                💰 Maliza Muuzo
            </button>
        </div>
    </div>
</div>

<script>
let cart = [];

function addToCart(id, name, price, stock) {
    if (stock <= 0) { Swal.fire({icon:'warning',title:'Bidhaa imeishi',text:'Samahani, bidhaa hii haipo kwenye stoo.',confirmButtonColor:'#024938'}); return; }
    const existing = cart.find(i => i.product_id === id);
    if (existing) {
        if (existing.qty >= stock) { Swal.fire({icon:'warning',title:'Stoo haiitosi',text:'Umefikia idadi ya stoo.',confirmButtonColor:'#024938'}); return; }
        existing.qty++;
    } else {
        cart.push({ product_id: id, name, price, qty: 1, stock });
    }
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const empty = document.getElementById('emptyCart');
    if (cart.length === 0) { container.innerHTML = ''; container.appendChild(empty); empty.style.display='block'; updateTotals(); return; }
    empty.style.display = 'none';
    container.innerHTML = cart.map((item, i) => `
        <div class="flex items-center gap-2 bg-emerald-50/50 rounded-lg p-2.5 border border-emerald-100">
            <div class="flex-1">
                <p class="font-bold text-xs text-gray-700 leading-tight">${item.name}</p>
                <p class="text-xs text-emerald-600 font-bold">TZS ${formatNum(item.price)}</p>
            </div>
            <div class="flex items-center gap-1">
                <button onclick="changeQty(${i}, -1)" class="w-7 h-7 rounded-lg bg-white border border-gray-200 font-black text-gray-600 hover:bg-red-50 hover:border-red-200">−</button>
                <span class="w-8 text-center font-black text-sm">${item.qty}</span>
                <button onclick="changeQty(${i}, 1)" class="w-7 h-7 rounded-lg bg-white border border-gray-200 font-black text-gray-600 hover:bg-emerald-50 hover:border-emerald-200">+</button>
            </div>
            <button onclick="removeItem(${i})" class="text-red-400 hover:text-red-600 ml-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    `).join('');
    updateTotals();
}

function changeQty(i, delta) {
    cart[i].qty += delta;
    if (cart[i].qty <= 0) { cart.splice(i, 1); }
    if (cart[i] && cart[i].qty > cart[i].stock) { cart[i].qty = cart[i].stock; }
    renderCart();
}

function removeItem(i) { cart.splice(i, 1); renderCart(); }
function clearCart() { cart = []; renderCart(); }

function formatNum(n) { return n.toLocaleString('sw-TZ'); }

function updateTotals() {
    const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = subtotal - discount;
    document.getElementById('cartSubtotal').textContent = 'TZS ' + formatNum(subtotal);
    document.getElementById('cartTotal').textContent = 'TZS ' + formatNum(Math.max(0, total));
    document.getElementById('paidInput').value = Math.max(0, total);
    updateChange();
}

function updateChange() {
    const total = parseFloat(document.getElementById('cartTotal').textContent.replace(/[^0-9]/g, '')) || 0;
    const paid = parseFloat(document.getElementById('paidInput').value) || 0;
    document.getElementById('changeDisplay').textContent = 'TZS ' + formatNum(Math.max(0, paid - total));
}

async function checkout() {
    if (cart.length === 0) { Swal.fire({icon:'warning',title:'Kikapu tupu',text:'Ongeza bidhaa kwanza.',confirmButtonColor:'#024938'}); return; }
    const btn = document.getElementById('checkoutBtn');
    btn.disabled = true; btn.textContent = 'Inashughulikia...';

    try {
        const res = await fetch('{{ route("pos.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                items: cart,
                customer_id: document.getElementById('customerId').value || null,
                payment_method: document.getElementById('paymentMethod').value,
                discount: parseFloat(document.getElementById('discountInput').value) || 0,
                paid: parseFloat(document.getElementById('paidInput').value) || 0,
            })
        });
        const data = await res.json();
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Muuzo Umekamilika! 🎉',
                html: `Risiti: <b>${data.receipt_no}</b><br>Jumla: <b>TZS ${formatNum(data.total)}</b><br>Change: <b>TZS ${formatNum(data.change)}</b>`,
                confirmButtonText: 'Chapisha Risiti',
                showCancelButton: true,
                cancelButtonText: 'Funga',
                confirmButtonColor: '#024938'
            }).then(r => {
                if (r.isConfirmed) {
                    window.open('{{ url("/pos/receipt") }}/' + data.sale_id, '_blank');
                }
                clearCart();
                location.reload();
            });
        } else {
            Swal.fire({icon:'error', title:'Hitilafu!', text:data.message, confirmButtonColor:'#024938'});
        }
    } catch(e) {
        Swal.fire({icon:'error', title:'Tatizo la Mtandao', text:'Jaribu tena.', confirmButtonColor:'#024938'});
    } finally {
        btn.disabled = false; btn.textContent = '💰 Maliza Muuzo';
    }
}

// Search
document.getElementById('posSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.dataset.name || '';
        const barcode = card.dataset.barcode || '';
        card.style.display = (name.includes(q) || barcode.includes(q)) ? '' : 'none';
    });
});

// Category filter
document.getElementById('posCategory').addEventListener('change', function() {
    const cat = this.value;
    document.querySelectorAll('.product-card').forEach(card => {
        card.style.display = (!cat || card.dataset.category === cat) ? '' : 'none';
    });
});
</script>
@endsection
