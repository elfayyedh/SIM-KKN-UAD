<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini akan menyimpan DPL mana
     * yang dievaluasi oleh Tim Monev mana.
     */
    public function up(): void
    {
        Schema::create('evaluasi_monev', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('id_tim_monev'); 
            
            $table->uuid('id_dpl'); 
            
            $table->timestamps();

            $table->foreign('id_tim_monev')->references('id')->on('tim_monev')->onDelete('cascade');
            $table->foreign('id_dpl')->references('id')->on('dpl')->onDelete('cascade');

            $table->unique(['id_tim_monev', 'id_dpl']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_monev');
    }
};