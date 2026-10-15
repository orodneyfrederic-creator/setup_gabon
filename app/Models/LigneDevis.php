<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneDevis extends Model
{
    use HasFactory;

    protected $table = 'ligne_devis';

    protected $fillable = [
        'id_devis',
        'id_p',
        'designation',
        'quantite',
        'prix_unitaire',
        'total_ligne',
    ];

    public function devis()
    {
        return $this->belongsTo(Devis::class, 'id_devis');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_p');
    }
}
