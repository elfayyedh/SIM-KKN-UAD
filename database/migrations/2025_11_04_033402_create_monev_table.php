<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monev', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_user_role');
            $table->uuid('id_kkn');
            $table->string('nip', 20);
            $table->timestamps();

            $table->foreign('id_user_role')->references('id')->on('user_role');
            $table->foreign('id_kkn')->references('id')->on('kkn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monev');
    }
};
