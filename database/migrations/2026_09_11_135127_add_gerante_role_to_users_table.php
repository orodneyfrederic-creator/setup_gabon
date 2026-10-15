<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Étend l'ENUM 'role' pour inclure la valeur 'gerante'.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'vendeuse', 'directeur', 'gerante') NOT NULL DEFAULT 'client'");
        }
    }

    /**
     * Revenir à l'ENUM d'origine (sans 'gerante').
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'vendeuse', 'directeur') NOT NULL DEFAULT 'client'");
        }
    }
};
