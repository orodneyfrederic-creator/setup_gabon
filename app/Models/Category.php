<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'image',
        'negociable',
        'remise_max_pct',
    ];

    protected function casts(): array
    {
        return [
            'negociable' => 'boolean',
            'remise_max_pct' => 'integer',
        ];
    }

    public function produits()
    {
        return $this->hasMany(Produit::class, 'id_cat');
    }
}
