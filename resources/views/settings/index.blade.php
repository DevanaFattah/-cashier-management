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

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('settingsManager', () => ({
            shop: { name: '', address: '', path_logo: '' },
            currentTheme: 'ember',
            messageSuccess: '',
            messageError: '',
            logoFile: null,
            logoPreviewUrl: '',
            isLogoUploading: false,
            logoUploadPct: 0,
            logoUploadFileName: '',

            async init() {
                try {
                    const res = await fetch('/api/settings');
                    const json = await res.json();
                    if (json.success) {
                        this.shop = json.data;
                        this.currentTheme = json.data.setting?.theme || 'ember';
                        this.logoPreviewUrl = this.shop.path_logo ? ('/storage/' + this.shop.path_logo) : '';
                    }
                } catch (e) {
                    console.error('Gagal mengambil pengaturan toko:', e);
                }
            },

            async saveInfo() {
                this.messageSuccess = '';
                this.messageError = '';
                try {
                    const res = await fetch('/api/settings', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            name: this.shop.name,
                            address: this.shop.address
                        })
                    });
                    const json = await res.json();
                    if (res.ok) {
                        this.messageSuccess = 'Berhasil mengubah Nama dan Alamat';
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        throw new Error(json.message || 'Gagal mengubah pengaturan');
                    }
                } catch (e) {
                    this.messageError = e.message;
                }
            },

            async changeTheme(themeName) {
                this.messageSuccess = '';
                this.messageError = '';
                try {
                    const res = await fetch('/api/settings/theme', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ theme: themeName })
                      });
                      const json = await res.json();
                      if (res.ok) {
                          this.currentTheme = themeName;
                          this.messageSuccess = 'Theme berhasil diubah';
                          setTimeout(() => window.location.reload(), 800);
                      } else {
                          throw new Error(json.message || 'Gagal mengubah tema');
                      }
                  } catch (e) {
                      this.messageError = e.message;
                  }
            },

            handleDragOver(e) { e.preventDefault(); document.getElementById('logoDrop').classList.add('drag-over'); },
            handleDragLeave(e) { document.getElementById('logoDrop').classList.remove('drag-over'); },
            handleDrop(e) { e.preventDefault(); document.getElementById('logoDrop').classList.remove('drag-over'); const file = e.dataTransfer.files[0]; if (file) this.processFile(file); },
            handleLogoSelect(input) { if (input.files && input.files[0]) this.processFile(input.files[0]); },

            processFile(file) {
                const allowed = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];
                if (!allowed.includes(file.type)) { alert('Format file tidak didukung.'); return; }
                if (file.size > 2 * 1024 * 1024) { alert('Ukuran file melebihi 2MB.'); return; }

                this.logoFile = file;
                this.logoPreviewUrl = URL.createObjectURL(file);
                this.logoUploadFileName = file.name;
                this.isLogoUploading = true;
                this.logoUploadPct = 0;
                
                const interval = setInterval(() => {
                    if (this.logoUploadPct < 100) this.logoUploadPct += 10;
                    else { clearInterval(interval); this.isLogoUploading = false; }
                }, 80);
            },

            async uploadLogo() {
                if (!this.logoFile) return;
                const formData = new FormData();
                formData.append('logo', this.logoFile);
                try {
                    const res = await fetch('/api/settings/logo', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    });
                    if (res.ok) { this.messageSuccess = 'Logo berhasil diubah'; setTimeout(() => window.location.reload(), 1000); }
                } catch (e) { this.messageError = 'Gagal mengunggah logo'; }
            }
        }));
    });
</script>

<div class="settings-wrap" x-data="settingsManager">

    <x-slot:shop>
        1
    </x-slot:shop>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Toko</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola data toko berdasarkan preferensi anda</p>
        </div>
    </div>

    <div class="flash-success" x-show="messageSuccess" x-text="messageSuccess" style="display: none;"></div>
    <div class="flash-error" x-show="messageError" x-text="messageError" style="display: none;"></div>

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
            <form @submit.prevent="saveInfo()">
                <label class="s-label" for="store_name">Nama Toko</label>
                <input type="text" id="store_name" x-model="shop.name" placeholder="Nama Toko..." class="s-input">
                <label class="s-label" for="address">Alamat Toko</label>
                <input type="text" id="address" x-model="shop.address" placeholder="Alamat Toko..." class="s-input">
                <button type="submit" class="btn-save">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Info Toko
                </button>
            </form>
        </div>
    </div>

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
            <form @submit.prevent="uploadLogo()">
                <div class="logo-upload-area">
                    <input type="file" id="logoInput" accept=".jpg,.jpeg,.png,.svg,.webp" style="display:none" @change="handleLogoSelect($event.target)">
                    <div class="logo-dropzone" id="logoDrop" @click="document.getElementById('logoInput').click()" @dragover.prevent="handleDragOver($event)" @dragleave.prevent="handleDragLeave($event)" @drop.prevent="handleDrop($event)">
                        <div class="logo-preview-wrap">
                            <template x-if="logoPreviewUrl"><img id="logoPreviewImg" :src="logoPreviewUrl" alt="Logo"></template>
                            <template x-if="!logoPreviewUrl"><div class="logo-placeholder"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg></div></template>
                        </div>
                        <div class="logo-dropzone-text">
                            <p class="logo-dropzone-title" x-text="logoPreviewUrl ? 'Ganti logo toko' : 'Upload logo toko'"></p>
                            <p class="logo-dropzone-sub">Drag & drop atau klik untuk memilih file</p>
                        </div>
                        <button type="button" class="btn-upload-logo" @click.stop="document.getElementById('logoInput').click()">Upload File</button>
                    </div>
                    <div class="upload-progress" :class="{ 'show': isLogoUploading }">
                        <span x-text="logoUploadFileName"></span>
                        <div class="upload-progress-track"><div class="upload-progress-fill" :style="'width: ' + logoUploadPct + '%'"></div></div>
                    </div>
                </div>
                <button type="submit" class="btn-save" :disabled="!logoFile" style="margin-top:16px;">Simpan Logo</button>
            </form>
        </div>
    </div>

    <div class="s-card">
        <div class="s-card-header">
            <div class="s-card-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary, #f97316)">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
            </div>
            <div>
                <p class="s-card-title">Tema Warna</p>
                <p class="s-card-sub">Pilih palet warna untuk tampilan sistem</p>
            </div>
        </div>
        <div class="s-card-body">
            @php
                $themeColors = [
                    'ember'  => ['primary' => '#f97316', 'accent' => '#fb923c', 'sidebar' => '#0f172a', 'bg' => '#fff7ed'],
                    'ocean'  => ['primary' => '#0ea5e9', 'accent' => '#38bdf8', 'sidebar' => '#0c1a2e', 'bg' => '#f0f9ff'],
                    'forest' => ['primary' => '#16a34a', 'accent' => '#4ade80', 'sidebar' => '#052e16', 'bg' => '#f0fdf4'],
                    'violet' => ['primary' => '#7c3aed', 'accent' => '#a78bfa', 'sidebar' => '#1e1035', 'bg' => '#faf5ff'],
                    'rose'   => ['primary' => '#e11d48', 'accent' => '#fb7185', 'sidebar' => '#1a0010', 'bg' => '#fff1f2'],
                ];
            @endphp
            @php
                $themeLabels = [
                    'ember'  => 'Ember',
                    'ocean'  => 'Ocean',
                    'forest' => 'Forest',
                    'violet' => 'Violet',
                    'rose'   => 'Rose',
                ];
            @endphp
            <div class="theme-grid">
                @foreach($themeColors as $key => $colors)
                <div class="theme-option">
                    <input type="radio" name="theme" id="theme_{{ $key }}" value="{{ $key }}" :checked="currentTheme === '{{ $key }}'" @change="changeTheme('{{ $key }}')">
                    <label for="theme_{{ $key }}" class="theme-tile" style="--t-primary: {{ $colors['primary'] }}">
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
        </div>
    </div>
</div>
</x-layouts.main>