<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProduitGeranteNegociationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string, 1: bool|null}>
     */
    public static function choixNegociable(): array
    {
        return [
            'hériter de la catégorie' => ['inherit', null],
            'forcer activé' => ['1', true],
            'forcer désactivé' => ['0', false],
        ];
    }

    #[DataProvider('choixNegociable')]
    public function test_la_gerante_cree_un_produit_avec_chaque_choix_de_negociation(string $choix, ?bool $attendu): void
    {
        $gerante = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);

        $this->actingAs($gerante, 'staff')
            ->post(route('gerante.produits.store'), [
                'reference' => 'DELL-3530',
                'designation' => 'Dell Vostro 3530',
                'prix_unitaire' => 450000,
                'quantite' => 5,
                'id_cat' => Category::create(['nom' => 'PC Portables'])->id,
                'id_m' => Marque::create(['nom' => 'Dell'])->id,
                'negociable' => $choix,
                'nb_tours_max' => '',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('gerante.produits.index'));

        $produit = Produit::where('reference', 'DELL-3530')->firstOrFail();
        $this->assertSame($attendu, $produit->negociable);
        $this->assertSame(3, $produit->nb_tours_max);
    }
}
