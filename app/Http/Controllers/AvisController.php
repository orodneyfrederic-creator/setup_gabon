<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Produit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Dépôt d'un avis par un client qui a reçu le produit. Un client a un seul avis par produit,
 * qu'il peut modifier ; un avis masqué par la boutique ne peut pas être republié par le client.
 */
class AvisController extends Controller
{
    public function store(Request $request, Produit $produit): RedirectResponse
    {
        $client = Auth::guard('web')->user();

        if (! $client) {
            return redirect()->route('login')->with('info', 'Connectez-vous pour laisser un avis.');
        }

        $commande = $client->commandeLivreeContenant($produit);

        if (! $commande) {
            return back()->with('error', 'Seuls les clients qui ont reçu ce produit peuvent laisser un avis.');
        }

        $avisExistant = Avis::where('id_p', $produit->id)->where('id_ut', $client->id)->first();

        if ($avisExistant && ! $avisExistant->estPublie()) {
            return back()->with('error', 'Votre avis a été masqué par la boutique. Contactez-la pour en savoir plus.');
        }

        $donnees = $request->validate([
            'note' => ['required', 'integer', 'between:1,5'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ], [
            'note.required' => 'Choisissez une note de 1 à 5 étoiles.',
            'note.between' => 'Choisissez une note de 1 à 5 étoiles.',
            'commentaire.max' => 'Votre commentaire ne doit pas dépasser 1 000 caractères.',
        ]);

        Avis::updateOrCreate(
            ['id_p' => $produit->id, 'id_ut' => $client->id],
            [
                'id_c' => $commande->id,
                'note' => $donnees['note'],
                'commentaire' => filled($donnees['commentaire'] ?? null) ? trim($donnees['commentaire']) : null,
                'statut' => Avis::STATUT_PUBLIE,
            ]
        );

        return redirect(route('produits.show', $produit->id).'#avis')
            ->with('success', $avisExistant ? 'Votre avis a été modifié.' : 'Merci pour votre avis !');
    }
}
