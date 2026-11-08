<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Avis clients : réservés aux clients qui ont reçu le produit (commande livrée),
     * un seul avis par client et par produit. La boutique peut masquer un avis, jamais le modifier.
     */
    public function up(): void
    {
        Schema::create('avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_p')->constrained('produits')->cascadeOnDelete();
            $table->foreignId('id_ut')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_c')->nullable()->constrained('commandes')->nullOnDelete();
            $table->unsignedTinyInteger('note');
            $table->text('commentaire')->nullable();
            $table->string('statut', 20)->default('publie');
            $table->string('motif_masquage')->nullable();
            $table->timestamps();

            $table->unique(['id_p', 'id_ut']);
            $table->index(['id_p', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};
