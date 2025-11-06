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
        Schema::create('penugasan_evaluasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_tim_monev');
            $table->uuid('id_dpl');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();

            $table->foreign('id_tim_monev')->references('id')->on('tim_monev');
            $table->foreign('id_dpl')->references('id')->on('dpl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan_evaluasis');
    }
};
