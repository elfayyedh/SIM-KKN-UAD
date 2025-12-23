<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $loginInput = $request->input('email'); 
        $password = $request->input('password');
        $credentials = [];

        if (is_numeric($loginInput)) {
            $mahasiswa = Mahasiswa::where('nim', $loginInput)->with('userRole.user')->first();
            
            if ($mahasiswa) {
                // Cek status mahasiswa
                if ($mahasiswa->status == 0) {
                    return redirect()->back()->with(['error' => 'Akun Anda tidak aktif. Silakan hubungi administrator.']);
                }
                
                if ($mahasiswa->userRole && $mahasiswa->userRole->user) {
                    $emailAsli = $mahasiswa->userRole->user->email;
                    $credentials = ['email' => $emailAsli, 'password' => $password];
                }
            }
        } else {
            $credentials = ['email' => $loginInput, 'password' => $password];
        }

        if (empty($credentials) || !Auth::attempt($credentials)) {
            return redirect()->back()->with(['error' => 'Username atau Password yang Anda masukkan salah']);
        }

        $request->session()->regenerate();
        $user = Auth::user(); 
        $dosen = $user->dosen; 

        if ($dosen) {
            session()->forget('selected_role');

            $isDpl = $dosen->isDpl();
            $isMonev = $dosen->isMonev();

            session([
                'user_is_dosen' => true,
                'user_has_role_dpl' => $isDpl,
                'user_has_role_monev' => $isMonev,
            ]);

            $activeRole = null;
            if ($isDpl) {
                $activeRole = 'dpl';
            } elseif ($isMonev) {
                $activeRole = 'monev';
            }

            session(['active_role' => $activeRole]);

            if (is_null($activeRole)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->back()->with('error', 'Anda terdaftar sebagai Dosen, namun belum memiliki penugasan DPL atau Tim Monev.');
            }

            return redirect()->route('dashboard');

        } else {
            session()->forget([
                'user_is_dosen', 
                'user_has_role_dpl', 
                'user_has_role_monev', 
                'active_role'
            ]);

            $roles = $user->userRoles; 
            if ($roles->isEmpty()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->back()->with(['error' => 'Akun Anda valid, tapi tidak memiliki peran. Hubungi Admin.']);
            }
            
            $defaultRole = $roles->first();
            session(['selected_role' => $defaultRole->id]);
            return redirect()->route('dashboard');
        }
    }

    public function logout(Request $request)
    {
        // Menghapus sesi pengguna
        Auth::logout();

        // Menghapus semua data sesi yang terkait dengan pengguna
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Mengarahkan pengguna ke halaman login atau beranda
        return redirect()->route('login.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}