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
        Schema::create('logbook_sholat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mahasiswa');
            $table->enum('waktu', ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya']);
            $table->string('status')->default('belum diisi');
            $table->date('tanggal');
            $table->string('jumlah_jamaah', 10)->nullable();
            $table->string('imam', 50)->nullable();

            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_sholat');
    }
};
