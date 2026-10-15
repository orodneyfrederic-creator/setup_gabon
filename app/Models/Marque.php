<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marque extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'logo'];

    public function produits()
    {
        return $this->hasMany(Produit::class, 'id_m');
    }
}
