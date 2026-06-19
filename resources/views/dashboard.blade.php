<x-layouts.main title="Dashboard">

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
        box-shadow: 0 20px 40px -12px rgba(249, 115, 22, 0.18);
    }

    /* Glowing orange ring on revenue card */
    .revenue-card {
        background: linear-gradient(135deg, #1a0f00 0%, #0f172a 60%);
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
        background: radial-gradient(circle, rgba(249,115,22,0.35) 0%, transparent 70%);
        pointer-events: none;
    }
    .revenue-card::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -20px;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(251,146,60,0.15) 0%, transparent 70%);
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
    .delay-3 { animation-delay: 0.19s; }
    .delay-4 { animation-delay: 0.26s; }
    .delay-5 { animation-delay: 0.33s; }

    /* Transaction row hover */
    .trx-row {
        transition: background 0.2s ease, transform 0.2s ease;
        border-left: 2px solid transparent;
    }
    .trx-row:hover {
        background: linear-gradient(90deg, rgba(249,115,22,0.06), transparent);
        border-left-color: var(--primary);
        transform: translateX(4px);
    }

    /* Badge pulse */
    .pulse-dot {
        animation: pulseDot 2s ease-in-out infinite;
    }
    @keyframes pulseDot {
        0%, 100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.5); }
        50% { box-shadow: 0 0 0 5px rgba(74, 222, 128, 0); }
    }

    /* Settings button */
    .settings-btn {
        transition: all 0.2s ease;
    }
    .settings-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(249,115,22,0.06);
    }
    .settings-btn svg {
        transition: transform 0.5s ease;
    }
    .settings-btn:hover svg {
        transform: rotate(90deg);
    }

    /* Number counter style */
    .stat-number {
        font-family: 'Syne', sans-serif;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.03em;
    }

    /* Top products pill */
    .product-rank {
        font-family: 'Syne', sans-serif;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.05em;
    }

    /* Subtle grid pattern background */
    .grid-bg {
        background-image:
            linear-gradient(rgba(249,115,22,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(249,115,22,0.03) 1px, transparent 1px);
        background-size: 32px 32px;
    }
</style>

<div class="dash-root min-h-full grid-bg">
    <div class="p-8 max-w-[1400px] mx-auto">

        {{-- ── Header ── --}}
        <div class="flex items-center justify-between mb-10 fade-up">
            <div>
                <p class="text-xs font-semibold tracking-[0.2em] text-orange-400 uppercase mb-1">
                    ● LIVE — {{ now()->format('d M Y') }}
                </p>
                <h1 class="font-display text-3xl font-800 text-slate-900 tracking-tight leading-none">
                    Overview
                </h1>
            </div>
            <div class="flex items-center gap-3">
                {{-- Status badge --}}
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-full text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></span>
                    Sistem Aktif
                </div>
                {{-- Settings --}}
                <button class="settings-btn flex items-center gap-2 border border-slate-200 bg-white text-slate-500 px-4 py-2 rounded-full text-xs font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </button>
            </div>
        </div>

        {{-- ── Stat Cards ── --}}
        <div class="grid grid-cols-3 gap-5 mb-8">
            {{-- Transaksi --}}
            <div class="stat-card fade-up delay-1 bg-white rounded-2xl border border-slate-100 overflow-hidden cursor-default">
                <div class="stat-card-inner p-6" style="--stripe-color: var(--primary);">
                    <div class="before:bg-orange-400" style=""></div>
                    <div class="flex items-start justify-between mb-6">
                        <div class="relative">
                            <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                12%
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="stat-number text-4xl font-bold text-slate-900 mb-1">{{ number_format($totalTransactions) }}</p>
                        <p class="text-sm font-medium text-slate-500">Total Transaksi</p>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-50">
                            <div class="h-1 flex-1 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full w-[78%] bg-gradient-to-r from-orange-400 to-amber-400 rounded-full bar-fill-animated" style="--target-width: 78%"></div>
                            </div>
                            <span class="text-xs text-slate-400 font-medium">Bulan ini</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Produk --}}
            <div class="stat-card fade-up delay-2 bg-white rounded-2xl border border-slate-100 overflow-hidden cursor-default">
                <div class="stat-card-inner p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                            {{ $activeProducts }} aktif
                        </span>
                    </div>
                    <div>
                        <p class="stat-number text-4xl font-bold text-slate-900 mb-1">{{ number_format($activeProducts) }}</p>
                        <p class="text-sm font-medium text-slate-500">Jumlah Produk</p>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-50">
                            <div class="h-1 flex-1 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full w-[60%] bg-gradient-to-r from-blue-400 to-sky-400 rounded-full bar-fill-animated" style="--target-width: 60%"></div>
                            </div>
                            <span class="text-xs text-slate-400 font-medium">Stok tersedia</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Revenue --}}
            <div class="stat-card fade-up delay-3 revenue-card rounded-2xl overflow-hidden cursor-default grain-overlay">
                <div class="relative z-10 p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgba(249,115,22,0.2);">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-orange-300 rounded-full px-2.5 py-1" style="background: rgba(249,115,22,0.2);">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            24%
                        </span>
                    </div>
                    <div>
                        <p class="stat-number text-4xl font-bold text-white mb-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                        <p class="text-sm font-medium text-slate-400">Total Pendapatan</p>
                        <div class="flex items-center gap-2 mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
                            <div class="h-1 flex-1 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1);">
                                <div class="h-full w-[88%] rounded-full bar-fill-animated" style="background: linear-gradient(90deg, var(--primary), var(--primary)); --target-width: 88%"></div>
                            </div>
                            <span class="text-xs font-medium text-slate-500">Bulan ini</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Bottom Section ── --}}
        <div class="grid grid-cols-5 gap-5">

            {{-- Recent Transactions --}}
            <div class="col-span-3 fade-up delay-4">
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden h-full">

                    {{-- Panel header --}}
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <h2 class="font-display font-700 text-slate-800 text-sm tracking-tight">Transaksi Terbaru</h2>
                        </div>
                        <a href="{{ route('transactions.index') }}" class="group flex items-center gap-1.5 text-xs font-semibold text-orange-500 hover:text-orange-600 transition-colors">
                            Lihat Semua
                            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Transactions list --}}
                    <div class="divide-y divide-slate-50">
                        @forelse($recentTransactions as $trx)
                        <div class="trx-row flex items-center gap-4 px-6 py-4">
                            {{-- Avatar --}}
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(249, 115, 22, 0.1);">
                                <svg class="w-4 h-4" style="color: #f97316;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 font-display tracking-tight">{{ $trx->id }}</p>
                                <p class="text-xs text-slate-400 truncate mt-0.5">{{ $trx->created_at->diffForHumans() }}</p>
                            </div>

                            {{-- Amount --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs font-bold text-slate-800">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</p>
                            </div>

                            {{-- Badge --}}
                            <span class="flex-shrink-0 text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100" style="text-transform: uppercase;">
                                ✓ {{ $trx->payment_method }}
                            </span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/50">
                        <p class="text-xs text-slate-400 font-medium">Menampilkan 4 transaksi terakhir · <span class="text-orange-500">248 total bulan ini</span></p>
                    </div>
                </div>
            </div>

            {{-- Top Products --}}
            <div class="col-span-2 fade-up delay-5">
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden h-full">

                    {{-- Panel header --}}
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <h2 class="font-display font-700 text-slate-800 text-sm tracking-tight">Produk Terlaris</h2>
                        </div>
                        <a href="{{ route('products.index') }}" class="group flex items-center gap-1.5 text-xs font-semibold text-orange-500 hover:text-orange-600 transition-colors">
                            Kelola
                            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Products --}}
                    <div class="px-6 py-5 space-y-5">
                        @php
                            $styles = [
                                0 => ['color' => '#f97316', 'bg' => '#fff7ed'], // Orange
                                1 => ['color' => '#3b82f6', 'bg' => '#eff6ff'], // Blue
                                2 => ['color' => '#f59e0b', 'bg' => '#fffbeb'], // Amber
                                3 => ['color' => '#22c55e', 'bg' => '#f0fdf4'], // Green
                                4 => ['color' => '#a855f7', 'bg' => '#faf5ff'], // Purple
                            ];
                        @endphp
                        @forelse($topProducts as $index => $item)
                        @php
                            $style = $styles[$index] ?? ['color' => '#64748b', 'bg' => '#f1f5f9'];
                            $pct = round(($item->total_qty / $totalSales) * 100);
                            $rank = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <div class="group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2.5">
                                    {{-- Rank pill --}}
                                    <span class="product-rank w-6 h-6 rounded-lg flex items-center justify-center text-[10px]"
                                          style="background: {{ $style['bg'] }}; color: {{ $style['color'] }};">
                                        {{ $rank }}
                                    </span>
                                    <span class="text-sm font-semibold text-slate-700">{{ $item->product->name ?? 'Produk' }}</span>
                                </div>
                                <span class="text-xs font-bold text-slate-400">{{ $item->total_qty }} <span class="font-normal text-slate-300">terjual</span></span>
                            </div>

                            {{-- Progress bar --}}
                            <div class="relative h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="absolute inset-y-0 left-0 rounded-full bar-fill-animated transition-all"
                                     style="width: {{ $pct }}%; --target-width: {{ $pct }}%; background: linear-gradient(90deg, {{ $style['color'] }}, {{ $style['color'] }}cc);">
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-slate-400">
                            Belum ada data penjualan.
                        </div>
                        @endforelse
                    </div>

                    {{-- Footer note --}}
                    @if($topProducts->isNotEmpty())
                    @php
                        $topOne = $topProducts->first();
                        $topOnePct = round(($topOne->total_qty / $totalSales) * 100);
                    @endphp
                    <div class="px-6 pb-5">
                        <div class="rounded-xl p-3.5 flex items-center gap-3" style="background: linear-gradient(135deg, #fff7ed, #ffedd5);">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-orange-700">{{ $topOne->product->name ?? 'Produk' }} dominan</p>
                                <p class="text-xs text-orange-500 mt-0.5">Terjual {{ $topOne->total_qty }}x — {{ $topOnePct }}% dari total volume penjualan</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>{{-- /grid --}}
    </div>{{-- /container --}}
</div>{{-- /dash-root --}}

</x-layouts.main>
