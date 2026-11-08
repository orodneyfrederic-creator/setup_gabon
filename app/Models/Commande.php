<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as Notifications;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_c',
        'statut',
        'montant_c',
        'id_ut',
        'client_nom',
        'client_telephone',
        'client_email',
        'jeton_suivi',
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

    public function estInvitee(): bool
    {
        return $this->id_ut === null;
    }

    public function nomClient(): string
    {
        return $this->client_nom ?: (trim(($this->user?->prenom ?? '').' '.($this->user?->nom ?? '')) ?: 'Client');
    }

    public function prenomClient(): string
    {
        return $this->user?->prenom ?: (string) strtok($this->nomClient(), ' ');
    }

    public function telephoneClient(): ?string
    {
        return $this->client_telephone ?: $this->user?->tel;
    }

    public function emailClient(): ?string
    {
        return $this->client_email ?: $this->user?->email;
    }

    /**
     * Lien de suivi : l'espace client pour un compte, le lien secret pour une commande sans compte.
     */
    public function urlSuivi(): string
    {
        return $this->estInvitee()
            ? route('commandes.suivi', $this->jeton_suivi)
            : route('orders.show', $this->id);
    }

    /**
     * Envoie un e-mail au client, qu'il ait un compte ou seulement une adresse donnée à la commande.
     * Ne fait rien pour un client sans compte qui n'a pas donné d'e-mail.
     */
    public function notifierClient(Notification $notification): void
    {
        if ($this->user) {
            $this->user->notify($notification);
        } elseif ($this->client_email) {
            Notifications::route('mail', $this->client_email)->notify($notification);
        }
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
