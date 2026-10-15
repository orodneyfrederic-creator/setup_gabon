<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('client_nom');
            $table->string('client_email')->nullable();
            $table->string('client_tel')->nullable();
            $table->string('client_entreprise')->nullable();
            $table->enum('statut', ['brouillon', 'envoye', 'accepte', 'refuse', 'facture'])->default('brouillon');
            $table->text('notes')->nullable();
            $table->date('date_validite')->nullable();
            $table->foreignId('id_ut')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('montant_total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
