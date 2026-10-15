<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $table = 'factures';

    protected $fillable = [
        'reference',
        'numero',
        'id_devis',
        'client_nom',
        'client_email',
        'client_telephone',
        'client_entreprise',
        'montant_total',
        'statut',
        'notes',
        'date_echeance',
    ];

    public function devis()
    {
        return $this->belongsTo(Devis::class, 'id_devis');
    }

    public function lignes()
    {
        return $this->hasMany(LigneFacture::class, 'id_facture');
    }
}
