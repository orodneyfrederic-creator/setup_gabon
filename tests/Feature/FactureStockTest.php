<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\LigneDevis;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactureStockTest extends TestCase
{
    use RefreshDatabase;

    private function gerante(): Staff
    {
        return Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);
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

    private function creerDevisAccepte(Produit $produit, int $quantite): Devis
    {
        $devis = Devis::create([
            'numero' => 'DEV-TEST-001',
            'client_nom' => 'Entreprise Test',
            'statut' => 'accepte',
            'montant_total' => $produit->prix_unitaire * $quantite,
        ]);

        LigneDevis::create([
            'id_devis' => $devis->id,
            'id_p' => $produit->id,
            'designation' => $produit->designation,
            'quantite' => $quantite,
            'prix_unitaire' => $produit->prix_unitaire,
            'total_ligne' => $produit->prix_unitaire * $quantite,
        ]);

        return $devis;
    }

    public function test_generer_une_facture_sort_les_produits_du_stock(): void
    {
        $produit = $this->creerProduit(10);
        $devis = $this->creerDevisAccepte($produit, 3);

        $this->actingAs($this->gerante(), 'staff')
            ->post(route('gerante.factures.create_from_devis', $devis))
            ->assertSessionHas('success');

        $this->assertSame(7, $produit->fresh()->quantite);
        $this->assertDatabaseHas('ligne_factures', ['id_p' => $produit->id, 'quantite' => 3]);
        $this->assertDatabaseHas('mouvement_stocks', ['id_p' => $produit->id, 'type' => 'sortie', 'quantite' => 3]);
    }

    public function test_une_facture_est_refusee_si_le_stock_est_insuffisant(): void
    {
        $produit = $this->creerProduit(2);
        $devis = $this->creerDevisAccepte($produit, 3);

        $this->actingAs($this->gerante(), 'staff')
            ->post(route('gerante.factures.create_from_devis', $devis))
            ->assertSessionHas('error', 'Stock insuffisant pour facturer : Imprimante HP LaserJet (demandé : 3, en stock : 2).');

        $this->assertSame(2, $produit->fresh()->quantite);
        $this->assertDatabaseCount('factures', 0);
    }

    public function test_annuler_une_facture_remet_les_produits_en_stock_une_seule_fois(): void
    {
        $produit = $this->creerProduit(10);
        $devis = $this->creerDevisAccepte($produit, 3);
        $gerante = $this->gerante();

        $this->actingAs($gerante, 'staff')->post(route('gerante.factures.create_from_devis', $devis));
        $facture = Facture::firstOrFail();

        $this->actingAs($gerante, 'staff')
            ->patch(route('gerante.factures.status', $facture), ['statut' => 'annulee'])
            ->assertSessionHas('success');
        $this->assertSame(10, $produit->fresh()->quantite);

        $this->actingAs($gerante, 'staff')
            ->patch(route('gerante.factures.status', $facture), ['statut' => 'annulee'])
            ->assertSessionHas('error');
        $this->assertSame(10, $produit->fresh()->quantite);
    }

    public function test_une_facture_emise_propose_l_enregistrement_du_paiement(): void
    {
        $produit = $this->creerProduit(10);
        $devis = $this->creerDevisAccepte($produit, 1);
        $gerante = $this->gerante();

        $this->actingAs($gerante, 'staff')->post(route('gerante.factures.create_from_devis', $devis));
        $facture = Facture::firstOrFail();

        $this->actingAs($gerante, 'staff')
            ->get(route('gerante.factures.show', $facture))
            ->assertSee('Enregistrer le paiement');

        $this->actingAs($gerante, 'staff')
            ->patch(route('gerante.factures.status', $facture), ['statut' => 'payee'])
            ->assertSessionHas('success', 'Paiement enregistré.');
        $this->assertSame('payee', $facture->fresh()->statut);
    }
}
