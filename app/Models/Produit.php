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
        'quantite',
        'seuil_alerte',
        'image',
        'video',
        'reference',
        'id_cat',
        'id_m',
    ];

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
