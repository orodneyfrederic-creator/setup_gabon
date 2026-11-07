<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Produit;
use App\Services\NegociationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        protected NegociationService $negociationService
    ) {}

    /**
     * Page de passer la commande (Checkout).
     */
    public function index()
    {
        $user = Auth::user();

        return view('checkout.index', compact('user'));
    }

    /**
     * Traiter et enregistrer la commande, puis rediriger directement vers WhatsApp.
     * SÉCURITÉ : Le prix catalogue et le prix négocié sont strictement vérifiés côté serveur.
     * Une offre négociée est consommée avec un verrou transactionnel pessimiste (lockForUpdate).
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => ['required', 'string'],
            'methode_paiement' => ['required', 'string', 'in:especes,airtel_money,moov_money'],
            'adresse_livraison' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'items.required' => 'Votre panier est vide.',
            'methode_paiement.required' => 'Veuillez sélectionner un mode de paiement.',
            'adresse_livraison.required' => 'Veuillez préciser votre lieu ou adresse de livraison à Libreville.',
        ]);

        $cartItems = json_decode($request->items, true);

        if (empty($cartItems) || ! is_array($cartItems)) {
            return back()->with('error', 'Votre panier ne contient aucun article.');
        }

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $totalMontant = 0;
            $lignes = [];
            $negociationsInvalides = [];

            foreach ($cartItems as $item) {
                $produit = Produit::find($item['id']);
                if (! $produit) {
                    continue;
                }

                $qte = max(1, (int) ($item['quantite'] ?? 1));
                $prixCatalogue = (int) $produit->prix_unitaire;
                $prixFinal = $prixCatalogue;
                $negociationConso = null;

                // Si le client revendique une offre négociée sur cet article
                if (! empty($item['negociation_id'])) {
                    $negociationConso = $this->negociationService->consommerPourCommande(
                        (int) $item['negociation_id'],
                        $user->id,
                        $produit->id,
                        $qte
                    );

                    if (! $negociationConso) {
                        $negociationsInvalides[(int) $item['negociation_id']] = $produit->designation;

                        continue;
                    }

                    $prixFinal = (int) $negociationConso->prix_final_convenu;
                }

                $sousTotal = $prixFinal * $qte;
                $totalMontant += $sousTotal;

                $lignes[] = [
                    'id_p' => $produit->id,
                    'designation' => $produit->designation,
                    'quantite_com' => $qte,
                    'prix_unitaire_com' => $prixFinal,
                    'prix_catalogue_unitaire' => $prixCatalogue,
                    'prix_final_unitaire' => $prixFinal,
                    'negociation_id' => $negociationConso?->id,
                    'negociation_ref' => $negociationConso?->reference,
                ];
            }

            // Une offre revendiquée mais non applicable bloque la commande : le client doit
            // savoir que le prix négocié ne s'applique plus avant de payer le prix catalogue.
            if (! empty($negociationsInvalides)) {
                DB::rollBack();

                return back()
                    ->with('error', 'L\'offre négociée n\'est plus valable pour : '.implode(', ', $negociationsInvalides).' (offre expirée, déjà utilisée, quantité supérieure à l\'accord ou prix catalogue modifié). L\'article a été retiré de votre panier : ajoutez-le de nouveau au prix catalogue ou relancez une négociation.')
                    ->with('negociations_invalides', array_keys($negociationsInvalides));
            }

            if (empty($lignes)) {
                DB::rollBack();

                return back()->with('error', 'Aucun produit valide trouvé dans votre panier.');
            }

            // Création de la commande en BDD (statut 'en_attente' avant confirmation gérante)
            $commande = Commande::create([
                'id_ut' => $user->id,
                'date_c' => now(),
                'statut' => 'en_attente',
                'montant_c' => $totalMontant,
            ]);

            foreach ($lignes as $ligne) {
                LigneCommande::create([
                    'id_c' => $commande->id,
                    'id_p' => $ligne['id_p'],
                    'quantite_com' => $ligne['quantite_com'],
                    'prix_unitaire_com' => $ligne['prix_unitaire_com'],
                    'prix_catalogue_unitaire' => $ligne['prix_catalogue_unitaire'],
                    'prix_final_unitaire' => $ligne['prix_final_unitaire'],
                    'negociation_id' => $ligne['negociation_id'],
                ]);
            }

            DB::commit();

            // Formatage du message WhatsApp officiel
            $labelsPaiement = [
                'especes' => 'Espèces (À la livraison)',
                'airtel_money' => 'Airtel Money',
                'moov_money' => 'Moov Money',
            ];
            $modePaiementText = $labelsPaiement[$request->methode_paiement] ?? $request->methode_paiement;
            $adresseLivraison = trim($request->adresse_livraison);

            $userPhone = $user->tel ?: 'Non renseigné';
            $userName = trim($user->prenom.' '.$user->nom);

            $msg = "Bonjour SETUP GABON, je me nomme {$userName}, et je souhaite valider la commande suivante :\n\n";
            $msg .= " *COMMANDE N°* : #{$commande->id}\n";
            $msg .= " *TÉLÉPHONE* : {$userPhone}\n";
            $msg .= " *LIEU DE LIVRAISON* : {$adresseLivraison}\n";
            $msg .= " *MODE DE PAIEMENT* : {$modePaiementText}\n\n";
            $msg .= " *ARTICLES COMMANDÉS* :\n";

            foreach ($lignes as $l) {
                $st = number_format($l['prix_final_unitaire'] * $l['quantite_com'], 0, ',', ' ');
                $mentionNego = '';
                if ($l['negociation_ref']) {
                    $mentionNego = " *(Offre négociée validée #{$l['negociation_ref']})*";
                }
                $msg .= "• {$l['designation']} x{$l['quantite_com']} : {$st} FCFA{$mentionNego}\n";
            }

            $totalStr = number_format($totalMontant, 0, ',', ' ');
            $msg .= "\n *TOTAL À PAYER* : {$totalStr} FCFA\n\n";
            $msg .= "_La commande est enregistrée dans votre système sous le statut 'En attente de confirmation'._";

            $whatsappUrl = 'https://wa.me/24166131270?text='.urlencode($msg);

            return redirect()->away($whatsappUrl);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors de la validation de la commande : '.$e->getMessage());
        }
    }
}
