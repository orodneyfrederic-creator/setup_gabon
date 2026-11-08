<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Modération des avis : la gérante peut masquer un avis contraire aux règles (injures, données personnelles,
 * hors sujet) et le republier. Elle ne peut pas modifier ce que le client a écrit.
 */
class AvisGeranteController extends Controller
{
    public function index(Request $request): View
    {
        $statut = in_array($request->query('statut'), [Avis::STATUT_PUBLIE, Avis::STATUT_MASQUE], true) ? $request->query('statut') : null;

        $avis = Avis::with(['produit', 'user'])
            ->when($statut, fn ($requete) => $requete->where('statut', $statut))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $noteMoyenne = Avis::publies()->avg('note');
        $nbPublies = Avis::publies()->count();
        $nbMasques = Avis::where('statut', Avis::STATUT_MASQUE)->count();

        return view('gerante.avis.index', compact('avis', 'statut', 'noteMoyenne', 'nbPublies', 'nbMasques'));
    }

    public function moderer(Request $request, Avis $avis): RedirectResponse
    {
        $donnees = $request->validate([
            'action' => ['required', 'in:masquer,republier'],
            'motif_masquage' => ['required_if:action,masquer', 'nullable', 'string', 'max:255'],
        ], [
            'motif_masquage.required_if' => 'Indiquez pourquoi cet avis est masqué.',
        ]);

        if ($donnees['action'] === 'masquer') {
            $avis->update(['statut' => Avis::STATUT_MASQUE, 'motif_masquage' => $donnees['motif_masquage']]);

            return back()->with('success', 'Avis masqué : il n\'apparaît plus sur la fiche produit.');
        }

        $avis->update(['statut' => Avis::STATUT_PUBLIE, 'motif_masquage' => null]);

        return back()->with('success', 'Avis republié.');
    }
}
