<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    use HasFactory;

    protected $fillable = [
        'rue',
        'ville',
        'pays',
        'type',
        'id_c',
        'id_ut',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_ut');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_c');
    }
}
