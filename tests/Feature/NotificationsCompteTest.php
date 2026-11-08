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
use App\Notifications\NouvelleCommandeBoutique;
use App\Notifications\StatutCommandeModifie;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class NotificationsCompteTest extends TestCase
{
    use RefreshDatabase;

    private function creerProduit(int $stock = 5): Produit
    {
        return Produit::create([
            'designation' => 'HP Probook 440 G8',
            'prix_unitaire' => 465000,
            'quantite' => $stock,
            'reference' => 'LAP-HP-001',
            'id_cat' => Category::create(['nom' => 'Ordinateurs portables'])->id,
            'id_m' => Marque::create(['nom' => 'HP'])->id,
        ]);
    }

    public function test_un_lien_de_reinitialisation_est_envoye_au_client_existant(): void
    {
        Notification::fake();
        $client = User::factory()->create(['email' => 'paul@exemple.ga']);

        $this->post(route('password.email'), ['email' => 'paul@exemple.ga'])
            ->assertSessionHas('success');

        Notification::assertSentTo($client, ResetPassword::class);
    }

    public function test_une_adresse_inconnue_recoit_la_meme_reponse_sans_envoi(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'inconnu@exemple.ga'])
            ->assertSessionHas('success')
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_le_client_choisit_un_nouveau_mot_de_passe_avec_un_lien_valide(): void
    {
        $client = User::factory()->create(['email' => 'paul@exemple.ga']);
        $token = Password::broker('users')->createToken($client);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'paul@exemple.ga',
            'password' => 'nouveau-secret-2026',
            'password_confirmation' => 'nouveau-secret-2026',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('nouveau-secret-2026', $client->fresh()->password));
    }

    public function test_un_lien_invalide_est_refuse(): void
    {
        $client = User::factory()->create(['email' => 'paul@exemple.ga']);
        $ancienMotDePasse = $client->password;

        $this->post(route('password.update'), [
            'token' => 'jeton-invalide',
            'email' => 'paul@exemple.ga',
            'password' => 'nouveau-secret-2026',
            'password_confirmation' => 'nouveau-secret-2026',
        ])->assertSessionHasErrors('email');

        $this->assertSame($ancienMotDePasse, $client->fresh()->password);
    }

    public function test_une_commande_envoie_l_accuse_de_reception_et_alerte_la_boutique(): void
    {
        Notification::fake();
        $client = User::factory()->create();
        $produit = $this->creerProduit();

        $this->actingAs($client, 'web')->post(route('checkout.store'), [
            'items' => json_encode([['id' => $produit->id, 'quantite' => 1]]),
            'methode_paiement' => 'especes',
            'adresse_livraison' => 'Akanda, carrefour GP',
            'accepte_conditions' => '1',
        ]);

        $commande = Commande::firstOrFail();
        Notification::assertSentTo($client, CommandeEnregistree::class, fn ($notification) => $notification->commande->is($commande));
        Notification::assertSentTo(new AnonymousNotifiable, NouvelleCommandeBoutique::class,
            fn ($notification, $canaux, $destinataire) => $destinataire->routes['mail'] === config('boutique.email'));
    }

    public function test_le_client_est_prevenu_quand_sa_commande_est_confirmee(): void
    {
        Notification::fake();
        $client = User::factory()->create();
        $produit = $this->creerProduit(10);
        $commande = Commande::create(['id_ut' => $client->id, 'date_c' => now(), 'statut' => 'en_attente', 'montant_c' => 465000]);
        LigneCommande::create(['id_c' => $commande->id, 'id_p' => $produit->id, 'quantite_com' => 1, 'prix_unitaire_com' => 465000]);
        $gerante = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);

        $this->actingAs($gerante, 'staff')
            ->patch(route('gerante.commandes.statut', $commande), ['statut' => 'confirmee'])
            ->assertSessionHas('success');

        Notification::assertSentTo($client, StatutCommandeModifie::class, fn ($notification) => $notification->commande->statut === 'confirmee');
    }

    public function test_l_accuse_de_reception_contient_le_recapitulatif(): void
    {
        $client = User::factory()->create(['prenom' => 'Paul']);
        $produit = $this->creerProduit();
        $commande = Commande::create([
            'id_ut' => $client->id, 'date_c' => now(), 'statut' => 'en_attente', 'montant_c' => 465000,
            'adresse_livraison' => 'Akanda', 'methode_paiement' => 'moov_money', 'conditions_acceptees_at' => now(),
        ]);
        LigneCommande::create(['id_c' => $commande->id, 'id_p' => $produit->id, 'quantite_com' => 1, 'prix_unitaire_com' => 465000]);

        $rendu = (string) (new CommandeEnregistree($commande))->toMail($client)->render();

        $this->assertStringContainsString('HP Probook 440 G8', $rendu);
        $this->assertStringContainsString('465 000 FCFA', $rendu);
        $this->assertStringContainsString('Moov Money', $rendu);
        $this->assertStringContainsString(route('orders.show', $commande->id), $rendu);
    }
}
