<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitors_can_access_public_routes(): void
    {
        $category = Category::create(['nom' => 'Informatique']);
        $marque = Marque::create(['nom' => 'HP']);
        $produit = Produit::create([
            'designation' => 'PC Portable HP',
            'description' => 'Super PC',
            'prix_unitaire' => 350000,
            'quantite' => 10,
            'reference' => 'REF-HP-001',
            'id_cat' => $category->id,
            'id_m' => $marque->id,
        ]);

        $this->get('/')->assertStatus(200);
        $this->get('/a-propos')->assertStatus(200);
        $this->get('/categories')->assertStatus(200);
        $this->get("/produits/{$produit->id}")->assertStatus(200);
    }

    public function test_visitors_are_redirected_to_login_when_accessing_protected_client_routes(): void
    {
        $this->get('/mon-compte')
            ->assertRedirect(route('login'))
            ->assertSessionHas('info');

        $this->get('/commandes')
            ->assertRedirect(route('login'))
            ->assertSessionHas('info');

        $this->get('/checkout')
            ->assertRedirect(route('login'))
            ->assertSessionHas('info');
    }

    public function test_authenticated_clients_can_access_protected_client_routes(): void
    {
        $client = User::create([
            'nom' => 'Nzoghe',
            'prenom' => 'Paul',
            'email' => 'paul@gmail.com',
            'password' => 'password123',
            'role' => 'client',
        ]);

        $this->actingAs($client, 'web')
            ->get('/mon-compte')
            ->assertStatus(200)
            ->assertSee('Mon Compte Personnel');

        $this->actingAs($client, 'web')
            ->get('/commandes')
            ->assertStatus(200)
            ->assertSee('Mes Commandes');

        $this->actingAs($client, 'web')
            ->get('/checkout')
            ->assertStatus(200)
            ->assertSee('Finaliser ma commande');
    }

    public function test_regular_clients_cannot_access_gerante_dashboard(): void
    {
        $client = User::create([
            'nom' => 'Nzoghe',
            'prenom' => 'Paul',
            'email' => 'paul2@gmail.com',
            'password' => 'password123',
            'role' => 'client',
        ]);

        $this->actingAs($client, 'web')
            ->get('/gerante/dashboard')
            ->assertStatus(403);
    }
}
