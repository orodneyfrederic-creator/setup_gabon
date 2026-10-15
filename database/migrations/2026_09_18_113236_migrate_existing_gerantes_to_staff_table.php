<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Move non-client users from 'users' table to 'staff' table.
     */
    public function up(): void
    {
        $nonClients = DB::table('users')->where('role', '!=', 'client')->get();

        foreach ($nonClients as $user) {
            DB::table('staff')->updateOrInsert(
                ['email' => $user->email],
                [
                    'passwd' => $user->password,
                    'role' => $user->role ?? 'gerante',
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            );
        }

        DB::table('users')->where('role', '!=', 'client')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
