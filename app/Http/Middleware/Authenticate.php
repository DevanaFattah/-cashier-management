<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan user sudah login sebelum mengakses route tertentu.
 * Jika belum login → redirect ke halaman login.
 */
class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            // Simpan URL yang ingin diakses agar bisa redirect setelah login
            if (! $request->expectsJson()) {
                session(['url.intended' => $request->url()]);
            }

            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
