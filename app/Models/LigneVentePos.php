<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneVentePos extends Model
{
    protected $table = 'ligne_vente_pos';

    protected $fillable = [
        'id_vente_pos',
        'id_p',
        'designation',
        'quantite',
        'prix_unitaire',
        'total_ligne',
    ];

    public function vente()
    {
        return $this->belongsTo(VentePos::class, 'id_vente_pos');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_p');
    }
}
