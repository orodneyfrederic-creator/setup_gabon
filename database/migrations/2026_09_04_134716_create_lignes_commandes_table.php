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
        Schema::create('lignes_commandes', function (Blueprint $table) {
            $table->id(); // id_lcmp
            $table->integer('quantite_com');
            $table->decimal('prix_unitaire_com', 10, 2);

            // Clés étrangères
            $table->foreignId('id_c')->constrained('commandes')->onDelete('cascade');
            $table->foreignId('id_p')->constrained('produits')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lignes_commandes');
    }
};
