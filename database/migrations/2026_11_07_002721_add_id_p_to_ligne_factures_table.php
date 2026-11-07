<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Relie chaque ligne de facture à son produit pour pouvoir sortir (et restituer) le stock.
     */
    public function up(): void
    {
        Schema::table('ligne_factures', function (Blueprint $table) {
            $table->foreignId('id_p')->nullable()->after('id_facture')->constrained('produits')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ligne_factures', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_p');
        });
    }
};
