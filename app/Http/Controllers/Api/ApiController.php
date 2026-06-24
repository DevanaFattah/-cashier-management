<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Shop;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /**
     * Get dashboard overview statistics.
     */
    public function getDashboardStats()
    {
        $totalTransactions = Transaction::count();
        $todayTransactions = Transaction::whereDate('created_at', today())->count();
        $totalRevenue = Transaction::sum('grand_total');
        $activeProducts = Product::where('stock', '>', 0)->count();

        $recentTransactions = Transaction::with('user')->latest()->take(4)->get();

        $topProducts = \App\Models\TransactionDetail::select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(qty) as total_qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $totalSales = \App\Models\TransactionDetail::sum('qty') ?: 1;

        return response()->json([
            'success' => true,
            'message' => 'Statistik dashboard berhasil diambil',
            'data' => [
                'totalTransactions' => $totalTransactions,
                'todayTransactions' => $todayTransactions,
                'totalRevenue' => $totalRevenue,
                'activeProducts' => $activeProducts,
                'recentTransactions' => $recentTransactions,
                'topProducts' => $topProducts,
                'totalSales' => $totalSales
            ]
        ]);
    }

    /**
     * Get dashboard overview statistics for cashier (scoped to logged-in user).
     */
    public function getKasirStats()
    {
        $userId = auth()->id();
        $todayTransactions = Transaction::where('user_id', $userId)->whereDate('created_at', today())->count();
        $todayRevenue = Transaction::where('user_id', $userId)->whereDate('created_at', today())->sum('grand_total');

        $recentTransactions = Transaction::where('user_id', $userId)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $topProducts = \App\Models\TransactionDetail::select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(qty) as total_qty'))
            ->whereHas('transaction', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(3)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Statistik dashboard kasir berhasil diambil',
            'data' => [
                'todayTransactions' => $todayTransactions,
                'todayRevenue' => $todayRevenue,
                'recentTransactions' => $recentTransactions,
                'topProducts' => $topProducts
            ]
        ]);
    }

    /**
     * Get list of products.
     */
    public function getProducts(Request $request)
    {
        $products = Product::orderBy('created_at', 'desc')->get(['id', 'code', 'name', 'price', 'stock']);
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'data' => $products
        ]);
    }

    /**
     * Get list of transactions.
     */
    public function getTransactions(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $transactions = Transaction::with('details.product', 'user')
            ->latest()
            ->paginate($perPage);

        // Calculate today's revenue (only count settled transactions or all non-cancelled ones depending on business needs. Here we count all except cancel/deny/expire)
        $todayRevenue = Transaction::whereDate('created_at', today())
            ->whereNotIn('payment_status', ['cancel', 'deny', 'expire'])
            ->sum('grand_total');

        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi berhasil diambil',
            'data' => $transactions->items(),
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'total' => $transactions->total(),
            'per_page' => $transactions->perPage(),
            'today_revenue' => $todayRevenue,
        ]);
    }

    /**
     * Get active shop settings.
     */
    public function getSettings()
    {
        $shop = Shop::with('setting')->first();
        if ($shop) {
            $shop->setting_theme = $shop->setting?->theme;
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan toko berhasil diambil',
            'data' => $shop
        ]);
    }

    /**
     * Update shop details (Name & Address).
     */
    public function updateSettings(Request $request)
    {
        $shop = Shop::first();
        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $shop->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nama dan alamat toko berhasil diperbarui',
            'data' => $shop
        ]);
    }

    /**
     * Update active theme.
     */
    public function updateTheme(Request $request)
    {
        $shop = Shop::first();
        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'theme' => 'required|in:ember,ocean,forest,violet,rose',
        ]);

        $setting = Setting::where('theme', $validated['theme'])->first();
        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Tema tidak valid'
            ], 422);
        }

        $shop->update([
            'setting_id' => $setting->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tema berhasil diubah',
            'data' => $shop->load('setting')
        ]);
    }

    /**
     * Upload shop logo.
     */
    public function uploadLogo(Request $request)
    {
        $shop = Shop::first();
        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'logo' => 'required|file|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($shop->path_logo && Storage::disk('public')->exists($shop->path_logo)) {
                Storage::disk('public')->delete($shop->path_logo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $shop->update([
                'path_logo' => $path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logo berhasil diupload',
                'data' => $shop
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File logo tidak ditemukan'
        ], 400);
    }
}
