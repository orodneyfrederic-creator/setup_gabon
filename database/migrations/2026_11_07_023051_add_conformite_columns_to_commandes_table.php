<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Conserve les informations contractuelles de chaque commande
     * (loi gabonaise n°025/2021, art. 56 et 61 : la preuve incombe au professionnel).
     */
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('adresse_livraison', 500)->nullable()->after('montant_c');
            $table->string('methode_paiement', 30)->nullable()->after('adresse_livraison');
            $table->timestamp('conditions_acceptees_at')->nullable()->after('methode_paiement');
            $table->string('version_conditions', 20)->nullable()->after('conditions_acceptees_at');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['adresse_livraison', 'methode_paiement', 'conditions_acceptees_at', 'version_conditions']);
        });
    }
};
