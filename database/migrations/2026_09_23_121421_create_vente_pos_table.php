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
        Schema::create('vente_pos', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->decimal('montant_total', 10, 2);
            $table->string('methode_paiement')->default('Especes');
            $table->foreignId('id_ut')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vente_pos');
    }
};
