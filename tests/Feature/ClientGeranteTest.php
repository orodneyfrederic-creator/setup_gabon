<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientGeranteTest extends TestCase
{
    use RefreshDatabase;

    public function test_gerante_can_access_clients_index_and_see_registered_clients(): void
    {
        $gerante = Staff::create([
            'email' => 'gerante@gmail.com',
            'passwd' => 'gerante241',
            'role' => 'gerante',
        ]);

        $client = User::create([
            'nom' => 'Ndong',
            'prenom' => 'Jean',
            'email' => 'jean.ndong@gmail.com',
            'password' => 'password123',
            'tel' => '+24166000000',
            'role' => 'client',
        ]);

        $response = $this->actingAs($gerante, 'staff')
            ->get(route('gerante.clients.index'));

        $response->assertStatus(200);
        $response->assertSee('Liste des Clients');
        $response->assertSee('jean.ndong@gmail.com');
        $response->assertSee('Jean Ndong');
    }

    public function test_gerante_can_view_client_details(): void
    {
        $gerante = Staff::create([
            'email' => 'gerante@gmail.com',
            'passwd' => 'gerante241',
            'role' => 'gerante',
        ]);

        $client = User::create([
            'nom' => 'Obame',
            'prenom' => 'Marc',
            'email' => 'marc.obame@gmail.com',
            'password' => 'password123',
            'tel' => '+24177000000',
            'role' => 'client',
        ]);

        $response = $this->actingAs($gerante, 'staff')
            ->get(route('gerante.clients.show', $client->id));

        $response->assertStatus(200);
        $response->assertSee('Fiche Client : Marc Obame');
        $response->assertSee('marc.obame@gmail.com');
    }
}
