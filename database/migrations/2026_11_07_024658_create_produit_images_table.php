<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Photos supplémentaires d'un produit (la photo principale reste dans produits.image).
     */
    public function up(): void
    {
        Schema::create('produit_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_p')->constrained('produits')->cascadeOnDelete();
            $table->string('chemin');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['id_p', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produit_images');
    }
};
