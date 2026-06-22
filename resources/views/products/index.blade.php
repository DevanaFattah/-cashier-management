<x-layouts.main title="Manajemen Produk">

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productManager', () => ({
                showModal: false,
                editMode: false,
                deleteConfirm: false,
                deleteId: null,
                form: { id: null, code: '', name: '', price: '', stock: '' },
                products: [],
                search: '',
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

                get filtered() {
                    if (!this.search) return this.products;
                    const q = this.search.toLowerCase();
                    return this.products.filter(p =>
                        p.name.toLowerCase().includes(q) ||
                        p.code.toLowerCase().includes(q)
                    );
                },

                formatRupiah(val) {
                    return 'IDR ' + Number(val).toLocaleString('id-ID').replace(/,/g, '.');
                },

                openAdd() {
                    this.editMode = false;
                    this.form = { id: null, code: '', name: '', price: '', stock: '' };
                    this.showModal = true;
                },

                openEdit(p) {
                    this.editMode = true;
                    this.form = { ...p };
                    this.showModal = true;
                },

                async save() {
                    if (!this.form.name || !this.form.price || !this.form.stock) return;
                    const url    = this.editMode ? '/api/products/' + this.form.id : '/api/products';
                    const method = this.editMode ? 'PUT' : 'POST';
                    try {
                        const res = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify(this.form)
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal menyimpan data');
                        if (this.editMode) {
                            const idx = this.products.findIndex(p => p.id === this.form.id);
                            if (idx !== -1) this.products[idx] = data.product;
                        } else {
                            this.products.unshift(data.product);
                        }
                        this.showModal = false;
                    } catch (e) {
                        alert(e.message);
                    }
                },

                confirmDelete(id) {
                    this.deleteId = id;
                    this.deleteConfirm = true;
                },

                async doDelete() {
                    try {
                        const res = await fetch('/api/products/' + this.deleteId, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken }
                        });
                        if (!res.ok) throw new Error('Gagal menghapus data');
                        this.products = this.products.filter(p => p.id !== this.deleteId);
                        this.deleteConfirm = false;
                        this.deleteId = null;
                    } catch (e) {
                        alert(e.message);
                    }
                }
            }));
        });
    </script>

    {{-- Inject CSRF token safely --}}
    <script>
        window.__csrf__     = '{{ csrf_token() }}';
    </script>

    <div class="p-8" x-data="productManager">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Manajemen Produk</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola data produk toko Anda</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-2 border border-green-400 text-green-600 px-4 py-2 rounded-full text-sm font-medium">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Scan Connected
                </span>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 mb-5">
            <div class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input x-model="search" type="text" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent">
                </div>
                <button @click="openAdd()" class="gradient-btn text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Produk
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">No</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Kode</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Nama Produk</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Harga</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Stok</th>
                        <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(p, i) in filtered" :key="p.id">
                        <tr class="border-b border-gray-50 hover:bg-orange-50 transition">
                            <td class="px-6 py-4 text-sm text-slate-400" x-text="String(i+1).padStart(2,'0')"></td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded-lg" x-text="p.code"></span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-800" x-text="p.name"></td>
                            <td class="px-6 py-4 text-sm text-slate-600" x-text="formatRupiah(p.price)"></td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold"
                                    :class="p.stock > 50 ? 'text-green-600' : p.stock > 20 ? 'text-yellow-600' : 'text-red-600'"
                                    x-text="p.stock + ' pcs'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button @click="openEdit(p)" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="confirmDelete(p.id)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filtered.length === 0">
                        <td colspan="6" class="text-center py-12 text-slate-400 text-sm">Tidak ada produk ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Add/Edit Modal --}}
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
             @click.self="showModal=false">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-slate-800" x-text="editMode ? 'Edit Produk' : 'Tambah Produk Baru'"></h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 block">Kode Produk</label>
                        <input x-model="form.code" type="text" placeholder="Kode produk..." class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Produk</label>
                        <input x-model="form.name" type="text" placeholder="Nama produk..." class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 block">Harga (IDR)</label>
                        <input x-model="form.price" type="number" placeholder="50000" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 block">Stok</label>
                        <input x-model="form.stock" type="number" placeholder="100" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 flex gap-3 justify-end">
                    <button @click="showModal=false" class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm text-slate-600 hover:bg-gray-50 transition">Batal</button>
                    <button @click="save()" class="gradient-btn text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:shadow-md transition" x-text="editMode ? 'Simpan Perubahan' : 'Tambah Produk'"></button>
                </div>
            </div>
        </div>

        {{-- Delete Confirm Modal --}}
        <div x-show="deleteConfirm" x-transition class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click.self="deleteConfirm=false">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop>
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Hapus Produk?</h3>
                <p class="text-slate-400 text-sm mb-6">Tindakan ini tidak dapat dibatalkan. Produk akan dihapus secara permanen.</p>
                <div class="flex gap-3">
                    <button @click="deleteConfirm=false" class="flex-1 border border-gray-200 rounded-xl py-2.5 text-sm text-slate-600 hover:bg-gray-50 transition">Batal</button>
                    <button @click="doDelete()" class="flex-1 bg-red-500 text-white rounded-xl py-2.5 text-sm font-semibold hover:bg-red-600 transition">Hapus</button>
                </div>
            </div>
        </div>

    </div>
</x-layouts.main>
