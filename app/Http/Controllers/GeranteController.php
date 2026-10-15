<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Facture;
use App\Models\Produit;
use App\Models\User;
use App\Models\VenteCaisse;

class GeranteController extends Controller
{
    /**
     * Affiche le tableau de bord de la gérante.
     */
    public function dashboard()
    {
        $totalClients = User::where('role', 'client')->count();
        $totalCommandes = Commande::count();
        $totalProduits = Produit::count();

        $caVentes = VenteCaisse::sum('montant_total');
        $caFactures = Facture::where('statut', 'payee')->sum('montant_total');
        $chiffreAffaires = $caVentes + $caFactures;

        // Produits en stock bas
        $alertesStock = Produit::stockBas()->get();

        return view('gerante.dashboard', compact('totalClients', 'totalCommandes', 'totalProduits', 'chiffreAffaires', 'alertesStock'));
    }
}
