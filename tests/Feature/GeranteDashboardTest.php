<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commande;
use App\Models\Marque;
use App\Models\Negociation;
use App\Models\Produit;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeranteDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_tableau_de_bord_donne_acces_aux_commandes_en_ligne_et_aux_negociations_en_attente(): void
    {
        $client = User::factory()->create();
        Commande::create(['id_ut' => $client->id, 'date_c' => now(), 'statut' => 'en_attente', 'montant_c' => 100000]);
        Commande::create(['id_ut' => $client->id, 'date_c' => now(), 'statut' => 'livree', 'montant_c' => 100000]);

        $produit = Produit::create([
            'designation' => 'Imprimante HP LaserJet',
            'prix_unitaire' => 100000,
            'quantite' => 5,
            'reference' => 'HP-LJ',
            'id_cat' => Category::create(['nom' => 'Imprimantes'])->id,
            'id_m' => Marque::create(['nom' => 'HP'])->id,
        ]);
        Negociation::create([
            'reference' => 'NEG-TEST-020',
            'id_p' => $produit->id,
            'id_ut' => $client->id,
            'prix_catalogue' => 100000,
            'prix_plancher' => 98000,
            'statut' => 'en_attente_gerante',
            'tours_max' => 3,
        ]);

        $gerante = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);

        $this->actingAs($gerante, 'staff')
            ->get(route('gerante.dashboard'))
            ->assertOk()
            ->assertSee(route('gerante.commandes.index'), false)
            ->assertSee(route('gerante.negociations.index'), false)
            ->assertSee('1 en attente')
            ->assertSee('1 à décider');
    }
}
