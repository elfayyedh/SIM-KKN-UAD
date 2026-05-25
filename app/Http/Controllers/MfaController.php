<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MfaController extends Controller
{
    public function index()
    {
        // Pastikan punya sesi mfa_verified === false
        if (session('mfa_verified') !== false) {
            return redirect()->route('dashboard');
        }

        return view('auth.mfa');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|numeric|digits:6'
        ], [
            'otp_code.required' => 'Kode OTP wajib diisi.',
            'otp_code.numeric' => 'Kode OTP harus berupa angka.',
            'otp_code.digits' => 'Kode OTP harus 6 digit.'
        ]);

        $user = Auth::user();

        // Cek kecocokan OTP & belum kedaluwarsa
        if ($user->otp_code == $request->otp_code && now()->lessThanOrEqualTo($user->otp_expires_at)) {
            // Berhasil
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            session(['mfa_verified' => true]);

            // Lanjut ke pengalihan Role, sama seperti di AuthController
            $dosen = $user->dosen; 
            if ($dosen) {
                // Role dosen sudah di-set di AuthController, tinggal redirect
                return redirect()->route('dashboard')->with('success', 'Verifikasi berhasil.');
            } else {
                return redirect()->route('dashboard')->with('success', 'Verifikasi berhasil.');
            }
        }

        return back()->with('error', 'Kode OTP tidak valid atau sudah kedaluwarsa.');
    }
}
