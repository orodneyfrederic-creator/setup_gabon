<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltresCatalogueTest extends TestCase
{
    use RefreshDatabase;

    private Marque $hp;

    private Marque $dell;

    protected function setUp(): void
    {
        parent::setUp();

        $categorie = Category::create(['nom' => 'Ordinateurs portables']);
        $this->hp = Marque::create(['nom' => 'HP']);
        $this->dell = Marque::create(['nom' => 'Dell']);
        Marque::create(['nom' => 'Lenovo']);

        foreach ([
            ['HP Probook', 465000, 5, $this->hp],
            ['HP Elitebook', 350000, 0, $this->hp],
            ['Dell Vostro', 250000, 3, $this->dell],
        ] as [$nom, $prix, $stock, $marque]) {
            Produit::create([
                'designation' => $nom,
                'prix_unitaire' => $prix,
                'quantite' => $stock,
                'reference' => strtoupper(str_replace(' ', '-', $nom)),
                'id_cat' => $categorie->id,
                'id_m' => $marque->id,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $filtres
     * @return list<string>
     */
    private function designationsAffichees(array $filtres): array
    {
        $reponse = $this->get(route('home', $filtres))->assertOk();

        return $reponse->viewData('produits')->pluck('designation')->all();
    }

    public function test_filtrer_par_marque(): void
    {
        $designations = $this->designationsAffichees(['marque' => $this->dell->id]);

        $this->assertSame(['Dell Vostro'], $designations);
    }

    public function test_filtrer_par_fourchette_de_prix_meme_inversee(): void
    {
        $this->assertSame(['HP Elitebook'], $this->designationsAffichees(['prix_min' => 300000, 'prix_max' => 400000]));
        $this->assertSame(['HP Elitebook'], $this->designationsAffichees(['prix_min' => 400000, 'prix_max' => 300000]));
    }

    public function test_afficher_uniquement_les_produits_en_stock(): void
    {
        $designations = $this->designationsAffichees(['en_stock' => 1, 'tri' => 'nom']);

        $this->assertSame(['Dell Vostro', 'HP Probook'], $designations);
    }

    public function test_trier_par_prix_croissant_et_decroissant(): void
    {
        $this->assertSame(['Dell Vostro', 'HP Elitebook', 'HP Probook'], $this->designationsAffichees(['tri' => 'prix_croissant']));
        $this->assertSame(['HP Probook', 'HP Elitebook', 'Dell Vostro'], $this->designationsAffichees(['tri' => 'prix_decroissant']));
    }

    public function test_des_valeurs_invalides_sont_ignorees_sans_erreur(): void
    {
        $designations = $this->designationsAffichees(['marque' => 'abc', 'prix_min' => 'x', 'tri' => 'inconnu']);

        $this->assertCount(3, $designations);
    }

    public function test_seules_les_marques_presentes_au_catalogue_sont_proposees(): void
    {
        $this->get(route('home'))
            ->assertViewHas('marques', fn ($marques) => $marques->pluck('nom')->all() === ['Dell', 'HP']);
    }

    public function test_un_filtre_actif_peut_etre_retire_individuellement(): void
    {
        $this->get(route('home', ['marque' => $this->hp->id, 'en_stock' => 1]))
            ->assertSee('Marque : HP')
            ->assertSee('En stock uniquement')
            ->assertSee('Retirer le filtre Marque : HP');
    }
}
