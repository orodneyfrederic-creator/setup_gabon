<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Negociation;
use App\Models\Produit;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NegociationGeranteDecisionTest extends TestCase
{
    use RefreshDatabase;

    private function creerNegociationEnAttente(): Negociation
    {
        $produit = Produit::create([
            'designation' => 'Imprimante HP LaserJet',
            'prix_unitaire' => 100000,
            'prix_achat' => 90000,
            'quantite' => 5,
            'reference' => 'HP-LJ',
            'id_cat' => Category::create(['nom' => 'Imprimantes', 'negociable' => true])->id,
            'id_m' => Marque::create(['nom' => 'HP'])->id,
            'decision_manuelle' => true,
        ]);

        return Negociation::create([
            'reference' => 'NEG-TEST-010',
            'id_p' => $produit->id,
            'id_ut' => User::factory()->create()->id,
            'prix_catalogue' => 100000,
            'prix_plancher' => 98000,
            'statut' => 'en_attente_gerante',
            'tours_utilises' => 1,
            'tours_max' => 3,
        ]);
    }

    private function gerante(): Staff
    {
        return Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);
    }

    public function test_une_contre_offre_au_dessus_du_prix_catalogue_est_refusee(): void
    {
        $negociation = $this->creerNegociationEnAttente();

        $this->actingAs($this->gerante(), 'staff')
            ->post(route('gerante.negociations.decider', $negociation), ['action' => 'contre_offre', 'montant' => 120000])
            ->assertSessionHas('error', 'Le montant ne peut pas dépasser le prix catalogue (100 000 FCFA).');

        $this->assertSame('en_attente_gerante', $negociation->fresh()->statut);
        $this->assertSame(0, $negociation->offres()->count());
    }

    public function test_un_accord_sous_le_prix_d_achat_est_refuse(): void
    {
        $negociation = $this->creerNegociationEnAttente();

        $this->actingAs($this->gerante(), 'staff')
            ->post(route('gerante.negociations.decider', $negociation), ['action' => 'accepter', 'montant' => 85000])
            ->assertSessionHas('error', 'Le montant ne peut pas être inférieur au prix d\'achat (90 000 FCFA).');

        $this->assertSame('en_attente_gerante', $negociation->fresh()->statut);
        $this->assertNull($negociation->fresh()->prix_final_convenu);
    }

    public function test_une_contre_offre_dans_les_bornes_est_transmise_au_client(): void
    {
        $negociation = $this->creerNegociationEnAttente();

        $this->actingAs($this->gerante(), 'staff')
            ->post(route('gerante.negociations.decider', $negociation), ['action' => 'contre_offre', 'montant' => 95000])
            ->assertSessionHas('success', 'Contre-offre transmise au client.');

        $this->assertSame('en_cours', $negociation->fresh()->statut);
        $this->assertDatabaseHas('negociation_offres', [
            'id_negociation' => $negociation->id,
            'auteur' => 'gerante',
            'type_action' => 'contre_offre',
            'montant_propose' => 95000,
        ]);
    }

    public function test_la_gerante_peut_refuser_sans_envoyer_de_montant(): void
    {
        $negociation = $this->creerNegociationEnAttente();

        $this->actingAs($this->gerante(), 'staff')
            ->post(route('gerante.negociations.decider', $negociation), ['action' => 'refuser'])
            ->assertSessionHas('success', 'Négociation refusée.');

        $this->assertSame('refusee', $negociation->fresh()->statut);
    }
}
