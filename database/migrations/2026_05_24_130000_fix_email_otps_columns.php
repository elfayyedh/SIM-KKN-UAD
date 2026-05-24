<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('email_otps')) {
            return;
        }

        Schema::table('email_otps', function (Blueprint $table) {
            // If table currently has only id/timestamps, add missing OTP columns.
            // NOTE: We avoid dropping/redefining primary key to prevent issues.

            if (!Schema::hasColumn('email_otps', 'user_id')) {
                $table->string('user_id')->nullable();
            }

            if (!Schema::hasColumn('email_otps', 'otp_hash')) {
                $table->string('otp_hash')->nullable();
            }

            if (!Schema::hasColumn('email_otps', 'expires_at')) {
                $table->timestamp('expires_at')->nullable();
            }

            if (!Schema::hasColumn('email_otps', 'used_at')) {
                $table->timestamp('used_at')->nullable();
            }

            if (!Schema::hasColumn('email_otps', 'ip_address')) {
                $table->string('ip_address', 45)->nullable();
            }

            if (!Schema::hasColumn('email_otps', 'user_agent')) {
                $table->text('user_agent')->nullable();
            }

            // Add indexes if possible (only if columns exist and we can safely add)
            // Laravel doesn't support checking index existence easily across DB engines,
            // so we keep it simple and rely on nullable columns existing.
        });
    }

    public function down(): void
    {
        // Do not remove columns on rollback to avoid breaking existing flows.
    }
};

