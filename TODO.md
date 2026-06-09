# TODO - Fitur Lockout Login (15 kali gagal)

- [ ] Buat migration & skema tabel untuk menyimpan percobaan login gagal per username (NIM/email) + status terkunci (lockout permanen).
- [ ] (Opsional) Buat model Eloquent untuk tabel lockout.
- [ ] Update `app/Http/Controllers/AuthController.php`:
    - [ ] Normalisasi username (numeric => NIM, selain numeric => email input).
    - [ ] Cek apakah username sudah terkunci sebelum `Auth::attempt`.
    - [ ] Jika gagal, increment counter.
    - [ ] Saat count mencapai 15, tandai terkunci permanen.
    - [ ] Setelah terkunci, tolak login dengan pesan yang jelas.
- [ ] (Opsional) Tambahkan test feature untuk skenario 14 gagal vs 15 gagal.
- [x] Jalankan `php artisan migrate`.
- [x] Jalankan `php artisan test` (jika test dibuat) dan validasi manual login.
