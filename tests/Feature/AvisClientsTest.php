<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Category;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AvisClientsTest extends TestCase
{
    use RefreshDatabase;

    private Produit $produit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->produit = Produit::create([
            'designation' => 'HP Probook 440 G8',
            'prix_unitaire' => 465000,
            'quantite' => 5,
            'reference' => 'LAP-HP-001',
            'id_cat' => Category::create(['nom' => 'Ordinateurs portables'])->id,
            'id_m' => Marque::create(['nom' => 'HP'])->id,
        ]);
    }

    private function clientAyantRecu(string $statut = 'livree', array $attributs = []): User
    {
        $client = User::factory()->create($attributs);
        $commande = Commande::create(['id_ut' => $client->id, 'date_c' => now(), 'statut' => $statut, 'montant_c' => 465000]);
        LigneCommande::create(['id_c' => $commande->id, 'id_p' => $this->produit->id, 'quantite_com' => 1, 'prix_unitaire_com' => 465000]);

        return $client;
    }

    private function publier(User $client, array $donnees = ['note' => 4, 'commentaire' => 'Très bon PC, rapide.']): TestResponse
    {
        return $this->actingAs($client, 'web')->post(route('avis.store', $this->produit), $donnees);
    }

    public function test_un_client_qui_a_recu_le_produit_publie_un_avis(): void
    {
        $client = $this->clientAyantRecu(attributs: ['prenom' => 'Paul', 'nom' => 'Obame']);

        $this->publier($client)->assertSessionHas('success');
        auth('web')->logout();

        $this->get(route('produits.show', $this->produit->id))
            ->assertSee('Très bon PC, rapide.')
            ->assertSee('Paul O.')
            ->assertDontSee('Obame')
            ->assertSee('Achat vérifié')
            ->assertSee('"aggregateRating"', false);
    }

    public function test_un_client_dont_la_commande_n_est_pas_livree_ne_peut_pas_noter(): void
    {
        $client = $this->clientAyantRecu('confirmee');

        $this->publier($client)->assertSessionHas('error');

        $this->assertSame(0, Avis::count());
    }

    public function test_un_client_qui_n_a_pas_achete_ne_peut_pas_noter(): void
    {
        $client = User::factory()->create();

        $this->publier($client)->assertSessionHas('error');

        $this->assertSame(0, Avis::count());
    }

    public function test_un_visiteur_est_invite_a_se_connecter(): void
    {
        $this->post(route('avis.store', $this->produit), ['note' => 5])->assertRedirect(route('login'));

        $this->assertSame(0, Avis::count());
    }

    public function test_la_note_doit_etre_comprise_entre_1_et_5(): void
    {
        $client = $this->clientAyantRecu();

        $this->publier($client, ['note' => 6])->assertSessionHasErrors('note');
        $this->publier($client, ['note' => 0])->assertSessionHasErrors('note');
    }

    public function test_un_second_envoi_modifie_l_avis_au_lieu_d_en_creer_un_autre(): void
    {
        $client = $this->clientAyantRecu();

        $this->publier($client);
        $this->publier($client, ['note' => 2, 'commentaire' => 'Batterie faible finalement.']);

        $avis = Avis::sole();
        $this->assertSame(2, $avis->note);
        $this->assertSame('Batterie faible finalement.', $avis->commentaire);
    }

    public function test_le_commentaire_est_affiche_echappe(): void
    {
        $client = $this->clientAyantRecu();

        $this->publier($client, ['note' => 5, 'commentaire' => '<script>alert(1)</script>']);

        $this->get(route('produits.show', $this->produit->id))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_un_avis_masque_par_la_gerante_disparait_et_ne_peut_pas_etre_republie_par_le_client(): void
    {
        $client = $this->clientAyantRecu();
        $this->publier($client, ['note' => 1, 'commentaire' => 'Propos injurieux']);
        $gerante = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);

        $this->actingAs($gerante, 'staff')
            ->patch(route('gerante.avis.moderer', Avis::sole()), ['action' => 'masquer'])
            ->assertSessionHasErrors('motif_masquage');

        $this->actingAs($gerante, 'staff')
            ->patch(route('gerante.avis.moderer', Avis::sole()), ['action' => 'masquer', 'motif_masquage' => 'Propos injurieux ou discriminatoires'])
            ->assertSessionHas('success');

        $this->get(route('produits.show', $this->produit->id))->assertDontSee('Propos injurieux');

        $this->publier($client, ['note' => 5, 'commentaire' => 'Republication'])->assertSessionHas('error');
        $this->assertSame(Avis::STATUT_MASQUE, Avis::sole()->statut);
    }

    public function test_la_gerante_consulte_la_liste_des_avis(): void
    {
        $this->publier($this->clientAyantRecu());
        $gerante = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);

        $this->actingAs($gerante, 'staff')->get(route('gerante.avis.index'))
            ->assertOk()
            ->assertSee('Très bon PC, rapide.');
    }

    public function test_un_client_ne_peut_pas_moderer_les_avis(): void
    {
        $client = $this->clientAyantRecu();
        $this->publier($client);

        $this->actingAs($client, 'web')
            ->patch(route('gerante.avis.moderer', Avis::sole()), ['action' => 'masquer', 'motif_masquage' => 'x'])
            ->assertForbidden();

        $this->assertTrue(Avis::sole()->estPublie());
    }
}
