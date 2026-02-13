<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $loginInput = $request->input('email'); 
        $password = $request->input('password');
        
        $loginBerhasil = false;

        if (env('PORTAL_LOGIN_URL')) {
            try {
                $response = Http::withOptions(['verify' => false]) 
                    ->asForm()
                    ->withHeaders([
                        'U4D-API-KEY' => env('PORTAL_API_KEY'), 
                    ])->post(env('PORTAL_LOGIN_URL'), [
                        'email' => $loginInput,
                        'password' => $password,
                    ]);

                $hasilApi = $response->json();

                if ($response->successful() && isset($hasilApi['status_code']) && $hasilApi['status_code'] == 'success') {
                    $emailResmi = $hasilApi['user_email']; 
                    
                    $user = User::where('email', $emailResmi)->first();
                    
                    if ($user) {
                        Auth::login($user); // Login Paksa
                        $loginBerhasil = true;
                    } else {
                        return redirect()->back()->with(['error' => 'Login Portal Sukses, tapi akun Anda belum terdaftar di database SIM KKN.']);
                    }
                }
            } catch (\Exception $e) {
                // Silent error: Jika API gagal diakses (misal server down), 
            }
        }

        if (!$loginBerhasil) {
            $emailUntukCekLokal = $loginInput;
            
            if (is_numeric($loginInput)) {
                $mhs = \App\Models\Mahasiswa::where('nim', $loginInput)->with('userRole.user')->first();
                if ($mhs && $mhs->userRole && $mhs->userRole->user) {
                    $emailUntukCekLokal = $mhs->userRole->user->email;
                }
            }

            if (Auth::attempt(['email' => $emailUntukCekLokal, 'password' => $password])) {
                $loginBerhasil = true;
            }
        }

        if (!$loginBerhasil) {
            return redirect()->back()->with(['error' => 'Login Gagal! Username atau Password salah.']);
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
            if ($isDpl) $activeRole = 'dpl';
            elseif ($isMonev) $activeRole = 'monev';
            session(['active_role' => $activeRole]);
            if (is_null($activeRole)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->back()->with('error', 'Anda terdaftar sebagai Dosen, namun belum memiliki penugasan DPL atau Tim Monev.');
            }
            return redirect()->route('dashboard');
        } else {
            session()->forget(['user_is_dosen', 'user_has_role_dpl', 'user_has_role_monev', 'active_role']);
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
        // Ambil user sebelum logout lokal untuk dikirim ke API
        $user = Auth::user();

        // 1. TEMBAK API LOGOUT (TAMBAHAN)
        if ($user) {
            try {
                Http::asForm()->withHeaders([
                    'apikey' => env('PORTAL_API_KEY'),
                ])->post(env('PORTAL_LOGOUT_URL'), [
                    'email' => $user->email,
                    'password' => '', // Kosongkan jika API tidak mewajibkan pass saat logout
                ]);
            } catch (\Exception $e) {
                // Silent error: Jika API logout gagal (misal server down), 
                // tetap lanjutkan logout lokal agar user bisa keluar dari SIM KKN.
            }
        }

        // 2. LOGOUT LOKAL (LOGIKA LAMA)
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

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