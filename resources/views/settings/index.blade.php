<x-layouts.main title="Pengaturan Toko">

<style>
    /* ── Settings Page ── */
    .settings-wrap { padding: 32px; max-width: 1140px; }

    /* Section card */
    .s-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .s-card-header {
        padding: 20px 24px 16px;
        border-bottom: 1px solid #f8fafc;
        display: flex; align-items: center; gap: 12px;
    }
    .s-card-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: var(--primary-10, rgba(249,115,22,.1));
        flex-shrink: 0;
    }
    .s-card-title { font-size: 14px; font-weight: 700; color: #0f172a; }
    .s-card-sub   { font-size: 12px; color: #94a3b8; margin-top: 1px; }
    .s-card-body  { padding: 24px; }

    /* Form inputs */
    .s-label {
        display: block;
        font-size: 11px; font-weight: 700;
        letter-spacing: .1em; text-transform: uppercase;
        color: #64748b; margin-bottom: 8px;
    }
    .s-input {
        width: 100%;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px; color: #0f172a;
        font-family: 'DM Sans', sans-serif;
        outline: none;
        transition: border-color .2s, box-shadow .2s, background .2s;
        margin-bottom: 18px;
    }
    .s-input:focus {
        border-color: var(--primary, #f97316);
        background: white;
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary, #f97316) 12%, transparent);
    }

    /* ── Logo Upload ── */
    .logo-upload-area {
        position: relative;
        cursor: pointer;
    }
    .logo-dropzone {
        border: 2px dashed var(--primary, #f97316);
        border-radius: 16px;
        background: color-mix(in srgb, var(--primary, #f97316) 4%, transparent);
        padding: 28px 24px;
        display: flex; align-items: center; gap: 20px;
        transition: background .2s, border-color .2s, transform .2s;
        cursor: pointer;
    }
    .logo-dropzone:hover {
        background: color-mix(in srgb, var(--primary, #f97316) 8%, transparent);
        transform: scale(1.005);
    }
    .logo-dropzone.drag-over {
        background: color-mix(in srgb, var(--primary, #f97316) 14%, transparent);
        border-style: solid;
    }

    /* Logo preview circle */
    .logo-preview-wrap {
        width: 64px; height: 64px; flex-shrink: 0;
        border-radius: 14px;
        border: 2px solid color-mix(in srgb, var(--primary, #f97316) 25%, transparent);
        overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        background: white;
    }
    .logo-preview-wrap img {
        width: 100%; height: 100%; object-fit: contain;
    }
    .logo-preview-wrap .logo-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background: color-mix(in srgb, var(--primary, #f97316) 10%, transparent);
    }

    .logo-dropzone-text { flex: 1; }
    .logo-dropzone-title {
        font-size: 14px; font-weight: 600; color: #0f172a;
        margin-bottom: 4px;
    }
    .logo-dropzone-sub {
        font-size: 12px; color: #94a3b8; line-height: 1.5;
    }
    .logo-type-pills { display: flex; gap: 5px; margin-top: 8px; flex-wrap: wrap; }
    .logo-type-pill {
        font-size: 9px; font-weight: 700; letter-spacing: .08em;
        padding: 2px 7px; border-radius: 20px; text-transform: uppercase;
        background: color-mix(in srgb, var(--primary, #f97316) 12%, transparent);
        color: var(--primary, #f97316);
        border: 1px solid color-mix(in srgb, var(--primary, #f97316) 20%, transparent);
    }

    /* Upload button inside dropzone */
    .btn-upload-logo {
        flex-shrink: 0;
        display: flex; align-items: center; gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        border: none; cursor: pointer;
        font-size: 13px; font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        color: white;
        background: linear-gradient(135deg, var(--primary, #f97316), var(--accent, #fb923c));
        box-shadow: 0 4px 14px color-mix(in srgb, var(--primary, #f97316) 35%, transparent);
        transition: transform .18s cubic-bezier(.34,1.56,.64,1), box-shadow .18s;
        position: relative; overflow: hidden;
    }
    .btn-upload-logo::before {
        content: '';
        position: absolute; top: 0; left: -100%; width: 60%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.2), transparent);
        transition: left .4s ease;
        pointer-events: none;
    }
    .btn-upload-logo:hover { transform: translateY(-2px); box-shadow: 0 8px 24px color-mix(in srgb, var(--primary, #f97316) 4%, transparent); }
    .btn-upload-logo:hover::before { left: 160%; }
    .btn-upload-logo:active { transform: scale(.97); }

    /* Upload progress bar */
    .upload-progress {
        margin-top: 12px;
        background: white;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 14px 16px;
        display: none;
    }
    .upload-progress.show { display: block; }
    .upload-progress-top {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 10px;
    }
    .upload-progress-name { font-size: 12px; font-weight: 600; color: #0f172a; }
    .upload-progress-pct  { font-size: 12px; font-weight: 700; color: var(--primary, #f97316); }
    .upload-progress-track {
        height: 4px; background: #f1f5f9; border-radius: 20px; overflow: hidden;
    }
    .upload-progress-fill {
        height: 100%; border-radius: 20px;
        background: linear-gradient(90deg, var(--primary, #f97316), var(--accent, #fb923c));
        width: 0%; transition: width .08s linear;
    }
    .upload-success {
        display: none; align-items: center; gap: 8px;
        margin-top: 10px;
        padding: 9px 14px; border-radius: 10px;
        background: #f0fdf4; border: 1px solid #bbf7d0;
        font-size: 12px; font-weight: 600; color: #16a34a;
    }
    .upload-success.show { display: flex; }

    /* ── Theme Switcher ── */
    .theme-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }
    .theme-option { position: relative; }
    .theme-option input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }

    .theme-tile {
        border: 2px solid #f1f5f9;
        border-radius: 16px;
        padding: 14px 10px;
        cursor: pointer;
        display: flex; flex-direction: column; align-items: center; gap: 10px;
        transition: border-color .2s, transform .2s, box-shadow .2s;
        background: #fafafa;
        position: relative; overflow: hidden;
    }
    .theme-tile:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,.08);
    }

    /* Active state — border uses the theme's own primary color */
    .theme-option input:checked ~ .theme-tile {
        border-color: var(--t-primary);
        background: white;
        box-shadow: 0 6px 20px color-mix(in srgb, var(--t-primary) 20%, transparent);
        transform: translateY(-3px);
    }

    /* Check badge */
    .theme-tile-check {
        position: absolute; top: 8px; right: 8px;
        width: 18px; height: 18px; border-radius: 50%;
        background: var(--t-primary);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transform: scale(0);
        transition: opacity .2s, transform .25s cubic-bezier(.34,1.56,.64,1);
    }
    .theme-option input:checked ~ .theme-tile .theme-tile-check {
        opacity: 1; transform: scale(1);
    }

    /* Swatch row */
    .theme-swatches {
        display: flex; gap: 5px; align-items: center;
    }
    .theme-swatch {
        border-radius: 6px;
        transition: transform .2s;
    }
    .theme-swatch:first-child { width: 22px; height: 22px; }
    .theme-swatch:not(:first-child) { width: 14px; height: 22px; }

    /* Mini preview sidebar */
    .theme-mini-preview {
        width: 100%; border-radius: 8px; overflow: hidden;
        display: flex; height: 36px; gap: 2px;
    }
    .theme-mini-sidebar {
        width: 12px; border-radius: 4px 0 0 4px;
        display: flex; flex-direction: column;
        align-items: center; gap: 2px; padding-top: 3px;
    }
    .theme-mini-dot { width: 5px; height: 5px; border-radius: 50%; }
    .theme-mini-dot.active { background: var(--t-primary); }
    .theme-mini-dot.inactive { background: rgba(255,255,255,.2); }
    .theme-mini-content { flex: 1; border-radius: 0 4px 4px 0; padding: 4px; }
    .theme-mini-bar {
        height: 3px; border-radius: 2px; margin-bottom: 3px;
    }
    .theme-mini-cards { display: flex; gap: 2px; }
    .theme-mini-card { flex: 1; height: 14px; background: white; border-radius: 2px; }

    .theme-name {
        font-size: 11px; font-weight: 600; color: #475569;
        text-align: center; letter-spacing: .02em;
    }
    .theme-option input:checked ~ .theme-tile .theme-name {
        color: var(--t-primary);
    }

    /* ── Save Button ── */
    .btn-save {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 28px;
        border-radius: 12px; border: none; cursor: pointer;
        font-size: 14px; font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        color: white;
        background: linear-gradient(135deg, var(--primary, #f97316), var(--accent, #fb923c));
        box-shadow: 0 4px 16px color-mix(in srgb, var(--primary, #f97316) 35%, transparent);
        transition: transform .18s cubic-bezier(.34,1.56,.64,1), box-shadow .18s, filter .18s;
        position: relative; overflow: hidden;
    }
    .btn-save::before {
        content: '';
        position: absolute; top: 0; left: -100%; width: 60%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.2), transparent);
        transition: left .4s ease; pointer-events: none;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 28px color-mix(in srgb, var(--primary, #f97316) 4%, transparent); }
    .btn-save:hover::before { left: 160%; }
    .btn-save:active { transform: scale(.97); }
    .btn-save:disabled { opacity: .5; cursor: not-allowed; transform: none; }

    /* Flash message */
    .flash-success {
        display: flex; align-items: center; gap: 10px;
        background: #f0fdf4; border: 1px solid #bbf7d0;
        border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
        font-size: 13px; font-weight: 600; color: #16a34a;
    }
    .flash-error {
        display: flex; align-items: center; gap: 10px;
        background: #fef2f2; border: 1px solid #fecaca;
        border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
        font-size: 13px; font-weight: 600; color: #dc2626;
    }
</style>

<div class="settings-wrap">

    <x-slot:shop>
        {!! $shop->id !!}
    </x-slot:shop>

    {{-- ── Page Header ── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Toko</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola data toko berdasarkan preferensi anda</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flash-success">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="flash-error">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Section 1: Info Toko ── --}}
    <div class="s-card">
        <div class="s-card-header">
            <div class="s-card-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary, #f97316)">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="s-card-title">Informasi Toko</p>
                <p class="s-card-sub">Nama dan alamat toko yang tampil di struk</p>
            </div>
        </div>
        <div class="s-card-body">
            <form method="POST" action="{{ route('settings.update', $shop->id) }}" id="infoForm">
                @csrf @method('PATCH')

                <label class="s-label" for="name">Nama Toko</label>
                <input
                    type="text" id="store_name" name="name"
                    value="{{ old('store_name', $shop->name ?? '') }}"
                    placeholder="Nama Toko..."
                    class="s-input @error('name') border-red-400 @enderror"
                >
                @error('name')
                    <p class="text-xs text-red-500 -mt-3 mb-4">{{ $message }}</p>
                @enderror

                <label class="s-label" for="address">Alamat Toko</label>
                <input
                    type="text" id="address" name="address"
                    value="{{ old('address', $shop->address ?? '') }}"
                    placeholder="Alamat Toko..."
                    class="s-input @error('address') border-red-400 @enderror"
                >
                @error('address')
                    <p class="text-xs text-red-500 -mt-3 mb-4">{{ $message }}</p>
                @enderror

                <button type="submit" class="btn-save">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Info Toko
                </button>
            </form>
        </div>
    </div>

    {{-- ── Section 2: Logo Toko ── --}}
    <div class="s-card">
        <div class="s-card-header">
            <div class="s-card-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary, #f97316)">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="s-card-title">Logo Toko</p>
                <p class="s-card-sub">Tampil di sidebar dan struk. Maks 2MB · JPG, PNG, SVG, WEBP</p>
            </div>
        </div>
        <div class="s-card-body">
            <form method="POST" action="{{ route('settings.logo', $shop->id) }}" enctype="multipart/form-data" id="logoForm">
                @csrf @method('PATCH')

                <div class="logo-upload-area">
                    {{-- Hidden file input --}}
                    <input
                        type="file" id="logoInput" name="logo"
                        accept=".jpg,.jpeg,.png,.svg,.webp"
                        style="display:none"
                        onchange="handleLogoSelect(this)"
                    >

                    {{-- Dropzone --}}
                    <div class="logo-dropzone"
                         id="logoDrop"
                         onclick="document.getElementById('logoInput').click()"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         ondrop="handleDrop(event)">

                        {{-- Current / preview logo --}}
                        <div class="logo-preview-wrap">
                            @if($shop->path_logo ?? false)
                                <img id="logoPreviewImg"
                                     src="{{ Storage::url($shop->path_logo) }}"
                                     alt="Logo Toko">
                            @else
                                <div class="logo-placeholder" id="logoPlaceholder">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--primary, #f97316)">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                    </svg>
                                </div>
                                <img id="logoPreviewImg" src="" alt="" style="display:none; width:100%; height:100%; object-fit:contain;">
                            @endif
                        </div>

                        {{-- Text --}}
                        <div class="logo-dropzone-text">
                            <p class="logo-dropzone-title" id="dropTitle">
                                {{ $shop->path_logo ? 'Ganti logo toko' : 'Upload logo toko' }}
                            </p>
                            <p class="logo-dropzone-sub">Drag & drop atau klik tombol untuk memilih file</p>
                            <div class="logo-type-pills">
                                <span class="logo-type-pill">JPG</span>
                                <span class="logo-type-pill">PNG</span>
                                <span class="logo-type-pill">SVG</span>
                                <span class="logo-type-pill">WEBP</span>
                            </div>
                        </div>

                        {{-- Button --}}
                        <button type="button" class="btn-upload-logo" onclick="event.stopPropagation(); document.getElementById('logoInput').click()">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12V4m0 0L8 8m4-4l4 4"/>
                            </svg>
                            Upload File
                        </button>
                    </div>

                    {{-- Progress bar --}}
                    <div class="upload-progress" id="uploadProgress">
                        <div class="upload-progress-top">
                            <span class="upload-progress-name" id="uploadFileName">—</span>
                            <span class="upload-progress-pct" id="uploadPct">0%</span>
                        </div>
                        <div class="upload-progress-track">
                            <div class="upload-progress-fill" id="uploadFill"></div>
                        </div>
                    </div>

                    {{-- Success --}}
                    <div class="upload-success" id="uploadSuccess">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        File dipilih — klik "Simpan Logo" untuk menyimpan
                    </div>
                </div>

                @error('logo')
                    <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                @enderror

                <div style="margin-top: 16px;">
                    <button type="submit" class="btn-save" id="btnSaveLogo" disabled>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Logo
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Section 3: Theme ── --}}
    <div class="s-card">
        <div class="s-card-header">
            <div class="s-card-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary, #f97316)">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
            </div>
            <div>
                <p class="s-card-title">Tema Warna</p>
                <p class="s-card-sub">Pilih palet warna untuk seluruh tampilan sistem</p>
            </div>
        </div>
        <div class="s-card-body">
            <form method="POST" action="{{ route('settings.updateTheme', $shop->id) }}" id="themeForm">
                @csrf @method('PATCH')

                <div class="theme-grid">
                    @php
                        $themes = config('themes');
                        $currentTheme = $shop->setting->theme ?? 'ember';

                        $themeColors = [
                            'ember'  => ['primary' => '#f97316', 'accent' => '#fb923c', 'sidebar' => '#0f172a', 'bg' => '#fff7ed'],
                            'ocean'  => ['primary' => '#0ea5e9', 'accent' => '#38bdf8', 'sidebar' => '#0c1a2e', 'bg' => '#f0f9ff'],
                            'forest' => ['primary' => '#16a34a', 'accent' => '#4ade80', 'sidebar' => '#052e16', 'bg' => '#f0fdf4'],
                            'violet' => ['primary' => '#7c3aed', 'accent' => '#a78bfa', 'sidebar' => '#1e1035', 'bg' => '#faf5ff'],
                            'rose'   => ['primary' => '#e11d48', 'accent' => '#fb7185', 'sidebar' => '#1a0010', 'bg' => '#fff1f2'],
                        ];

                        $themeLabels = [
                            'ember'  => 'Ember',
                            'ocean'  => 'Ocean',
                            'forest' => 'Forest',
                            'violet' => 'Violet',
                            'rose'   => 'Rose',
                        ];
                    @endphp

                    @foreach($themeColors as $key => $colors)
                    <div class="theme-option">
                        <input
                            type="radio"
                            name="theme"
                            id="theme_{{ $key }}"
                            value="{{ $key }}"
                            {{ $currentTheme === $key ? 'checked' : '' }}
                            onchange="document.getElementById('themeForm').submit()"
                            >
                        <label for="theme_{{ $key }}"
                               class="theme-tile"
                               style="--t-primary: {{ $colors['primary'] }}">

                            {{-- Check mark --}}
                            <div class="theme-tile-check">
                                <svg width="10" height="10" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>

                            {{-- Mini preview --}}
                            <div class="theme-mini-preview">
                                <div class="theme-mini-sidebar" style="background: {{ $colors['sidebar'] }}">
                                    <div class="theme-mini-dot active" style="background: {{ $colors['primary'] }}; width:5px; height:5px; border-radius:50%;"></div>
                                    <div class="theme-mini-dot inactive" style="background: rgba(255,255,255,.2); width:5px; height:5px; border-radius:50%;"></div>
                                    <div class="theme-mini-dot inactive" style="background: rgba(255,255,255,.2); width:5px; height:5px; border-radius:50%;"></div>
                                </div>
                                <div class="theme-mini-content" style="background: {{ $colors['bg'] }}">
                                    <div class="theme-mini-bar" style="background: {{ $colors['primary'] }}; width: 70%; height: 3px; border-radius: 2px; margin-bottom: 3px;"></div>
                                    <div class="theme-mini-cards">
                                        <div style="flex:1; height:14px; background:white; border-radius:2px;"></div>
                                        <div style="flex:1; height:14px; background:white; border-radius:2px;"></div>
                                        <div style="flex:1; height:14px; background: {{ $colors['sidebar'] }}; border-radius:2px;"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Swatches --}}
                            <div class="theme-swatches">
                                <div class="theme-swatch" style="background: {{ $colors['primary'] }}; width:22px; height:22px; border-radius:6px;"></div>
                                <div class="theme-swatch" style="background: {{ $colors['accent'] }}; width:14px; height:22px; border-radius:6px;"></div>
                                <div class="theme-swatch" style="background: {{ $colors['sidebar'] }}; width:14px; height:22px; border-radius:6px;"></div>
                            </div>

                            {{-- Name --}}
                            <span class="theme-name">{{ $themeLabels[$key] }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>

                {{-- Current theme info --}}
                <div style="margin-top: 16px; padding: 12px 16px; background: color-mix(in srgb, var(--primary, #f97316) 6%, transparent); border-radius: 12px; border: 1px solid color-mix(in srgb, var(--primary, #f97316) 15%, transparent);">
                    <p style="font-size: 12px; color: var(--primary, #f97316); font-weight: 600;">
                        ● Tema aktif: <span style="text-transform: capitalize;">{{ $currentTheme }}</span>
                        — perubahan langsung tersimpan saat kamu memilih tema
                    </p>
                </div>
            </form>
        </div>
    </div>

</div>{{-- /settings-wrap --}}

<script>
/* ── Logo Upload JS ── */
function handleDragOver(e) {
    e.preventDefault();
    document.getElementById('logoDrop').classList.add('drag-over');
}
function handleDragLeave(e) {
    document.getElementById('logoDrop').classList.remove('drag-over');
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('logoDrop').classList.remove('drag-over');
    var file = e.dataTransfer.files[0];
    if (file) processFile(file);
}
function handleLogoSelect(input) {
    if (input.files && input.files[0]) processFile(input.files[0]);
}

function processFile(file) {
    var allowed = ['image/jpeg','image/png','image/svg+xml','image/webp'];
    if (!allowed.includes(file.type)) {
        alert('Format file tidak didukung. Gunakan JPG, PNG, SVG, atau WEBP.');
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file melebihi 2MB.');
        return;
    }

    // Preview
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = document.getElementById('logoPreviewImg');
        var placeholder = document.getElementById('logoPlaceholder');
        img.src = e.target.result;
        img.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        document.getElementById('dropTitle').textContent = 'Ganti logo toko';
    };
    reader.readAsDataURL(file);

    // Fake progress animation
    showUploadProgress(file.name);

    // Enable save button after "upload"
    setTimeout(function() {
        document.getElementById('btnSaveLogo').disabled = false;
    }, 1200);
}

function showUploadProgress(name) {
    var progress = document.getElementById('uploadProgress');
    var fill     = document.getElementById('uploadFill');
    var pct      = document.getElementById('uploadPct');
    var success  = document.getElementById('uploadSuccess');

    document.getElementById('uploadFileName').textContent = name;
    progress.classList.add('show');
    success.classList.remove('show');
    fill.style.width = '0%';
    pct.textContent  = '0%';

    var val = 0;
    var iv = setInterval(function() {
        var step = val < 70 ? Math.random() * 10 + 5 : Math.random() * 4 + 1;
        val = Math.min(100, val + step);
        var r = Math.round(val);
        fill.style.width = r + '%';
        pct.textContent  = r + '%';
        if (val >= 100) {
            clearInterval(iv);
            setTimeout(function() {
                progress.classList.remove('show');
                success.classList.add('show');
            }, 300);
        }
    }, 60);
}
</script>

</x-layouts.main>