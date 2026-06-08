<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckMfa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user belum login, lewatkan saja (biar auth middleware yang tangani)
        if (!Auth::check()) {
            return $next($request);
        }

        // Kalau di session mfa belum terverifikasi
        if (session('mfa_verified') === false) {
            // Izinkan akses ke rute mfa
            if ($request->routeIs('mfa.index') || $request->routeIs('mfa.verify') || $request->routeIs('logout')) {
                return $next($request);
            }
            
            // Redirect ke halaman OTP
            return redirect()->route('mfa.index');
        }

        return $next($request);
    }
}
