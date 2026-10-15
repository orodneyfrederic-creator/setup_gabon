<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevisCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_gerante_can_create_devis_without_mass_assignment_error(): void
    {
        $gerante = Staff::create([
            'email' => 'gerante@gmail.com',
            'passwd' => 'gerante241',
            'role' => 'gerante',
        ]);

        $category = Category::create([
            'nom' => 'Accessoires',
        ]);

        $marque = Marque::create([
            'nom' => 'Logitech',
        ]);

        $produit = Produit::create([
            'id_cat' => $category->id,
            'id_m' => $marque->id,
            'reference' => 'REF-KEY-001',
            'designation' => 'Clavier Gamer RGB',
            'prix_unitaire' => 25000,
            'quantite' => 10,
            'seuil_alerte' => 2,
        ]);

        $response = $this->actingAs($gerante, 'staff')
            ->post(route('gerante.devis.store'), [
                'client_nom' => 'Alain Mba',
                'client_telephone' => '+24166001122',
                'client_email' => 'alain.mba@gmail.com',
                'lignes' => [
                    [
                        'id_p' => $produit->id,
                        'quantite' => 2,
                    ],
                ],
            ]);

        if (session('error')) {
            dump(session('error'));
        }

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('devis', [
            'client_nom' => 'Alain Mba',
            'montant_total' => 50000,
        ]);
    }
}
