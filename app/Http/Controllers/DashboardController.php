<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index () 
    {
        return view('dashboard');
    }

    public function cashier () 
    {
        return view('cashier');
    }

    public function products () 
    {
        return view('products.index');
    }

    public function transactions () 
    {
        return view('transactions.index');
    }

    public function kasirDashboard () 
    {
        return view('kasir.dashboard');
    }
}
