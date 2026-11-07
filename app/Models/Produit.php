<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'description',
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
        ];
    }

    public function negociations()
    {
        return $this->hasMany(Negociation::class, 'id_p');
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

    public function scopeStockBas($query)
    {
        return $query->whereRaw('quantite <= seuil_alerte');
    }
}
