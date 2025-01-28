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
        Schema::create('logbook_kegiatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_logbook_harian');
            $table->uuid('id_kegiatan');
            $table->uuid('id_mahasiswa');
            $table->uuid('id_unit');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('jenis', ['bantu', 'bersama', 'individu']);
            $table->integer('total_jkem');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('id_logbook_harian')->references('id')->on('logbook_harian')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('id_kegiatan')->references('id')->on('kegiatan')->onDelete('cascade');
            $table->foreign('id_unit')->references('id')->on('unit')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('logbook_kegiatan');
    }
};
