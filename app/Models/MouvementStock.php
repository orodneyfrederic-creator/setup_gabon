<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    protected $fillable = [
        'id_p',
        'type',
        'quantite',
        'motif',
        'id_ut',
    ];
}
