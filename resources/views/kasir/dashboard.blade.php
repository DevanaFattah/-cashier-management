<x-layouts.main title="Dashboard Kasir">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap');

    .dash-root * {
        font-family: 'DM Sans', sans-serif;
    }

    .dash-root .font-display {
        font-family: 'Syne', sans-serif;
    }

    /* Grain overlay */
    .grain-overlay::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
        border-radius: inherit;
        pointer-events: none;
        z-index: 1;
    }

    /* Card hover lift */
    .stat-card {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -12px var(--primary-20);
    }

    /* Glowing accent ring on revenue card */
    .revenue-card {
        background: linear-gradient(135deg, var(--revenue) 0%, var(--sidebar) 60%);
        position: relative;
        overflow: hidden;
    }
    .revenue-card::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, color-mix(in srgb, var(--primary) 35%, transparent) 0%, transparent 70%);
        pointer-events: none;
    }
    .revenue-card::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -20px;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, color-mix(in srgb, var(--accent) 15%, transparent) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Stripe accent on top of stat cards */
    .stat-card-inner {
        position: relative;
    }
    .stat-card-inner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        border-radius: 16px 16px 0 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .stat-card:hover .stat-card-inner::before {
        opacity: 1;
    }

    /* Animated progress bars */
    .bar-fill-animated {
        animation: barGrow 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        transform-origin: left;
    }
    @keyframes barGrow {
        from { width: 0% !important; }
        to { width: var(--target-width); }
    }

    /* Staggered card entrance */
    .fade-up {
        opacity: 0;
        transform: translateY(24px);
        animation: fadeUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    @keyframes fadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
    .delay-1 { animation-delay: 0.05s; }
    .delay-2 { animation-delay: 0.12s; }
    .delay-3 { animation-delay: 0.18s; }
    .delay-4 { animation-delay: 0.24s; }
    .delay-5 { animation-delay: 0.3s; }

    /* Custom grid pattern */
    .grid-bg {
        background-color: #fafafa;
        background-image: 
            radial-gradient(#e5e7eb 1px, transparent 1px), 
            radial-gradient(#e5e7eb 1px, #fafafa 1px);
        background-size: 24px 24px;
        background-position: 0 0, 12px 12px;
    }

    .pulse-dot {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .5; transform: scale(1.2); }
    }

    /* Settings button custom styles */
    .settings-btn {
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .settings-btn:hover {
        border-color: #cbd5e1;
        color: #334155;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px -2px rgba(0,0,0,0.05);
    }

    /* Custom scrollbar for transaction list */
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Hover effect for transaction rows */
    .trx-row {
        transition: all 0.2s ease;
    }
    .trx-row:hover {
        background-color: #fafaf9;
    }

    /* Theme variables helper classes */
    .text-primary-var {
        color: var(--primary);
    }
    .text-primary-var:hover {
        color: var(--accent);
    }
    .bg-primary-10 {
        background-color: var(--primary-10);
    }
    .bg-primary-20 {
        background-color: var(--primary-20);
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kasirDashboardApp', () => ({
            stats: {
                todayTransactions: 0,
                todayRevenue: 0,
                recentTransactions: [],
                topProducts: []
            },
            loading: true,

            async init() {
                try {
                    const res = await fetch('/api/kasir/stats');
                    const json = await res.json();
                    if (json.success) {
                        this.stats = json.data;
                    }
                } catch (err) {
                    console.error('Gagal memuat statistik kasir:', err);
                } finally {
                    this.loading = false;
                }
            },

            formatRp(val) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(val || 0);
            },

            formatNumber(val) {
                return new Intl.NumberFormat('id-ID').format(val || 0);
            },

            getTimeAgo(dateStr) {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                if (diffMins < 1) return 'Baru saja';
                if (diffMins < 60) return diffMins + ' menit yang lalu';
                const diffHours = Math.floor(diffMins / 60);
                if (diffHours < 24) return diffHours + ' jam yang lalu';
                return date.toLocaleDateString('id-ID');
            }
        }));
    });
</script>

<div class="dash-root min-h-full grid-bg" x-data="kasirDashboardApp">
    <div class="p-8 max-w-[1400px] mx-auto">

        {{-- ── Header ── --}}
        <div class="flex items-center justify-between mb-10 fade-up">
            <div>
                <p class="text-xs font-semibold tracking-[0.2em] uppercase mb-1" style="color: var(--accent);">
                    ● LIVE KASIR — {{ now()->format('d M Y') }}
                </p>
                <h1 class="font-display text-3xl font-800 text-slate-900 tracking-tight leading-none">
                    Dashboard Kasir
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-full text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></span>
                    Sistem Aktif
                </div>
                <a href="{{ route('cashier') }}" class="settings-btn flex items-center gap-2 border border-slate-200 bg-white text-slate-700 px-4 py-2 rounded-full text-xs font-bold" style="background: var(--primary); color: white; border-color: var(--primary);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Buka Kasir (POS)
                </a>
            </div>
        </div>

        {{-- ── Stat Cards ── --}}
        <div class="grid grid-cols-2 gap-5 mb-8">
            {{-- Transaksi Hari Ini --}}
            <div class="stat-card fade-up delay-1 bg-white rounded-2xl border border-slate-100 overflow-hidden cursor-default">
                <div class="stat-card-inner p-6" style="--stripe-color: var(--primary);">
                    <div class="before:bg-primary-10" style=""></div>
                    <div class="flex items-start justify-between mb-6">
                        <div class="relative">
                            <div class="w-11 h-11 bg-primary-10 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-var" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="stat-number text-4xl font-bold text-slate-900 mb-1" x-text="formatNumber(stats.todayTransactions)">0</p>
                        <p class="text-sm font-medium text-slate-500">Transaksi Hari Ini</p>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-50">
                            <span class="text-xs text-slate-400 font-medium">Berdasarkan data kasir Anda</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pendapatan Hari Ini --}}
            <div class="stat-card fade-up delay-2 revenue-card rounded-2xl overflow-hidden cursor-default grain-overlay">
                <div class="relative z-10 p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: var(--primary-20);">
                            <svg class="w-5 h-5 text-primary-var" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="stat-number text-4xl font-bold text-white mb-1" x-text="formatRp(stats.todayRevenue)">Rp 0</p>
                        <p class="text-sm font-medium text-slate-400">Pendapatan Hari Ini</p>
                        <div class="flex items-center gap-2 mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
                            <span class="text-xs text-slate-500 font-medium">Berdasarkan grand total hari ini</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Bottom Section ── --}}
        <div class="grid grid-cols-5 gap-5">

            {{-- Recent Transactions --}}
            <div class="col-span-3 fade-up delay-3">
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden h-full">

                    {{-- Panel header --}}
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary-var" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <h2 class="font-display font-700 text-slate-800 text-sm tracking-tight">Transaksi Terakhir Anda</h2>
                        </div>
                        <a href="{{ route('transactions.index') }}" class="group flex items-center gap-1.5 text-xs font-semibold text-primary-var hover:text-accent transition-colors">
                            Lihat Semua
                            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Transactions list --}}
                    <div class="divide-y divide-slate-50">
                        <template x-for="trx in stats.recentTransactions" :key="trx.id">
                            <div class="trx-row flex items-center gap-4 px-6 py-4">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 bg-primary-10">
                                    <svg class="w-4 h-4 text-primary-var" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 font-display tracking-tight" x-text="'TRX-' + trx.id"></p>
                                    <p class="text-xs text-slate-400 truncate mt-0.5" x-text="getTimeAgo(trx.created_at)"></p>
                                </div>

                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs font-bold text-slate-800" x-text="formatRp(trx.grand_total)"></p>
                                </div>

                                <span class="flex-shrink-0 text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100" style="text-transform: uppercase;" x-text="'✓ ' + trx.payment_method"></span>
                            </div>
                        </template>
                        <div x-show="stats.recentTransactions.length === 0" class="text-center py-8 text-slate-400">
                            Tidak ada transaksi terbaru.
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/50">
                        <p class="text-xs text-slate-400 font-medium">Menampilkan sampai 5 transaksi terakhir milik Anda</p>
                    </div>
                </div>
            </div>

            {{-- Top Products by this Cashier --}}
            <div class="col-span-2 fade-up delay-4">
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden h-full">

                    {{-- Panel header --}}
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary-var" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <h2 class="font-display font-700 text-slate-800 text-sm tracking-tight">Produk Terlaris Anda</h2>
                        </div>
                    </div>

                    {{-- Products --}}
                    <div class="px-6 py-5 space-y-5">
                        <template x-for="(item, idx) in stats.topProducts" :key="idx">
                            <div class="group">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2.5">
                                        <span class="product-rank w-6 h-6 rounded-lg flex items-center justify-center text-[10px]"
                                              :style="'background: ' + (idx === 0 ? '#fff7ed' : idx === 1 ? '#eff6ff' : idx === 2 ? '#fffbeb' : '#faf5ff') + '; color: ' + (idx === 0 ? '#f97316' : idx === 1 ? '#3b82f6' : idx === 2 ? '#f59e0b' : '#a855f7') + ';'">
                                            <span x-text="String(idx + 1).padStart(2, '0')"></span>
                                        </span>
                                        <span class="text-sm font-semibold text-slate-700" x-text="item.product?.name || 'Produk'"></span>
                                    </div>
                                    <span class="text-xs font-bold text-slate-400"><span x-text="item.total_qty"></span> <span class="font-normal text-slate-300">terjual</span></span>
                                </div>
                            </div>
                        </template>
                        <div x-show="stats.topProducts.length === 0" class="text-center py-8 text-slate-400">
                            Belum ada data penjualan oleh Anda.
                        </div>
                    </div>

                    <div class="px-6 pb-5">
                        <div class="rounded-xl p-3.5 flex items-center gap-3" style="background: linear-gradient(135deg, var(--primary-10), var(--primary-20));">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: var(--primary-30);">
                                <svg class="w-4 h-4 text-primary-var" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900" style="color: var(--sidebar);">Top 3 Produk Terlaris</p>
                                <p class="text-xs mt-0.5" style="color: var(--primary);">Menampilkan produk dengan kuantitas penjualan tertinggi khusus transaksi Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</x-layouts.main>
