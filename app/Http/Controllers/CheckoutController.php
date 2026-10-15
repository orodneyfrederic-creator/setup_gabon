<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
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

            foreach ($cartItems as $item) {
                $produit = Produit::find($item['id']);
                if ($produit) {
                    $qte = max(1, (int) $item['quantite']);
                    $prixUnit = $produit->prix;
                    $sousTotal = $prixUnit * $qte;
                    $totalMontant += $sousTotal;

                    $lignes[] = [
                        'id_p' => $produit->id,
                        'designation' => $produit->designation,
                        'quantite_com' => $qte,
                        'prix_unitaire_com' => $prixUnit,
                    ];
                }
            }

            if (empty($lignes)) {
                DB::rollBack();

                return back()->with('error', 'Aucun produit valide trouvé dans votre panier.');
            }

            // Création de la commande en BDD
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
                ]);
            }

            DB::commit();

            // Formatage du message WhatsApp
            $labelsPaiement = [
                'especes' => 'Espèces (À la livraison)',
                'airtel_money' => 'Airtel Money',
                'moov_money' => 'Moov Money',
            ];
            $modePaiementText = $labelsPaiement[$request->methode_paiement] ?? $request->methode_paiement;
            $adresseLivraison = trim($request->adresse_livraison);

            $userPhone = $user->tel ?: 'Non renseigné';
            $userName = trim($user->prenom.' '.$user->nom);

            $msg = "Bonjour SETUP GABON,je me nomme {$userName}, et je souhaite valider la commande suivante :\n\n";
            $msg .= " *COMMANDE N°* : #{$commande->id}\n";
            // $msg .= " *CLIENT* : {$userName}\n";
            $msg .= " *TÉLÉPHONE* : {$userPhone}\n";
            $msg .= " *LIEU DE LIVRAISON* : {$adresseLivraison}\n";
            $msg .= " *MODE DE PAIEMENT* : {$modePaiementText}\n\n";
            $msg .= " *ARTICLES COMMANDÉS* :\n";

            foreach ($lignes as $l) {
                $st = number_format($l['prix_unitaire_com'] * $l['quantite_com'], 0, ',', ' ');
                $msg .= "• {$l['designation']} x{$l['quantite_com']} ({$st} FCFA)\n";
            }

            $totalStr = number_format($totalMontant, 0, ',', ' ');
            $msg .= "\n *TOTAL À PAYER* : {$totalStr} FCFA";

            $whatsappUrl = 'https://wa.me/24166131270?text='.urlencode($msg);

            return redirect()->away($whatsappUrl);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors de la validation de la commande : '.$e->getMessage());
        }
    }
}
