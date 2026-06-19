<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'POS System' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @php  
        // $configTheme = config('themes.' . $theme ?? 'rose');
        // dd($theme);
        $configTheme = config('themes.' . $theme ?? 'ember');
        // dd($configTheme);
    @endphp
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:   {{ $configTheme['primary'] }};
            --accent:    {{ $configTheme['accent'] }};
            --sidebar:   {{ $configTheme['sidebar'] }};
            --revenue:   {{ $configTheme['revenue'] }};

            /* Turunan otomatis — tidak perlu diubah manual */
            --primary-10: color-mix(in srgb, var(--primary) 10%, transparent);
            --primary-20: color-mix(in srgb, var(--primary) 20%, transparent);
            --primary-30: color-mix(in srgb, var(--primary) 30%, transparent);
        }

        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .sidebar-icon { transition: all 0.2s ease; }
        .sidebar-icon:hover { transform: scale(1.1); }

        /* Pakai var(--primary) bukan hardcode #F97316 */
        .nav-active {
            background: var(--primary);
            border-radius: 12px;
        }

        .gradient-btn {
            background: linear-gradient(135deg, var(--primary), var(--accent));
        }
        .gradient-btn:hover {
            background: linear-gradient(135deg, var(--sidebar), var(--primary));
            /* atau pakai filter jika ingin lebih gelap */
            filter: brightness(0.92);
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body style="background: var(--primary);" class="min-h-screen flex items-center justify-center p-6">
    <div class="flex w-full max-w-7xl h-[92vh] rounded-3xl overflow-hidden shadow-2xl">

        {{-- Sidebar --}}
        <div class="w-20 bg-slate-900 flex flex-col items-center py-6 gap-3 flex-shrink-0">
            {{-- Logo --}}
            <div class="w-12 h-12 gradient-btn rounded-xl flex items-center justify-center mb-4 shadow-lg">
                @if ($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="w-full h-full rounded-xl object-cover">
                @else
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zm-9 8H7v-2h4v2zm6 0h-4v-2h4v2zM4 7V5a2 2 0 012-2h12a2 2 0 012 2v2H4z"/>
                    </svg>
                @endif
            </div>

            {{-- Nav Items --}}
            <a href="{{ route('dashboard') }}" class="sidebar-icon w-12 h-12 flex items-center justify-center rounded-xl {{ request()->routeIs('dashboard') ? 'nav-active' : 'hover:bg-slate-700' }}">
                <svg class="w-6 h-6 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>

            <a href="{{ route('cashier') }}" class="sidebar-icon w-12 h-12 flex items-center justify-center rounded-xl {{ request()->routeIs('cashier') ? 'nav-active' : 'hover:bg-slate-700' }}">
                <svg class="w-6 h-6 {{ request()->routeIs('cashier') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </a>

            <a href="{{ route('products.index') }}" class="sidebar-icon w-12 h-12 flex items-center justify-center rounded-xl {{ request()->routeIs('products*') ? 'nav-active' : 'hover:bg-slate-700' }}">
                <svg class="w-6 h-6 {{ request()->routeIs('products*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </a>

            <a href="{{ route('transactions.index') }}" class="sidebar-icon w-12 h-12 flex items-center justify-center rounded-xl {{ request()->routeIs('transactions*') ? 'nav-active' : 'hover:bg-slate-700' }}">
                <svg class="w-6 h-6 {{ request()->routeIs('transactions*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </a>

            <a href="{{ route('settings.index', $shop) }}"
                class="sidebar-icon w-12 h-12 flex items-center justify-center rounded-xl {{ request()->routeIs('settings*') ? 'nav-active' : 'hover:bg-slate-700' }}">
                    <svg class="w-6 h-6 {{ request()->routeIs('settings*') ? 'text-white' : 'text-slate-400' }}"
                        fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0
                                002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0
                                001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0
                                00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0
                                00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0
                                00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0
                                00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0
                                001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>

            {{-- Spacer + Logout --}}
            <div class="flex-1"></div>
            {{-- <a href="#" class="sidebar-icon w-12 h-12 flex items-center justify-center rounded-xl hover:bg-slate-700">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </a> --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="sidebar-icon w-12 h-12 flex items-center justify-center rounded-xl hover:bg-slate-700">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 bg-gray-50 overflow-y-auto">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>
</html>
