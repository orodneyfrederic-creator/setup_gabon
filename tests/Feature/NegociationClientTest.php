<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Negociation;
use App\Models\Produit;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class NegociationClientTest extends TestCase
{
    use RefreshDatabase;

    private function creerProduitNegociable(): Produit
    {
        $categorie = Category::create(['nom' => 'PC Portables', 'negociable' => true, 'remise_max_pct' => 2]);
        $marque = Marque::create(['nom' => 'Dell']);

        return Produit::create([
            'designation' => 'Dell Vostro 3530',
            'prix_unitaire' => 450000,
            'quantite' => 10,
            'reference' => 'DELL-3530',
            'id_cat' => $categorie->id,
            'id_m' => $marque->id,
        ]);
    }

    private function creerNegociationAcceptee(User $client, Produit $produit, int $quantite): Negociation
    {
        return Negociation::create([
            'reference' => 'NEG-TEST-001',
            'id_p' => $produit->id,
            'id_ut' => $client->id,
            'quantite' => $quantite,
            'prix_catalogue' => 450000,
            'prix_plancher' => 441000,
            'prix_final_convenu' => 441000,
            'statut' => 'acceptee',
            'tours_utilises' => 1,
            'tours_max' => 3,
            'expire_a' => now()->addHours(24),
        ]);
    }

    /**
     * @param  array<int, array<string, int>>  $items
     */
    private function passerCommande(User $client, array $items): TestResponse
    {
        return $this->actingAs($client, 'web')->post(route('checkout.store'), [
            'items' => json_encode($items),
            'methode_paiement' => 'especes',
            'adresse_livraison' => 'Libreville, Louis',
        ]);
    }

    public function test_le_prix_negocie_est_applique_dans_la_limite_de_la_quantite_convenue(): void
    {
        $client = User::factory()->create();
        $produit = $this->creerProduitNegociable();
        $negociation = $this->creerNegociationAcceptee($client, $produit, 1);

        $this->passerCommande($client, [
            ['id' => $produit->id, 'quantite' => 1, 'negociation_id' => $negociation->id],
        ]);

        $this->assertDatabaseHas('lignes_commandes', [
            'id_p' => $produit->id,
            'quantite_com' => 1,
            'prix_final_unitaire' => 441000,
            'negociation_id' => $negociation->id,
        ]);
        $this->assertSame('utilisee', $negociation->fresh()->statut);
    }

    public function test_une_quantite_superieure_a_l_accord_bloque_la_commande(): void
    {
        $client = User::factory()->create();
        $produit = $this->creerProduitNegociable();
        $negociation = $this->creerNegociationAcceptee($client, $produit, 1);

        $this->passerCommande($client, [
            ['id' => $produit->id, 'quantite' => 5, 'negociation_id' => $negociation->id],
        ])
            ->assertSessionHas('error', fn (string $message) => str_contains($message, 'L\'offre négociée n\'est plus valable pour : Dell Vostro 3530'))
            ->assertSessionHas('negociations_invalides', [$negociation->id]);

        $this->assertDatabaseCount('commandes', 0);
        $this->assertSame('acceptee', $negociation->fresh()->statut);
    }

    public function test_une_offre_conclue_avant_un_changement_de_prix_catalogue_bloque_la_commande(): void
    {
        $client = User::factory()->create();
        $produit = $this->creerProduitNegociable();
        $negociation = $this->creerNegociationAcceptee($client, $produit, 1);
        $produit->update(['prix_unitaire' => 400000]);

        $this->passerCommande($client, [
            ['id' => $produit->id, 'quantite' => 1, 'negociation_id' => $negociation->id],
        ])->assertSessionHas('negociations_invalides', [$negociation->id]);

        $this->assertDatabaseCount('commandes', 0);
        $this->assertSame('acceptee', $negociation->fresh()->statut);
    }

    public function test_le_client_ne_peut_plus_proposer_apres_le_dernier_tour(): void
    {
        $client = User::factory()->create();
        $produit = $this->creerProduitNegociable();
        $negociation = Negociation::create([
            'reference' => 'NEG-TEST-003',
            'id_p' => $produit->id,
            'id_ut' => $client->id,
            'prix_catalogue' => 450000,
            'prix_plancher' => 441000,
            'statut' => 'en_cours',
            'tours_utilises' => 3,
            'tours_max' => 3,
        ]);

        $this->actingAs($client, 'web')
            ->postJson(route('negociations.proposer', $negociation), ['montant' => 300000])
            ->assertUnprocessable()
            ->assertJson(['message' => 'Vous avez utilisé tous vos tours de négociation. Acceptez la dernière offre ou abandonnez la négociation.']);

        $this->assertSame(0, $negociation->offres()->count());
        $this->assertSame(3, $negociation->fresh()->tours_utilises);
    }

    public function test_un_compte_staff_ne_peut_pas_agir_sur_la_negociation_d_un_client_de_meme_identifiant(): void
    {
        $client = User::factory()->create();
        $staff = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);
        $this->assertSame($client->id, $staff->id_staff);

        $produit = $this->creerProduitNegociable();
        $negociation = Negociation::create([
            'reference' => 'NEG-TEST-002',
            'id_p' => $produit->id,
            'id_ut' => $client->id,
            'prix_catalogue' => 450000,
            'prix_plancher' => 441000,
            'statut' => 'en_cours',
            'tours_max' => 3,
        ]);

        $this->actingAs($staff, 'staff')
            ->postJson(route('negociations.abandonner', $negociation))
            ->assertForbidden();

        $this->assertSame('en_cours', $negociation->fresh()->statut);
    }

    public function test_un_compte_staff_ne_peut_pas_ouvrir_de_negociation(): void
    {
        $staff = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);
        $produit = $this->creerProduitNegociable();

        $this->actingAs($staff, 'staff')
            ->postJson(route('negociations.initier', $produit))
            ->assertForbidden()
            ->assertJson(['message' => 'Les comptes du personnel ne peuvent pas négocier de prix.']);

        $this->assertDatabaseCount('negociations', 0);
    }

    public function test_le_client_proprietaire_peut_proposer_une_offre(): void
    {
        $client = User::factory()->create();
        $produit = $this->creerProduitNegociable();

        $negociationId = $this->actingAs($client, 'web')
            ->postJson(route('negociations.initier', $produit))
            ->assertOk()
            ->json('negociation.id');

        $this->actingAs($client, 'web')
            ->postJson(route('negociations.proposer', $negociationId), ['montant' => 445000])
            ->assertOk()
            ->assertJson(['statut' => 'acceptee', 'prix_final' => 445000]);
    }
}
