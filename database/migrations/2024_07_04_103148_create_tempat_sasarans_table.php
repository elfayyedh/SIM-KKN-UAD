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
        Schema::create('tempat_sasaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mahasiswa')->nullable();
            $table->string('tempat', 50);
            $table->string('sasaran', 50);
            $table->uuid('id_proker');
            $table->timestamps();

            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('id_proker')->references('id')->on('proker')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tempat_sasaran');
    }
};
