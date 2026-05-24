# TODO - OTP Email Layer (Login)

- [ ] Tambah migration tabel `email_otps` (user_id, otp_hash, expires_at, used_at, metadata)
- [ ] Tambah model `EmailOtp`
- [ ] Tambah mailable `EmailOtpMail` untuk kirim OTP 6 digit (expiry 5 menit)
- [x] Tambah route dan controller flow OTP:
    - [x] Modify `AuthController@login` jadi: validasi password -> generate OTP -> simpan hash -> kirim email -> redirect ke halaman OTP (tanpa login dulu)
    - [x] Buat halaman OTP `/login/otp` dan aksi `verifyOtp`
    - [x] Pada OTP valid: tandai used_at -> `Auth::login($user)` -> redirect dashboard

- [x] Tambah request validation `VerifyOtpRequest` (otp numeric 6 digit)
- [x] Buat view `resources/views/auth/otp.blade.php`
- [x] Update `routes/web.php` untuk route OTP

- [x] Jalankan `php artisan migrate`

- [ ] Test manual: login salah/password salah, OTP expire, OTP benar, dan akses saat sudah login
