<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\MouvementStock;
use App\Models\Produit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FactureController extends Controller
{
    /**
     * Transitions autorisées : une facture émise peut être payée ou annulée,
     * une facture payée peut encore être annulée (remboursement), une facture annulée est définitive.
     *
     * @var array<string, list<string>>
     */
    private const TRANSITIONS_AUTORISEES = [
        'emise' => ['payee', 'annulee'],
        'payee' => ['annulee'],
        'annulee' => [],
    ];

    public function index(Request $request)
    {
        $query = Facture::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($sousRequete) use ($search) {
                $sousRequete->where('numero', 'like', "%{$search}%")
                    ->orWhere('client_nom', 'like', "%{$search}%")
                    ->orWhere('client_entreprise', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $factures = $query->latest()->paginate(15)->withQueryString();

        return view('gerante.factures.index', compact('factures'));
    }

    /**
     * Génère la facture d'un devis accepté et sort du stock les produits facturés.
     */
    public function createFromDevis(Devis $devi): RedirectResponse
    {
        if ($devi->statut !== 'accepte') {
            return back()->with('error', 'Seul un devis accepté peut être transformé en facture.');
        }

        if ($devi->facture) {
            return redirect()->route('gerante.factures.show', $devi->facture)
                ->with('info', 'Ce devis a déjà été facturé.');
        }

        $devi->load('lignes');

        $stockManquant = [];
        foreach ($devi->lignes->whereNotNull('id_p')->groupBy('id_p') as $idProduit => $lignes) {
            $produit = Produit::find($idProduit);
            $quantiteFacturee = (int) $lignes->sum('quantite');

            if ($produit && $produit->quantite < $quantiteFacturee) {
                $stockManquant[] = "{$produit->designation} (demandé : {$quantiteFacturee}, en stock : {$produit->quantite})";
            }
        }

        if ($stockManquant !== []) {
            return back()->with('error', 'Stock insuffisant pour facturer : '.implode(', ', $stockManquant).'.');
        }

        $facture = DB::transaction(function () use ($devi) {
            $facture = Facture::create([
                'numero' => 'FAC-'.date('Ymd').'-'.strtoupper(Str::random(4)),
                'id_devis' => $devi->id,
                'client_nom' => $devi->client_nom,
                'client_email' => $devi->client_email,
                'client_telephone' => $devi->client_tel,
                'client_entreprise' => $devi->client_entreprise,
                'montant_total' => $devi->montant_total,
                'statut' => 'emise',
                'notes' => 'Facture générée à partir du devis '.$devi->numero,
            ]);

            foreach ($devi->lignes as $ligneDevis) {
                LigneFacture::create([
                    'id_facture' => $facture->id,
                    'id_p' => $ligneDevis->id_p,
                    'designation' => $ligneDevis->designation,
                    'quantite' => $ligneDevis->quantite,
                    'prix_unitaire' => $ligneDevis->prix_unitaire,
                    'total_ligne' => $ligneDevis->total_ligne,
                ]);
            }

            $this->mouvementerStock($facture, 'sortie');

            return $facture;
        });

        return redirect()->route('gerante.factures.show', $facture)->with('success', 'Facture générée avec succès. Le stock a été mis à jour.');
    }

    public function show(Facture $facture)
    {
        $facture->load('lignes.produit', 'devis');

        return view('gerante.factures.show', compact('facture'));
    }

    /**
     * Change le statut d'une facture ; une annulation remet en stock les produits facturés.
     */
    public function status(Request $request, Facture $facture): RedirectResponse
    {
        $validated = $request->validate([
            'statut' => 'required|in:payee,annulee',
        ]);

        $ancienStatut = $facture->statut;
        $nouveauStatut = $validated['statut'];

        if (! in_array($nouveauStatut, self::TRANSITIONS_AUTORISEES[$ancienStatut] ?? [], true)) {
            return back()->with('error', "Transition de statut non autorisée : {$ancienStatut} → {$nouveauStatut}.");
        }

        DB::transaction(function () use ($facture, $nouveauStatut) {
            $facture->update(['statut' => $nouveauStatut]);

            if ($nouveauStatut === 'annulee') {
                $this->mouvementerStock($facture, 'entree');
            }
        });

        return back()->with('success', $nouveauStatut === 'annulee'
            ? 'Facture annulée. Les produits ont été remis en stock.'
            : 'Paiement enregistré.');
    }

    public function pdf(Request $request, Facture $facture)
    {
        $facture->load('lignes.produit', 'devis');

        $pdf = Pdf::loadView('gerante.factures.pdf', compact('facture'));

        if ($request->has('print')) {
            return $pdf->stream('Facture_'.$facture->numero.'.pdf');
        }

        return $pdf->download('Facture_'.$facture->numero.'.pdf');
    }

    /**
     * Sort (ou remet) en stock les produits liés aux lignes de la facture et trace chaque mouvement.
     */
    private function mouvementerStock(Facture $facture, string $type): void
    {
        foreach ($facture->lignes()->whereNotNull('id_p')->get() as $ligne) {
            $produit = Produit::lockForUpdate()->find($ligne->id_p);

            if (! $produit) {
                continue;
            }

            $type === 'sortie'
                ? $produit->decrement('quantite', $ligne->quantite)
                : $produit->increment('quantite', $ligne->quantite);

            MouvementStock::create([
                'id_p' => $produit->id,
                'type' => $type,
                'quantite' => $ligne->quantite,
                'motif' => $type === 'sortie'
                    ? "Facture {$facture->numero}"
                    : "Annulation facture {$facture->numero}",
            ]);
        }
    }
}
