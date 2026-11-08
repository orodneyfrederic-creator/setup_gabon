<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\CommandeEnregistree;
use App\Notifications\StatutCommandeModifie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommandeSansCompteTest extends TestCase
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

    /**
     * @param  array<string, mixed>  $surcharges
     * @return array<string, mixed>
     */
    private function formulaire(array $surcharges = []): array
    {
        return array_merge([
            'items' => json_encode([['id' => $this->produit->id, 'quantite' => 1, 'prix' => 1]]),
            'methode_paiement' => 'especes',
            'adresse_livraison' => 'Akanda, carrefour GP',
            'accepte_conditions' => '1',
            'client_nom' => 'Mireille Ndong',
            'client_telephone' => '066 12 34 56',
            'client_email' => 'mireille@exemple.ga',
        ], $surcharges);
    }

    public function test_un_visiteur_accede_au_formulaire_de_commande_sans_se_connecter(): void
    {
        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Nom et prénom')
            ->assertSee('Déjà client ?');
    }

    public function test_un_visiteur_commande_sans_compte_et_arrive_sur_son_lien_de_suivi(): void
    {
        Notification::fake();

        $reponse = $this->post(route('checkout.store'), $this->formulaire());

        $commande = Commande::sole();
        $reponse->assertRedirect(route('commandes.suivi', $commande->jeton_suivi))->assertSessionHas('commande_confirmee');
        $this->assertNull($commande->id_ut);
        $this->assertSame('Mireille Ndong', $commande->client_nom);
        $this->assertSame('066 12 34 56', $commande->client_telephone);
        $this->assertEquals(465000, $commande->montant_c, 'Le prix vient du catalogue, pas du panier envoyé par le navigateur.');
        $this->assertSame(40, strlen($commande->jeton_suivi));
        $this->assertSame('mireille@exemple.ga', $commande->client_email);

        Notification::assertSentTo(new AnonymousNotifiable, CommandeEnregistree::class,
            fn ($notification, $canaux, $destinataire) => ($destinataire->routes['mail'] ?? null) === 'mireille@exemple.ga'
                && str_contains((string) $notification->toMail($destinataire)->render(), $commande->urlSuivi()));
    }

    public function test_le_nom_et_le_telephone_sont_obligatoires_sans_compte(): void
    {
        $this->post(route('checkout.store'), $this->formulaire(['client_nom' => '', 'client_telephone' => 'abc']))
            ->assertSessionHasErrors(['client_nom', 'client_telephone']);

        $this->assertSame(0, Commande::count());
    }

    public function test_l_e_mail_est_facultatif(): void
    {
        Notification::fake();

        $this->post(route('checkout.store'), $this->formulaire(['client_email' => '']))->assertSessionHasNoErrors();

        $this->assertNull(Commande::sole()->client_email);
        Notification::assertNotSentTo(new AnonymousNotifiable, CommandeEnregistree::class);
    }

    public function test_une_offre_negociee_n_est_pas_applicable_sans_compte(): void
    {
        $items = json_encode([['id' => $this->produit->id, 'quantite' => 1, 'negociation_id' => 999]]);

        $this->post(route('checkout.store'), $this->formulaire(['items' => $items]))
            ->assertSessionHas('error');

        $this->assertSame(0, Commande::count());
    }

    public function test_le_lien_de_suivi_affiche_la_commande_sans_connexion(): void
    {
        $this->post(route('checkout.store'), $this->formulaire());
        $commande = Commande::sole();

        $this->get(route('commandes.suivi', $commande->jeton_suivi))
            ->assertOk()
            ->assertSee('Commande n° '.$commande->id)
            ->assertSee('HP Probook 440 G8')
            ->assertSee('noindex', false);
    }

    public function test_un_lien_de_suivi_inconnu_renvoie_une_erreur_404(): void
    {
        $this->get('/suivi-commande/'.str_repeat('a', 40))->assertNotFound();
    }

    public function test_un_client_connecte_sans_telephone_le_renseigne_a_la_commande(): void
    {
        $client = User::factory()->create(['tel' => null]);

        $this->actingAs($client, 'web')
            ->post(route('checkout.store'), $this->formulaire(['client_nom' => null]))
            ->assertRedirect(route('orders.show', Commande::sole()->id));

        $this->assertSame('066 12 34 56', $client->fresh()->tel);
        $this->assertSame($client->email, Commande::sole()->client_email);
    }

    public function test_la_gerante_voit_et_traite_une_commande_sans_compte(): void
    {
        Notification::fake();
        $this->post(route('checkout.store'), $this->formulaire());
        $commande = Commande::sole();
        $gerante = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);

        $this->actingAs($gerante, 'staff')->get(route('gerante.commandes.index'))
            ->assertOk()->assertSee('Mireille Ndong')->assertSee('sans compte');
        $this->actingAs($gerante, 'staff')->get(route('gerante.commandes.show', $commande))
            ->assertOk()->assertSee('066 12 34 56');

        $this->actingAs($gerante, 'staff')
            ->patch(route('gerante.commandes.statut', $commande), ['statut' => 'confirmee'])
            ->assertSessionHas('success');

        Notification::assertSentTo(new AnonymousNotifiable, StatutCommandeModifie::class,
            fn ($notification, $canaux, $destinataire) => ($destinataire->routes['mail'] ?? null) === 'mireille@exemple.ga');
    }

    public function test_une_commande_est_conservee_si_le_client_supprime_son_compte(): void
    {
        $client = User::factory()->create();
        $commande = Commande::create(['id_ut' => $client->id, 'client_nom' => 'Paul Obame', 'date_c' => now(), 'statut' => 'livree', 'montant_c' => 465000]);
        LigneCommande::create(['id_c' => $commande->id, 'id_p' => $this->produit->id, 'quantite_com' => 1, 'prix_unitaire_com' => 465000]);

        $client->delete();

        $this->assertNull($commande->fresh()->id_ut);
        $this->assertSame('Paul Obame', $commande->fresh()->nomClient());
    }
}
