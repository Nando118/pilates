<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah pengguna terautentikasi dan memiliki peran admin
        if (!Auth::check() || !$request->user()->hasRole("admin") && !$request->user()->hasRole("super_admin")) {
            // Redirect ke halaman yang diinginkan jika tidak memiliki akses
            return abort(403, "Unauthorized");
        }

        return $next($request);
    }
}
