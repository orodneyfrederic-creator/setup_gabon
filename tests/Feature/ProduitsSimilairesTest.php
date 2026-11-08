<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProduitsSimilairesTest extends TestCase
{
    use RefreshDatabase;

    private function produit(string $designation, int $prix, int $stock, Category $categorie): Produit
    {
        return Produit::create([
            'designation' => $designation,
            'prix_unitaire' => $prix,
            'quantite' => $stock,
            'reference' => strtoupper(str_replace(' ', '-', $designation)),
            'id_cat' => $categorie->id,
            'id_m' => Marque::firstOrCreate(['nom' => 'HP'])->id,
        ]);
    }

    public function test_les_produits_similaires_sont_de_la_meme_categorie_en_stock_d_abord_puis_par_prix_proche(): void
    {
        $portables = Category::create(['nom' => 'Ordinateurs portables']);
        $souris = Category::create(['nom' => 'Souris']);

        $reference = $this->produit('HP Probook', 400000, 5, $portables);
        $this->produit('HP Elitebook', 410000, 0, $portables);
        $this->produit('Dell Vostro', 600000, 2, $portables);
        $this->produit('Lenovo Thinkpad', 380000, 3, $portables);
        $this->produit('Souris Logitech', 400000, 10, $souris);

        $designations = $reference->produitsSimilaires()->pluck('designation')->all();

        $this->assertSame(['Lenovo Thinkpad', 'Dell Vostro', 'HP Elitebook'], $designations);
    }

    public function test_la_fiche_produit_affiche_la_section_dans_la_meme_categorie(): void
    {
        $portables = Category::create(['nom' => 'Ordinateurs portables']);
        $reference = $this->produit('HP Probook', 400000, 5, $portables);
        $this->produit('Lenovo Thinkpad', 380000, 3, $portables);

        $this->get(route('produits.show', $reference->id))
            ->assertOk()
            ->assertSee('Dans la même catégorie')
            ->assertSee('Lenovo Thinkpad');
    }

    public function test_sans_autre_produit_dans_la_categorie_la_section_est_absente(): void
    {
        $reference = $this->produit('HP Probook', 400000, 5, Category::create(['nom' => 'Ordinateurs portables']));

        $this->get(route('produits.show', $reference->id))
            ->assertOk()
            ->assertDontSee('Dans la même catégorie');
    }
}
