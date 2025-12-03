<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // --- INI KODE PAKSA-MATI ---
        // Kita tidak pakai 'if' statement sama sekali.
        // Kita perintahkan middleware ini untuk SELALU
        // menghapus header CSP, tidak peduli environment.
        
        $response = $next($request);
        $response->headers->remove('Content-Security-Policy');
        return $response;
    }
}