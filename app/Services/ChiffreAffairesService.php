<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Facture;
use App\Models\VentePos;
use Carbon\CarbonInterface;

/**
 * Source unique du chiffre d'affaires, partagée par le tableau de bord et la page Statistiques.
 *
 * Sont comptés : les ventes en caisse, les commandes en ligne confirmées ou livrées
 * et les factures payées.
 */
class ChiffreAffairesService
{
    /** Période affichée par défaut, en jours, sur le tableau de bord et les Statistiques. */
    public const PERIODE_PAR_DEFAUT_JOURS = 30;

    /** @var list<string> */
    public const STATUTS_COMMANDE_ENCAISSES = ['confirmee', 'livree'];

    /**
     * @return array{caisse: int, en_ligne: int, factures: int, total: int}
     */
    public function calculer(?CarbonInterface $depuis = null): array
    {
        $caisse = (int) VentePos::query()
            ->when($depuis, fn ($requete) => $requete->where('created_at', '>=', $depuis))
            ->sum('montant_total');

        $enLigne = (int) Commande::query()
            ->whereIn('statut', self::STATUTS_COMMANDE_ENCAISSES)
            ->when($depuis, fn ($requete) => $requete->where('created_at', '>=', $depuis))
            ->sum('montant_c');

        $factures = (int) Facture::query()
            ->where('statut', 'payee')
            ->when($depuis, fn ($requete) => $requete->where('created_at', '>=', $depuis))
            ->sum('montant_total');

        return [
            'caisse' => $caisse,
            'en_ligne' => $enLigne,
            'factures' => $factures,
            'total' => $caisse + $enLigne + $factures,
        ];
    }

    /**
     * Point de départ d'une période de N jours, identique partout dans l'application.
     */
    public function debutPeriode(int $jours = self::PERIODE_PAR_DEFAUT_JOURS): CarbonInterface
    {
        return now()->subDays($jours)->startOfDay();
    }
}
