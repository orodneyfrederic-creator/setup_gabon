<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduitImage extends Model
{
    protected $table = 'produit_images';

    protected $fillable = [
        'id_p',
        'chemin',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'id_p');
    }
}
