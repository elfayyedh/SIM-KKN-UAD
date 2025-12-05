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
        Schema::table('unit', function (Blueprint $table) {
            $table->uuid('id_tim_monev')->nullable()->after('id_dpl');
            $table->foreign('id_tim_monev')->references('id')->on('tim_monev')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit', function (Blueprint $table) {
            $table->dropForeign(['id_tim_monev']);
            $table->dropColumn('id_tim_monev');
        });
    }
};
