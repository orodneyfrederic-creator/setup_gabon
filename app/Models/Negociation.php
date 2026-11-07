<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Negociation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'id_p',
        'id_ut',
        'quantite',
        'prix_catalogue',
        'prix_plancher',
        'prix_final_convenu',
        'statut',
        'tours_utilises',
        'tours_max',
        'expire_a',
        'delai_relance_expire_a',
        'id_staff_decision',
        'notes_gerante',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
            'prix_catalogue' => 'integer',
            'prix_plancher' => 'integer',
            'prix_final_convenu' => 'integer',
            'tours_utilises' => 'integer',
            'tours_max' => 'integer',
            'expire_a' => 'datetime',
            'delai_relance_expire_a' => 'datetime',
        ];
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'id_p');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_ut');
    }

    public function staffDecision(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'id_staff_decision', 'id_staff');
    }

    public function offres(): HasMany
    {
        return $this->hasMany(NegociationOffre::class, 'id_negociation')->orderBy('id', 'asc');
    }

    public function derniereOffre(): ?NegociationOffre
    {
        return $this->offres()->latest('id')->first();
    }

    /**
     * Indique si l'offre acceptée est encore valide dans le temps.
     */
    public function estValide(): bool
    {
        return $this->statut === 'acceptee'
            && $this->expire_a
            && Carbon::now()->lessThanOrEqualTo($this->expire_a);
    }

    /**
     * Indique si la négociation est active (soit en négociation, soit acceptée et utilisable).
     */
    public function estActive(): bool
    {
        return in_array($this->statut, ['en_cours', 'en_attente_gerante'], true)
            || ($this->statut === 'acceptee' && $this->estValide());
    }

    /**
     * Indique si le client est sous le coup d'un délai de relance anti-sondage.
     */
    public function estEnDelaiRelance(): bool
    {
        return $this->delai_relance_expire_a
            && Carbon::now()->lessThan($this->delai_relance_expire_a);
    }
}
