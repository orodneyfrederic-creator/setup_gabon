<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    use HasFactory;

    protected $table = 'devis';

    protected $fillable = [
        'reference',
        'numero',
        'client_nom',
        'client_email',
        'client_tel',
        'client_telephone',
        'client_entreprise',
        'statut',
        'notes',
        'date_validite',
        'id_ut',
        'user_id',
        'montant_total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_ut');
    }

    public function lignes()
    {
        return $this->hasMany(LigneDevis::class, 'id_devis');
    }

    public function facture()
    {
        return $this->hasOne(Facture::class, 'id_devis');
    }
}
