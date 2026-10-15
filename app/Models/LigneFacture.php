<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneFacture extends Model
{
    use HasFactory;

    protected $table = 'ligne_factures';

    protected $fillable = [
        'id_facture',
        'designation',
        'quantite',
        'prix_unitaire',
        'total_ligne',
    ];

    public function facture()
    {
        return $this->belongsTo(Facture::class, 'id_facture');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_p');
    }
}
