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
        Schema::create('informasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->uuid('author');
            $table->text('isi');
            $table->string('type');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('author')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('informasi');
    }
};