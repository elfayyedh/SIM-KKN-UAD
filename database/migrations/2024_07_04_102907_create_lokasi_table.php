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
        Schema::create('lokasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_kecamatan');
            $table->string('nama', 100);
            $table->timestamps();

            $table->foreign('id_kecamatan')->references('id')->on('kecamatan')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lokasi');
    }
};
