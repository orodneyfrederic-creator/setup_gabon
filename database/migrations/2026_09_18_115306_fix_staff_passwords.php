<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $staffMembers = DB::table('staff')->get();

        foreach ($staffMembers as $staff) {
            // Si le mot de passe n'est pas déjà hashé avec bcrypt/argon (longueur < 60 ou ne commence pas par $2y$)
            if (! str_starts_with($staff->passwd, '$2y$') && ! str_starts_with($staff->passwd, '$2a$')) {
                DB::table('staff')
                    ->where('id_staff', $staff->id_staff)
                    ->update([
                        'passwd' => Hash::make($staff->passwd),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
