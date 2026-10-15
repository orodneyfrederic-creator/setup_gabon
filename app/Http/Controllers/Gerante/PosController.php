<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\LigneVentePos;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\VentePos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Affiche l'interface de la caisse avec le catalogue de produits.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $produits = Produit::query()
            ->when($search, fn ($q) => $q->where('designation', 'like', "%{$search}%")
                ->orWhere('reference', 'like', "%{$search}%"))
            ->where('quantite', '>', 0)
            ->with(['category', 'marque'])
            ->orderBy('designation')
            ->get();

        return view('gerante.pos.index', compact('produits', 'search'));
    }

    /**
     * Enregistre la vente, déduit le stock et redirige vers le reçu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'panier' => 'required|string',
            'methode_paiement' => 'required|string|in:Especes,Carte,Mobile Money',
        ]);

        $panier = json_decode($request->input('panier'), true);

        if (empty($panier)) {
            return back()->withErrors(['panier' => 'Le panier est vide.']);
        }

        DB::beginTransaction();

        try {
            $montantTotal = 0;

            foreach ($panier as $item) {
                $montantTotal += $item['prix_unitaire'] * $item['quantite'];
            }

            $vente = VentePos::create([
                'numero' => 'POS-'.strtoupper(uniqid()),
                'montant_total' => $montantTotal,
                'methode_paiement' => $request->input('methode_paiement'),
                'id_ut' => auth('staff')->id(),
            ]);

            foreach ($panier as $item) {
                $produit = Produit::findOrFail($item['id']);

                if ($produit->quantite < $item['quantite']) {
                    DB::rollBack();

                    return back()->withErrors([
                        'panier' => "Stock insuffisant pour : {$produit->designation} (disponible : {$produit->quantite})",
                    ]);
                }

                LigneVentePos::create([
                    'id_vente_pos' => $vente->id,
                    'id_p' => $produit->id,
                    'designation' => $produit->designation,
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire'],
                    'total_ligne' => $item['prix_unitaire'] * $item['quantite'],
                ]);

                $produit->decrement('quantite', $item['quantite']);

                MouvementStock::create([
                    'id_p' => $produit->id,
                    'type' => 'sortie',
                    'quantite' => $item['quantite'],
                    'motif' => "Vente Caisse (POS) — {$vente->numero}",
                ]);
            }

            DB::commit();

            return redirect()->route('gerante.pos.receipt', $vente->id)
                ->with('success', 'Vente enregistrée avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['panier' => 'Erreur lors de l\'enregistrement : '.$e->getMessage()]);
        }
    }

    /**
     * Affiche le reçu de la vente.
     */
    public function receipt($id)
    {
        $vente = VentePos::with(['lignes.produit', 'staff'])->findOrFail($id);

        return view('gerante.pos.receipt', compact('vente'));
    }
}
