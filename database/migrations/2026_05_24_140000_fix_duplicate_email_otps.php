<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Migration duplikat create_email_otps_table.php pernah terjadi.
        // Migration terbaru hanya memastikan kolom yang dibutuhkan ada di `email_otps`.
        // Kalau kolom sudah ada, tidak ada perubahan.

        if (!Schema::hasTable('email_otps')) {
            // Jika tabel belum ada, biarkan migrate lain yang membuatnya.
            return;
        }

        Schema::table('email_otps', function (Blueprint $table) {
            if (!Schema::hasColumn('email_otps', 'user_id')) {
                $table->string('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('email_otps', 'otp_hash')) {
                $table->string('otp_hash')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('email_otps', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('otp_hash');
            }
            if (!Schema::hasColumn('email_otps', 'used_at')) {
                $table->timestamp('used_at')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('email_otps', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('used_at');
            }
            if (!Schema::hasColumn('email_otps', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }

            // Index tidak ditambahkan karena pengecekan index existence lintas DB tidak mudah.
            // Kinerja tetap OK karena query OTP sederhana.
        });

        // Jika ada data yang terbuat dari migration duplikat (hanya id/timestamps),
        // flow OTP akan gagal tapi ini minimal memperbaiki skema agar flow bisa jalan setelah OTP dikirim ulang.
    }

    public function down(): void
    {
        // Jangan rollback kolom untuk menghindari mematahkan fitur.
    }
};

