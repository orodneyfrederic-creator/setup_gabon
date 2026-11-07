<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartageProduitTest extends TestCase
{
    use RefreshDatabase;

    private function creerProduit(array $attributs = []): Produit
    {
        return Produit::create(array_merge([
            'designation' => 'HP Probook 440 G8',
            'description' => 'Ordinateur portable 14 pouces, Intel Core i5.',
            'prix_unitaire' => 465000,
            'quantite' => 5,
            'reference' => 'LAP-HP-001',
            'id_cat' => Category::create(['nom' => 'Ordinateurs portables'])->id,
            'id_m' => Marque::create(['nom' => 'HP'])->id,
        ], $attributs));
    }

    public function test_la_fiche_produit_expose_un_apercu_de_partage_et_des_donnees_structurees(): void
    {
        $produit = $this->creerProduit();

        $this->get(route('produits.show', $produit->id))
            ->assertOk()
            ->assertSee('<meta property="og:type" content="product">', false)
            ->assertSee('<meta property="og:image" content="'.route('produits.apercu', $produit->id).'">', false)
            ->assertSee('<link rel="canonical" href="'.route('produits.show', $produit->id).'">', false)
            ->assertSee('"@type":"Product"', false)
            ->assertSee('"price":465000', false)
            ->assertSee('"priceCurrency":"XAF"', false)
            ->assertSee('"availability":"https://schema.org/InStock"', false);
    }

    public function test_l_apercu_d_un_produit_sans_photo_renvoie_le_visuel_de_la_boutique(): void
    {
        $produit = $this->creerProduit();

        $this->get(route('produits.apercu', $produit->id))
            ->assertRedirect(asset('img/partage-setup.jpg'));
    }

    public function test_le_plan_du_site_liste_les_produits_et_les_pages_legales(): void
    {
        $produit = $this->creerProduit();

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('produits.show', $produit->id).'</loc>', false)
            ->assertSee('<loc>'.route('legal.cgv').'</loc>', false);
    }
}
