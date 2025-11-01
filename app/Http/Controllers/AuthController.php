<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // Ambil kredensial dari request
        $credentials = $request->only('email', 'password');

        // Coba autentikasi
        if (Auth::attempt($credentials)) {
            // Autentikasi berhasil, redirect ke dashboard
            return redirect()->route('dashboard');
        } else {
            // Autentikasi gagal, redirect kembali dengan pesan error
            return redirect()->back()->with(['error' => 'Email atau Password yang Anda masukkan salah']);
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
