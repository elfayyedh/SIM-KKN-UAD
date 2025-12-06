<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 1. Pastikan Auth di-import

class RoleSelectionController extends Controller
{
    /**
     * Ganti peran aktif (session) untuk user.
     * Dipanggil dari link "Ganti Peran" di header.
     *
     * @param string $role_id Ini adalah ID dari tabel 'user_role' (BUKAN 'roles')
     * @return \Illuminate\Http\RedirectResponse
     */

    public function chooseRole()
    {
        $user = Auth::user();
        
        if (session('user_is_dosen', false)) {
            return redirect()->route('dashboard');
        }

        $roles = $user->userRoles()->with('role')->get();

        if ($roles->count() == 1) {
            session(['selected_role' => $roles->first()->id]);
            return redirect()->route('dashboard');
        }

        return view('auth.choose-role', [
            'roles' => $roles
        ]);
    }

    public function setRole($role_id) 
    {
        $hasRole = Auth::user()->userRoles()->where('id', $role_id)->exists();
        if (!$hasRole) {
            abort(403, 'Anda tidak memiliki hak akses untuk peran tersebut.');
        }
        session(['selected_role' => $role_id]);
        return redirect()->route('dashboard');
    }
}