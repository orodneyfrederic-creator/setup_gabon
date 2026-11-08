<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'tel',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function commandes()
    {
        return $this->hasMany(Commande::class, 'id_ut');
    }

    public function avis()
    {
        return $this->hasMany(Avis::class, 'id_ut');
    }

    /**
     * Commande livrée la plus récente contenant ce produit : elle prouve l'achat et ouvre le droit de laisser un avis.
     */
    public function commandeLivreeContenant(Produit $produit): ?Commande
    {
        return $this->commandes()
            ->where('statut', 'livree')
            ->whereHas('lignesCommandes', fn ($lignes) => $lignes->where('id_p', $produit->id))
            ->latest('date_c')
            ->first();
    }

    public function adresses()
    {
        return $this->hasMany(Adresse::class, 'id_ut');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_ut');
    }

    public function negociations()
    {
        return $this->hasMany(Negociation::class, 'id_ut');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'id_ut_client');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'id_ut_expediteur');
    }
}
