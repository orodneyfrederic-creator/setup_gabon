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
        Schema::create('adresses', function (Blueprint $table) {
            $table->id(); // id_a
            $table->string('rue');
            $table->string('ville');
            $table->string('pays')->default('Gabon');
            $table->string('type'); // ex: livraison, facturation

            // Clés étrangères (Commande et Utilisateur selon ton MPD)
            $table->foreignId('id_c')->nullable()->constrained('commandes')->onDelete('set null');
            $table->foreignId('id_ut')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adresses');
    }
};
