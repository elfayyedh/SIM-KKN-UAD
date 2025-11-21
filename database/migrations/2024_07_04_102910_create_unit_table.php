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
        Schema::create('unit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_dpl');
            $table->uuid('id_tim_monev')->nullable();
            $table->uuid('id_kkn');
            $table->uuid('id_lokasi')->nullable();
            $table->string('nama', 10);
            $table->date('tanggal_penerjunan')->nullable();
            $table->date('tanggal_penarikan')->nullable();
            $table->timestamps();

            $table->foreign('id_kkn')->references('id')->on('kkn');
            $table->foreign('id_dpl')->references('id')->on('dpl');
            $table->foreign('id_lokasi')->references('id')->on('lokasi');
            $table->foreign('id_tim_monev')->references('id')->on('tim_monev')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('unit');
    }
};
