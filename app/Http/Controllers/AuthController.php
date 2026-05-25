<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Display login page
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Login process
     */
    public function login(LoginRequest $request)
    {
        $loginInput = $request->input('email');
        $password = $request->input('password');

        $credentials = [];

        // Login menggunakan NIM
        if (is_numeric($loginInput)) {

            $mahasiswa = Mahasiswa::where('nim', $loginInput)
                ->with('userRole.user')
                ->first();

            if ($mahasiswa) {

                if ($mahasiswa->status == 0) {
                    return redirect()->back()->with([
                        'error' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
                    ]);
                }

                if ($mahasiswa->userRole && $mahasiswa->userRole->user) {

                    $emailAsli = $mahasiswa->userRole->user->email;

                    $credentials = [
                        'email' => $emailAsli,
                        'password' => $password
                    ];
                }
            }

        } else {

            // Login menggunakan email
            $credentials = [
                'email' => $loginInput,
                'password' => $password
            ];
        }

        Log::info('LOGIN attempt received', [
            'login_input' => $loginInput,
            'is_numeric_login_input' => is_numeric($loginInput),
            'credentials_email' => $credentials['email'] ?? null,
        ]);

        // Validasi credential kosong
        if (empty($credentials)) {

            Log::warning('LOGIN credentials empty');

            return redirect()->back()->with([
                'error' => 'Username atau Password yang Anda masukkan salah'
            ]);
        }

        // Ambil user
        $userForCheck = Auth::getProvider()->retrieveByCredentials([
            'email' => $credentials['email'],
        ]);

        // Validasi password
        if (!$userForCheck || !Hash::check($password, $userForCheck->password)) {

            Log::warning('LOGIN credentials invalid', [
                'credentials_email' => $credentials['email'] ?? null,
                'user_found' => (bool) $userForCheck,
            ]);

            return redirect()->back()->with([
                'error' => 'Username atau Password yang Anda masukkan salah'
            ]);
        }

        // Ambil user final
        $user = Auth::getProvider()->retrieveByCredentials([
            'email' => $credentials['email']
        ]);

        if (!$user) {

            return redirect()->back()->with([
                'error' => 'User tidak ditemukan'
            ]);
        }

        // Rate limit OTP
        $rateKey = 'email-otp:' . $user->email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {

            $seconds = RateLimiter::availableIn($rateKey);

            return redirect()->back()->with([
                'error' => 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.'
            ]);
        }

        RateLimiter::hit($rateKey, 60);

        // OTP testing sementara
        $otp = '123456';

        $otpHash = Hash::make($otp);

        $expiresAt = Carbon::now()->addMinutes(5);

        $emailOtpId = (string) Str::uuid();

        // Simpan OTP ke database
        EmailOtp::create([
            'id' => $emailOtpId,
            'user_id' => $user->id,
            'otp_hash' => $otpHash,
            'expires_at' => $expiresAt,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        // Kirim email OTP
        Mail::to($user->email)->send(
            new EmailOtpMail($user->email, $otp, 5)
        );

        // Simpan session OTP
        $request->session()->put('login_pending_email_otp_id', $emailOtpId);

        $request->session()->put('login_pending_user_id', $user->id);

        $request->session()->regenerate();

        return redirect()->route('login.otp.show');
    }

    /**
     * Show OTP page
     */
    public function showOtpForm()
    {
        if (
            !session()->has('login_pending_email_otp_id') ||
            !session()->has('login_pending_user_id')
        ) {

            return redirect()->route('login.index')->with(
                'error',
                'Sesi verifikasi OTP tidak valid. Silakan login ulang.'
            );
        }

        return view('auth.otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $otp = (string) $request->input('otp');

        // OTP sementara testing
        if ($otp !== '123456') {

            return redirect()->back()->with([
                'error' => 'OTP tidak valid.'
            ]);
        }

        // Ambil user id dari session
        $pendingUserId = session('login_pending_user_id');

        if (!$pendingUserId) {

            return redirect()->route('login.index')->with([
                'error' => 'Session login tidak ditemukan.'
            ]);
        }

        // Cari user
        $user = User::find($pendingUserId);

        if (!$user) {

            return redirect()->route('login.index')->with([
                'error' => 'User tidak ditemukan.'
            ]);
        }

        // Login user
        Auth::login($user);

        // Hapus session OTP
        session()->forget([
            'login_pending_email_otp_id',
            'login_pending_user_id'
        ]);

        // Cek dosen
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

            session([
                'active_role' => $activeRole
            ]);

            return redirect()->route('dashboard');
        }

        // Role mahasiswa/admin
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

            return redirect()->route('login.index')->with([
                'error' => 'Akun tidak memiliki role.'
            ]);
        }

        $defaultRole = $roles->first();

        session([
            'selected_role' => $defaultRole->id
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}