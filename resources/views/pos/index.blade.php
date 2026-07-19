@extends('layouts.dashboard')

@section('title', 'POS — Sales')

@section('content')
<div class="flex h-[calc(100vh-4rem)] overflow-hidden relative">
    <!-- Left: Products -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Search & filters -->
        <div class="p-3 sm:p-4 bg-white border-b border-gray-100">
            <div class="flex gap-2 sm:gap-3 items-center">
                <div class="relative flex-1">
                    <svg class="absolute left-3 sm:left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="posSearch" type="text" placeholder="Search products..."
                           class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium transition-all">
                </div>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <select id="posCategory" class="pl-9 pr-6 sm:pr-8 py-2 sm:py-2.5 rounded-xl border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium bg-white appearance-none cursor-pointer transition-all">
                        <option value="">All</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if($activeShift)
            <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-emerald-600">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Shift open — TZS {{ number_format($activeShift->opening_float, 0) }}
            </div>
            @else
            <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-red-500">
                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                No active shift.
                <a href="{{ route('shifts.index') }}" class="text-emerald-600 underline font-bold">Open here</a>
            </div>
            @endif
        </div>

        <!-- Products grid -->
        <div id="productsGrid" class="flex-1 overflow-y-auto p-3 sm:p-4 pb-20 lg:pb-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 content-start">
            @foreach($products as $product)
            @php $stock = $product->branchStock->first(); $qty = $stock?->qty ?? 0; @endphp
            <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $qty }})"
                    class="product-card group relative bg-white rounded-xl border border-gray-200 p-2 sm:p-3 text-left hover:border-emerald-400 hover:shadow-md transition-all {{ $qty <= 0 ? 'opacity-50' : '' }}"
                    data-category="{{ $product->category_id }}"
                    data-name="{{ strtolower($product->name) }}"
                    data-barcode="{{ strtolower($product->barcode ?? '') }}">
                <div class="aspect-square rounded-lg bg-gray-50 mb-2 grid place-items-center overflow-hidden">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-300 group-hover:text-emerald-300 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @endif
                </div>
                <p class="font-semibold text-[11px] sm:text-xs text-gray-700 line-clamp-2 leading-tight">{{ $product->name }}</p>
                <p class="mt-1 font-bold text-xs sm:text-sm text-emerald-600">TZS {{ number_format($product->selling_price, 0) }}</p>
                <span class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 text-[9px] sm:text-[10px] font-bold px-1.5 sm:px-2 py-0.5 rounded-full {{ $qty > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">{{ $qty > 0 ? $qty . ' ' . $product->unit : 'Out' }}</span>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Mobile Cart Overlay -->
    <div id="cartOverlay" class="fixed inset-0 bg-black/40 z-40 lg:hidden hidden" onclick="toggleCart()"></div>

    <!-- Right: Cart (Desktop sidebar + Mobile bottom drawer) -->
    <div id="cartPanel" class="w-full sm:w-[380px] bg-white border-l border-gray-100 flex flex-col overflow-hidden fixed lg:relative bottom-0 left-0 right-0 z-50 lg:z-auto h-[85vh] lg:h-auto transform translate-y-full lg:translate-y-0 transition-transform duration-300 ease-out rounded-t-2xl lg:rounded-none">
        <!-- Mobile drag handle -->
        <div class="lg:hidden flex justify-center pt-2 pb-1 cursor-pointer" onclick="toggleCart()">
            <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
        </div>

        <!-- Cart header -->
        <div class="p-3 sm:p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Cart
                <span id="cartCount" class="text-[10px] font-bold text-white bg-emerald-500 rounded-full px-2 py-0.5">0</span>
            </h3>
            <div class="flex items-center gap-3">
                <button onclick="clearCart()" class="text-xs font-semibold text-red-500 hover:text-red-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear
                </button>
                <button onclick="toggleCart()" class="lg:hidden text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Customer -->
        <div class="p-3 border-b border-gray-100">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <select id="customerId" class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium appearance-none cursor-pointer transition-all">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->phone ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Cart items -->
        <div id="cartItems" class="flex-1 overflow-y-auto p-3 space-y-2">
            <div id="emptyCart" class="text-center py-12 text-gray-400">
                <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <p class="text-sm font-medium">Tap products to start</p>
            </div>
        </div>

        <!-- Totals + Payment -->
        <div class="border-t border-gray-100">
            <div class="px-4 pt-3 space-y-1.5">
                <div class="flex justify-between text-xs font-semibold text-gray-500">
                    <span>Subtotal:</span>
                    <span id="cartSubtotal">TZS 0</span>
                </div>
                <div class="flex justify-between text-xs font-semibold text-gray-500 items-center">
                    <span>Discount:</span>
                    <input id="discountInput" type="number" value="0" min="0" onchange="updateTotals()" class="w-24 text-right px-2 py-1 rounded-lg border border-gray-200 text-xs font-semibold focus:border-emerald-400 focus:ring-1 focus:ring-emerald-100 outline-none">
                </div>
                <div class="flex justify-between text-base font-bold text-emerald-700 border-t border-gray-100 pt-2">
                    <span>Total to Pay:</span>
                    <span id="cartTotal">TZS 0</span>
                </div>
            </div>

            <div class="px-4 py-3 space-y-2">
                <div>
                    <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Payment Method</label>
                    <select id="paymentMethod" class="w-full mt-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-medium appearance-none cursor-pointer transition-all">
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="tigo_pesa">Tigo Pesa</option>
                        <option value="airtel_money">Airtel Money</option>
                        <option value="halopesa">Halopesa</option>
                        <option value="bank">Bank / Card</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Amount Paid</label>
                    <input id="paidInput" type="number" value="0" min="0" oninput="updateChange()" class="w-full mt-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm font-bold">
                </div>
                <div class="flex justify-between text-xs font-bold text-gold-600">
                    <span>Change:</span>
                    <span id="changeDisplay">TZS 0</span>
                </div>
            </div>

            <div class="p-4 pt-2">
                <button id="checkoutBtn" onclick="checkout()"
                        class="w-full btn-gold font-bold py-3 rounded-xl text-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Maliza Muuzo
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Floating Cart Bar -->
    <div id="mobileCartBar" class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between shadow-lg" onclick="toggleCart()">
        <div class="flex items-center gap-2">
            <div class="relative">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span id="mobileCartCount" class="absolute -top-1.5 -right-1.5 text-[9px] font-bold text-white bg-emerald-500 rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">0</span>
            </div>
            <span class="text-sm font-bold text-gray-700">Cart</span>
        </div>
        <div class="flex items-center gap-2">
            <span id="mobileCartTotal" class="text-sm font-bold text-emerald-600">TZS 0</span>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
        </div>
    </div>
</div>

<script>
let cart = [];

function addToCart(id, name, price, stock) {
    if (stock <= 0) { showToast('warning', 'Out of Stock', 'Sorry, this product is not available.'); return; }
    const existing = cart.find(i => i.product_id === id);
    if (existing) {
        if (existing.qty >= stock) { showToast('warning', 'Insufficient Stock', 'You have reached the available stock.'); return; }
        existing.qty++;
    } else {
        cart.push({ product_id: id, name, price, qty: 1, stock });
    }
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const empty = document.getElementById('emptyCart');
    const countEl = document.getElementById('cartCount');
    const mobileCount = document.getElementById('mobileCartCount');
    if (cart.length === 0) {
        container.innerHTML = ''; container.appendChild(empty); empty.style.display='block';
        countEl.textContent='0'; if(mobileCount) mobileCount.textContent='0';
        updateTotals(); return;
    }
    empty.style.display = 'none';
    const totalQty = cart.reduce((s, i) => s + i.qty, 0);
    countEl.textContent = totalQty;
    if(mobileCount) mobileCount.textContent = totalQty;
    container.innerHTML = cart.map((item, i) => `
        <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-2.5 border border-gray-100 hover:border-emerald-200 transition-colors">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-xs text-gray-700 leading-tight truncate">${item.name}</p>
                <p class="text-xs text-emerald-600 font-bold mt-0.5">TZS ${formatNum(item.price)} <span class="text-gray-400 font-normal">×${item.qty}</span></p>
            </div>
            <div class="flex items-center gap-1">
                <button onclick="changeQty(${i}, -1)" class="w-7 h-7 rounded-lg bg-white border border-gray-200 font-bold text-gray-600 hover:bg-red-50 hover:border-red-200 hover:text-red-500 transition-colors flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                </button>
                <span class="w-7 text-center font-bold text-sm text-gray-700">${item.qty}</span>
                <button onclick="changeQty(${i}, 1)" class="w-7 h-7 rounded-lg bg-white border border-gray-200 font-bold text-gray-600 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-600 transition-colors flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>
            <button onclick="removeItem(${i})" class="text-gray-300 hover:text-red-500 transition-colors ml-0.5">
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
    const mobileTotal = document.getElementById('mobileCartTotal');
    if (mobileTotal) mobileTotal.textContent = 'TZS ' + formatNum(Math.max(0, total));
    updateChange();
}

function updateChange() {
    const total = parseFloat(document.getElementById('cartTotal').textContent.replace(/[^0-9]/g, '')) || 0;
    const paid = parseFloat(document.getElementById('paidInput').value) || 0;
    document.getElementById('changeDisplay').textContent = 'TZS ' + formatNum(Math.max(0, paid - total));
}

async function checkout() {
    if (cart.length === 0) { showToast('warning', 'Empty Cart', 'Add products first.'); return; }
    const btn = document.getElementById('checkoutBtn');
    btn.disabled = true; btn.textContent = 'Processing...';

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
            showToast('success', 'Sale Completed!', `Receipt: ${data.receipt_no} | Total: TZS ${formatNum(data.total)} | Change: TZS ${formatNum(data.change)}`);
            setTimeout(() => {
                window.open('{{ url("/pos/receipt") }}/' + data.sale_id, '_blank');
                clearCart();
                location.reload();
            }, 1500);
        } else {
            showToast('error', 'Error!', data.message);
        }
    } catch(e) {
        showToast('error', 'Network Error', 'Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Complete Sale';
    }
}

// Toggle cart (mobile)
function toggleCart() {
    const panel = document.getElementById('cartPanel');
    const overlay = document.getElementById('cartOverlay');
    panel.classList.toggle('translate-y-full');
    overlay.classList.toggle('hidden');
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
