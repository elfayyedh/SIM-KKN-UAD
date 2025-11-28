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
        // 1. Tabel Master Kriteria (Soal yang diatur Admin di Wizard KKN)
        Schema::create('kriteria_monev', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_kkn'); // Relasi ke KKN (Supaya beda periode = beda soal)
            
            $table->string('judul'); // Contoh: "Penilaian JKEM"
            $table->string('keterangan')->nullable(); // Contoh: "1: <30%, 2:..."
            
            // Utk Variable Dinamis (Contoh: 'total_jkem')
            // Nanti di controller direplace dengan angka otomatis
            $table->string('variable_key')->nullable(); 
            
            // Utk Link Tombol (Contoh: '#proker')
            $table->string('link_url')->nullable(); 
            $table->string('link_text')->nullable(); 
            
            $table->integer('urutan')->default(0); // Biar admin bisa atur urutan tampil
            $table->timestamps();
            
            $table->foreign('id_kkn')->references('id')->on('kkn')->onDelete('cascade');
        });

        // 2. Tabel Jawaban Detail (Menyimpan skor 1, 2, atau 3 per soal)
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