<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_dosens_table.php
    // di file 2025_..._create_dosens_table.php
    public function up()
    {
        Schema::create('dosen', function (Blueprint $table) { 
            $table->uuid('id')->primary();
            $table->uuid('id_user');
            $table->string('nip', 20);
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
