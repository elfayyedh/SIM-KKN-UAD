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
        Schema::create('proker', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_unit');
            $table->uuid('id_bidang');
            $table->string('nama', 100);
            $table->foreign('id_unit')->references('id')->on('unit')->onDelete('cascade');
            $table->foreign('id_bidang')->references('id')->on('bidang_proker')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proker');
    }
};
