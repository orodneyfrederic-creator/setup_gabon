<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Durée de la garantie boutique par catégorie : 1 mois pour les ordinateurs, 2 semaines pour le reste.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedSmallInteger('garantie_jours')->default(14)->after('remise_max_pct');
        });

        DB::table('categories')->where('nom', 'like', '%rdinateur%')->update(['garantie_jours' => 30]);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('garantie_jours');
        });
    }
};
