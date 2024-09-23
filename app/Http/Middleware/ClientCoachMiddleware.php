<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientCoachMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah pengguna terautentikasi
        if (!Auth::check()) {
            return abort(403, "Unauthorized");
        }

        // Periksa apakah pengguna memiliki peran 'coach' atau 'client'
        if (!$request->user()->hasRole('coach') && !$request->user()->hasRole('client')) {
            // Jika pengguna tidak memiliki peran 'coach' dan tidak memiliki peran 'client', akses ditolak
            return abort(403, "Unauthorized");
        }

        return $next($request);
    }
}
