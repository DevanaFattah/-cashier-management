# 🏪 Sistem POS & Manajemen Kasir Franchise (Manajemen Kasir)

Selamat datang di repositori **Manajemen Kasir**, sebuah sistem *Point of Sale* (POS) dan manajemen toko franchise yang dirancang dengan fleksibilitas tinggi. Aplikasi ini mengusung konsep **Hybrid Monolith** (Backend API-like dengan frontend Blade & Alpine.js), mendukung re-branding dinamis (penggantian nama toko, logo, dan tema warna langsung dari panel admin), manajemen transaksi, integrasi pembayaran digital, manajemen stok otomatis, serta cetak struk (Thermal 80mm & Invoice A4).

---

## 📌 Fitur Utama Aplikasi

1. **Dynamic Theming & Branding System**: Logo, nama, alamat toko, dan tema warna sistem (`ember`, `ocean`, `forest`, `violet`, `rose`) dapat diubah secara *real-time* dari dashboard admin dan langsung diterapkan ke seluruh halaman serta layout struk PDF.
2. **Role-Based Access Control (RBAC)**: Terdapat 3 role pengguna dengan hak akses yang terkelola dengan baik:
   - **Superadmin**: Memiliki kontrol penuh, manajemen akun user, konfigurasi toko, produk, transaksi, dan log sistem.
   - **Owner**: Memantau statistik performa toko global, total omzet, penjualan produk terlaris, dan pengaturan tema/identitas toko.
   - **Kasir**: Melakukan transaksi POS, melihat daftar transaksi kasir, dan memantau rangkuman performa harian kasir bersangkutan.
3. **Cashier POS Interface**: Halaman kasir interaktif yang mendukung pencarian kode/barcode produk, manajemen keranjang belanja secara asinkron (Alpine.js), kalkulasi kembalian, stok *checking*, dan penentuan metode pembayaran.
4. **Integrasi Payment Gateway**: Mendukung pembayaran nontunai menggunakan **Midtrans Snap** (QRIS/Transfer Bank) serta metode tunai (*cash*).
5. **Cetak & Preview Struk (PDF Engine)**: Ekspor transaksi ke format PDF dengan dua opsi layout: **Thermal (80mm)** untuk printer kasir mini dan **Invoice A4** untuk arsip kantor.
6. **API & Postman Ready**: Seluruh operasi transaksi, pengelolaan produk, dan data statistik dapat diuji menggunakan collection API Postman yang disertakan.

---

## 💻 Kebutuhan Sistem (System Requirements)

Sebelum memulai instalasi, pastikan lingkungan kerja (server/komputer lokal) Anda memenuhi spesifikasi berikut:
- **PHP** >= 8.2
- **Composer** (Dependency Manager untuk PHP)
- **Node.js** >= 18 & **npm** (untuk build manager frontend Vite)
- **MySQL** / **MariaDB** (sebagai database utama)
- **Ekstensi PHP yang Wajib Aktif**: 
  - (Cek pada file php.ini lalu aktifkan setiap ekstensi di bawah (WAJIB))
  - `GD` (Diperlukan oleh DomPDF untuk merender/mencetak logo toko pada struk PDF)
  - `PDO_MySQL`
  - `BCMath`
  - `Ctype`, `Fileinfo`, `Mbstring`, `XML`, `Zip`

---

## ⚙️ Langkah Instalasi & Konfigurasi Lokal

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di komputer lokal Anda:

### 1. Klon Repositori (Clone Repository)
```bash
git clone https://github.com/DevanaFattah/-cashier-management.git
cd -cashier-management
```

### 2. Salin Berkas Lingkungan (Configure Environment File)
Salin berkas `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka berkas `.env` dan konfigurasikan koneksi database Anda pada bagian berikut:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=manajemen-kasir
DB_USERNAME=root
DB_PASSWORD=
```
*Catatan: Buat database baru bernama `manajemen-kasir` di DBMS Anda (seperti phpMyAdmin atau Laragon).*

Lalu, isi kredensial **Midtrans Sandbox** Anda ke bagian paling bawah berkas `.env` (Kredensial Sandbox ini dapat Anda temukan pada lampiran berkas terpisah atau menggunakan akun Midtrans Sandbox Anda sendiri):
```env
MIDTRANS_SERVER_KEY=masukkan_server_key_sandbox_di_sini
MIDTRANS_CLIENT_KEY=masukkan_client_key_sandbox_di_sini
MIDTRANS_IS_PRODUCTION=false
```

### 3. Pasang Dependensi Backend (Install Composer Packages)
```bash
composer install
```

### 4. Pasang Dependensi Frontend (Install Node Packages)
```bash
npm install
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Jalankan Migrasi & Pengisian Data Awal (Migration & Database Seeding)
Jalankan perintah ini untuk membangun tabel database beserta data uji bawaan (seperti daftar produk awal, user akun demo, dan data toko default):
```bash
php artisan migrate:fresh --seed
```

### 7. Buat Symbolic Link untuk Media Storage
Agar gambar logo toko yang diunggah dapat diakses oleh aplikasi dan sistem pencetak PDF, jalankan:
```bash
php artisan storage:link
```

### 8. Jalankan Aplikasi
Buka dua terminal terpisah untuk menjalankan server backend dan frontend compiler:

* **Terminal 1** (Laravel Backend Server):
  ```bash
  php artisan serve
  ```
  *(Aplikasi akan berjalan di http://127.0.0.1:8000)*

* **Terminal 2** (Vite Hot-Reloading Asset):
  ```bash
  npm run dev
  ```

---

## 🔑 Akun Demo Pengujian (Demo Accounts)

Untuk mempermudah dosen/penguji dalam menilai fitur Role-Based Access Control, gunakan akun demo di bawah ini untuk login ke aplikasi:

| Peran (Role) | Alamat Email | Kata Sandi (Password) | Hak Akses Utama |
|---|---|---|---|
| **Super Admin** | `superadmin@kasir.app` | `password` | Manajemen Toko, User, Produk, Transaksi, Log, dll |
| **Owner (Pemilik)**| `owner@kasir.app` | `password` | Statistik Keuangan Toko Global, Ubah Tema & Branding Toko |
| **Kasir** | `kasir@kasir.app` | `password` | Halaman POS Kasir, Cetak Struk, Riwayat & Statistik Kasir Pribadi |

---

## 📊 Matriks Hak Akses Halaman (RBAC Matrix)

| Halaman / Fitur | URL Route | Kasir | Owner | Superadmin |
|---|---|:---:|:---:|:---:|
| **Dashboard Utama (Toko Global)** | `/dashboard` | ❌ | ✅ | ✅ |
| **Dashboard Kasir (Personal Stats)**| `/kasir/dashboard` | ✅ | ❌ | ✅ |
| **Halaman Transaksi Kasir (POS)** | `/cashier` | ✅ | ❌ | ✅ |
| **Manajemen Produk (CRUD)** | `/products` | ✅ | ❌ | ✅ |
| **Riwayat Transaksi** | `/transactions/dashboard` | ✅ | ❌ | ✅ |
| **Hapus Transaksi (Cascade Details)**| Route DELETE | ❌ | ❌ | ✅ |
| **Pengaturan Branding & Tema** | `/settings` | ❌ | ✅ | ✅ |
| **Manajemen Pengguna (User CRUD)** | `/users` | ❌ | ❌ | ✅ |

---

## 🧪 Pengujian API via Postman

Aplikasi ini menyertakan file collection Postman untuk mempermudah pengujian REST API secara mandiri.
- Berkas collection: **`postman_collection.json`** yang terletak di root direktori project.
- **Cara Impor ke Postman**:
  1. Buka aplikasi Postman.
  2. Klik tombol **Import** di kiri atas.
  3. Pilih file `postman_collection.json` yang ada di direktori project ini.
- **CSRF & Autentikasi di Postman**:
  - Endpoint `/login` dan `/api/*` telah dikonfigurasi agar dapat diakses oleh Postman tanpa hambatan isu token CSRF (Bypass CSRF Token Mismatch).
  - Anda hanya perlu menembak request **`POST /login`** terlebih dahulu untuk mendapatkan session cookie, lalu memanggil endpoint API lainnya.

---

## 💡 Informasi Tambahan untuk Penguji (Troubleshooting)

1. **Logo Struk PDF Tidak Muncul / Error "GD required"**:
   - Jika saat mencetak struk muncul error atau logo tidak termuat, pastikan ekstensi **GD** pada PHP lokal Anda sudah diaktifkan.
   - Pada **Laragon**: Klik kanan ikon Laragon > **PHP** > **Extensions** > centang **gd** > **Reload/Restart** Laragon.
   - Pada **XAMPP / PHP standalone**: Buka file `php.ini`, temukan `;extension=gd` lalu hapus tanda titik koma (`;`) di depannya sehingga menjadi `extension=gd`, kemudian restart apache/server.
2. **Simulasi Transaksi Non-Tunai**:
   - Saat transaksi POS menggunakan metode pembayaran QRIS/Transfer Bank, kasir akan memunculkan popup pembayaran dari Midtrans.
   - Anda dapat menggunakan **[Midtrans Payment Simulator](https://simulator.sandbox.midtrans.com/)** untuk menyimulasikan transaksi pembayaran sukses agar status transaksi berubah secara otomatis menjadi *Settlement*.

---

Terima kasih atas perhatiannya. Semoga aplikasi ini dapat memberikan nilai dan gambaran sistem POS yang handal dan fleksibel. 
Selamat menguji! 🚀
