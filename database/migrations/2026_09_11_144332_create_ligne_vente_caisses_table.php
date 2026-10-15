<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ligne_vente_caisses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vente')->constrained('vente_caisses')->cascadeOnDelete();
            $table->foreignId('id_p')->nullable()->constrained('produits')->nullOnDelete();
            $table->string('designation');
            $table->integer('quantite')->default(1);
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('total_ligne', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_vente_caisses');
    }
};
