<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sav_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('client_nom');
            $table->string('client_tel');
            $table->string('designation_produit');
            $table->text('description_panne');
            $table->enum('statut', ['ouvert', 'en_cours', 'resolu', 'clos'])->default('ouvert');
            $table->date('date_retour_prevue')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sav_tickets');
    }
};
