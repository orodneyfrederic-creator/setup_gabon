<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Negociation;
use App\Models\Staff;
use App\Services\NegociationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NegociationGeranteController extends Controller
{
    public function __construct(
        protected NegociationService $negociationService
    ) {}

    /**
     * Liste et tableau de bord des négociations.
     */
    public function index(Request $request)
    {
        $statut = $request->input('statut');

        $query = Negociation::with(['produit.category', 'user'])
            ->latest('id');

        if ($statut) {
            $query->where('statut', $statut);
        }

        $negociations = $query->paginate(15)->withQueryString();

        // Statistiques globales
        $nbEnAttente = Negociation::where('statut', 'en_attente_gerante')->count();
        $nbAcceptees = Negociation::where('statut', 'acceptee')->orWhere('statut', 'utilisee')->count();
        $nbTotal = Negociation::count();
        $tauxAcceptation = $nbTotal > 0 ? round(($nbAcceptees / $nbTotal) * 100) : 0;

        // Économie moyenne accordée aux clients (FCFA)
        $remiseMoyenne = (int) Negociation::whereIn('statut', ['acceptee', 'utilisee'])
            ->selectRaw('AVG(prix_catalogue - prix_final_convenu) as remise_moy')
            ->value('remise_moy') ?: 0;

        return view('gerante.negociations.index', compact(
            'negociations',
            'statut',
            'nbEnAttente',
            'nbAcceptees',
            'tauxAcceptation',
            'remiseMoyenne'
        ));
    }

    /**
     * Détail d'une négociation avec historique des offres.
     */
    public function show(Negociation $negociation)
    {
        $negociation->load(['produit.category', 'user', 'offres', 'staffDecision']);

        // Calcul de la marge estimée si prix d'achat disponible
        $margeEstimee = null;
        if ($negociation->produit->prix_achat && $negociation->prix_final_convenu) {
            $margeEstimee = $negociation->prix_final_convenu - $negociation->produit->prix_achat;
        }

        return view('gerante.negociations.show', compact('negociation', 'margeEstimee'));
    }

    /**
     * Traitement de la décision manuelle par la gérante.
     */
    public function decider(Request $request, Negociation $negociation)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:accepter,contre_offre,refuser'],
            'montant' => ['nullable', 'integer', 'min:1'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $staff = Auth::guard('staff')->user();

        if (! $staff instanceof Staff) {
            abort(403, 'Seul un compte du personnel peut décider d\'une négociation.');
        }

        $resultat = $this->negociationService->repondreParGerante(
            $negociation,
            $staff,
            $validated['action'],
            isset($validated['montant']) ? (int) $validated['montant'] : null,
            $validated['message'] ?? null
        );

        if (! $resultat['success']) {
            return back()->with('error', $resultat['message']);
        }

        return back()->with('success', $resultat['message']);
    }

    /**
     * Configuration globale et par catégorie des règles de négociation.
     */
    public function regles()
    {
        $categories = Category::all();

        return view('gerante.negociations.regles', compact('categories'));
    }

    public function updateRegles(Request $request)
    {
        $validated = $request->validate([
            'categories' => ['required', 'array'],
            'categories.*.id' => ['required', 'exists:categories,id'],
            'categories.*.negociable' => ['nullable', 'boolean'],
            'categories.*.remise_max_pct' => ['required', 'integer', 'min:0', 'max:50'],
        ]);

        foreach ($validated['categories'] as $catData) {
            Category::where('id', $catData['id'])->update([
                'negociable' => ! empty($catData['negociable']),
                'remise_max_pct' => (int) $catData['remise_max_pct'],
            ]);
        }

        return back()->with('success', 'Règles de négociation par catégorie mises à jour avec succès.');
    }
}
