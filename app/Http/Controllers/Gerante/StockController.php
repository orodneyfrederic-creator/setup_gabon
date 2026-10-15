<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\MouvementStock;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::with('category');

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where('designation', 'like', "%{$search}%")
                ->orWhere('reference', 'like', "%{$search}%");
        }

        $produits = $query->paginate(15);
        $alertesStock = Produit::stockBas()->count();

        return view('gerante.stock.index', compact('produits', 'alertesStock'));
    }

    public function createMouvement(Produit $produit)
    {
        return view('gerante.stock.create_mouvement', compact('produit'));
    }

    public function storeMouvement(Request $request, Produit $produit)
    {
        $validated = $request->validate([
            'type' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1',
            'motif' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $quantite = (int) $validated['quantite'];
            $type = $validated['type'];

            if ($type === 'sortie' && $produit->quantite < $quantite) {
                return back()->with('error', 'Quantité insuffisante en stock pour cette sortie.')->withInput();
            }

            // Enregistrer le mouvement
            MouvementStock::create([
                'id_p' => $produit->id,
                'user_id' => Auth::id(),
                'type_mouvement' => $type,
                'quantite' => $quantite,
                'motif' => $validated['motif'],
            ]);

            // Mettre à jour le stock du produit
            if ($type === 'entree') {
                $produit->increment('quantite', $quantite);
            } else {
                $produit->decrement('quantite', $quantite);
            }

            DB::commit();

            return redirect()->route('gerante.stock.index')->with('success', 'Mouvement de stock enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du mouvement: '.$e->getMessage())->withInput();
        }
    }

    public function historique(Produit $produit)
    {
        $mouvements = $produit->mouvements()->with('user')->latest()->paginate(20);

        return view('gerante.stock.historique', compact('produit', 'mouvements'));
    }
}
