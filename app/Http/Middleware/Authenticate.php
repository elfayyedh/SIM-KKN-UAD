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
        if (Auth::guard($guards)->guest()) {
            return redirect()->route('login.index'); 
        }

        if (Auth::check()) {
            if (session('user_is_dosen', false)) {
                return $next($request); 
            }
            
            $user = Auth::user();
            $userRoles = $user->userRoles; 

            if ($userRoles->count() > 1 && !session('selected_role')) {
                return redirect()->route('choose.role'); 
            }

            if ($userRoles->count() > 0 && !session('selected_role')) {
                session(['selected_role' => $userRoles->first()->id]);
            }

            if ($userRoles->count() == 0) {
                 Auth::logout();
                 $request->session()->invalidate();
                 $request->session()->regenerateToken();
                 return redirect()->route('login.index')->with('error', 'Akun Anda tidak memiliki peran.');
            }
        }

        return $next($request);
    }
}