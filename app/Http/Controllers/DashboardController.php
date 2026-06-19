<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index () 
    {
        $totalTransactions = \App\Models\Transaction::count();
        $todayTransactions = \App\Models\Transaction::whereDate('created_at', today())->count();
        $totalRevenue = \App\Models\Transaction::sum('grand_total');
        $activeProducts = Product::where('stock', '>', 0)->count();

        $recentTransactions = \App\Models\Transaction::with('user')->latest()->take(4)->get();

        $topProducts = \App\Models\TransactionDetail::select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(qty) as total_qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $totalSales = \App\Models\TransactionDetail::sum('qty') ?: 1;

        return view('dashboard', compact('totalTransactions', 'todayTransactions', 'totalRevenue', 'activeProducts', 'recentTransactions', 'topProducts', 'totalSales'));
    }

    public function cashier () 
    {
        $products = Product::where('stock', '>', 0)->get(['id', 'code', 'name', 'price', 'stock']);
        return view('cashier', compact('products'));
    }

    public function products () 
    {
        $products = Product::orderBy('created_at', 'desc')->get(['id', 'code', 'name', 'price', 'stock']);
        return view('products.index', compact('products'));
    }

    public function transactions () 
    {
        $transactions = \App\Models\Transaction::with('details.product')->latest()->get();
        return view('transactions.index', compact('transactions'));
    }
}
