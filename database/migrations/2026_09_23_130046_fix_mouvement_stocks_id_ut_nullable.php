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
        Schema::table('mouvement_stocks', function (Blueprint $table) {
            $table->dropForeign(['id_ut']);
            $table->unsignedBigInteger('id_ut')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mouvement_stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('id_ut')->nullable(false)->change();
            $table->foreign('id_ut')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
