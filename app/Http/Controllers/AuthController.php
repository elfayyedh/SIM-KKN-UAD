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
use App\Models\LoginHistory;
use App\Mail\NewDeviceLoginAlert;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

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
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip()
        ]);

        if (!$verifyResponse->json('success')) {
            RateLimiter::hit($throttleKey, 60);
            return redirect()->back()->with(['error' => 'Validasi CAPTCHA gagal. Pastikan Anda mencentang reCAPTCHA.']);
        }

        $loginBerhasil = false;

        $localUser = User::where('email', $loginInput)->first();
        $isSeeder = false;

        if ($localUser) {
            $isSeeder = !str_ends_with(strtolower($localUser->email), 'uad.ac.id') 
                        || str_contains($localUser->email, '.test')
                        || $localUser->email === 'lppm@uad.ac.id';
        }

        if ($isSeeder) {
            if (Auth::attempt(['email' => $loginInput, 'password' => $password])) {
                RateLimiter::clear($throttleKey);
                $loginBerhasil = true;
            } else {
                RateLimiter::hit($throttleKey, 60);
                return redirect()->back()->with(['error' => 'Login Gagal! Password akun seeder salah.']);
            }
        } 
        else {
            if (config('services.portal.url')) {
                try {
                    $response = Http::withOptions(['verify' => false])
                        ->asForm()
                        ->withHeaders([
                            'U4D-API-KEY' => config('services.portal.api_key'),
                        ])->post(config('services.portal.url'), [
                            'email' => $loginInput,
                            'password' => $password,
                        ]);

                    $hasilApi = $response->json();

                    if ($response->successful() && isset($hasilApi['status_code']) && $hasilApi['status_code'] == 'success') {
                        $emailResmi = $hasilApi['user_email'];
                        $user = User::where('email', $emailResmi)->first();

                        if ($user) {
                            Auth::login($user);
                            RateLimiter::clear($throttleKey);
                            $loginBerhasil = true;
                        } else {
                            RateLimiter::hit($throttleKey, 60);
                            return redirect()->back()->with(['error' => 'Akun Anda belum didaftarkan oleh Admin.']);
                        }
                    } else {
                        RateLimiter::hit($throttleKey, 60);
                        return redirect()->back()->with(['error' => 'Login Gagal! Akun Portal salah atau tidak ditemukan.']);
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with(['error' => 'Gagal terhubung ke API Portal UAD. Silakan coba beberapa saat lagi.']);
                }
            } else {
                return redirect()->back()->with(['error' => 'Sistem login portal belum dikonfigurasikan. Silakan hubungi administrator.']);
            }
        }

        $request->session()->regenerate();
        $user = Auth::user();

        $ip_address = $request->ip();
        $user_agent = $request->userAgent();

        $isFamiliarDevice = LoginHistory::where('user_id', $user->id)
            ->where('ip_address', $ip_address)
            ->where('user_agent', $user_agent)
            ->exists();

        $history = LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'is_abnormal' => !$isFamiliarDevice,
        ]);

        if (!$isFamiliarDevice && $user->email) {
            try {
                Mail::to($user->email)->send(new NewDeviceLoginAlert($history, $user));
            } catch (\Exception $e) {}
        }

        $isAdmin = false;
        foreach ($user->userRoles as $userRole) {
            if ($userRole->role && $userRole->role->nama_role == 'Admin') {
                $isAdmin = true;
                break;
            }
        }

        if ($isAdmin) {
            $otp = rand(100000, 999999);
            $user->otp_code = $otp;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();
            
            try {
                Mail::to($user->email)->send(new OtpMail($otp, $user));
            } catch (\Exception $e) {}

            session(['mfa_verified' => false]);
        } else {
            session(['mfa_verified' => true]);
        }

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
                return redirect()->back()->with('error', 'Anda belum memiliki hak akses penugasan Dosen/Tim Monev.');
            }

            if (session('mfa_verified') === false) {
                return redirect()->route('mfa.index');
            }

            return redirect()->route('dashboard');
        }

        session()->forget([
            'user_is_dosen',
            'user_has_role_dpl',
            'user_has_role_monev',
            'active_role'
        ]);

        $roles = $user->userRoles()->with(['kkn', 'mahasiswa', 'role'])->get();

        if ($roles->isEmpty()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->back()->with(['error' => 'Akun Anda valid, tapi tidak memiliki peran di sistem ini.']);
        }

        $adminRole = $roles->first(function($userRole) {
            return $userRole->role && $userRole->role->nama_role == 'Admin';
        });

        if ($adminRole) {
            session(['selected_role' => $adminRole->id]);
            if (session('mfa_verified') === false) {
                return redirect()->route('mfa.index');
            }
            return redirect()->route('dashboard');
        }

        $today = \Carbon\Carbon::now();
        $runningRoles = $roles->filter(function($userRole) use ($today) {
            return $userRole->kkn
                && $userRole->kkn->status
                && $today->between($userRole->kkn->tanggal_mulai, $userRole->kkn->tanggal_selesai);
        });

        $activeRole = $runningRoles->first(function($userRole) {
            return $userRole->mahasiswa && $userRole->mahasiswa->status == 1;
        });

        if (!$activeRole) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->back()->with([
                'error' => 'Login Gagal! Anda tidak memiliki periode KKN yang aktif saat ini, atau akun Anda telah dinonaktifkan.'
            ]);
        }

        session(['selected_role' => $activeRole->id]);

        if (session('mfa_verified') === false) {
            return redirect()->route('mfa.index');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user && config('services.portal.logout_url')) {
            try {
                Http::asForm()->withHeaders([
                    'apikey' => config('services.portal.api_key'),
                ])->post(config('services.portal.logout_url'), [
                    'email' => $user->email,
                    'password' => '',
                ]);
            } catch (\Throwable $e) {
                // silent
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.index');
    }
}