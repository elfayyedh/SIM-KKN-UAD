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
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_bidang_proker');
            $table->uuid('id_dpl');
            $table->text('komentar');
            $table->timestamps();

            $table->foreign('id_bidang_proker')->references('id')->on('bidang_proker')->onDelete('cascade');
            $table->foreign('id_dpl')->references('id')->on('dpl')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
