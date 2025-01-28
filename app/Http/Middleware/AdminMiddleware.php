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
        if (Auth::check()) {
            $user = Auth::user();

            // Jika pengguna memiliki lebih dari 1 role dan session 'selected_role' belum diatur
            if ($user->userRoles->find(session('selected_role'))->role->nama_role != "Admin") {
                return redirect()->route('not-found');
            }

        }
        return $next($request);
    }
}