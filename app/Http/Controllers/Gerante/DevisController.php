<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\LigneDevis;
use App\Models\Produit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DevisController extends Controller
{
    public function index(Request $request)
    {
        $query = Devis::query();

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

        $devis = $query->latest()->paginate(15)->withQueryString();

        return view('gerante.devis.index', compact('devis'));
    }

    public function create()
    {
        $produits = Produit::where('quantite', '>', 0)->get();

        return view('gerante.devis.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_nom' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_telephone' => 'required|string|max:20',
            'client_entreprise' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.id_p' => 'required|exists:produits,id',
            'lignes.*.quantite' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $numero = 'DEV-'.date('Ymd').'-'.strtoupper(Str::random(4));

            $devis = Devis::create([
                'numero' => $numero,
                'id_ut' => Auth::guard('web')->check() ? Auth::guard('web')->id() : null,
                'client_nom' => $validated['client_nom'],
                'client_email' => $validated['client_email'] ?? null,
                'client_tel' => $validated['client_telephone'],
                'client_entreprise' => $validated['client_entreprise'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'statut' => 'brouillon',
                'montant_total' => 0,
            ]);

            $montantTotal = 0;

            foreach ($validated['lignes'] as $ligne) {
                $produit = Produit::find($ligne['id_p']);
                $quantite = $ligne['quantite'];
                $prixUnitaire = $produit->prix_unitaire;
                $sousTotal = $prixUnitaire * $quantite;

                LigneDevis::create([
                    'id_devis' => $devis->id,
                    'id_p' => $produit->id,
                    'designation' => $produit->designation,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'total_ligne' => $sousTotal,
                ]);

                $montantTotal += $sousTotal;
            }

            $devis->update(['montant_total' => $montantTotal]);

            DB::commit();

            return redirect()->route('gerante.devis.show', $devis)->with('success', 'Devis créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors de la création du devis : '.$e->getMessage())->withInput();
        }
    }

    public function show(Devis $devi)
    {
        $devi->load('lignes.produit');

        return view('gerante.devis.show', compact('devi'));
    }

    public function edit(Devis $devi)
    {
        if ($devi->statut !== 'brouillon') {
            return redirect()->route('gerante.devis.show', $devi)->with('error', 'Seuls les devis en brouillon peuvent être modifiés.');
        }

        $devi->load('lignes.produit');
        $produits = Produit::where('quantite', '>', 0)->get();

        return view('gerante.devis.edit', compact('devi', 'produits'));
    }

    public function update(Request $request, Devis $devi)
    {
        if ($devi->statut !== 'brouillon') {
            return redirect()->route('gerante.devis.show', $devi)->with('error', 'Impossible de modifier ce devis.');
        }

        $validated = $request->validate([
            'client_nom' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_telephone' => 'required|string|max:20',
            'client_entreprise' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.id_p' => 'required|exists:produits,id',
            'lignes.*.quantite' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $devi->update([
                'client_nom' => $validated['client_nom'],
                'client_email' => $validated['client_email'],
                'client_telephone' => $validated['client_telephone'],
                'client_entreprise' => $validated['client_entreprise'],
                'notes' => $validated['notes'],
            ]);

            // Supprimer les anciennes lignes
            $devi->lignes()->delete();

            $montantTotal = 0;

            // Recréer les lignes
            foreach ($validated['lignes'] as $ligne) {
                $produit = Produit::find($ligne['id_p']);
                $quantite = $ligne['quantite'];
                $prixUnitaire = $produit->prix_unitaire;
                $sousTotal = $prixUnitaire * $quantite;

                LigneDevis::create([
                    'id_devis' => $devi->id,
                    'id_p' => $produit->id,
                    'designation' => $produit->designation,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'total_ligne' => $sousTotal,
                ]);

                $montantTotal += $sousTotal;
            }

            $devi->update(['montant_total' => $montantTotal]);

            DB::commit();

            return redirect()->route('gerante.devis.show', $devi)->with('success', 'Devis mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors de la modification : '.$e->getMessage())->withInput();
        }
    }

    public function status(Request $request, Devis $devi)
    {
        $validated = $request->validate([
            'statut' => 'required|in:brouillon,envoye,accepte,refuse,facture',
        ]);

        $devi->update(['statut' => $validated['statut']]);

        // Si accepté, on pourrait proposer de le transformer en facture
        if ($validated['statut'] === 'accepte') {
            return redirect()->route('gerante.devis.show', $devi)->with('success', 'Devis accepté. Vous pouvez maintenant générer la facture.');
        }

        return redirect()->route('gerante.devis.show', $devi)->with('success', 'Statut du devis mis à jour.');
    }

    public function destroy(Devis $devi)
    {
        if ($devi->statut === 'accepte') {
            return back()->with('error', 'Impossible de supprimer un devis accepté.');
        }

        $devi->lignes()->delete();
        $devi->delete();

        return redirect()->route('gerante.devis.index')->with('success', 'Devis supprimé avec succès.');
    }

    public function pdf(Request $request, Devis $devi)
    {
        $devi->load('lignes.produit');

        $pdf = Pdf::loadView('gerante.devis.pdf', compact('devi'));

        if ($request->has('print')) {
            return $pdf->stream('Devis_'.$devi->numero.'.pdf');
        }

        return $pdf->download('Devis_'.$devi->numero.'.pdf');
    }
}
