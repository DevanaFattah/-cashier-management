<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin(): View|RedirectResponse
    {
        // Jika sudah login, redirect sesuai role
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'kasir') {
                return redirect()->intended(route('kasir.dashboard'));
            }
            return redirect()->intended(route('dashboard'));
        }

        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $redirectRoute = $user->role === 'kasir' ? 'kasir.dashboard' : 'dashboard';
            $redirectUrl = route($redirectRoute);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil. Selamat datang kembali, ' . $user->name . '!',
                    'redirect_url' => $redirectUrl,
                    'user' => $user
                ]);
            }

            return redirect()->intended($redirectUrl)
                ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password yang Anda masukkan salah.'
            ], 401);
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Anda telah berhasil keluar.'
            ]);
        }

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar.');
    }
}
