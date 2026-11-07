<?php

namespace Tests\Feature;

use App\Models\Commande;
use App\Models\Facture;
use App\Models\Staff;
use App\Models\User;
use App\Models\VentePos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChiffreAffairesTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_tableau_de_bord_et_les_statistiques_affichent_le_meme_chiffre_d_affaires(): void
    {
        $client = User::factory()->create();
        VentePos::create(['numero' => 'POS-1', 'montant_total' => 100000, 'methode_paiement' => 'Especes']);
        Commande::create(['id_ut' => $client->id, 'date_c' => now(), 'statut' => 'confirmee', 'montant_c' => 200000]);
        Commande::create(['id_ut' => $client->id, 'date_c' => now(), 'statut' => 'en_attente', 'montant_c' => 999000]);
        Facture::create(['numero' => 'FAC-1', 'client_nom' => 'Entreprise', 'montant_total' => 50000, 'statut' => 'payee']);
        Facture::create(['numero' => 'FAC-2', 'client_nom' => 'Entreprise', 'montant_total' => 888000, 'statut' => 'emise']);

        $gerante = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);

        $this->actingAs($gerante, 'staff')
            ->get(route('gerante.dashboard'))
            ->assertViewHas('chiffreAffaires', 350000);

        $this->actingAs($gerante, 'staff')
            ->get(route('gerante.analytics.index'))
            ->assertViewHas('caTotal', 350000);
    }
}
