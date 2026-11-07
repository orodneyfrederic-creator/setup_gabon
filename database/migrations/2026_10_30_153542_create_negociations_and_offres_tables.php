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
        Schema::create('negociations', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->unique();
            $table->foreignId('id_p')->constrained('produits')->onDelete('cascade');
            $table->foreignId('id_ut')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('quantite')->default(1);
            $table->unsignedInteger('prix_catalogue');
            $table->unsignedInteger('prix_plancher');
            $table->unsignedInteger('prix_final_convenu')->nullable();
            $table->enum('statut', [
                'en_cours',
                'en_attente_gerante',
                'acceptee',
                'refusee',
                'expiree',
                'utilisee',
                'annulee',
            ])->default('en_cours');
            $table->unsignedInteger('tours_utilises')->default(0);
            $table->unsignedInteger('tours_max')->default(3);
            $table->dateTime('expire_a')->nullable();
            $table->dateTime('delai_relance_expire_a')->nullable();
            $table->unsignedBigInteger('id_staff_decision')->nullable();
            $table->foreign('id_staff_decision')->references('id_staff')->on('staff')->onDelete('set null');
            $table->text('notes_gerante')->nullable();
            $table->timestamps();

            $table->index(['id_ut', 'id_p', 'statut']);
            $table->index('statut');
        });

        Schema::create('negociation_offres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_negociation')->constrained('negociations')->onDelete('cascade');
            $table->enum('auteur', ['client', 'systeme', 'gerante']);
            $table->unsignedInteger('tour_numero');
            $table->unsignedInteger('montant_propose');
            $table->enum('type_action', [
                'proposition',
                'contre_offre',
                'acceptation',
                'refus',
                'derniere_offre',
                'mise_en_attente',
            ]);
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['id_negociation', 'tour_numero']);
        });

        // Contrainte de clé étrangère sur lignes_commandes.negociation_id si la colonne existe
        if (Schema::hasColumn('lignes_commandes', 'negociation_id')) {
            Schema::table('lignes_commandes', function (Blueprint $table) {
                $table->foreign('negociation_id')->references('id')->on('negociations')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('lignes_commandes', 'negociation_id')) {
            Schema::table('lignes_commandes', function (Blueprint $table) {
                $table->dropForeign(['negociation_id']);
            });
        }

        Schema::dropIfExists('negociation_offres');
        Schema::dropIfExists('negociations');
    }
};
