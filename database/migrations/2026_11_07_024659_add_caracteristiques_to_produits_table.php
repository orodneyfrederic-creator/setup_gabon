<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fiche technique structurée : liste ordonnée de paires { libelle, valeur }.
     */
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->json('caracteristiques')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn('caracteristiques');
        });
    }
};
