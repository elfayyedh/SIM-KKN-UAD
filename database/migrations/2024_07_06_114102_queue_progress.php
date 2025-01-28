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
        Schema::create('queue_progress', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status', 20)->nullable();
            $table->string('message', 250)->nullable();
            $table->string('progress', 10)->nullable();
            $table->string('total', 10)->nullable();
            $table->string('step', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
