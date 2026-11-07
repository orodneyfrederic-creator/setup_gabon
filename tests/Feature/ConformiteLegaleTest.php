<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commande;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ConformiteLegaleTest extends TestCase
{
    use RefreshDatabase;

    private function creerProduit(string $categorie, int $garantieJours): Produit
    {
        return Produit::create([
            'designation' => 'HP Probook 440 G8',
            'prix_unitaire' => 465000,
            'quantite' => 5,
            'reference' => 'LAP-HP-001',
            'id_cat' => Category::create(['nom' => $categorie, 'garantie_jours' => $garantieJours])->id,
            'id_m' => Marque::create(['nom' => 'HP'])->id,
        ]);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function pagesLegales(): array
    {
        return [
            'CGV' => ['legal.cgv', '14 jours calendaires'],
            'retours' => ['legal.retours', 'Garantie boutique : ordinateurs portables et fixes'],
            'confidentialité' => ['legal.confidentialite', 'APDPVP'],
            'mentions légales' => ['legal.mentions', 'Petit Paris, galerie Mouenga'],
        ];
    }

    #[DataProvider('pagesLegales')]
    public function test_chaque_page_legale_est_publique_et_affiche_son_contenu(string $route, string $texteAttendu): void
    {
        $this->get(route($route))
            ->assertOk()
            ->assertSee($texteAttendu);
    }

    public function test_les_mentions_legales_n_affichent_un_identifiant_que_s_il_est_renseigne(): void
    {
        config(['boutique.rccm' => null]);
        $this->get(route('legal.mentions'))->assertDontSee('RCCM :');

        config(['boutique.rccm' => 'GA-LBV-01-2026-B12-00001']);
        $this->get(route('legal.mentions'))->assertSee('GA-LBV-01-2026-B12-00001');
    }

    public function test_une_commande_sans_acceptation_des_cgv_est_refusee(): void
    {
        $client = User::factory()->create();
        $produit = $this->creerProduit('Ordinateurs portables', 30);

        $this->actingAs($client, 'web')
            ->post(route('checkout.store'), [
                'items' => json_encode([['id' => $produit->id, 'quantite' => 1]]),
                'methode_paiement' => 'especes',
                'adresse_livraison' => 'Akanda, carrefour GP',
            ])
            ->assertSessionHasErrors(['accepte_conditions' => 'Veuillez lire et accepter les conditions générales de vente pour commander.']);

        $this->assertDatabaseCount('commandes', 0);
    }

    public function test_une_commande_acceptee_conserve_les_preuves_et_affiche_l_accuse_de_reception(): void
    {
        $client = User::factory()->create();
        $produit = $this->creerProduit('Ordinateurs portables', 30);

        $reponse = $this->actingAs($client, 'web')
            ->post(route('checkout.store'), [
                'items' => json_encode([['id' => $produit->id, 'quantite' => 1]]),
                'methode_paiement' => 'airtel_money',
                'adresse_livraison' => 'Akanda, carrefour GP',
                'accepte_conditions' => '1',
            ]);

        $commande = Commande::firstOrFail();
        $reponse->assertRedirect(route('orders.show', $commande->id))
            ->assertSessionHas('whatsapp_url', fn (string $url) => str_starts_with($url, 'https://wa.me/24166131270?text='));

        $this->assertSame('Akanda, carrefour GP', $commande->adresse_livraison);
        $this->assertSame('airtel_money', $commande->methode_paiement);
        $this->assertNotNull($commande->conditions_acceptees_at);
        $this->assertSame(config('boutique.version_conditions'), $commande->version_conditions);

        $this->actingAs($client, 'web')
            ->withSession(['commande_confirmee' => true, 'whatsapp_url' => 'https://wa.me/24166131270'])
            ->get(route('orders.show', $commande->id))
            ->assertOk()
            ->assertSee('Commande n° '.$commande->id.' enregistrée')
            ->assertSee('Envoyer ma commande sur WhatsApp')
            ->assertSee('Airtel Money');
    }

    public function test_la_fiche_produit_affiche_la_garantie_de_sa_categorie(): void
    {
        $ordinateur = $this->creerProduit('Ordinateurs portables', 30);

        $this->get(route('produits.show', $ordinateur->id))
            ->assertOk()
            ->assertSee('Garantie boutique 1 mois');

        $accessoire = Produit::create([
            'designation' => 'Câble HDMI 2 m',
            'prix_unitaire' => 5000,
            'quantite' => 10,
            'reference' => 'CAB-001',
            'id_cat' => Category::create(['nom' => 'Câbles', 'garantie_jours' => 14])->id,
            'id_m' => Marque::first()->id,
        ]);

        $this->get(route('produits.show', $accessoire->id))
            ->assertSee('Garantie boutique 2 semaines');
    }

    public function test_le_tableau_de_bord_signale_les_informations_legales_manquantes(): void
    {
        config(['boutique.rccm' => null]);
        $gerante = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);

        $this->actingAs($gerante, 'staff')
            ->get(route('gerante.dashboard'))
            ->assertSee('Informations légales à compléter avant la mise en ligne')
            ->assertSee('Numéro RCCM (BOUTIQUE_RCCM)');
    }
}
