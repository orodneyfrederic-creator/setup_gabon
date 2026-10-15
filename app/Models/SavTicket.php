<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'client_nom',
        'client_tel',
        'designation_produit',
        'description_panne',
        'statut',
        'date_retour_prevue',
        'notes',
    ];

    protected $casts = [
        'date_retour_prevue' => 'date',
    ];
}
