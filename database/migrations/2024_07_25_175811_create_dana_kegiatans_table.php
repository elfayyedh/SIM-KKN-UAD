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
        Schema::create('dana_kegiatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_logbook_kegiatan');
            $table->uuid('id_unit')->references('id')->on('unit');
            $table->integer('jumlah');
            $table->string('sumber', 10);
            $table->timestamps();

            $table->foreign('id_logbook_kegiatan')->references('id')->on('logbook_kegiatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dana_kegiatan');
    }
};
