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
        Schema::create('evaluasi_mahasiswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('id_tim_monev'); 
            $table->uuid('id_mahasiswa'); 
            $table->tinyInteger('eval_jkem')->nullable();
            $table->tinyInteger('eval_form1')->nullable();
            $table->tinyInteger('eval_form2')->nullable();
            $table->tinyInteger('eval_form3')->nullable(); 
            $table->tinyInteger('eval_form4')->nullable(); 
            $table->tinyInteger('eval_sholat')->nullable(); 

            $table->text('catatan_monev')->nullable();
            
            $table->timestamps();

            $table->foreign('id_tim_monev')->references('id')->on('tim_monev')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');

            $table->unique(['id_tim_monev', 'id_mahasiswa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_mahasiswa');
    }
};