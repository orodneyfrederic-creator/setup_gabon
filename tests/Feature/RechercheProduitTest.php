<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RechercheProduitTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_recherche_combinee_a_une_categorie_ne_renvoie_que_cette_categorie(): void
    {
        $marque = Marque::create(['nom' => 'HP']);
        $ordinateurs = Category::create(['nom' => 'Ordinateurs']);
        $imprimantes = Category::create(['nom' => 'Imprimantes']);

        $pc = Produit::create([
            'designation' => 'PC Portable HP',
            'prix_unitaire' => 350000,
            'quantite' => 5,
            'reference' => 'HP-PC',
            'id_cat' => $ordinateurs->id,
            'id_m' => $marque->id,
        ]);
        Produit::create([
            'designation' => 'LaserJet Pro',
            'description' => 'Imprimante de marque HP',
            'prix_unitaire' => 150000,
            'quantite' => 5,
            'reference' => 'HP-LJ',
            'id_cat' => $imprimantes->id,
            'id_m' => $marque->id,
        ]);

        $this->get(route('home', ['search' => 'HP', 'category' => $ordinateurs->id]))
            ->assertOk()
            ->assertViewHas('produits', fn ($produits) => $produits->pluck('id')->all() === [$pc->id]);
    }
}
