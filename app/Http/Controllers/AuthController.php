<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\LoginAttempt;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {

        $throttleKey = 'login_bruteforce_' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return redirect()->back()->with(['error' => "Terlalu banyak percobaan gagal. Akun dikunci sementara. Silakan coba lagi dalam $seconds detik."]);
        }

        $loginInput = $request->input('email');
        $password = $request->input('password');

        // ==== VERIFIKASI GOOGLE RECAPTCHA ====
        $recaptchaResponse = $request->input('g-recaptcha-response');
        $verifyResponse = Http::withOptions(['verify' => false])
        ->asForm()
        ->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip()
        ]);

        if (!$verifyResponse->json('success')) {
            RateLimiter::hit($throttleKey, 60);
            return redirect()->back()->with(['error' => 'Validasi CAPTCHA gagal. Pastikan Anda mencentang reCAPTCHA.']);
        }

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
                        Auth::login($user);
                        $loginBerhasil = true;
                    } else {
                        RateLimiter::hit($throttleKey, 60);
                        return redirect()->back()->with(['error' => 'Login Portal Sukses, tapi akun belum terdaftar di database.']);
                    }
                }
            } catch (\Exception $e) {
                // silent
            }
        }

        if (!$loginBerhasil) {

            $loginInput = $request->input('email');
            $password = $request->input('password');

            $usernameKey = trim((string) $loginInput);

            $loginAttempt = LoginAttempt::firstOrCreate(
                ['username' => $usernameKey],
                ['failed_attempts' => 0, 'locked' => false, 'locked_at' => null]
            );

            if ($loginAttempt->locked) {
                $lockedUntil = $loginAttempt->locked_at?->copy()->addHour();

                if ($lockedUntil && now()->lt($lockedUntil)) {
                    $remainingSeconds = now()->diffInSeconds($lockedUntil, false);
                    return redirect()->back()->with(['error' => "Akun Anda terkunci sementara. Coba lagi dalam {$remainingSeconds} detik."]);
                }

                // lock sementara sudah lewat, reset status
                $loginAttempt->locked = false;
                $loginAttempt->failed_attempts = 0;
                $loginAttempt->locked_at = null;
                $loginAttempt->save();
            }


            $emailUntukCekLokal = $loginInput;

            if (is_numeric($loginInput)) {
                $mhs = Mahasiswa::where('nim', $loginInput)->with('userRole.user')->first();

                if ($mhs && $mhs->userRole && $mhs->userRole->user) {
                    $emailUntukCekLokal = $mhs->userRole->user->email;
                }
            }

            $isAuthenticated = Auth::attempt([
                'email' => $emailUntukCekLokal,
                'password' => $password
            ]);

            if ($isAuthenticated) {

                RateLimiter::clear($throttleKey);

                $loginAttempt->failed_attempts = 0;
                $loginAttempt->locked = false;
                $loginAttempt->locked_at = null;
                $loginAttempt->save();

            } else {

                RateLimiter::hit($throttleKey, 60);

                $loginAttempt->failed_attempts++;

                if ($loginAttempt->failed_attempts >= 15) {
                    $loginAttempt->locked = true;
                    $loginAttempt->locked_at = now(); // lock aktif selama 1 jam
                    $loginAttempt->failed_attempts = 15; // jaga konsistensi
                }



                $loginAttempt->save();

                return redirect()->back()->with(['error' => 'Login Gagal! Username atau Password salah.']);
            }
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
                return redirect()->back()->with('error', 'Anda belum memiliki role.');
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
            return redirect()->back()->with(['error' => 'Akun Anda valid, tapi tidak memiliki peran.']);
        }

        session(['selected_role' => $roles->first()->id]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            try {
                Http::asForm()->withHeaders([
                    'apikey' => env('PORTAL_API_KEY'),
                ])->post(env('PORTAL_LOGOUT_URL'), [
                    'email' => $user->email,
                    'password' => '',
                ]);
            } catch (\Exception $e) {
                // silent
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.index');
    }
}