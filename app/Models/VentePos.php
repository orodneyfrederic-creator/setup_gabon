<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentePos extends Model
{
    protected $table = 'vente_pos';

    protected $fillable = [
        'numero',
        'montant_total',
        'methode_paiement',
        'id_ut',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'id_ut', 'id_staff');
    }

    public function lignes()
    {
        return $this->hasMany(LigneVentePos::class, 'id_vente_pos');
    }
}
