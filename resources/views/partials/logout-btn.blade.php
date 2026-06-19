{{--
    ================================================================
    SNIPPET: Tombol Logout di Sidebar
    Letakkan ini di dalam resources/views/layouts/main.blade.php
    di bagian bawah sidebar (menggantikan <a href="#"> logout lama)
    ================================================================
--}}

{{-- Logout Form (hidden, di-trigger oleh button) --}}
<form method="POST" action="{{ route('logout') }}" id="logoutForm" style="display:none">
    @csrf
</form>

{{-- Tombol Logout --}}
<button
    type="button"
    onclick="document.getElementById('logoutForm').submit()"
    class="sidebar-icon w-12 h-12 flex items-center justify-center rounded-xl hover:bg-red-500 group transition"
    title="Keluar"
>
    <svg class="w-6 h-6 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
    </svg>
</button>


{{--
    ================================================================
    SNIPPET: Info User di Sidebar (opsional, taruh di atas logout)
    ================================================================
--}}
<div class="px-2 mb-2">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto text-xs font-bold text-white"
         style="background: var(--primary);"
         title="{{ auth()->user()->name }}">
        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
    </div>
</div>
