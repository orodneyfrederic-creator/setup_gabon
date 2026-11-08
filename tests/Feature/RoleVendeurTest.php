<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleVendeurTest extends TestCase
{
    use RefreshDatabase;

    private function vendeur(array $attributs = []): Staff
    {
        return Staff::create(array_merge([
            'nom' => 'Junior',
            'email' => 'vendeur@setup.ga',
            'passwd' => 'vendeur2026',
            'role' => Staff::ROLE_VENDEUR,
            'actif' => true,
        ], $attributs));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function ecransDuVendeur(): array
    {
        return [
            'caisse' => ['gerante.pos.index'],
            'commandes en ligne' => ['gerante.commandes.index'],
            'stock' => ['gerante.stock.index'],
            'SAV' => ['gerante.sav.index'],
            'mon compte' => ['gerante.compte.edit'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function ecransReservesALaGerante(): array
    {
        return [
            'tableau de bord' => ['gerante.dashboard'],
            'statistiques' => ['gerante.analytics.index'],
            'produits' => ['gerante.produits.index'],
            'factures' => ['gerante.factures.index'],
            'devis' => ['gerante.devis.index'],
            'clients' => ['gerante.clients.index'],
            'négociations' => ['gerante.negociations.index'],
            'avis' => ['gerante.avis.index'],
            'personnel' => ['gerante.personnel.index'],
        ];
    }

    #[DataProvider('ecransDuVendeur')]
    public function test_un_vendeur_accede_aux_ecrans_du_quotidien(string $route): void
    {
        $this->actingAs($this->vendeur(), 'staff')->get(route($route))->assertOk();
    }

    #[DataProvider('ecransReservesALaGerante')]
    public function test_un_vendeur_n_accede_pas_aux_ecrans_de_la_gerante(string $route): void
    {
        $this->actingAs($this->vendeur(), 'staff')->get(route($route))->assertForbidden();
    }

    public function test_le_menu_du_vendeur_ne_montre_que_ses_ecrans(): void
    {
        $this->actingAs($this->vendeur(), 'staff')->get(route('gerante.pos.index'))
            ->assertSee('Caisse')
            ->assertSee('Commandes en ligne')
            ->assertDontSee('Statistiques')
            ->assertDontSee('Personnel')
            ->assertSee('Espace vendeur');
    }

    public function test_un_vendeur_arrive_sur_la_caisse_apres_connexion(): void
    {
        $this->vendeur();

        $this->post('/login', ['email' => 'vendeur@setup.ga', 'password' => 'vendeur2026'])
            ->assertRedirect(route('gerante.pos.index'));
    }

    public function test_un_compte_desactive_ne_peut_pas_se_connecter(): void
    {
        $this->vendeur(['actif' => false]);

        $this->post('/login', ['email' => 'vendeur@setup.ga', 'password' => 'vendeur2026'])
            ->assertSessionHasErrors(['email' => 'Ce compte a été désactivé. Contactez la gérante.']);

        $this->assertGuest('staff');
    }

    public function test_un_compte_desactive_en_cours_de_session_est_deconnecte(): void
    {
        $vendeur = $this->vendeur();
        $this->actingAs($vendeur, 'staff');
        $vendeur->update(['actif' => false]);

        $this->get(route('gerante.pos.index'))->assertRedirect(route('login'));

        $this->assertGuest('staff');
    }

    public function test_un_client_n_accede_jamais_a_l_espace_de_gestion(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client, 'web')->get(route('gerante.pos.index'))->assertForbidden();
    }
}
