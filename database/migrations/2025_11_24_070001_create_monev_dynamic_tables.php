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
        Schema::create('kriteria_monev', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_kkn'); // Relasi ke KKN
            $table->string('judul'); 
            $table->string('keterangan')->nullable();
            $table->string('variable_key')->nullable(); 
            $table->string('link_url')->nullable(); 
            $table->string('link_text')->nullable(); 
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->foreign('id_kkn')->references('id')->on('kkn')->onDelete('cascade');
        });

        Schema::create('evaluasi_mahasiswa_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_evaluasi_mahasiswa');
            $table->uuid('id_kriteria_monev');
            $table->integer('nilai');
            $table->timestamps();

            $table->foreign('id_evaluasi_mahasiswa')->references('id')->on('evaluasi_mahasiswa')->onDelete('cascade');
            $table->foreign('id_kriteria_monev')->references('id')->on('kriteria_monev')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_mahasiswa_detail');
        Schema::dropIfExists('kriteria_monev');
    }
};