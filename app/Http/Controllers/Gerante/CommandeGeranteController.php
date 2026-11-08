<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\MouvementStock;
use App\Notifications\StatutCommandeModifie;
use App\Services\NegociationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommandeGeranteController extends Controller
{
    /**
     * Transitions de statut autorisées. Garantit la cohérence du stock :
     * il n'est décrémenté qu'en quittant 'en_attente' et restauré qu'en annulant.
     * 'annulee' est un statut final.
     *
     * @var array<string, list<string>>
     */
    private const TRANSITIONS_AUTORISEES = [
        'en_attente' => ['confirmee', 'livree', 'annulee'],
        'confirmee' => ['livree', 'annulee'],
        'livree' => ['annulee'],
        'annulee' => [],
    ];

    public function __construct(
        protected NegociationService $negociationService
    ) {}

    /**
     * Liste des commandes en ligne avec filtrage par statut.
     */
    public function index(Request $request)
    {
        $statut = $request->input('statut');

        $query = Commande::with(['user', 'paiement', 'lignesCommandes.produit', 'lignesCommandes.negociation'])
            ->latest('id');

        if ($statut) {
            $query->where('statut', $statut);
        }

        $commandes = $query->paginate(15)->withQueryString();

        $nbEnAttente = Commande::where('statut', 'en_attente')->count();
        $nbConfirmees = Commande::where('statut', 'confirmee')->count();
        $nbLivrees = Commande::where('statut', 'livree')->count();

        return view('gerante.commandes.index', compact(
            'commandes',
            'statut',
            'nbEnAttente',
            'nbConfirmees',
            'nbLivrees'
        ));
    }

    /**
     * Détail d'une commande.
     */
    public function show(Commande $commande)
    {
        $commande->load(['user', 'paiement', 'lignesCommandes.produit', 'lignesCommandes.negociation']);

        $statutsPossibles = [$commande->statut, ...(self::TRANSITIONS_AUTORISEES[$commande->statut] ?? [])];

        return view('gerante.commandes.show', compact('commande', 'statutsPossibles'));
    }

    /**
     * Mise à jour du statut d'une commande par la gérante.
     * Si la commande passe à 'confirmee' ou 'livree' :
     * -> Décrémente le stock et enregistre le MouvementStock.
     * Si la commande passe à 'annulee' :
     * -> Libère les éventuelles négociations et restaure le stock si elle était déjà confirmée.
     */
    public function updateStatut(Request $request, Commande $commande)
    {
        $validated = $request->validate([
            'statut' => ['required', 'in:en_attente,confirmee,livree,annulee'],
            'motif_annulation' => ['nullable', 'string', 'max:255'],
        ]);

        $ancienStatut = $commande->statut;
        $nouveauStatut = $validated['statut'];

        if ($ancienStatut === $nouveauStatut) {
            return back()->with('info', 'Le statut est identique.');
        }

        if (! in_array($nouveauStatut, self::TRANSITIONS_AUTORISEES[$ancienStatut] ?? [], true)) {
            return back()->with('error', "Transition de statut non autorisée : {$ancienStatut} → {$nouveauStatut}.");
        }

        $staff = Auth::guard('staff')->user() ?? Auth::user();

        DB::beginTransaction();
        try {
            // PASSAGE À CONFIRMÉE / VALIDÉE : DÉCRÉMENTATION DU STOCK
            if (in_array($nouveauStatut, ['confirmee', 'livree'], true) && $ancienStatut === 'en_attente') {
                foreach ($commande->lignesCommandes as $ligne) {
                    $produit = $ligne->produit;
                    if ($produit) {
                        if ($produit->quantite < $ligne->quantite_com) {
                            DB::rollBack();

                            return back()->with('error', "Stock insuffisant pour l'article {$produit->designation} (Disponible : {$produit->quantite}, Demandé : {$ligne->quantite_com}).");
                        }

                        $produit->decrement('quantite', $ligne->quantite_com);

                        MouvementStock::create([
                            'id_p' => $produit->id,
                            'type' => 'sortie',
                            'quantite' => $ligne->quantite_com,
                            'motif' => "Vente en ligne commande #{$commande->id}",
                            'id_ut' => $staff?->id_staff ?? $commande->id_ut,
                        ]);
                    }
                }
            }

            // ANNULATION D'UNE COMMANDE : RESTAURATION DU STOCK ET LIBÉRATION DE L'OFFRE
            if ($nouveauStatut === 'annulee') {
                // Si la commande avait déjà décrémenté le stock
                if (in_array($ancienStatut, ['confirmee', 'livree'], true)) {
                    foreach ($commande->lignesCommandes as $ligne) {
                        $produit = $ligne->produit;
                        if ($produit) {
                            $produit->increment('quantite', $ligne->quantite_com);

                            MouvementStock::create([
                                'id_p' => $produit->id,
                                'type' => 'entree',
                                'quantite' => $ligne->quantite_com,
                                'motif' => "Restauration annulation commande #{$commande->id}",
                                'id_ut' => $staff?->id_staff ?? $commande->id_ut,
                            ]);
                        }
                    }
                }

                // Libérer la négociation
                $this->negociationService->libererNegociation($commande);
            }

            $commande->update(['statut' => $nouveauStatut]);

            DB::commit();

            if (in_array($nouveauStatut, StatutCommandeModifie::STATUTS_NOTIFIES, true)) {
                try {
                    $commande->notifierClient(new StatutCommandeModifie($commande->fresh()));
                } catch (\Throwable $erreur) {
                    report($erreur);
                }
            }

            return back()->with('success', "Statut de la commande mis à jour : {$nouveauStatut}.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors de la mise à jour : '.$e->getMessage());
        }
    }
}
