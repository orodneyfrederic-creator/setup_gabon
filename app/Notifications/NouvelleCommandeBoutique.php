<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Alerte envoyée à l'adresse de la boutique pour chaque nouvelle commande en ligne.
 */
class NouvelleCommandeBoutique extends Notification
{
    use Queueable;

    public function __construct(public Commande $commande) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $commande = $this->commande->loadMissing(['user', 'lignesCommandes.produit']);
        $coordonnees = array_filter([
            $commande->nomClient().($commande->estInvitee() ? ' (sans compte)' : ''),
            $commande->telephoneClient() ?: 'téléphone non renseigné',
            $commande->emailClient(),
        ]);

        $message = (new MailMessage)
            ->subject('Nouvelle commande n° '.$commande->id.' – '.number_format($commande->montant_c, 0, ',', ' ').' FCFA')
            ->greeting('Nouvelle commande en ligne')
            ->line('Client : '.implode(' · ', $coordonnees))
            ->line('Livraison : '.$commande->adresse_livraison.' · Paiement : '.$commande->libelleMethodePaiement());

        foreach ($commande->lignesCommandes as $ligne) {
            $prix = $ligne->prix_final_unitaire ?? $ligne->prix_unitaire_com;
            $message->line('• '.($ligne->produit?->designation ?? 'Produit').' × '.$ligne->quantite_com.' : '.number_format($prix * $ligne->quantite_com, 0, ',', ' ').' FCFA');
        }

        return $message
            ->line('**Total : '.number_format($commande->montant_c, 0, ',', ' ').' FCFA**')
            ->action('Traiter la commande', route('gerante.commandes.show', $commande))
            ->salutation('SETUP-Gabon');
    }
}
