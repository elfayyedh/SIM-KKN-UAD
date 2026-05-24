<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // NOTE:
        // Di project ini, kolom users.id bertipe string (UUID), sehingga foreignId() (bigint) tidak kompatibel.
        // Karena itu, kolom user_id disimpan sebagai string tanpa foreign key constraint.
        Schema::create('email_otps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id');
            $table->string('otp_hash');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
            $table->index(['otp_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_otps');
    }
};


