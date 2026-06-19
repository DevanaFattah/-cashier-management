<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionDetail;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(11)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'shop_id' => '1',
            'roles' => 'superadmin'
        ]);

        User::factory()->create([
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'shop_id' => '1',
            'roles' => 'cashier'
        ]);

        Setting::create([
            'theme' => 'ember',
            'active' => 1,
        ]);
        
        Setting::create([
            'theme' => 'ocean',
            'active' => 0,
        ]);

        Setting::create([
            'theme' => 'forest',
            'active' => 0,
        ]);

        Setting::create([
            'theme' => 'violet',
            'active' => 0,
        ]);

        Setting::create([
            'theme' => 'rose',
            'active' => 0,
        ]);

        Shop::create([
            'name' => 'Toko Maju Jaya',
            'address' => 'Jl. Dr. Supomo No.23, Sriwedari, Kec. Laweyan, Kota Surakarta, Jawa Tengah 57141',
            'path_logo' => 'dsadasdas',
            'setting_id' => 2
        ]);

        
        $products = [
            // Data Utama
            ['code' => '#0OCC12', 'name' => 'Chocolate Stick', 'price' => 50000, 'stock' => 100],
            ['code' => '#0SWC12', 'name' => 'Potato Snack', 'price' => 25000, 'stock' => 75],
            ['code' => '#0SSC12', 'name' => 'Orange Juice', 'price' => 17000, 'stock' => 50],
            ['code' => '#0RVC12', 'name' => 'Mineral Water', 'price' => 5000, 'stock' => 200],
            ['code' => '#0MRC12', 'name' => 'Soda Water', 'price' => 10000, 'stock' => 80],
            ['code' => '#0MCC12', 'name' => 'Shampo', 'price' => 30000, 'stock' => 40],
            // Data Dummy Tambahan
            ['code' => '#0BND12', 'name' => 'Bread', 'price' => 15000, 'stock' => 60],
            ['code' => '#0CFE12', 'name' => 'Coffee Cup', 'price' => 20000, 'stock' => 120],
            ['code' => '#0MKK12', 'name' => 'Fresh Milk', 'price' => 22000, 'stock' => 45],
            ['code' => '#0TSH12', 'name' => 'Tissue Box', 'price' => 12000, 'stock' => 150],
        ];

        

        $transactions = [
            // Transaksi 1 (Data Utama)
            [
                'user_id' => 1, 
                'grand_total' => 269000, 
                'payment_method' => 'cash',
                'created_at' => now()
            ],
            // Transaksi 2 (Beli Bread x2, Coffee x1 = 30k + 20k)
            [
                'user_id' => 1, 
                'grand_total' => 50000, 
                'payment_method' => 'qris',
                'created_at' => now()->subHours(2)
            ],
            // Transaksi 3 (Beli Mineral Water x5, Fresh Milk x2 = 25k + 44k)
            [
                'user_id' => 2, 
                'grand_total' => 69000, 
                'payment_method' => 'cash',
                'created_at' => now()->subDays(1)
            ],
            // Transaksi 4 (Beli Potato Snack x2, Soda x3, Tissue x1 = 50k + 30k + 12k)
            [
                'user_id' => 1, 
                'grand_total' => 92000, 
                'payment_method' => 'debit',
                'created_at' => now()->subDays(2)
            ],
            // Transaksi 5 (Beli Choco Stick x1, Orange Juice x3 = 50k + 51k)
            [
                'user_id' => 2, 
                'grand_total' => 101000, 
                'payment_method' => 'qris',
                'created_at' => now()->subDays(3)
            ],
        ];

        $details = [
            // ---------- DETAIL TRANSAKSI 1 ----------
            ['transaction_id' => 1, 'product_id' => 1, 'qty' => 3, 'subtotal' => 150000],
            ['transaction_id' => 1, 'product_id' => 2, 'qty' => 1, 'subtotal' => 25000],
            ['transaction_id' => 1, 'product_id' => 3, 'qty' => 2, 'subtotal' => 34000],
            ['transaction_id' => 1, 'product_id' => 4, 'qty' => 4, 'subtotal' => 20000],
            ['transaction_id' => 1, 'product_id' => 5, 'qty' => 1, 'subtotal' => 10000],
            ['transaction_id' => 1, 'product_id' => 6, 'qty' => 1, 'subtotal' => 30000],

            // ---------- DETAIL TRANSAKSI 2 ----------
            // (Bread x2, Coffee x1)
            ['transaction_id' => 2, 'product_id' => 7, 'qty' => 2, 'subtotal' => 30000],
            ['transaction_id' => 2, 'product_id' => 8, 'qty' => 1, 'subtotal' => 20000],

            // ---------- DETAIL TRANSAKSI 3 ----------
            // (Mineral Water x5, Fresh Milk x2)
            ['transaction_id' => 3, 'product_id' => 4, 'qty' => 5, 'subtotal' => 25000],
            ['transaction_id' => 3, 'product_id' => 9, 'qty' => 2, 'subtotal' => 44000],

            // ---------- DETAIL TRANSAKSI 4 ----------
            // (Potato Snack x2, Soda x3, Tissue x1)
            ['transaction_id' => 4, 'product_id' => 2, 'qty' => 2, 'subtotal' => 50000],
            ['transaction_id' => 4, 'product_id' => 5, 'qty' => 3, 'subtotal' => 30000],
            ['transaction_id' => 4, 'product_id' => 10, 'qty' => 1, 'subtotal' => 12000],

            // ---------- DETAIL TRANSAKSI 5 ----------
            // (Choco Stick x1, Orange Juice x3)
            ['transaction_id' => 5, 'product_id' => 1, 'qty' => 1, 'subtotal' => 50000],
            ['transaction_id' => 5, 'product_id' => 3, 'qty' => 3, 'subtotal' => 51000],
        ];

        foreach ($products as $key => $value) {
            Product::create($value);
        }

        foreach ($transactions as $key => $value) {
                Transaction::create($value);
        }

        foreach ($details as $key => $value) {
                TransactionDetail::create($value);
        };

    }
}
