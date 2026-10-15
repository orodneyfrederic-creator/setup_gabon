<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('id_devis')->nullable()->constrained('devis')->nullOnDelete();
            $table->string('client_nom');
            $table->string('client_email')->nullable();
            $table->enum('statut', ['emise', 'payee', 'annulee'])->default('emise');
            $table->decimal('montant_total', 10, 2)->default(0);
            $table->date('date_echeance')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
