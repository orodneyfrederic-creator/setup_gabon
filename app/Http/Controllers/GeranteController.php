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

        // Informations légales encore manquantes avant la mise en ligne (loi n°025/2021 art. 31, loi n°001/2011 modifiée art. 78)
        $manquesConformite = array_keys(array_filter([
            'Forme juridique (BOUTIQUE_FORME_JURIDIQUE)' => ! config('boutique.forme_juridique'),
            'Numéro RCCM (BOUTIQUE_RCCM)' => ! config('boutique.rccm'),
            'Numéro NIF (BOUTIQUE_NIF)' => ! config('boutique.nif'),
            'Responsable de la publication (BOUTIQUE_RESPONSABLE_PUBLICATION)' => ! config('boutique.responsable_publication'),
            'Hébergeur du site (BOUTIQUE_HEBERGEUR_NOM)' => ! config('boutique.hebergeur.nom'),
            'Récépissé de déclaration APDPVP (BOUTIQUE_APDPVP_RECEPISSE)' => ! config('boutique.apdpvp_recepisse'),
        ]));

        return view('gerante.dashboard', compact(
            'totalClients', 'totalCommandes', 'totalProduits', 'chiffreAffaires', 'alertesStock',
            'commandesEnAttente', 'negociationsEnAttente', 'periodeJours', 'dernieresCommandes', 'manquesConformite'
        ));
    }
}
