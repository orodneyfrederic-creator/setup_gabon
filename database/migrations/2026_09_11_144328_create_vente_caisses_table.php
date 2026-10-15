<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vente_caisses', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('client_nom')->nullable();
            $table->decimal('montant_total', 10, 2);
            $table->enum('mode_paiement', ['especes', 'mobile_money', 'carte', 'virement']);
            $table->foreignId('id_ut')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vente_caisses');
    }
};
