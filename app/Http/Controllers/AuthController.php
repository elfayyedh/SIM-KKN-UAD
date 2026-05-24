<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\Mahasiswa;
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

        Log::info('LOGIN attempt received', [
            'login_input' => $loginInput,
            'is_numeric_login_input' => is_numeric($loginInput),
            'credentials_email' => $credentials['email'] ?? null,
        ]);

        // Validasi kredensial manual untuk debugging dan akurasi
        // (Auth::validate($credentials) bisa gagal jika provider field tidak sesuai)
        if (empty($credentials)) {
            Log::warning('LOGIN credentials empty');
            return redirect()->back()->with(['error' => 'Username atau Password yang Anda masukkan salah']);
        }

        $userForCheck = Auth::getProvider()->retrieveByCredentials([
            'email' => $credentials['email'],
        ]);

        if (!$userForCheck || !Hash::check($password, $userForCheck->password)) {
            Log::warning('LOGIN credentials invalid', [
                'credentials_email' => $credentials['email'] ?? null,
                'user_found' => (bool) $userForCheck,
            ]);
            // Jangan sampai OTP/Email dikirim saat kredensial gagal
            return redirect()->back()->with(['error' => 'Username atau Password yang Anda masukkan salah']);
        }



        // Ambil user tanpa melakukan login
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $credentials['email']]);
        if (!$user) {
            return redirect()->back()->with(['error' => 'Username atau Password yang Anda masukkan salah']);
        }

        // Rate limit untuk permintaan OTP
        $rateKey = 'email-otp:' . $user->email . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return redirect()->back()->with(['error' => 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.']);
        }
        RateLimiter::hit($rateKey, 60);
        if (!$user) {
            return redirect()->back()->with(['error' => 'Username atau Password yang Anda masukkan salah']);
        }

        $otp = (string) random_int(100000, 999999);
        $otpHash = Hash::make($otp);
        $expiresAt = Carbon::now()->addMinutes(5);

        $emailOtpId = (string) Str::uuid();

        EmailOtp::create([
            'id' => $emailOtpId,
            'user_id' => $user->id,
            'otp_hash' => $otpHash,
            'expires_at' => $expiresAt,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        Mail::to($user->email)->send(new EmailOtpMail($user->email, $otp, 5));

        // Simpan OTP sementara di session
        $request->session()->put('login_pending_email_otp_id', $emailOtpId);
        $request->session()->put('login_pending_user_id', $user->id);
        $request->session()->regenerate();

return redirect()->route('login.otp.show');
    }

    public function showOtpForm()
    {
        if (!session()->has('login_pending_email_otp_id') || !session()->has('login_pending_user_id')) {
            return redirect()->route('login.index')->with('error', 'Sesi verifikasi OTP tidak valid. Silakan login ulang.');
        }

        return view('auth.otp');
    }


    public function verifyOtp(VerifyOtpRequest $request)
    {
        $pendingOtpId = $request->session()->get('login_pending_email_otp_id');
        $pendingUserId = $request->session()->get('login_pending_user_id');

        if (!$pendingOtpId || !$pendingUserId) {
            return redirect()->route('login.index')->with('error', 'Sesi verifikasi OTP tidak valid. Silakan login ulang.');
        }

        $otp = $request->input('otp');

        $emailOtp = EmailOtp::where('id', $pendingOtpId)
            ->where('user_id', $pendingUserId)
            ->first();

        if (!$emailOtp) {
            return redirect()->back()->with('error', 'OTP tidak valid.');
        }

        if ($emailOtp->used_at !== null) {
            return redirect()->back()->with('error', 'OTP sudah digunakan.');
        }

        if (Carbon::now()->greaterThan($emailOtp->expires_at)) {
            return redirect()->back()->with('error', 'OTP sudah kedaluwarsa.');
        }

        if (!Hash::check($otp, $emailOtp->otp_hash)) {
            return redirect()->back()->with('error', 'OTP salah.');
        }

        $emailOtp->used_at = Carbon::now();
        $emailOtp->save();

        $request->session()->forget(['login_pending_email_otp_id', 'login_pending_user_id']);

        $user = $emailOtp->user;
        Auth::login($user);

        // lanjut alur existing setelah login
        // (menjaga logika role selection yang ada di sistem saat sudah Auth::user())
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
        }

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

    public function logout(Request $request)
    {
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