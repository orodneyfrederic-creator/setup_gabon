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
        Schema::table('vente_pos', function (Blueprint $table) {
            $table->dropForeign(['id_ut']);
            $table->unsignedBigInteger('id_ut')->nullable()->change();
            $table->foreign('id_ut')->references('id_staff')->on('staff')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vente_pos', function (Blueprint $table) {
            $table->dropForeign(['id_ut']);
        });
    }
};
