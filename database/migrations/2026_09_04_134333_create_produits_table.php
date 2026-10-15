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
        Schema::create('produits', function (Blueprint $table) {
            $table->id(); // id_p
            $table->string('designation');
            $table->text('description')->nullable();
            $table->decimal('prix_unitaire', 10, 2);
            $table->integer('quantite')->default(0);
            $table->string('image')->nullable();
            $table->string('reference')->unique();

            // Clés étrangères
            $table->foreignId('id_cat')->constrained('categories')->onDelete('cascade');
            $table->foreignId('id_m')->constrained('marques')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
