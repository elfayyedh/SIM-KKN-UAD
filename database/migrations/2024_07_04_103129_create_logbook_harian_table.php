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
        Schema::create('logbook_harian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mahasiswa');
            $table->uuid('id_unit');
            $table->date('tanggal');
            $table->string('status', 20)->nullable(false);
            $table->integer('total_jkem')->nullable();
            $table->timestamps();

            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('id_unit')->references('id')->on('unit')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('logbook_harian');
    }
};
