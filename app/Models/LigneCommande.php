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
        'prix_catalogue_unitaire',
        'prix_final_unitaire',
        'negociation_id',
        'id_c',
        'id_p',
    ];

    protected function casts(): array
    {
        return [
            'quantite_com' => 'integer',
            'prix_unitaire_com' => 'integer',
            'prix_catalogue_unitaire' => 'integer',
            'prix_final_unitaire' => 'integer',
        ];
    }

    public function negociation()
    {
        return $this->belongsTo(Negociation::class, 'negociation_id');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_c');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_p');
    }
}
