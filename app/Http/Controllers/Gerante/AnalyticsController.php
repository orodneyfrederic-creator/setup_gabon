<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Devis;
use App\Models\LigneVentePos;
use App\Models\VentePos;
use App\Services\ChiffreAffairesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request, ChiffreAffairesService $chiffreAffairesService)
    {
        // Période sélectionnée (7, 30 ou 90 jours)
        $periodeDemandee = $request->integer('period', ChiffreAffairesService::PERIODE_PAR_DEFAUT_JOURS);
        $period = (string) (in_array($periodeDemandee, [7, 30, 90], true) ? $periodeDemandee : ChiffreAffairesService::PERIODE_PAR_DEFAUT_JOURS);
        $startDate = $chiffreAffairesService->debutPeriode((int) $period);

        // 1. Chiffres clés : même calcul que le tableau de bord (caisse + en ligne confirmé/livré + factures payées)
        $chiffreAffaires = $chiffreAffairesService->calculer($startDate);
        $caPos = $chiffreAffaires['caisse'];
        $caOnline = $chiffreAffaires['en_ligne'];
        $totalFacturesPayees = $chiffreAffaires['factures'];
        $caTotal = $chiffreAffaires['total'];

        $nbVentesPos = VentePos::where('created_at', '>=', $startDate)->count();
        $nbCommandesOnline = Commande::where('created_at', '>=', $startDate)->count();

        $panierMoyenPos = $nbVentesPos > 0 ? round($caPos / $nbVentesPos) : 0;
        $totalDevis = Devis::where('created_at', '>=', $startDate)->count();

        // 2. Évolution Ventes (Jour par jour pour le graphique de tendance)
        $dates = [];
        $salesPosData = [];
        $salesOnlineData = [];

        for ($i = (int) $period - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('d/m');

            $posSum = VentePos::whereDate('created_at', $dateStr)->sum('montant_total');
            $onlineSum = Commande::whereDate('created_at', $dateStr)
                ->whereIn('statut', ChiffreAffairesService::STATUTS_COMMANDE_ENCAISSES)
                ->sum('montant_c');

            $salesPosData[] = (float) $posSum;
            $salesOnlineData[] = (float) $onlineSum;
        }

        // 3. Répartition des méthodes de paiement (POS)
        $methodePaiements = VentePos::where('created_at', '>=', $startDate)
            ->select('methode_paiement', DB::raw('SUM(montant_total) as total'))
            ->groupBy('methode_paiement')
            ->pluck('total', 'methode_paiement')
            ->toArray();

        $paymentLabels = [];
        $paymentData = [];

        foreach ($methodePaiements as $method => $total) {
            $paymentLabels[] = $method ?? 'Espèces';
            $paymentData[] = (float) $total;
        }

        // Si aucune vente enregistrée sur la période, afficher une répartition indicative
        if (empty($paymentData) || array_sum($paymentData) == 0) {
            $paymentLabels = ['Espèces', 'Carte Bancaire', 'Mobile Money'];
            $paymentData = [1, 1, 1];
        }

        // 4. Ventes par Catégorie
        $categoriesData = DB::table('ligne_vente_pos')
            ->join('produits', 'ligne_vente_pos.id_p', '=', 'produits.id')
            ->join('categories', 'produits.id_cat', '=', 'categories.id')
            ->where('ligne_vente_pos.created_at', '>=', $startDate)
            ->select('categories.nom as categorie', DB::raw('SUM(ligne_vente_pos.total_ligne) as total'))
            ->groupBy('categories.nom')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $catLabels = $categoriesData->pluck('categorie')->toArray();
        $catData = $categoriesData->pluck('total')->toArray();

        // 5. Top 5 des produits les plus vendus (Caisse POS)
        $topProduits = LigneVentePos::where('created_at', '>=', $startDate)
            ->select('designation', DB::raw('SUM(quantite) as total_qty'), DB::raw('SUM(total_ligne) as total_ca'))
            ->groupBy('designation')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('gerante.analytics.index', compact(
            'period',
            'caTotal',
            'caPos',
            'caOnline',
            'nbVentesPos',
            'panierMoyenPos',
            'totalDevis',
            'totalFacturesPayees',
            'dates',
            'salesPosData',
            'salesOnlineData',
            'paymentLabels',
            'paymentData',
            'catLabels',
            'catData',
            'topProduits'
        ));
    }
}
