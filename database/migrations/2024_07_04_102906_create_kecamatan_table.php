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
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_kabupaten');
            $table->string('nama', 50);
            $table->timestamps();

            $table->foreign('id_kabupaten')->references('id')->on('kabupaten')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kecamatan');
    }
};
