<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    use HasFactory;

    protected $table = 'lignes_commandes';

    protected $fillable = [
        'quantite_com',
        'prix_unitaire_com',
        'id_c',
        'id_p',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_c');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_p');
    }
}
