<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenRoleSwitchController extends Controller
{
    public function switchRole(Request $request)
    {
        if (!session('user_is_dosen', false)) {
            abort(403, 'Hanya Dosen yang bisa pindah peran DPL/Monev.');
        }

        $request->validate([
            'role' => ['required', Rule::in(['dpl', 'monev'])],
        ]);

        $newRole = $request->role;

        if ($newRole == 'dpl' && !session('user_has_role_dpl')) {
            abort(403, 'Akses ditolak.');
        }
        if ($newRole == 'monev' && !session('user_has_role_monev')) {
            abort(403, 'Akses ditolak.');
        }

        session(['active_role' => $newRole]);

        return redirect()->route('dashboard');
    }
}