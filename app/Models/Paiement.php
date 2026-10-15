<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_p',
        'montant_p',
        'mode_p',
        'statut',
        'id_c',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_c');
    }
}
