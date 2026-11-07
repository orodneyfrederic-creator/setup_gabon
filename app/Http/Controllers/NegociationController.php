<?php

namespace App\Http\Controllers;

use App\Models\Negociation;
use App\Models\Produit;
use App\Services\NegociationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NegociationController extends Controller
{
    public function __construct(
        protected NegociationService $negociationService
    ) {}

    /**
     * Ouvre ou reprend une session de négociation pour un produit.
     */
    public function initier(Request $request, Produit $produit): JsonResponse
    {
        if (Auth::guard('staff')->check() && ! Auth::guard('web')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Les comptes du personnel ne peuvent pas négocier de prix.',
            ], 403);
        }

        $user = Auth::guard('web')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez vous connecter pour négocier le prix de cet équipement.',
                'require_auth' => true,
            ], 401);
        }

        $quantite = max(1, (int) $request->input('quantite', 1));

        $resultat = $this->negociationService->demarrerOuReprendre($user, $produit, $quantite);

        if (! $resultat['success']) {
            return response()->json($resultat, 422);
        }

        $negociation = $resultat['negociation'];

        return response()->json([
            'success' => true,
            'message' => $resultat['message'],
            'negociation' => [
                'id' => $negociation->id,
                'reference' => $negociation->reference,
                'prix_catalogue' => $negociation->prix_catalogue,
                'prix_final_convenu' => $negociation->prix_final_convenu,
                'statut' => $negociation->statut,
                'tours_utilises' => $negociation->tours_utilises,
                'tours_max' => $negociation->tours_max,
                'expire_a' => $negociation->expire_a ? $negociation->expire_a->toIso8601String() : null,
                'offres' => $negociation->offres->map(fn ($o) => [
                    'id' => $o->id,
                    'auteur' => $o->auteur,
                    'tour_numero' => $o->tour_numero,
                    'montant_propose' => $o->montant_propose,
                    'type_action' => $o->type_action,
                    'message' => $o->message,
                    'heure' => $o->created_at->format('H:i'),
                ]),
            ],
        ]);
    }

    /**
     * Soumet une offre de prix par le client.
     */
    public function proposer(Request $request, Negociation $negociation): JsonResponse
    {
        if (! $this->appartientAuClient($negociation)) {
            return response()->json([
                'success' => false,
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $validated = $request->validate([
            'montant' => ['required', 'integer', 'min:1'],
        ], [
            'montant.required' => 'Veuillez saisir votre offre.',
            'montant.integer' => 'Le montant doit être un nombre entier en FCFA.',
            'montant.min' => 'Le montant proposé doit être supérieur à zéro.',
        ]);

        $offreClient = (int) $validated['montant'];

        $resultat = $this->negociationService->soumettreOffre($negociation, $offreClient);

        if (! $resultat['success']) {
            return response()->json($resultat, 422);
        }

        $negociationFresh = $resultat['negociation'];

        return response()->json([
            'success' => true,
            'statut' => $resultat['statut'],
            'prix_final' => $resultat['prix_final'] ?? null,
            'contre_offre' => $resultat['contre_offre'] ?? null,
            'tours_restants' => $resultat['tours_restants'] ?? 0,
            'message' => $resultat['message'],
            'negociation' => [
                'id' => $negociationFresh->id,
                'statut' => $negociationFresh->statut,
                'prix_final_convenu' => $negociationFresh->prix_final_convenu,
                'tours_utilises' => $negociationFresh->tours_utilises,
                'tours_max' => $negociationFresh->tours_max,
                'expire_a' => $negociationFresh->expire_a ? $negociationFresh->expire_a->toIso8601String() : null,
                'offres' => $negociationFresh->offres->map(fn ($o) => [
                    'id' => $o->id,
                    'auteur' => $o->auteur,
                    'tour_numero' => $o->tour_numero,
                    'montant_propose' => $o->montant_propose,
                    'type_action' => $o->type_action,
                    'message' => $o->message,
                    'heure' => $o->created_at->format('H:i'),
                ]),
            ],
        ]);
    }

    /**
     * Le client accepte la contre-offre ou la dernière offre formulée par le système.
     */
    public function accepterContreOffre(Negociation $negociation): JsonResponse
    {
        if (! $this->appartientAuClient($negociation)) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
        }

        $resultat = $this->negociationService->accepterContreOffre($negociation);

        if (! $resultat['success']) {
            return response()->json($resultat, 422);
        }

        $negociationFresh = $resultat['negociation'];

        return response()->json([
            'success' => true,
            'statut' => 'acceptee',
            'prix_final' => $resultat['prix_final'],
            'message' => $resultat['message'],
            'negociation' => [
                'id' => $negociationFresh->id,
                'statut' => $negociationFresh->statut,
                'prix_final_convenu' => $negociationFresh->prix_final_convenu,
                'expire_a' => $negociationFresh->expire_a ? $negociationFresh->expire_a->toIso8601String() : null,
                'offres' => $negociationFresh->offres->map(fn ($o) => [
                    'id' => $o->id,
                    'auteur' => $o->auteur,
                    'tour_numero' => $o->tour_numero,
                    'montant_propose' => $o->montant_propose,
                    'type_action' => $o->type_action,
                    'message' => $o->message,
                    'heure' => $o->created_at->format('H:i'),
                ]),
            ],
        ]);
    }

    /**
     * Le client abandonne la négociation en cours.
     */
    public function abandonner(Negociation $negociation): JsonResponse
    {
        if (! $this->appartientAuClient($negociation)) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
        }

        $resultat = $this->negociationService->abandonner($negociation, 'client');

        return response()->json($resultat, $resultat['success'] ? 200 : 422);
    }

    /**
     * Vérifie que la négociation appartient au client connecté via le guard 'web'.
     * Les comptes staff sont exclus : leurs identifiants (id_staff) ne correspondent pas à users.id.
     */
    private function appartientAuClient(Negociation $negociation): bool
    {
        $client = Auth::guard('web')->user();

        return $client !== null && (int) $negociation->id_ut === $client->id;
    }
}
