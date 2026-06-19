# Project Documentation & Task Tracker (`manajemen-kasir`)

This file serves as the single source of truth for agents and developers working on the Cashier Management System (`manajemen-kasir`). It defines the project stack, architecture, database schemas, coding standards, feature statuses, and known issues.

---

## 📌 Project Overview
A franchise cashier management system designed for dynamic re-branding.
- **Dynamic Settings:** Shop names, addresses, logos, and system-wide themes can be changed dynamically by the store administrator without code changes. The theme and logo update immediately across all pages and on printed receipts.
- **Cashier Transaction System:** A modern POS (Point of Sale) register interface that supports barcode scanning/product code searches, item quantity adjustment, real-time total calculation, stock deduction, database persistence, and printing receipts (in both Thermal 80mm and A4 sizes).

---

## 🛠️ Technology Stack & Environment Configuration

### 1. Core Stack
- **Backend Framework:** Laravel 11.x (PHP 8.2+)
- **Frontend Asset Builder:** Vite 5.x
- **Frontend Styling:** Tailwind CSS 3.x (compiled via Vite)
- **Frontend Reactivity:** Alpine.js (embedded inside Laravel Blade layouts)
- **State Management:** Alpine.js data-binding (`x-data`)
- **PDF Engine:** Barryvdh DomPDF (`dompdf/dompdf` wrapper)

### 2. Dependency Breakdown (`composer.json` / `package.json`)
- **PHP PDF library:** `barryvdh/laravel-dompdf` for receipt PDF generation.
- **Node CSS build:** Tailwind CSS + PostCSS + Autoprefixer.

### 3. Execution Commands
- **Local Dev Server (PHP):** `php artisan serve` (defaults to `http://127.0.0.1:8000`)
- **Frontend Compiler:** `npm run dev` (Vite hot-reloading)
- **Database Seeder:** `php artisan db:seed` or `php artisan migrate:fresh --seed`

---

## 📐 Architectural Standards

### 1. Dynamic Theming System
Themes are configured in `config/themes.php` and loaded dynamically via the shop profile settings. The database stores the name of the active theme (e.g. `ember`), which maps to a set of color configurations.
- Colors are injected into the DOM layout using CSS variables inside [main.blade.php](file:///d:/laragon/www/manajemen-kasir/resources/views/components/layouts/main.blade.php):
  - `--primary`: Main brand color (e.g., `#f97316` for ember, `#0ea5e9` for ocean)
  - `--accent`: Accent highlight color
  - `--sidebar`: Dark sidebar background
  - `--revenue`: Dark/saturated background gradient card values
- **Standard:** Avoid hardcoded Tailwind color utilities (e.g. `bg-orange-500` or `text-violet-600`) in general layouts. Use semantic dynamic styles (e.g., `bg-[var(--primary)]` or classes utilizing CSS variables like `.gradient-btn`).

### 2. Controller & Data Flow Guidelines
- Keep controllers thin. Database persistence, stock adjustments, and calculations must be handled within transactional database blocks (`DB::beginTransaction()`).
- Data returned to API endpoints must always be structured JSON with uniform messages and error properties.

---

## 📊 Database Schema Details

### 1. `shops`
- `id` (Primary Key, Auto-increment)
- `name` (String, Store name)
- `address` (Text, Store address)
- `path_logo` (String, File path under public storage disk)
- `setting_id` (Foreign Key referencing `settings.id`)

### 2. `settings`
- `id` (Primary Key, Auto-increment)
- `theme` (Enum: `ember`, `ocean`, `forest`, `violet`, `rose`)
- `active` (Boolean, Theme status indicator)

### 3. `products`
- `id` (Primary Key, Auto-increment)
- `code` (String, Unique barcode or item code)
- `name` (String, Product name)
- `price` (Unsigned Integer, Unit price in IDR)
- `stock` (Unsigned Integer, Warehouse stock quantity)

### 4. `transactions`
- `id` (Primary Key, Auto-increment)
- `user_id` (Foreign Key referencing `users.id`)
- `grand_total` (Unsigned Integer, Total transaction amount)
- `payment_method` (Enum/String, e.g., `cash`, `qris`, `debit`)
- `created_at` / `updated_at` (Timestamps)

### 5. `transaction_details`
- `id` (Primary Key, Auto-increment)
- `transaction_id` (Foreign Key referencing `transactions.id` with cascade delete)
- `product_id` (Foreign Key referencing `products.id`)
- `qty` (Unsigned Integer, Quantity purchased)
- `subtotal` (Unsigned Integer, unit price * qty)

---

## 📝 Feature Status Tracker

### 1. Dynamic Branding & Settings
- [x] Shop name & address update forms
- [x] Logo upload & preview via drag-and-drop UI
- [x] Color theme switcher (`ember`, `ocean`, `forest`, `violet`, `rose`)
- [x] Global layout theme integration using CSS variables
- [/] Receipt dynamic branding (Logo loads correctly, but name/address fallback variable check is buggy)

### 2. Cashier POS Page (`/cashier`)
- [x] Fetch products and search/add by code or name
- [x] Cart list with increment, decrement, and item removal
- [x] Total price calculation
- [x] Payment modal & change calculation
- [x] Stock verification during transaction checkout
- [/] Stock deduction in warehouse (Implemented in DB transaction, but needs confirmation on edge cases)
- [ ] Choose payment method in cashier UI (Currently hardcoded to `'cash'` in the backend controller)

### 3. Receipt Generation & Printing
- [x] DomPDF integration
- [x] Thermal (80mm width) receipt styling
- [x] A4 invoice styling
- [x] Stream/Preview in a new tab upon checkout completion
- [x] Download PDF receipts
- [/] Dynamic logo/name rendering (Buggy variable referencing in Blade templates)

### 4. Transaction History (`/transactions/dashboard`)
- [x] View history table
- [x] Modal for viewing transaction details
- [x] Delete transaction route (with cascade delete for details)
- [ ] Correct relationship loading (Currently crashes due to relationship name mismatch in Eloquent model)

### 5. Dashboard Overview (`/dashboard`)
- [x] General stats cards (Total transactions, active products count, total revenue)
- [x] Recent transactions section (Crashes due to array syntax on objects)
- [/] Top selling products section (Currently completely hardcoded in Blade view, despite backend data being sent)

---

## ⚠️ Discovered Bugs & Technical Debt

### 1. Eloquent Relationship Mismatch (Critical)
- **Problem:** In [Transaction.php](file:///d:/laragon/www/manajemen-kasir/app/Models/Transaction.php), the relationship is defined as `detailTransactions()`. However, [DashboardController.php](file:///d:/laragon/www/manajemen-kasir/app/Http/Controllers/DashboardController.php) (line 44), [ReceiptController.php](file:///d:/laragon/www/manajemen-kasir/app/Http/Controllers/ReceiptController.php) (line 23), and various Blade views reference `details` or `details.product`.
- **Error:** Accessing transactions list or loading receipt PDFs crashes with: `Call to undefined relationship [details] on model [App\Models\Transaction]`.
- **Fix:** Rename `detailTransactions()` to `details()` in [Transaction.php](file:///d:/laragon/www/manajemen-kasir/app/Models/Transaction.php).

### 2. Database Seeder Structural Bug
- **Problem:** In [DatabaseSeeder.php](file:///d:/laragon/www/manajemen-kasir/database/seeders/DatabaseSeeder.php) (lines 119-149), the `$details` array is nested inside the `$transactions` array instead of being a separate sibling array.
- **Error:** Running `php artisan db:seed` crashes with database integrity errors since it attempts to seed the `$details` array as a transaction item.
- **Fix:** Move `$details` array definition outside of the `$transactions` array in the seeder file.

### 3. Layout Closure Tag Mismatch
- **Problem:**
  - In [cashier.blade.php](file:///d:/laragon/www/manajemen-kasir/resources/views/cashier.blade.php) (line 263), the page opens with `<x-layouts.main>` but closes with `</x-layouts.app>`.
  - In [index.blade.php](file:///d:/laragon/www/manajemen-kasir/resources/views/transactions/index.blade.php) (line 185), the page opens with `<x-layouts.main>` but closes with `</x-layouts.app>`.
- **Fix:** Correct the closing tags to `</x-layouts.main>`.

### 4. Logout Form Action Typo
- **Problem:** In [main.blade.php](file:///d:/laragon/www/manajemen-kasir/resources/views/components/layouts/main.blade.php) (line 124), the logout form is written as `<form action="post" action="{{ route('logout') }}">`.
- **Error:** `action="post"` is a typo for `method="POST"`. Since `action` is defined twice, browsers submit a GET request to a literal `/post` route instead of triggering a POST request to `/logout`, breaking the logout flow entirely.
- **Fix:** Update to `<form method="POST" action="{{ route('logout') }}">`.

### 5. Dashboard Recent Transactions Array Access Bug
- **Problem:** In [dashboard.blade.php](file:///d:/laragon/www/manajemen-kasir/resources/views/dashboard.blade.php) (lines 327 and 332), Eloquent model attributes are accessed as array offsets: `$trx[3]` and `$trx[4]`.
- **Error:** `$trx` is an Eloquent object, not an array. This will throw an error or return null.
- **Fix:** Update to `$trx->grand_total` and `$trx->payment_method` (or their actual attribute names).

### 6. Hardcoded "Top Selling Products" in Dashboard
- **Problem:** In [dashboard.blade.php](file:///d:/laragon/www/manajemen-kasir/resources/views/dashboard.blade.php) (lines 369-397), the top products list is hardcoded using static mock data.
- **Fix:** Replace with a dynamic loop over the `$topProducts` variable sent by the controller.

### 7. Hardcoded Payment Method in Cashier Checkout
- **Problem:** The POS system has a hardcoded `'cash'` payment method on checkout.
- **Fix:** Add a dropdown/selector in the cashier payment modal for payment method selection (Cash, QRIS, Debit, etc.) and pass it dynamically to the store transaction payload.

### 8. Receipt Layout Variable Access
- **Problem:** The PDF receipts (`receipt-thermal.blade.php` and `receipt-a4.blade.php`) look for `$shopName` and `$shopAddress` variables, but `ReceiptController.php` only passes `$shop`.
- **Fix:** Update the PDF Blade templates to use `$shop->name` and `$shop->address` directly or explicitly pass the variables from the controller.

---

## 🎨 UI Design & Aesthetic Guidelines
1. **Glassmorphism & Rich Styling:** Use curated harmonized color systems (HSL tailored) utilizing the dynamic `--primary` configuration.
2. **Typography:** Use modern premium fonts (e.g. Plus Jakarta Sans or DM Sans) for system operations.
3. **Responsive layouts:** Layout cards must scale cleanly across desktop monitors and POS terminal resolutions.

---

## 🚀 Execution & Verification Guidelines for Next Agents
1. **Bug Elimination First:** Fix bugs 1-5 immediately before attempting verification of secondary features.
2. **Db Refresh and Seed Validation:** Ensure that running `php artisan migrate:fresh --seed` runs to completion without errors and inserts all necessary shop settings, default products, transactions, and details.
3. **Dynamic Theme Switch Verification:** Navigate to Settings, update the theme, and confirm that the primary UI colors, sidebar active states, and receipt layout headings match the selected color palette immediately without caching issues.
4. **PDF Output Inspection:** Confirm receipt PDFs correctly show the customized logo, the newly modified address, and the updated shop name.
