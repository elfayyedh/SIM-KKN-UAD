<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('evaluasi_mahasiswa', function (Blueprint $table) {
            $table->decimal('nilai_akhir', 5, 2)->nullable()->after('eval_sholat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluasi_mahasiswa', function (Blueprint $table) {
            //
        });
    }
};
