<x-layouts.main title="Kasir">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cashierApp', () => ({
                codeInput: '',
                cart: [],
                products: [],
                showPayModal: false,
                paymentAmount: '',
                paymentMethod: 'cash',
                paymentDone: false,
                savedTrxId: null,
                csrfToken: window.__csrf__,

                async init() {
                    try {
                        const res = await fetch('/api/products');
                        const json = await res.json();
                        if (json.success) {
                            this.products = json.data;
                        }
                    } catch (e) {
                        console.error('Gagal mengambil data produk:', e);
                    }
                },

                get total() {
                    return this.cart.reduce((sum, i) => sum + i.price * i.qty, 0);
                },

                get change() {
                    return Math.max(0, Number(this.paymentAmount) - this.total);
                },

                formatRp(val) {
                    return 'IDR ' + Number(val).toLocaleString('id-ID').replace(/,/g, '.');
                },

                addByCode() {
                    if (!this.codeInput.trim()) return;
                    const q = this.codeInput.trim().toLowerCase();
                    const p = this.products.find(p =>
                         p.code.replace('#', '').toLowerCase() === q ||
                         p.code.toLowerCase() === ('#' + q) ||
                         p.name.toLowerCase().includes(q)
                    );
                    if (!p) { alert('Produk tidak ditemukan!'); this.codeInput = ''; return; }
                    this.addProduct(p);
                    this.codeInput = '';
                },

                addProduct(p) {
                    const ex = this.cart.find(c => c.id === p.id);
                    if (ex) { ex.qty++; } else { this.cart.push({ ...p, qty: 1 }); }
                },

                increment(idx) { this.cart[idx].qty++; },

                decrement(idx) {
                    if (this.cart[idx].qty <= 1) { this.cart.splice(idx, 1); }
                    else { this.cart[idx].qty--; }
                },

                removeItem(idx) { this.cart.splice(idx, 1); },
                clearCart() { this.cart = []; },

                openPay() {
                    this.showPayModal = true;
                    this.paymentAmount = '';
                    this.paymentDone = false;
                },

                async processPayment() {
                    const isNonCash = ['qris', 'debit'].includes(this.paymentMethod);
                    if (!isNonCash && (!this.paymentAmount || Number(this.paymentAmount) < this.total)) return;
                    try {
                        const response = await fetch('/api/transactions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({
                                cart: this.cart,
                                grand_total: this.total,
                                payment_amount: isNonCash ? 0 : (this.paymentAmount || 0),
                                payment_method: this.paymentMethod
                            })
                        });
                        
                        let data;
                        try {
                            data = await response.json();
                        } catch (jsonErr) {
                            throw new Error('Server returned invalid JSON / HTTP Status ' + response.status);
                        }

                        if (!response.ok) { 
                            alert('Gagal: ' + (data.message || 'Error tidak diketahui')); 
                            return; 
                        }
                        
                        this.savedTrxId = data.transaction_id;

                        // Jika non-cash (QRIS/Debit) dan ada snap_token, luncurkan Midtrans Snap Popup
                        if (['qris', 'debit'].includes(this.paymentMethod) && data.snap_token) {
                            window.snap.pay(data.snap_token, {
                                onSuccess: (result) => {
                                    this.paymentDone = true;
                                    this.finishTransaction();
                                },
                                onPending: (result) => {
                                    this.paymentDone = true;
                                    this.finishTransaction();
                                },
                                onError: (result) => {
                                    alert('Pembayaran gagal: ' + result.status_message);
                                },
                                onClose: () => {
                                    alert('Pop-up pembayaran ditutup oleh pengguna.');
                                }
                            });
                        } else {
                            // Cash payment
                            this.paymentDone = true;
                            // Update local product stocks based on cart quantities
                            this.cart.forEach(item => {
                                const prod = this.products.find(p => p.id === item.id);
                                if (prod) prod.stock -= item.qty;
                            });
                        }
                    } catch (err) {
                        console.error('Payment Error:', err);
                        alert('Terjadi kesalahan koneksi: ' + err.message);
                    }
                },

                finishTransaction() {
                    this.cart = [];
                    this.showPayModal = false;
                    this.paymentAmount = '';
                    this.paymentDone = false;
                }
            }));
        });
    </script>

    {{-- Inject CSRF token safely --}}
    <script>
        window.__csrf__ = window.__csrf__ || '{{ csrf_token() }}';
    </script>

    <div class="p-8" x-data="cashierApp">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $shopName }}</h1>
                <p class="text-slate-400 text-sm mt-1">Cashier Transaction</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-2 border border-green-400 text-green-600 px-4 py-2 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M9 12a3 3 0 116 0 3 3 0 01-6 0z"/></svg>
                    Scan Connected
                </span>
            </div>
        </div>

        {{-- Input Bar --}}
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-4">
            <div class="flex items-center gap-3">
                <label class="text-sm font-semibold text-slate-600 whitespace-nowrap">Code Product</label>
                <div class="flex-1 flex rounded-xl border border-gray-200 focus-within:ring-2 focus-within:ring-[var(--primary)] focus-within:border-transparent overflow-hidden transition-all duration-200">
                    <span class="bg-gray-50 text-slate-400 font-mono px-4 py-2.5 flex items-center border-r border-gray-200 text-sm select-none">#</span>
                    <input
                        x-model="codeInput"
                        @keydown.enter="addByCode()"
                        type="text"
                        placeholder="OCC12"
                        class="w-full px-3 py-2.5 text-sm focus:outline-none font-mono bg-transparent"
                    >
                </div>
                <button @click="addByCode()" class="w-10 h-10 gradient-btn rounded-xl flex items-center justify-center text-white hover:shadow-md transition flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
                <div class="flex-1"></div>
                <button @click="openPay()" :disabled="cart.length === 0" class="gradient-btn text-white px-8 py-2.5 rounded-xl font-bold text-base hover:shadow-lg transition disabled:opacity-40 disabled:cursor-not-allowed">
                    Pay
                </button>
            </div>
        </div>

        {{-- Cart Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4 w-12">No</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Code</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Product Name</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Quantity</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Price</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Subtotal</th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, idx) in cart" :key="item.id">
                        <tr class="border-b border-gray-50 hover:bg-orange-50 transition">
                            <td class="px-6 py-4 text-sm text-slate-400" x-text="String(idx+1).padStart(2,'0')"></td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono font-medium text-slate-600" x-text="item.code"></span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-800" x-text="item.name"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button @click="decrement(idx)" class="w-7 h-7 border-2 border-orange-400 rounded-full flex items-center justify-center text-orange-500 hover:bg-orange-50 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                    </button>
                                    <span class="w-8 text-center text-sm font-bold text-slate-800" x-text="item.qty"></span>
                                    <button @click="increment(idx)" class="w-7 h-7 gradient-btn rounded-full flex items-center justify-center text-white hover:shadow-md transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600" x-text="formatRp(item.price)"></td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-800" x-text="formatRp(item.price * item.qty)"></td>
                            <td class="pr-4">
                                <button @click="removeItem(idx)" class="p-1.5 text-slate-300 hover:text-red-400 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="cart.length === 0">
                        <td colspan="7" class="text-center py-16 text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <p class="text-sm">Keranjang kosong. Masukkan kode produk untuk mulai transaksi.</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Total --}}
            <div class="flex justify-end p-5 border-t border-gray-100" x-show="cart.length > 0">
                <div class="bg-gray-50 rounded-2xl px-8 py-4 flex items-center gap-6 border border-gray-100">
                    <span class="text-slate-500 font-medium">Total Price:</span>
                    <span class="text-3xl font-bold text-slate-900" x-text="formatRp(total)"></span>
                </div>
            </div>
        </div>

        {{-- Quick Add Buttons --}}
        <div class="mt-4">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Tambah Cepat:</p>
            <div class="flex flex-wrap gap-2">
                <template x-for="p in products" :key="p.id">
                    <button @click="addProduct(p)"
                        class="px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-slate-600 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition"
                        x-text="p.name">
                    </button>
                </template>
            </div>
        </div>

        {{-- Payment Modal --}}
        <div x-show="showPayModal" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showPayModal=false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>

                {{-- Success State --}}
                <div x-show="paymentDone" class="p-8 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-1">Pembayaran Berhasil!</h3>
                    <p class="text-slate-400 text-sm mb-6">Transaksi telah disimpan.</p>
                    <div class="flex gap-2 mb-3">
                        <a :href="'/receipt/' + savedTrxId + '/preview?size=thermal'"
                            target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 border border-gray-200 rounded-xl py-2.5 text-sm text-slate-600 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Thermal (80mm)
                        </a>
                        <a :href="'/receipt/' + savedTrxId + '/preview?size=a4'"
                            target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 border border-gray-200 rounded-xl py-2.5 text-sm text-slate-600 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            A4
                        </a>
                    </div>
                    <button @click="finishTransaction()" class="w-full gradient-btn text-white py-3 rounded-xl font-bold hover:shadow-lg transition">
                        Transaksi Baru
                    </button>
                </div>

                {{-- Pay Form --}}
                <div x-show="!paymentDone">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-slate-800">Proses Pembayaran</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Order Summary --}}
                        <div class="bg-gray-50 rounded-xl p-4 max-h-48 overflow-y-auto space-y-2">
                            <template x-for="item in cart" :key="item.id">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-600" x-text="item.name + ' x' + item.qty"></span>
                                    <span class="font-medium text-slate-800" x-text="formatRp(item.price * item.qty)"></span>
                                </div>
                            </template>
                            <div class="border-t border-gray-200 pt-2 flex justify-between font-bold text-base">
                                <span>Total</span>
                                <span class="text-orange-500" x-text="formatRp(total)"></span>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 block">Metode Pembayaran</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="paymentMethod = 'cash'"
                                    :class="paymentMethod === 'cash' ? 'border-2 text-[var(--primary)] font-bold' : 'border border-gray-200 text-slate-600 hover:bg-gray-50'"
                                    :style="paymentMethod === 'cash' ? 'border-color: var(--primary); background-color: color-mix(in srgb, var(--primary) 10%, transparent);' : ''"
                                    class="py-2.5 px-3 rounded-xl text-sm transition text-center">
                                    Cash
                                </button>
                                <button type="button" @click="paymentMethod = 'qris'"
                                    :class="paymentMethod === 'qris' ? 'border-2 text-[var(--primary)] font-bold' : 'border border-gray-200 text-slate-600 hover:bg-gray-50'"
                                    :style="paymentMethod === 'qris' ? 'border-color: var(--primary); background-color: color-mix(in srgb, var(--primary) 10%, transparent);' : ''"
                                    class="py-2.5 px-3 rounded-xl text-sm transition text-center">
                                    QRIS
                                </button>
                                <button type="button" @click="paymentMethod = 'debit'"
                                    :class="paymentMethod === 'debit' ? 'border-2 text-[var(--primary)] font-bold' : 'border border-gray-200 text-slate-600 hover:bg-gray-50'"
                                    :style="paymentMethod === 'debit' ? 'border-color: var(--primary); background-color: color-mix(in srgb, var(--primary) 10%, transparent);' : ''"
                                    class="py-2.5 px-3 rounded-xl text-sm transition text-center">
                                    Debit
                                </button>
                            </div>
                        </div>

                        {{-- Payment Amount (hanya untuk Cash) --}}
                        <div x-show="paymentMethod === 'cash'">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 block">Jumlah Bayar (IDR)</label>
                            <input x-model="paymentAmount" type="number" :placeholder="total"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-orange-300 text-slate-800">
                        </div>

                        {{-- Kembalian / Kurang (hanya Cash) --}}
                        <template x-if="paymentMethod === 'cash'">
                            <div>
                                <div x-show="paymentAmount >= total" class="bg-green-50 rounded-xl p-3 flex justify-between items-center">
                                    <span class="text-sm text-green-600 font-medium">Kembalian</span>
                                    <span class="text-lg font-bold text-green-600" x-text="formatRp(change)"></span>
                                </div>
                                <div x-show="paymentAmount && paymentAmount < total" class="bg-red-50 rounded-xl p-3 flex justify-between items-center">
                                    <span class="text-sm text-red-500 font-medium">Kurang</span>
                                    <span class="text-base font-bold text-red-500" x-text="formatRp(total - paymentAmount)"></span>
                                </div>
                            </div>
                        </template>

                        {{-- Info untuk QRIS/Debit --}}
                        <div x-show="['qris', 'debit'].includes(paymentMethod)" class="bg-blue-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-blue-600 font-medium">Klik <strong>Lanjutkan</strong> untuk membuka halaman pembayaran Midtrans.</p>
                        </div>
                    </div>
                    <div class="p-6 border-t border-gray-100 flex gap-3">
                        <button @click="showPayModal=false" class="flex-1 border border-gray-200 rounded-xl py-3 text-sm text-slate-600 hover:bg-gray-50 transition">Batal</button>
                        <button @click="processPayment()"
                            :disabled="paymentMethod === 'cash' && (!paymentAmount || Number(paymentAmount) < total)"
                            class="flex-1 gradient-btn text-white rounded-xl py-3 text-sm font-bold hover:shadow-lg transition disabled:opacity-40 disabled:cursor-not-allowed"
                            x-text="['qris', 'debit'].includes(paymentMethod) ? 'Lanjutkan ke Midtrans' : 'Bayar Sekarang'">
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.main>
