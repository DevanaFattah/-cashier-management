<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect based on their role
        if ($user->role === 'kasir') {
            return redirect()->route('kasir.dashboard');
        } elseif (in_array($user->role, ['owner', 'superadmin'])) {
            return redirect()->route('dashboard');
        }

        abort(403, 'Unauthorized action.');
    }
}
