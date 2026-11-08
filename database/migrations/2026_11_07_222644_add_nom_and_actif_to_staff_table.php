<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Personnel : nom affiché et possibilité de désactiver un compte (départ d'un vendeur)
     * sans supprimer l'historique des ventes qu'il a enregistrées.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('nom', 120)->nullable()->after('id_staff');
            $table->boolean('actif')->default(true)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['nom', 'actif']);
        });
    }
};
