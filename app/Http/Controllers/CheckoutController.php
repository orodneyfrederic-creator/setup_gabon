<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Produit;
use App\Notifications\CommandeEnregistree;
use App\Notifications\NouvelleCommandeBoutique;
use App\Services\NegociationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Numéro de téléphone : chiffres, espaces, points ou tirets, avec indicatif « + » facultatif.
     */
    public const FORMAT_TELEPHONE = '/^\+?[0-9][0-9 .\-]{6,18}[0-9]$/';

    public function __construct(
        protected NegociationService $negociationService
    ) {}

    /**
     * Page de passer la commande (Checkout).
     */
    public function index()
    {
        $user = Auth::guard('web')->user();

        return view('checkout.index', compact('user'));
    }

    /**
     * Traiter et enregistrer la commande, afficher l'accusé de réception puis proposer l'envoi sur WhatsApp.
     * SÉCURITÉ : Le prix catalogue et le prix négocié sont strictement vérifiés côté serveur.
     * Une offre négociée est consommée avec un verrou transactionnel pessimiste (lockForUpdate).
     */
    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();
        $telephoneRequis = ! $user?->tel;

        $request->validate([
            'items' => ['required', 'string'],
            'methode_paiement' => ['required', 'string', 'in:especes,airtel_money,moov_money'],
            'adresse_livraison' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
            'accepte_conditions' => ['accepted'],
            'client_nom' => [$user ? 'nullable' : 'required', 'string', 'max:120'],
            'client_telephone' => [$telephoneRequis ? 'required' : 'nullable', 'string', 'regex:'.self::FORMAT_TELEPHONE],
            'client_email' => ['nullable', 'email', 'max:255'],
        ], [
            'items.required' => 'Votre panier est vide.',
            'methode_paiement.required' => 'Veuillez sélectionner un mode de paiement.',
            'adresse_livraison.required' => 'Veuillez préciser votre lieu ou adresse de livraison à Libreville.',
            'accepte_conditions.accepted' => 'Veuillez lire et accepter les conditions générales de vente pour commander.',
            'client_nom.required' => 'Indiquez votre nom pour que la boutique sache à qui livrer.',
            'client_telephone.required' => 'Indiquez un numéro de téléphone : la boutique vous appelle pour confirmer la livraison.',
            'client_telephone.regex' => 'Ce numéro de téléphone ne semble pas valide (ex. : 066 12 34 56).',
            'client_email.email' => 'Cette adresse e-mail ne semble pas valide.',
        ]);

        $cartItems = json_decode($request->items, true);

        if (empty($cartItems) || ! is_array($cartItems)) {
            return back()->withInput()->with('error', 'Votre panier ne contient aucun article.');
        }

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

                // Si le client revendique une offre négociée sur cet article (réservé aux comptes clients)
                if (! empty($item['negociation_id'])) {
                    $negociationConso = $user ? $this->negociationService->consommerPourCommande(
                        (int) $item['negociation_id'],
                        $user->id,
                        $produit->id,
                        $qte
                    ) : null;

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

                return back()->withInput()
                    ->with('error', 'L\'offre négociée n\'est plus valable pour : '.implode(', ', $negociationsInvalides).' (offre expirée, déjà utilisée, quantité supérieure à l\'accord ou prix catalogue modifié). L\'article a été retiré de votre panier : ajoutez-le de nouveau au prix catalogue ou relancez une négociation.')
                    ->with('negociations_invalides', array_keys($negociationsInvalides));
            }

            if (empty($lignes)) {
                DB::rollBack();

                return back()->withInput()->with('error', 'Aucun produit valide trouvé dans votre panier.');
            }

            $telephone = trim((string) $request->input('client_telephone')) ?: $user?->tel;

            // Création de la commande en BDD (statut 'en_attente' avant confirmation gérante).
            // Les coordonnées du client sont figées sur la commande, avec ou sans compte.
            $commande = Commande::create([
                'id_ut' => $user?->id,
                'client_nom' => $user ? trim($user->prenom.' '.$user->nom) : trim($request->input('client_nom')),
                'client_telephone' => $telephone,
                'client_email' => $user?->email ?? (trim((string) $request->input('client_email')) ?: null),
                'jeton_suivi' => Str::random(40),
                'date_c' => now(),
                'statut' => 'en_attente',
                'montant_c' => $totalMontant,
                'adresse_livraison' => trim($request->adresse_livraison),
                'methode_paiement' => $request->methode_paiement,
                'conditions_acceptees_at' => now(),
                'version_conditions' => config('boutique.version_conditions'),
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

            // Le numéro saisi complète le profil du client qui n'en avait pas
            if ($user && ! $user->tel && $telephone) {
                $user->forceFill(['tel' => $telephone])->save();
            }

            DB::commit();
        } catch (\Throwable $erreur) {
            DB::rollBack();
            report($erreur);

            return back()->withInput()->with('error', 'La commande n\'a pas pu être enregistrée à cause d\'une erreur technique. Réessayez dans un instant ou contactez la boutique sur WhatsApp.');
        }

        $this->notifierCommande($commande);

        // Accusé de réception immédiat avec récapitulatif (loi n°025/2021, art. 52) avant l'envoi sur WhatsApp
        return redirect()
            ->to($commande->urlSuivi())
            ->with('commande_confirmee', true)
            ->with('whatsapp_url', $this->lienWhatsApp($commande, $lignes));
    }

    /**
     * Message WhatsApp pré-rempli que le client envoie à la boutique.
     *
     * @param  list<array{designation: string, quantite_com: int, prix_final_unitaire: int, negociation_ref: ?string}>  $lignes
     */
    private function lienWhatsApp(Commande $commande, array $lignes): string
    {
        $msg = "Bonjour SETUP GABON, je me nomme {$commande->nomClient()}, et je souhaite valider la commande suivante :\n\n";
        $msg .= " *COMMANDE N°* : #{$commande->id}\n";
        $msg .= ' *TÉLÉPHONE* : '.($commande->telephoneClient() ?: 'Non renseigné')."\n";
        $msg .= " *LIEU DE LIVRAISON* : {$commande->adresse_livraison}\n";
        $msg .= " *MODE DE PAIEMENT* : {$commande->libelleMethodePaiement()}\n\n";
        $msg .= " *ARTICLES COMMANDÉS* :\n";

        foreach ($lignes as $ligne) {
            $sousTotal = number_format($ligne['prix_final_unitaire'] * $ligne['quantite_com'], 0, ',', ' ');
            $mentionNegociation = $ligne['negociation_ref'] ? " *(Offre négociée validée #{$ligne['negociation_ref']})*" : '';
            $msg .= "• {$ligne['designation']} x{$ligne['quantite_com']} : {$sousTotal} FCFA{$mentionNegociation}\n";
        }

        $msg .= "\n *TOTAL À PAYER* : ".number_format($commande->montant_c, 0, ',', ' ')." FCFA\n\n";
        $msg .= "Suivi de la commande : {$commande->urlSuivi()}";

        return 'https://wa.me/'.config('boutique.whatsapp').'?text='.urlencode($msg);
    }

    /**
     * Envoie l'accusé de réception au client et l'alerte à la boutique.
     * Un échec d'envoi est journalisé sans bloquer la commande, déjà enregistrée.
     */
    private function notifierCommande(Commande $commande): void
    {
        try {
            $commande->notifierClient(new CommandeEnregistree($commande));

            if (config('boutique.email')) {
                Notification::route('mail', config('boutique.email'))->notify(new NouvelleCommandeBoutique($commande));
            }
        } catch (\Throwable $erreur) {
            report($erreur);
        }
    }
}
