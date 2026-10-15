<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavGeranteTest extends TestCase
{
    use RefreshDatabase;

    public function test_gerante_can_access_sav_index_and_create_ticket(): void
    {
        $gerante = Staff::create([
            'email' => 'gerante@gmail.com',
            'passwd' => 'gerante241',
            'role' => 'gerante',
        ]);

        $response = $this->actingAs($gerante, 'staff')
            ->get(route('gerante.sav.index'));

        $response->assertStatus(200);
        $response->assertSee('Tickets SAV');

        $storeResponse = $this->actingAs($gerante, 'staff')
            ->post(route('gerante.sav.store'), [
                'client_nom' => 'Paul Nzoghe',
                'client_tel' => '+24166112233',
                'designation_produit' => 'Écran Asus ROG 27 pouces',
                'description_panne' => 'Affichage clignotant sur port DisplayPort',
            ]);

        $storeResponse->assertRedirect(route('gerante.sav.index'));
        $this->assertDatabaseHas('sav_tickets', [
            'client_nom' => 'Paul Nzoghe',
            'designation_produit' => 'Écran Asus ROG 27 pouces',
        ]);
    }
}
