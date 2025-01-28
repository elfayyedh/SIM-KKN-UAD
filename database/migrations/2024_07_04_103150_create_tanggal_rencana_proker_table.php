<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tanggal_rencana_proker', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_kegiatan');
            $table->uuid('id_kkn');
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('id_kegiatan')->references('id')->on('kegiatan')->onDelete('cascade');
            $table->foreign('id_kkn')->references('id')->on('kkn')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tanggal_rencana_proker');
    }
};
