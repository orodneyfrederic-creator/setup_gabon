<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_c',
        'statut',
        'montant_c',
        'id_ut',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_ut');
    }

    public function lignesCommandes()
    {
        return $this->hasMany(LigneCommande::class, 'id_c');
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'id_c');
    }

    public function adresses()
    {
        return $this->hasMany(Adresse::class, 'id_c');
    }
}
