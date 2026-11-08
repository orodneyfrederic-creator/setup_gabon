<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'description',
        'caracteristiques',
        'prix_unitaire',
        'prix_achat',
        'quantite',
        'seuil_alerte',
        'image',
        'video',
        'reference',
        'id_cat',
        'id_m',
        'negociable',
        'remise_max_pct',
        'nb_tours_max',
        'decision_manuelle',
    ];

    protected function casts(): array
    {
        return [
            'prix_unitaire' => 'integer',
            'prix_achat' => 'integer',
            'quantite' => 'integer',
            'seuil_alerte' => 'integer',
            'negociable' => 'boolean',
            'remise_max_pct' => 'integer',
            'nb_tours_max' => 'integer',
            'decision_manuelle' => 'boolean',
            'caracteristiques' => 'array',
        ];
    }

    public function negociations()
    {
        return $this->hasMany(Negociation::class, 'id_p');
    }

    /**
     * Photos supplémentaires, dans l'ordre choisi par la gérante.
     */
    public function images()
    {
        return $this->hasMany(ProduitImage::class, 'id_p')->orderBy('position')->orderBy('id');
    }

    /**
     * Toutes les photos du produit : la principale d'abord, puis les supplémentaires.
     *
     * @return list<string>
     */
    public function galerie(): array
    {
        return array_values(array_filter([
            $this->image,
            ...$this->images->pluck('chemin')->all(),
        ]));
    }

    /**
     * Lignes de la fiche technique, sans les lignes vides.
     *
     * @return list<array{libelle: string, valeur: string}>
     */
    public function ficheTechnique(): array
    {
        return array_values(array_filter(
            $this->caracteristiques ?? [],
            fn ($ligne) => filled($ligne['libelle'] ?? null) && filled($ligne['valeur'] ?? null)
        ));
    }

    public function avis()
    {
        return $this->hasMany(Avis::class, 'id_p');
    }

    /**
     * Produits de la même catégorie, en stock d'abord, puis par prix le plus proche.
     *
     * @return Collection<int, Produit>
     */
    public function produitsSimilaires(int $nombre = 4): Collection
    {
        if (! $this->id_cat) {
            return new Collection;
        }

        return self::query()
            ->with('category')
            ->where('id_cat', $this->id_cat)
            ->whereKeyNot($this->getKey())
            ->orderByRaw('CASE WHEN quantite > 0 THEN 0 ELSE 1 END')
            ->orderByRaw('ABS(prix_unitaire - ?)', [(int) $this->prix_unitaire])
            ->limit($nombre)
            ->get();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_cat');
    }

    public function marque()
    {
        return $this->belongsTo(Marque::class, 'id_m');
    }

    public function lignesCommandes()
    {
        return $this->hasMany(LigneCommande::class, 'id_p');
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class, 'id_p');
    }

    public function getPrixAttribute()
    {
        return $this->prix_unitaire;
    }

    /**
     * Durée de la garantie boutique, fixée par la catégorie (14 jours par défaut).
     */
    public function garantieJours(): int
    {
        return $this->category?->garantie_jours ?? (int) config('boutique.garantie.autres_jours');
    }

    /**
     * Garantie exprimée en langage courant : « 1 mois », « 2 semaines »…
     */
    public function libelleGarantie(): string
    {
        $jours = $this->garantieJours();

        return match (true) {
            $jours % 30 === 0 => ($jours / 30).' mois',
            $jours % 7 === 0 => ($jours / 7).' semaine'.($jours > 7 ? 's' : ''),
            default => $jours.' jours',
        };
    }

    public function scopeStockBas($query)
    {
        return $query->whereRaw('quantite <= seuil_alerte');
    }
}
