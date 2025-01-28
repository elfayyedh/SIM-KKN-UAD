<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSelectionController extends Controller
{
    public function chooseRole()
    {
        $user = Auth::user();
        $roles = $user->userRoles;

        return view('auth.choose-role', compact('roles'));
    }

    public function setRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:user_role,id',
        ]);

        session(['selected_role' => $request->role_id]);

        return redirect()->route('dashboard');
    }
}