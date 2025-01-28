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
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_proker');
            $table->uuid('id_mahasiswa');
            $table->string('nama', 100);
            $table->integer('frekuensi');
            $table->integer('jkem');
            $table->integer('total_jkem');
            $table->timestamps();

            $table->foreign('id_proker')->references('id')->on('proker')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
