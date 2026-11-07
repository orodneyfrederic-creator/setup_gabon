<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\User;
use App\Services\NegociationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NegociationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NegociationService $service;

    protected Marque $marque;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NegociationService;
        $this->marque = Marque::create(['nom' => 'Marque Test']);
    }

    /**
     * Teste l'arithmétique entière stricte et la précision des arrondis (section 3 du cahier des charges).
     */
    public function test_calcul_prix_plancher_avec_formule_entiere_et_exemples_de_reference(): void
    {
        // Exemple 1 du cahier des charges : P = 450 000, remise 2% -> S = 441 000
        $P1 = 450000;
        $S1 = $this->service->calculerPrixPlancher($P1, 2);
        $this->assertSame(441000, $S1);

        // Exemple 2 du cahier des charges : P = 123 457, remise 2% -> S = 120 988 (120 987,86 arrondi au supérieur)
        $P2 = 123457;
        $S2 = $this->service->calculerPrixPlancher($P2, 2);
        $this->assertSame(120988, $S2);

        // Remise 5% sur 100 000 -> 95 000
        $this->assertSame(95000, $this->service->calculerPrixPlancher(100000, 5));
    }

    /**
     * Teste que l'offre du client supérieure ou égale au plancher est acceptée immédiatement.
     */
    public function test_offre_superieure_ou_egale_au_plancher_est_acceptee_immediatement(): void
    {
        $cat = Category::create(['nom' => 'PC Portables', 'negociable' => true, 'remise_max_pct' => 2]);
        $produit = Produit::create([
            'designation' => 'Dell Vostro 3530',
            'prix_unitaire' => 450000,
            'quantite' => 5,
            'reference' => 'DELL-3530',
            'id_cat' => $cat->id,
            'id_m' => $this->marque->id,
        ]);
        $user = User::factory()->create();

        $session = $this->service->demarrerOuReprendre($user, $produit);
        $this->assertTrue($session['success']);
        $negociation = $session['negociation'];

        // Offre à 445 000 (au-dessus du plancher 441 000)
        $resultat = $this->service->soumettreOffre($negociation, 445000);

        $this->assertTrue($resultat['success']);
        $this->assertSame('acceptee', $resultat['statut']);
        $this->assertSame(445000, $resultat['prix_final']);
        $this->assertSame(445000, $negociation->fresh()->prix_final_convenu);
    }

    /**
     * Teste qu'une offre inférieure au plancher génère une contre-offre et que le dernier tour propose le plancher.
     */
    public function test_offre_sous_le_plancher_provoque_contre_offres_puis_dernier_prix(): void
    {
        $cat = Category::create(['nom' => 'Ordinateurs', 'negociable' => true, 'remise_max_pct' => 2]);
        $produit = Produit::create([
            'designation' => 'Tour Gamer',
            'prix_unitaire' => 450000,
            'quantite' => 3,
            'reference' => 'TOUR-01',
            'id_cat' => $cat->id,
            'id_m' => $this->marque->id,
            'nb_tours_max' => 3,
        ]);
        $user = User::factory()->create();

        $session = $this->service->demarrerOuReprendre($user, $produit);
        $negociation = $session['negociation'];

        // Tour 1 : offre trop basse (300 000) -> contre-offre tour 1 = ceil((P + S) / 2) = 445 500
        $r1 = $this->service->soumettreOffre($negociation, 300000);
        $this->assertSame('contre_offre', $r1['statut']);
        $this->assertSame(445500, $r1['contre_offre']);
        $this->assertSame(2, $r1['tours_restants']);

        // Tour 2 : offre encore trop basse (350 000) -> contre-offre tour 2 = 444 000
        $r2 = $this->service->soumettreOffre($negociation, 350000);
        $this->assertSame('contre_offre', $r2['statut']);
        $this->assertLessThan(445500, $r2['contre_offre']);
        $this->assertGreaterThan(441000, $r2['contre_offre']);
        $this->assertSame(1, $r2['tours_restants']);

        // Tour 3 (dernier tour) : offre toujours sous le plancher (400 000) -> dernière offre = S = 441 000
        $r3 = $this->service->soumettreOffre($negociation, 400000);
        $this->assertSame('derniere_offre', $r3['statut']);
        $this->assertSame(441000, $r3['contre_offre']); // Le plancher S
        $this->assertSame(0, $r3['tours_restants']);

        // Le client accepte la dernière offre ferme
        $rFinal = $this->service->accepterContreOffre($negociation);
        $this->assertTrue($rFinal['success']);
        $this->assertSame('acceptee', $rFinal['statut']);
        $this->assertSame(441000, $rFinal['prix_final']);
    }

    /**
     * Teste le garde-fou du prix d'achat : si S < prix_achat, la négociation est bloquée.
     */
    public function test_garde_fou_prix_achat_bloque_la_negociation(): void
    {
        $cat = Category::create(['nom' => 'Imprimantes', 'negociable' => true, 'remise_max_pct' => 5]);
        $produit = Produit::create([
            'designation' => 'Imprimante HP LaserJet',
            'prix_unitaire' => 100000,
            'prix_achat' => 98000, // Prix d'achat = 98 000
            'quantite' => 2,
            'reference' => 'HP-LJ',
            'id_cat' => $cat->id,
            'id_m' => $this->marque->id,
        ]);
        // Avec 5% de remise sur 100 000, S = 95 000 < 98 000 (prix d'achat)
        $user = User::factory()->create();

        $session = $this->service->demarrerOuReprendre($user, $produit);
        $this->assertFalse($session['success']);
        $this->assertStringContainsString('marge plancher critique', $session['message']);
    }

    /**
     * Teste l'anti-sondage : délai de relance de 24h après abandon ou refus.
     */
    public function test_anti_sondage_applique_delai_de_relance(): void
    {
        $cat = Category::create(['nom' => 'Écrans', 'negociable' => true]);
        $produit = Produit::create([
            'designation' => 'Écran 27 pouces',
            'prix_unitaire' => 200000,
            'quantite' => 5,
            'reference' => 'ECRAN-27',
            'id_cat' => $cat->id,
            'id_m' => $this->marque->id,
        ]);
        $user = User::factory()->create();

        $session = $this->service->demarrerOuReprendre($user, $produit);
        $negociation = $session['negociation'];

        // Le client abandonne
        $this->service->abandonner($negociation, 'client');
        $this->assertSame('refusee', $negociation->fresh()->statut);

        // Tentative immédiate de réouverture par le même client sur le même produit
        $tentative = $this->service->demarrerOuReprendre($user, $produit);
        $this->assertFalse($tentative['success']);
        $this->assertStringContainsString('délai de relance est actif', $tentative['message']);
    }

    /**
     * Teste l'usage unique : une négociation acceptée ne peut être consommée qu'une seule fois.
     */
    public function test_usage_unique_d_une_negociation_pour_commande(): void
    {
        $cat = Category::create(['nom' => 'Laptops', 'negociable' => true]);
        $produit = Produit::create([
            'designation' => 'Asus TUF',
            'prix_unitaire' => 500000,
            'quantite' => 5,
            'reference' => 'ASUS-TUF',
            'id_cat' => $cat->id,
            'id_m' => $this->marque->id,
        ]);
        $user = User::factory()->create();

        $session = $this->service->demarrerOuReprendre($user, $produit);
        $negociation = $session['negociation'];
        $this->service->soumettreOffre($negociation, 495000);

        // Première consommation : succès
        $conso1 = $this->service->consommerPourCommande($negociation->id, $user->id, $produit->id, 1);
        $this->assertNotNull($conso1);
        $this->assertSame('utilisee', $negociation->fresh()->statut);

        // Deuxième tentative : rejet (déjà utilisée)
        $conso2 = $this->service->consommerPourCommande($negociation->id, $user->id, $produit->id, 1);
        $this->assertNull($conso2);
    }
}
