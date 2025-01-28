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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_user_role');
            $table->uuid('id_prodi');
            $table->uuid('id_unit')->nullable();
            $table->uuid('id_kkn');
            $table->string('nim', 20);
            $table->integer('total_jkem')->default(0);
            $table->string('jabatan', 20)->nullable();
            $table->timestamps();

            $table->foreign('id_user_role')->references('id')->on('user_role');
            $table->foreign('id_prodi')->references('id')->on('prodi');
            $table->foreign('id_unit')->references('id')->on('unit');
            $table->foreign('id_kkn')->references('id')->on('kkn');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mahasiswa');
    }
};
