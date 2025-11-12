<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefreshUserRoles
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && session('user_is_dosen', false)) {
            
            $dosen = Auth::user()->dosen; 

            if (!$dosen) {
                 Auth::logout();
                 $request->session()->invalidate();
                 $request->session()->regenerateToken();
                 return redirect('/login')->with('error', 'Data Dosen tidak ditemukan.');
            }

            $isDpl = $dosen->isDpl();
            $isMonev = $dosen->isMonev();

            session([
                'user_has_role_dpl' => $isDpl,
                'user_has_role_monev' => $isMonev,
            ]);

            $activeRole = session('active_role');
            
            if ($activeRole == 'dpl' && !$isDpl) {
                $activeRole = $isMonev ? 'monev' : null; 
                session(['active_role' => $activeRole]);
            }
            elseif ($activeRole == 'monev' && !$isMonev) {
                $activeRole = $isDpl ? 'dpl' : null; 
                session(['active_role' => $activeRole]);
            }
            elseif (!$isDpl && !$isMonev) {
                session(['active_role' => null]);
            }

            if (is_null(session('active_role'))) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/login')->with('error', 'Penugasan DPL/Monev Anda telah dicabut oleh Admin.');
            }
        }

        return $next($request);
    }
}