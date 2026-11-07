<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categories
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('negociable')->default(false)->after('nom');
            $table->unsignedInteger('remise_max_pct')->default(2)->after('negociable');
        });

        // Activation par défaut sur les catégories clés d'équipements (PC, Imprimantes)
        DB::table('categories')
            ->where('nom', 'like', '%PC%')
            ->orWhere('nom', 'like', '%Ordinateur%')
            ->orWhere('nom', 'like', '%Portable%')
            ->orWhere('nom', 'like', '%Imprimante%')
            ->update(['negociable' => true, 'remise_max_pct' => 2]);

        // 2. Produits
        Schema::table('produits', function (Blueprint $table) {
            $table->boolean('negociable')->nullable()->comment('null = herite categorie, true/false = surcharge')->after('quantite');
            $table->unsignedInteger('prix_achat')->nullable()->after('prix_unitaire');
            $table->unsignedInteger('remise_max_pct')->nullable()->comment('null = herite categorie')->after('negociable');
            $table->unsignedInteger('nb_tours_max')->default(3)->after('remise_max_pct');
            $table->boolean('decision_manuelle')->default(false)->after('nb_tours_max');
        });

        // 3. Lignes Commandes
        Schema::table('lignes_commandes', function (Blueprint $table) {
            $table->unsignedInteger('prix_catalogue_unitaire')->default(0)->after('prix_unitaire_com');
            $table->unsignedInteger('prix_final_unitaire')->default(0)->after('prix_catalogue_unitaire');
            $table->unsignedBigInteger('negociation_id')->nullable()->after('id_p');
        });

        // Migration historique lignes commandes
        DB::statement('UPDATE lignes_commandes SET prix_catalogue_unitaire = CAST(prix_unitaire_com AS UNSIGNED), prix_final_unitaire = CAST(prix_unitaire_com AS UNSIGNED) WHERE prix_final_unitaire = 0');

        // 4. Lignes Vente POS
        if (Schema::hasTable('ligne_vente_pos')) {
            Schema::table('ligne_vente_pos', function (Blueprint $table) {
                $table->unsignedInteger('prix_catalogue_unitaire')->default(0)->after('prix_unitaire');
                $table->unsignedInteger('prix_final_unitaire')->default(0)->after('prix_catalogue_unitaire');
                $table->string('motif_remise')->nullable()->after('total_ligne');
            });
            DB::statement('UPDATE ligne_vente_pos SET prix_catalogue_unitaire = CAST(prix_unitaire AS UNSIGNED), prix_final_unitaire = CAST(prix_unitaire AS UNSIGNED) WHERE prix_final_unitaire = 0');
        }

        // 5. Lignes Devis
        if (Schema::hasTable('ligne_devis')) {
            Schema::table('ligne_devis', function (Blueprint $table) {
                $table->unsignedInteger('prix_catalogue_unitaire')->default(0)->after('prix_unitaire');
                $table->unsignedInteger('prix_final_unitaire')->default(0)->after('prix_catalogue_unitaire');
            });
            DB::statement('UPDATE ligne_devis SET prix_catalogue_unitaire = CAST(COALESCE(prix_unitaire, 0) AS UNSIGNED), prix_final_unitaire = CAST(COALESCE(prix_unitaire, 0) AS UNSIGNED) WHERE prix_final_unitaire = 0');
        }

        // 6. Lignes Factures
        if (Schema::hasTable('ligne_factures')) {
            Schema::table('ligne_factures', function (Blueprint $table) {
                $table->unsignedInteger('prix_catalogue_unitaire')->default(0)->after('prix_unitaire');
                $table->unsignedInteger('prix_final_unitaire')->default(0)->after('prix_catalogue_unitaire');
            });
            DB::statement('UPDATE ligne_factures SET prix_catalogue_unitaire = CAST(COALESCE(prix_unitaire, 0) AS UNSIGNED), prix_final_unitaire = CAST(COALESCE(prix_unitaire, 0) AS UNSIGNED) WHERE prix_final_unitaire = 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['negociable', 'remise_max_pct']);
        });

        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn(['negociable', 'prix_achat', 'remise_max_pct', 'nb_tours_max', 'decision_manuelle']);
        });

        Schema::table('lignes_commandes', function (Blueprint $table) {
            $table->dropColumn(['prix_catalogue_unitaire', 'prix_final_unitaire', 'negociation_id']);
        });

        if (Schema::hasTable('ligne_vente_pos')) {
            Schema::table('ligne_vente_pos', function (Blueprint $table) {
                $table->dropColumn(['prix_catalogue_unitaire', 'prix_final_unitaire', 'motif_remise']);
            });
        }

        if (Schema::hasTable('ligne_devis')) {
            Schema::table('ligne_devis', function (Blueprint $table) {
                $table->dropColumn(['prix_catalogue_unitaire', 'prix_final_unitaire']);
            });
        }

        if (Schema::hasTable('ligne_factures')) {
            Schema::table('ligne_factures', function (Blueprint $table) {
                $table->dropColumn(['prix_catalogue_unitaire', 'prix_final_unitaire']);
            });
        }
    }
};
