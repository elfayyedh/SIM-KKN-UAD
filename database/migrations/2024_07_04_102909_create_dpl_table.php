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
        Schema::create('dpl', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_kkn');
            $table->uuid('id_dosen');
            $table->timestamps();

            $table->foreign('id_kkn')->references('id')->on('kkn');
            $table->foreign('id_dosen')->references('id')->on('dosen');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dpl');
    }
};
