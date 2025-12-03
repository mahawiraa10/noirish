<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  ...string  $roles  // Ini akan nangkep 'admin' (atau role lain)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek ada user yang login gak & punya role gak
        if (! $request->user() || ! $request->user()->role) {
            // Kalo gak ada, tendang
            abort(403, 'Unauthorized action.');
        }

        // 2. Cek apakah role user ada di dalem daftar $roles yang diizinin
        if (in_array($request->user()->role, $roles)) {
            // 3. Kalo rolenya ada ('admin'), lanjutin ke halaman
            return $next($request);
        }

        // 4. Kalo rolenya gak cocok, tendang
        abort(403, 'This action is unauthorized.');
    }
}