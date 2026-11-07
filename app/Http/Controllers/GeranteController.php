<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Negociation;
use App\Models\Produit;
use App\Models\User;
use App\Services\ChiffreAffairesService;
use Illuminate\View\View;

class GeranteController extends Controller
{
    /**
     * Affiche le tableau de bord de la gérante.
     */
    public function dashboard(ChiffreAffairesService $chiffreAffairesService): View
    {
        $totalClients = User::where('role', 'client')->count();
        $totalCommandes = Commande::count();
        $totalProduits = Produit::count();

        // Même calcul et même période par défaut que la page Statistiques
        $periodeJours = ChiffreAffairesService::PERIODE_PAR_DEFAUT_JOURS;
        $chiffreAffaires = $chiffreAffairesService->calculer($chiffreAffairesService->debutPeriode($periodeJours))['total'];

        // Produits en stock bas
        $alertesStock = Produit::stockBas()->get();

        $commandesEnAttente = Commande::where('statut', 'en_attente')->count();
        $negociationsEnAttente = Negociation::where('statut', 'en_attente_gerante')->count();

        $dernieresCommandes = Commande::with('user')->latest('date_c')->take(5)->get();

        return view('gerante.dashboard', compact(
            'totalClients', 'totalCommandes', 'totalProduits', 'chiffreAffaires', 'alertesStock',
            'commandesEnAttente', 'negociationsEnAttente', 'periodeJours', 'dernieresCommandes'
        ));
    }
}
