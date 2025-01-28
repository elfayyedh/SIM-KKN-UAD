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
        Schema::create('user_role', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_user');
            $table->uuid('id_role');
            $table->uuid('id_kkn')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_role')->references('id')->on('roles');
            $table->foreign('id_kkn')->references('id')->on('kkn');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_role');
    }
};
