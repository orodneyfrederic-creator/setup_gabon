<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandeGeranteStatutTest extends TestCase
{
    use RefreshDatabase;

    private function creerCommande(string $statut, Produit $produit, int $quantite): Commande
    {
        $commande = Commande::create([
            'id_ut' => User::factory()->create()->id,
            'date_c' => now(),
            'statut' => $statut,
            'montant_c' => $produit->prix_unitaire * $quantite,
        ]);

        LigneCommande::create([
            'id_c' => $commande->id,
            'id_p' => $produit->id,
            'quantite_com' => $quantite,
            'prix_unitaire_com' => $produit->prix_unitaire,
        ]);

        return $commande;
    }

    private function creerProduit(int $stock): Produit
    {
        return Produit::create([
            'designation' => 'Imprimante HP LaserJet',
            'prix_unitaire' => 100000,
            'quantite' => $stock,
            'reference' => 'HP-LJ',
            'id_cat' => Category::create(['nom' => 'Imprimantes'])->id,
            'id_m' => Marque::create(['nom' => 'HP'])->id,
        ]);
    }

    private function gerante(): Staff
    {
        return Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);
    }

    public function test_confirmer_une_commande_en_attente_decremente_le_stock(): void
    {
        $produit = $this->creerProduit(10);
        $commande = $this->creerCommande('en_attente', $produit, 2);

        $this->actingAs($this->gerante(), 'staff')
            ->patch(route('gerante.commandes.statut', $commande), ['statut' => 'confirmee'])
            ->assertSessionHas('success');

        $this->assertSame('confirmee', $commande->fresh()->statut);
        $this->assertSame(8, $produit->fresh()->quantite);
    }

    public function test_la_fiche_commande_ne_propose_que_les_statuts_accessibles(): void
    {
        $commande = $this->creerCommande('confirmee', $this->creerProduit(8), 2);

        $this->actingAs($this->gerante(), 'staff')
            ->get(route('gerante.commandes.show', $commande))
            ->assertOk()
            ->assertSee('value="livree"', false)
            ->assertSee('value="annulee"', false)
            ->assertDontSee('value="en_attente"', false)
            ->assertSee('x-model="statutChoisi"', false);
    }

    public function test_une_commande_annulee_ne_peut_pas_etre_reconfirmee(): void
    {
        $produit = $this->creerProduit(10);
        $commande = $this->creerCommande('annulee', $produit, 2);

        $this->actingAs($this->gerante(), 'staff')
            ->patch(route('gerante.commandes.statut', $commande), ['statut' => 'confirmee'])
            ->assertSessionHas('error', 'Transition de statut non autorisée : annulee → confirmee.');

        $this->assertSame('annulee', $commande->fresh()->statut);
        $this->assertSame(10, $produit->fresh()->quantite);
    }

    public function test_une_commande_confirmee_ne_peut_pas_revenir_en_attente(): void
    {
        $produit = $this->creerProduit(8);
        $commande = $this->creerCommande('confirmee', $produit, 2);

        $this->actingAs($this->gerante(), 'staff')
            ->patch(route('gerante.commandes.statut', $commande), ['statut' => 'en_attente'])
            ->assertSessionHas('error', 'Transition de statut non autorisée : confirmee → en_attente.');

        $this->assertSame('confirmee', $commande->fresh()->statut);
        $this->assertSame(8, $produit->fresh()->quantite);
    }
}
