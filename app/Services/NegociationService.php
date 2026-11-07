<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Negociation;
use App\Models\NegociationOffre;
use App\Models\Produit;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Service centralisant l'intégralité de la logique déterministe de négociation de prix.
 *
 * RÈGLE D'OR : ZÉRO FLOAT.
 * Tous les calculs monétaires sont effectués en arithmétique entière stricte (FCFA).
 * Les arrondis du prix plancher et des contre-offres sont toujours effectués en faveur
 * du vendeur (arrondi au supérieur) afin de préserver la marge commerciale.
 */
class NegociationService
{
    public const DEFAULT_REMISE_MAX_PCT = 2;

    public const DEFAULT_NB_TOURS_MAX = 3;

    public const DUREE_VALIDITE_HEURES = 24;

    public const DELAI_RELANCE_HEURES = 24;

    /**
     * Calcule le prix plancher S en arithmétique entière stricte.
     *
     * Formule : S = ceil(P * (100 - pct) / 100)
     * En arithmétique entière : intdiv(P * (100 - pct) + 99, 100)
     *
     * Exemples de référence :
     * - P = 450 000, pct = 2  => intdiv(450000 * 98 + 99, 100) = 441 000 FCFA
     * - P = 123 457, pct = 2  => intdiv(123457 * 98 + 99, 100) = 120 988 FCFA
     */
    public function calculerPrixPlancher(int $prixCatalogue, int $remisePct): int
    {
        if ($prixCatalogue <= 0) {
            throw new InvalidArgumentException('Le prix catalogue doit être strictement positif.');
        }

        if ($remisePct < 0 || $remisePct > 100) {
            throw new InvalidArgumentException('Le pourcentage de remise doit être compris entre 0 et 100.');
        }

        $facteur = 100 - $remisePct;

        return intdiv(($prixCatalogue * $facteur) + 99, 100);
    }

    /**
     * Détermine si un produit est éligible à la négociation.
     * Résout la cascade : Produit -> Catégorie -> Faux.
     */
    public function isNegociable(Produit $produit): bool
    {
        // 1. Surcharge explicite sur le produit
        if ($produit->negociable !== null) {
            return (bool) $produit->negociable;
        }

        // 2. Héritage de la catégorie
        if ($produit->category && $produit->category->negociable !== null) {
            return (bool) $produit->category->negociable;
        }

        return false;
    }

    /**
     * Récupère le pourcentage de remise maximale applicable au produit.
     * Résout la cascade : Produit -> Catégorie -> Valeur par défaut.
     */
    public function getRemiseMaxPct(Produit $produit): int
    {
        if ($produit->remise_max_pct !== null && $produit->remise_max_pct >= 0) {
            return (int) $produit->remise_max_pct;
        }

        if ($produit->category && $produit->category->remise_max_pct !== null && $produit->category->remise_max_pct >= 0) {
            return (int) $produit->category->remise_max_pct;
        }

        return self::DEFAULT_REMISE_MAX_PCT;
    }

    /**
     * Récupère le nombre de tours maximum applicable au produit.
     */
    public function getNbToursMax(Produit $produit): int
    {
        return $produit->nb_tours_max && $produit->nb_tours_max > 0
            ? (int) $produit->nb_tours_max
            : self::DEFAULT_NB_TOURS_MAX;
    }

    /**
     * Calcule la contre-offre progressive du système en fonction du tour actuel.
     * Arithmétique entière : arrondi vers le haut (faveur du vendeur).
     */
    public function calculerContreOffre(int $P, int $S, int $tourActuel, int $toursMax): int
    {
        if ($tourActuel >= $toursMax) {
            return $S;
        }

        // Cas à 3 tours max :
        // - Tour 1 : ceil((P + S) / 2) = intdiv(P + S + 1, 2)
        // - Tour 2 : transition intermédiaire vers S
        // - Tour 3 : dernier tour = S
        if ($toursMax === 3) {
            if ($tourActuel === 1) {
                return intdiv($P + $S + 1, 2);
            }
            if ($tourActuel === 2) {
                return intdiv($P + (2 * $S) + 2, 3);
            }

            return $S;
        }

        // Cas général pour N tours
        $numerateur = (($toursMax - $tourActuel) * $P) + ($tourActuel * $S) + ($toursMax - 1);
        $contreOffre = intdiv($numerateur, $toursMax);

        return max($S, min($P, $contreOffre));
    }

    /**
     * Démarre une nouvelle négociation ou reprend une session active pour un client.
     */
    public function demarrerOuReprendre(User $user, Produit $produit, int $quantite = 1): array
    {
        if (! $this->isNegociable($produit)) {
            return [
                'success' => false,
                'message' => 'Ce produit n\'est pas éligible à la négociation de prix.',
            ];
        }

        if ($produit->quantite <= 0) {
            return [
                'success' => false,
                'message' => 'Ce produit est actuellement en rupture de stock.',
            ];
        }

        $P = (int) $produit->prix_unitaire;
        $pct = $this->getRemiseMaxPct($produit);
        $S = $this->calculerPrixPlancher($P, $pct);

        // Garde-fou marge : le prix plancher ne doit jamais être inférieur au prix d'achat
        if ($produit->prix_achat && $S < (int) $produit->prix_achat) {
            return [
                'success' => false,
                'message' => 'Négociation temporairement indisponible pour cet article (marge plancher critique).',
            ];
        }

        // Nettoyage / vérification des négociations existantes pour ce couple client / produit
        $existante = Negociation::where('id_ut', $user->id)
            ->where('id_p', $produit->id)
            ->latest('id')
            ->first();

        if ($existante) {
            // Si le prix catalogue du produit a changé entre-temps, expirer l'ancienne négociation
            if ($existante->prix_catalogue !== $P && in_array($existante->statut, ['en_cours', 'en_attente_gerante', 'acceptee'], true)) {
                $existante->update(['statut' => 'expiree']);
            } elseif ($existante->estActive()) {
                return [
                    'success' => true,
                    'negociation' => $existante->load('offres'),
                    'message' => 'Reprise de votre négociation en cours.',
                ];
            } elseif ($existante->estEnDelaiRelance()) {
                $heuresRestantes = Carbon::now()->diffInHours($existante->delai_relance_expire_a) + 1;

                return [
                    'success' => false,
                    'message' => "Un délai de relance est actif sur cet article. Veuillez réessayer dans {$heuresRestantes} heure(s).",
                ];
            }
        }

        // Création d'une nouvelle négociation
        $reference = 'NEG-'.date('Ym').'-'.strtoupper(bin2hex(random_bytes(3)));
        $toursMax = $this->getNbToursMax($produit);

        $negociation = Negociation::create([
            'reference' => $reference,
            'id_p' => $produit->id,
            'id_ut' => $user->id,
            'quantite' => max(1, $quantite),
            'prix_catalogue' => $P,
            'prix_plancher' => $S,
            'statut' => 'en_cours',
            'tours_utilises' => 0,
            'tours_max' => $toursMax,
        ]);

        return [
            'success' => true,
            'negociation' => $negociation->load('offres'),
            'message' => 'Session de négociation ouverte.',
        ];
    }

    /**
     * Traite l'offre formulée par le client.
     */
    public function soumettreOffre(Negociation $negociation, int $offreClient): array
    {
        if ($offreClient <= 0) {
            return [
                'success' => false,
                'message' => 'Veuillez proposer un montant entier supérieur à zéro.',
            ];
        }

        return DB::transaction(function () use ($negociation, $offreClient) {
            // Verrou pessimiste : deux offres simultanées ne peuvent pas consommer le même tour.
            $negociation = Negociation::lockForUpdate()->findOrFail($negociation->id);

            if ($negociation->statut !== 'en_cours') {
                return [
                    'success' => false,
                    'message' => 'Cette négociation n\'est plus en cours.',
                ];
            }

            if ($negociation->tours_utilises >= $negociation->tours_max) {
                return [
                    'success' => false,
                    'message' => 'Vous avez utilisé tous vos tours de négociation. Acceptez la dernière offre ou abandonnez la négociation.',
                ];
            }

            $P = $negociation->prix_catalogue;
            $S = $negociation->prix_plancher;
            $produit = $negociation->produit;
            $nouveauTour = $negociation->tours_utilises + 1;
            $toursMax = $negociation->tours_max;

            // Enregistrer l'offre du client
            NegociationOffre::create([
                'id_negociation' => $negociation->id,
                'auteur' => 'client',
                'tour_numero' => $nouveauTour,
                'montant_propose' => $offreClient,
                'type_action' => 'proposition',
                'message' => 'Proposition client : '.number_format($offreClient, 0, ',', ' ').' FCFA',
            ]);

            // CAS 1 : Offre >= Prix catalogue
            if ($offreClient >= $P) {
                $negociation->update([
                    'tours_utilises' => $nouveauTour,
                    'statut' => 'acceptee',
                    'prix_final_convenu' => $P,
                    'expire_a' => Carbon::now()->addHours(self::DUREE_VALIDITE_HEURES),
                ]);

                NegociationOffre::create([
                    'id_negociation' => $negociation->id,
                    'auteur' => 'systeme',
                    'tour_numero' => $nouveauTour,
                    'montant_propose' => $P,
                    'type_action' => 'acceptation',
                    'message' => 'Votre offre atteint le prix catalogue officiel. Offre validée au prix de '.number_format($P, 0, ',', ' ').' FCFA.',
                ]);

                return [
                    'success' => true,
                    'statut' => 'acceptee',
                    'prix_final' => $P,
                    'message' => 'Offre acceptée au prix catalogue.',
                    'negociation' => $negociation->fresh(['offres']),
                ];
            }

            // Mode décision manuelle configuré sur le produit
            if ($produit && $produit->decision_manuelle) {
                $negociation->update([
                    'tours_utilises' => $nouveauTour,
                    'statut' => 'en_attente_gerante',
                ]);

                NegociationOffre::create([
                    'id_negociation' => $negociation->id,
                    'auteur' => 'systeme',
                    'tour_numero' => $nouveauTour,
                    'montant_propose' => $offreClient,
                    'type_action' => 'mise_en_attente',
                    'message' => 'Votre offre a été transmise directement à la gérante pour examen. Vous recevrez une réponse sous peu.',
                ]);

                return [
                    'success' => true,
                    'statut' => 'en_attente_gerante',
                    'message' => 'Offre transmise à la gérante.',
                    'negociation' => $negociation->fresh(['offres']),
                ];
            }

            // CAS 2 : S <= Offre < P => Acceptation immédiate au prix de l'offre
            if ($offreClient >= $S) {
                $negociation->update([
                    'tours_utilises' => $nouveauTour,
                    'statut' => 'acceptee',
                    'prix_final_convenu' => $offreClient,
                    'expire_a' => Carbon::now()->addHours(self::DUREE_VALIDITE_HEURES),
                ]);

                NegociationOffre::create([
                    'id_negociation' => $negociation->id,
                    'auteur' => 'systeme',
                    'tour_numero' => $nouveauTour,
                    'montant_propose' => $offreClient,
                    'type_action' => 'acceptation',
                    'message' => 'Excellente proposition ! Nous acceptons votre offre de '.number_format($offreClient, 0, ',', ' ').' FCFA.',
                ]);

                return [
                    'success' => true,
                    'statut' => 'acceptee',
                    'prix_final' => $offreClient,
                    'message' => 'Offre acceptée !',
                    'negociation' => $negociation->fresh(['offres']),
                ];
            }

            // CAS 3 : Offre < S (sous le plancher) => Contre-offre ou dernier tour
            if ($nouveauTour < $toursMax) {
                $contreOffre = $this->calculerContreOffre($P, $S, $nouveauTour, $toursMax);

                $negociation->update([
                    'tours_utilises' => $nouveauTour,
                ]);

                NegociationOffre::create([
                    'id_negociation' => $negociation->id,
                    'auteur' => 'systeme',
                    'tour_numero' => $nouveauTour,
                    'montant_propose' => $contreOffre,
                    'type_action' => 'contre_offre',
                    'message' => 'Votre offre est un peu trop basse. Nous pouvons faire un effort et vous proposer '.number_format($contreOffre, 0, ',', ' ').' FCFA.',
                ]);

                return [
                    'success' => true,
                    'statut' => 'contre_offre',
                    'contre_offre' => $contreOffre,
                    'tours_restants' => $toursMax - $nouveauTour,
                    'message' => 'Contre-offre proposée.',
                    'negociation' => $negociation->fresh(['offres']),
                ];
            }

            // DERNIER TOUR : Le système formule sa dernière offre ferme (S)
            $negociation->update([
                'tours_utilises' => $nouveauTour,
            ]);

            NegociationOffre::create([
                'id_negociation' => $negociation->id,
                'auteur' => 'systeme',
                'tour_numero' => $nouveauTour,
                'montant_propose' => $S,
                'type_action' => 'derniere_offre',
                'message' => 'Ceci est notre tout dernier prix pour cet équipement : '.number_format($S, 0, ',', ' ').' FCFA. Vous pouvez accepter cette offre finale ou abandonner.',
            ]);

            return [
                'success' => true,
                'statut' => 'derniere_offre',
                'contre_offre' => $S,
                'tours_restants' => 0,
                'message' => 'Dernière offre ferme disponible.',
                'negociation' => $negociation->fresh(['offres']),
            ];
        });
    }

    /**
     * Le client accepte la contre-offre ou la dernière offre formulée par le système / gérante.
     */
    public function accepterContreOffre(Negociation $negociation): array
    {
        if ($negociation->statut !== 'en_cours' && $negociation->statut !== 'en_attente_gerante') {
            return [
                'success' => false,
                'message' => 'Action impossible pour cette négociation.',
            ];
        }

        $derniereOffreSysteme = NegociationOffre::where('id_negociation', $negociation->id)
            ->whereIn('auteur', ['systeme', 'gerante'])
            ->whereIn('type_action', ['contre_offre', 'derniere_offre'])
            ->orderByDesc('id')
            ->first();

        if (! $derniereOffreSysteme) {
            return [
                'success' => false,
                'message' => 'Aucune contre-offre en attente d\'acceptation.',
            ];
        }

        $prixFinal = $derniereOffreSysteme->montant_propose;

        DB::transaction(function () use ($negociation, $prixFinal) {
            $negociation->update([
                'statut' => 'acceptee',
                'prix_final_convenu' => $prixFinal,
                'expire_a' => Carbon::now()->addHours(self::DUREE_VALIDITE_HEURES),
            ]);

            NegociationOffre::create([
                'id_negociation' => $negociation->id,
                'auteur' => 'client',
                'tour_numero' => $negociation->tours_utilises,
                'montant_propose' => $prixFinal,
                'type_action' => 'acceptation',
                'message' => 'Le client a accepté l\'offre de '.number_format($prixFinal, 0, ',', ' ').' FCFA.',
            ]);
        });

        return [
            'success' => true,
            'statut' => 'acceptee',
            'prix_final' => $prixFinal,
            'message' => 'Félicitations ! Votre prix négocié a été validé.',
            'negociation' => $negociation->fresh(['offres']),
        ];
    }

    /**
     * Le client ou la gérante abandonne la négociation.
     * Déclenche le délai de relance anti-sondage (24h).
     */
    public function abandonner(Negociation $negociation, string $auteur = 'client'): array
    {
        if (! in_array($negociation->statut, ['en_cours', 'en_attente_gerante'], true)) {
            return [
                'success' => false,
                'message' => 'Cette négociation n\'est plus active.',
            ];
        }

        DB::transaction(function () use ($negociation, $auteur) {
            $negociation->update([
                'statut' => 'refusee',
                'delai_relance_expire_a' => Carbon::now()->addHours(self::DELAI_RELANCE_HEURES),
            ]);

            NegociationOffre::create([
                'id_negociation' => $negociation->id,
                'auteur' => $auteur,
                'tour_numero' => $negociation->tours_utilises,
                'montant_propose' => 0,
                'type_action' => 'refus',
                'message' => $auteur === 'client' ? 'Négociation clôturée sans accord par le client.' : 'Offre déclinée par la direction.',
            ]);
        });

        return [
            'success' => true,
            'statut' => 'refusee',
            'message' => 'Négociation clôturée.',
            'negociation' => $negociation->fresh(['offres']),
        ];
    }

    /**
     * Intervention manuelle de la gérante sur une négociation.
     */
    public function repondreParGerante(Negociation $negociation, Staff $staff, string $action, ?int $montant = null, ?string $message = null): array
    {
        if (! in_array($negociation->statut, ['en_cours', 'en_attente_gerante'], true)) {
            return [
                'success' => false,
                'message' => 'Cette négociation n\'est plus active.',
            ];
        }

        return DB::transaction(function () use ($negociation, $staff, $action, $montant, $message) {
            $tour = $negociation->tours_utilises + 1;

            if ($action === 'accepter') {
                $derniereOffreClient = $negociation->offres()->where('auteur', 'client')->latest('id')->first();
                $prixAccord = $montant ?: ($derniereOffreClient ? $derniereOffreClient->montant_propose : $negociation->prix_plancher);

                if ($erreur = $this->verifierMontantGerante($negociation, $prixAccord)) {
                    return ['success' => false, 'message' => $erreur];
                }

                $negociation->update([
                    'statut' => 'acceptee',
                    'prix_final_convenu' => $prixAccord,
                    'id_staff_decision' => $staff->id_staff,
                    'expire_a' => Carbon::now()->addHours(self::DUREE_VALIDITE_HEURES),
                ]);

                NegociationOffre::create([
                    'id_negociation' => $negociation->id,
                    'auteur' => 'gerante',
                    'tour_numero' => $tour,
                    'montant_propose' => $prixAccord,
                    'type_action' => 'acceptation',
                    'message' => $message ?: 'La gérante a accepté votre offre de '.number_format($prixAccord, 0, ',', ' ').' FCFA.',
                ]);

                return [
                    'success' => true,
                    'statut' => 'acceptee',
                    'message' => 'Offre acceptée avec succès.',
                ];
            }

            if ($action === 'contre_offre') {
                if (! $montant || $montant <= 0) {
                    return [
                        'success' => false,
                        'message' => 'Veuillez saisir un montant de contre-offre valide.',
                    ];
                }

                if ($erreur = $this->verifierMontantGerante($negociation, $montant)) {
                    return ['success' => false, 'message' => $erreur];
                }

                $negociation->update([
                    'statut' => 'en_cours',
                    'id_staff_decision' => $staff->id_staff,
                    'tours_utilises' => $tour,
                ]);

                NegociationOffre::create([
                    'id_negociation' => $negociation->id,
                    'auteur' => 'gerante',
                    'tour_numero' => $tour,
                    'montant_propose' => $montant,
                    'type_action' => 'contre_offre',
                    'message' => $message ?: 'Contre-offre de la gérante : '.number_format($montant, 0, ',', ' ').' FCFA.',
                ]);

                return [
                    'success' => true,
                    'statut' => 'contre_offre',
                    'message' => 'Contre-offre transmise au client.',
                ];
            }

            if ($action === 'refuser') {
                $negociation->update([
                    'statut' => 'refusee',
                    'id_staff_decision' => $staff->id_staff,
                    'delai_relance_expire_a' => Carbon::now()->addHours(self::DELAI_RELANCE_HEURES),
                ]);

                NegociationOffre::create([
                    'id_negociation' => $negociation->id,
                    'auteur' => 'gerante',
                    'tour_numero' => $tour,
                    'montant_propose' => 0,
                    'type_action' => 'refus',
                    'message' => $message ?: 'La gérante a refusé cette offre.',
                ]);

                return [
                    'success' => true,
                    'statut' => 'refusee',
                    'message' => 'Négociation refusée.',
                ];
            }

            return ['success' => false, 'message' => 'Action inconnue.'];
        });
    }

    /**
     * Borne un montant décidé par la gérante : jamais au-dessus du prix catalogue,
     * jamais sous le prix d'achat (si renseigné). Retourne le message d'erreur ou null.
     */
    private function verifierMontantGerante(Negociation $negociation, int $montant): ?string
    {
        if ($montant > $negociation->prix_catalogue) {
            return 'Le montant ne peut pas dépasser le prix catalogue ('.number_format($negociation->prix_catalogue, 0, ',', ' ').' FCFA).';
        }

        $prixAchat = (int) $negociation->produit?->prix_achat;
        if ($prixAchat > 0 && $montant < $prixAchat) {
            return 'Le montant ne peut pas être inférieur au prix d\'achat ('.number_format($prixAchat, 0, ',', ' ').' FCFA).';
        }

        return null;
    }

    /**
     * Valide et consomme atomiquement une offre négociée lors d'une commande.
     * VERROU STRICT: Vérifie l'authenticité de l'offre et passe à 'utilisee'.
     * Le prix négocié ne s'applique que dans la limite de la quantité convenue.
     */
    public function consommerPourCommande(int $negociationId, int $userId, int $produitId, int $quantiteDemandee): ?Negociation
    {
        return DB::transaction(function () use ($negociationId, $userId, $produitId, $quantiteDemandee) {
            $negociation = Negociation::where('id', $negociationId)
                ->where('id_ut', $userId)
                ->where('id_p', $produitId)
                ->lockForUpdate()
                ->first();

            if (! $negociation) {
                return null;
            }

            // Vérifier validité
            if ($negociation->statut !== 'acceptee' || ! $negociation->estValide()) {
                return null;
            }

            if ($quantiteDemandee > $negociation->quantite) {
                return null;
            }

            // L'accord a été conclu sur un prix catalogue qui a changé depuis : il n'est plus applicable.
            if ($negociation->prix_catalogue !== (int) $negociation->produit?->prix_unitaire) {
                return null;
            }

            // Marquer comme utilisée
            $negociation->update([
                'statut' => 'utilisee',
            ]);

            return $negociation;
        });
    }

    /**
     * Libère une offre en cas d'annulation de commande.
     */
    public function libererNegociation(Commande $commande): void
    {
        foreach ($commande->lignesCommandes as $ligne) {
            if ($ligne->negociation_id) {
                $negociation = Negociation::find($ligne->negociation_id);
                if ($negociation && $negociation->statut === 'utilisee') {
                    // Si l'offre n'a pas dépassé sa date d'expiration, on la remet en 'acceptee', sinon 'annulee'
                    $nouveauStatut = ($negociation->expire_a && Carbon::now()->lessThanOrEqualTo($negociation->expire_a))
                        ? 'acceptee'
                        : 'annulee';

                    $negociation->update(['statut' => $nouveauStatut]);
                }
            }
        }
    }
}
