<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Avis d'un client sur un produit qu'il a reçu (achat vérifié). La boutique peut masquer un avis
 * qui enfreint les règles (propos injurieux, données personnelles, hors sujet), jamais en modifier le contenu.
 */
class Avis extends Model
{
    public const STATUT_PUBLIE = 'publie';

    public const STATUT_MASQUE = 'masque';

    protected $table = 'avis';

    protected $fillable = [
        'id_p',
        'id_ut',
        'id_c',
        'note',
        'commentaire',
        'statut',
        'motif_masquage',
    ];

    protected function casts(): array
    {
        return [
            'note' => 'integer',
        ];
    }

    /**
     * @param  Builder<Avis>  $query
     */
    public function scopePublies(Builder $query): void
    {
        $query->where('statut', self::STATUT_PUBLIE);
    }

    public function estPublie(): bool
    {
        return $this->statut === self::STATUT_PUBLIE;
    }

    /**
     * Prénom et initiale du nom : l'avis est public, le nom complet n'est jamais affiché.
     */
    public function auteurAffiche(): string
    {
        $prenom = $this->user?->prenom ?: 'Client';
        $initiale = mb_substr((string) $this->user?->nom, 0, 1);

        return $initiale !== '' ? $prenom.' '.mb_strtoupper($initiale).'.' : $prenom;
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_p');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_ut');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_c');
    }
}
