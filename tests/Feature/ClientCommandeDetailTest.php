<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCommandeDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_detail_affiche_le_statut_confirme_et_le_prix_reellement_paye(): void
    {
        $client = User::factory()->create();
        $produit = Produit::create([
            'designation' => 'Dell Vostro 3530',
            'prix_unitaire' => 450000,
            'quantite' => 5,
            'reference' => 'DELL-3530',
            'id_cat' => Category::create(['nom' => 'PC Portables'])->id,
            'id_m' => Marque::create(['nom' => 'Dell'])->id,
        ]);
        $commande = Commande::create([
            'id_ut' => $client->id,
            'date_c' => now(),
            'statut' => 'confirmee',
            'montant_c' => 441000,
        ]);
        LigneCommande::create([
            'id_c' => $commande->id,
            'id_p' => $produit->id,
            'quantite_com' => 1,
            'prix_unitaire_com' => 450000,
            'prix_final_unitaire' => 441000,
        ]);

        $this->actingAs($client, 'web')
            ->get(route('orders.show', $commande->id))
            ->assertOk()
            ->assertSee('Confirmée')
            ->assertSee('1 × 441 000 FCFA')
            ->assertDontSee('450 000 FCFA');
    }
}
