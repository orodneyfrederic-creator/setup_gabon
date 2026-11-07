<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_c',
        'statut',
        'montant_c',
        'id_ut',
        'adresse_livraison',
        'methode_paiement',
        'conditions_acceptees_at',
        'version_conditions',
    ];

    protected function casts(): array
    {
        return [
            'date_c' => 'datetime',
            'conditions_acceptees_at' => 'datetime',
        ];
    }

    /**
     * Libellé lisible du mode de paiement choisi par le client.
     */
    public function libelleMethodePaiement(): ?string
    {
        return [
            'especes' => 'Espèces',
            'airtel_money' => 'Airtel Money',
            'moov_money' => 'Moov Money',
        ][$this->methode_paiement] ?? $this->methode_paiement;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_ut');
    }

    public function lignesCommandes()
    {
        return $this->hasMany(LigneCommande::class, 'id_c');
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'id_c');
    }

    public function adresses()
    {
        return $this->hasMany(Adresse::class, 'id_c');
    }
}
