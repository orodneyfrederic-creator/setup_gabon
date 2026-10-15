<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\LigneFacture;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $query = Facture::query();

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where('reference', 'like', "%{$search}%")
                ->orWhere('client_nom', 'like', "%{$search}%")
                ->orWhere('client_entreprise', 'like', "%{$search}%");
        }

        if ($request->has('statut') && ! empty($request->statut)) {
            $query->where('statut', $request->statut);
        }

        $factures = $query->latest()->paginate(15);

        return view('gerante.factures.index', compact('factures'));
    }

    public function createFromDevis(Devis $devi)
    {
        if ($devi->statut !== 'accepte') {
            return back()->with('error', 'Seul un devis accepté peut être transformé en facture.');
        }

        if ($devi->facture) {
            return redirect()->route('gerante.factures.show', $devi->facture)
                ->with('info', 'Ce devis a déjà été facturé.');
        }

        try {
            DB::beginTransaction();

            $numero = 'FAC-'.date('Ymd').'-'.strtoupper(Str::random(4));

            $facture = Facture::create([
                'numero' => $numero,
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
                    'designation' => $ligneDevis->designation,
                    'quantite' => $ligneDevis->quantite,
                    'prix_unitaire' => $ligneDevis->prix_unitaire,
                    'total_ligne' => $ligneDevis->total_ligne,
                ]);
            }

            DB::commit();

            return redirect()->route('gerante.factures.show', $facture)->with('success', 'Facture générée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors de la génération de la facture : '.$e->getMessage());
        }
    }

    public function show(Facture $facture)
    {
        $facture->load('lignes.produit', 'devis');

        return view('gerante.factures.show', compact('facture'));
    }

    public function status(Request $request, Facture $facture)
    {
        $validated = $request->validate([
            'statut' => 'required|in:impayee,payee,annulee',
        ]);

        $facture->update(['statut' => $validated['statut']]);

        return back()->with('success', 'Statut de la facture mis à jour.');
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
}
