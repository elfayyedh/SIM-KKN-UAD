<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Kita akan ubah tabel pivot ini agar sesuai standar 'attach()' Laravel
     */
    public function up(): void
    {
        Schema::table('evaluasi_monev', function (Blueprint $table) {
            $table->dropPrimary('id');

            $table->dropColumn('id');

            $table->dropTimestamps();

            $table->primary(['id_tim_monev', 'id_dpl']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluasi_monev', function (Blueprint $table) {
            $table->dropPrimary(['id_tim_monev', 'id_dpl']);

            $table->uuid('id')->first()->primary();

            $table->timestamps();
        });
    }
};