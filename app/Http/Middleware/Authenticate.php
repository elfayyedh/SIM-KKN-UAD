<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Cek apakah pengguna belum login
        if (Auth::guard($guards)->guest()) {
            return redirect()->route('login.index'); // Halaman login
        }

        if (Auth::check()) {
            $user = Auth::user();
            // Jika pengguna memiliki lebih dari 1 role dan session 'selected_role' belum diatur
            if ($user->userRoles->count() > 1 && !session('selected_role')) {
                return redirect()->route('choose.role');
            }

            // Jika session 'selected_role' belum diatur, set ke role pertama pengguna
            if (!session('selected_role')) {
                session(['selected_role' => $user->userRoles->first()->id]);
            }
        }

        return $next($request);
    }
}
