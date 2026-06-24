<x-layouts.main title="Riwayat Transaksi">

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('transactionManager', () => ({
                showDetail: false,
                selectedTrx: null,
                deleteConfirm: false,
                deleteId: null,
                transactions: [],
                currentPage: 1,
                lastPage: 1,
                totalTransactions: 0,
                todayRevenue: 0,
                perPage: 10,
                csrfToken: window.__csrf__,

                async init() {
                    await this.fetchTransactions();
                },

                async fetchTransactions(page = 1) {
                    try {
                        const res = await fetch(`/api/transactions?page=${page}&per_page=${this.perPage}`);
                        const json = await res.json();
                        if (json.success) {
                            this.transactions = json.data;
                            this.currentPage = json.current_page;
                            this.lastPage = json.last_page;
                            this.totalTransactions = json.total;
                            this.todayRevenue = json.today_revenue;
                        }
                    } catch (e) {
                        console.error('Gagal mengambil data transaksi:', e);
                    }
                },

                async nextPage() {
                    if (this.currentPage < this.lastPage) {
                        await this.fetchTransactions(this.currentPage + 1);
                    }
                },

                async prevPage() {
                    if (this.currentPage > 1) {
                        await this.fetchTransactions(this.currentPage - 1);
                    }
                },

                formatRp(val) {
                    return 'IDR ' + Number(val).toLocaleString('id-ID').replace(/,/g, '.');
                },

                openDetail(trx) {
                    this.selectedTrx = trx;
                    this.showDetail = true;
                },

                confirmDelete(id) {
                    this.deleteId = id;
                    this.deleteConfirm = true;
                },

                async doDelete() {
                    try {
                        const res = await fetch('/api/transactions/' + this.deleteId, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken }
                        });
                        if (!res.ok) throw new Error('Gagal menghapus transaksi');
                        await this.fetchTransactions(this.currentPage);
                        this.deleteConfirm = false;
                        this.deleteId = null;
                    } catch (e) {
                        alert(e.message);
                    }
                },

                printReceipt(trxId) {
                    window.open('/receipt/' + trxId + '/preview?size=thermal', '_blank');
                }
            }));
        });
    </script>

    {{-- Inject CSRF token safely --}}
    <script>
        window.__csrf__ = window.__csrf__ || '{{ csrf_token() }}';
    </script>

    <div class="p-8" x-data="transactionManager">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Riwayat Transaksi</h1>
                <p class="text-slate-400 text-sm mt-1">Semua transaksi yang telah dilakukan</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-2 border border-green-400 text-green-600 px-4 py-2 rounded-full text-sm font-medium">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Scan Connected
                </span>
                <a href="{{ route('cashier') }}" class="gradient-btn text-white px-5 py-2.5 rounded-full text-sm font-semibold flex items-center gap-2 hover:shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Transaksi Baru
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Total Transaksi</p>
                <p class="text-2xl font-bold text-slate-800" x-text="totalTransactions"></p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Total Pendapatan Hari Ini</p>
                <p class="text-2xl font-bold text-green-600" x-text="formatRp(todayRevenue)"></p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Halaman</p>
                <p class="text-2xl font-bold text-orange-500" x-text="currentPage + ' / ' + lastPage"></p>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">ID Transaksi</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Tanggal &amp; Waktu</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Jumlah Item</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Total Harga</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Metode</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Status</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="trx in transactions" :key="trx.id">
                        <tr class="border-b border-gray-50 hover:bg-orange-50 transition cursor-pointer">
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono font-bold text-slate-700" x-text="'TRX-' + trx.id"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600" x-text="new Date(trx.created_at).toLocaleString('id-ID')"></td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-slate-700"
                                    x-text="(trx.details ? trx.details.reduce((s,i) => s + i.qty, 0) : 0) + ' item'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-orange-500" x-text="formatRp(trx.grand_total)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-semibold capitalize" x-text="trx.payment_method || 'cash'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-green-50 text-green-600': trx.payment_status === 'settlement',
                                    'bg-yellow-50 text-yellow-600': trx.payment_status === 'pending',
                                    'bg-red-50 text-red-600': ['cancel', 'deny', 'expire'].includes(trx.payment_status)
                                }" class="text-xs px-3 py-1 rounded-full font-semibold capitalize" x-text="trx.payment_status || 'pending'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button @click="openDetail(trx)" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button @click="printReceipt(trx.id)" class="p-2 text-orange-400 hover:bg-orange-50 rounded-lg transition" title="Cetak Struk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </button>
                                    @if(auth()->user()->role === 'superadmin')
                                    <button @click="confirmDelete(trx.id)" class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="transactions.length === 0">
                        <td colspan="7" class="text-center py-12 text-slate-400 text-sm">Belum ada transaksi.</td>
                    </tr>
                </tbody>
            </table>

            {{-- Pagination Controls --}}
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-100">
                <div class="text-sm text-slate-500">
                    Menampilkan Halaman <span class="font-semibold text-slate-700" x-text="currentPage"></span> dari <span class="font-semibold text-slate-700" x-text="lastPage"></span> (<span class="font-semibold text-slate-700" x-text="totalTransactions"></span> total transaksi)
                </div>
                <div class="flex items-center gap-2">
                    <button @click="prevPage()" :disabled="currentPage === 1" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-slate-600 bg-white hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        Sebelumnya
                    </button>
                    <button @click="nextPage()" :disabled="currentPage === lastPage" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-slate-600 bg-white hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <div x-show="showDetail" x-transition class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click.self="showDetail=false">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg" @click.stop x-show="selectedTrx">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800" x-text="'Detail Transaksi TRX-' + selectedTrx?.id"></h3>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="new Date(selectedTrx?.created_at).toLocaleString('id-ID')"></p>
                    </div>
                    <button @click="showDetail=false" class="p-2 hover:bg-gray-100 rounded-xl transition">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left text-xs font-semibold text-slate-400 pb-3">Produk</th>
                                <th class="text-center text-xs font-semibold text-slate-400 pb-3">Qty</th>
                                <th class="text-right text-xs font-semibold text-slate-400 pb-3">Harga</th>
                                <th class="text-right text-xs font-semibold text-slate-400 pb-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in (selectedTrx?.details || [])" :key="idx">
                                <tr class="border-b border-gray-50">
                                    <td class="py-3 text-sm text-slate-700" x-text="item.product?.name || '-'"></td>
                                    <td class="py-3 text-sm text-center text-slate-500" x-text="item.qty"></td>
                                    <td class="py-3 text-sm text-right text-slate-500" x-text="formatRp(item.qty > 0 ? item.subtotal / item.qty : 0)"></td>
                                    <td class="py-3 text-sm text-right font-semibold text-slate-800" x-text="formatRp(item.subtotal)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="flex justify-between items-center pt-4 mt-2 border-t border-gray-200">
                        <span class="font-bold text-slate-700">Total</span>
                        <span class="text-xl font-bold text-orange-500" x-text="formatRp(selectedTrx?.grand_total)"></span>
                    </div>
                    <div class="flex justify-between items-center mt-2 text-sm text-slate-500">
                        <span>Metode Pembayaran</span>
                        <span class="capitalize font-medium" x-text="selectedTrx?.payment_method || 'cash'"></span>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 flex gap-3">
                    <button @click="printReceipt(selectedTrx?.id)" class="flex-1 border border-orange-300 text-orange-500 py-2.5 rounded-xl text-sm font-semibold hover:bg-orange-50 transition">Cetak Struk</button>
                    <button @click="showDetail=false" class="flex-1 gradient-btn text-white py-2.5 rounded-xl font-semibold hover:shadow-md transition">Tutup</button>
                </div>
            </div>
        </div>

        {{-- Delete Confirm --}}
        <div x-show="deleteConfirm" x-transition class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click.self="deleteConfirm=false">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop>
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Hapus Transaksi?</h3>
                <p class="text-slate-400 text-sm mb-6">Data transaksi ini akan dihapus permanen.</p>
                <div class="flex gap-3">
                    <button @click="deleteConfirm=false" class="flex-1 border border-gray-200 rounded-xl py-2.5 text-sm text-slate-600 hover:bg-gray-50 transition">Batal</button>
                    <button @click="doDelete()" class="flex-1 bg-red-500 text-white rounded-xl py-2.5 text-sm font-semibold hover:bg-red-600 transition">Hapus</button>
                </div>
            </div>
        </div>

    </div>
</x-layouts.main>
