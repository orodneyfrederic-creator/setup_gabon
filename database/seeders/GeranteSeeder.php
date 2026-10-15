<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GeranteSeeder extends Seeder
{
    /**
     * Crée le compte de la gérante si il n'existe pas déjà.
     */
    public function run(): void
    {
        Staff::updateOrCreate(
            ['email' => 'gerante@gmail.com'],
            [
                'email' => 'gerante@gmail.com',
                'passwd' => Hash::make('gerante241'),
                'role' => 'gerante',
            ]
        );

    }
}
