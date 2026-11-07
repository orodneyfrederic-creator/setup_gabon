<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegociationOffre extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_negociation',
        'auteur',
        'tour_numero',
        'montant_propose',
        'type_action',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'tour_numero' => 'integer',
            'montant_propose' => 'integer',
        ];
    }

    public function negociation(): BelongsTo
    {
        return $this->belongsTo(Negociation::class, 'id_negociation');
    }
}
