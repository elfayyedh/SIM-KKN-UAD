<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; 
class CheckActiveRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  (kirim 'dpl' atau 'monev')
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!session('user_is_dosen', false)) {
            abort(403, 'Akses ditolak.');
        }

        if (session('active_role') != $role) {
            
            $correctDashboard = session('active_role', 'dashboard'); 
            
            if ($request->routeIs('dashboard')) {
                return $next($request);
            }

            return redirect()->route($correctDashboard . '.dashboard')
                             ->with('error', 'Halaman tidak sesuai dengan peran aktif Anda.');
        }

        return $next($request);
    }
}