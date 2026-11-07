<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Accusé de réception envoyé au client dès l'enregistrement de sa commande
 * (loi n°025/2021, art. 52 et 56 : accusé de réception et confirmation écrite).
 */
class CommandeEnregistree extends Notification
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
        $commande = $this->commande->loadMissing('lignesCommandes.produit');

        $message = (new MailMessage)
            ->subject('Commande n° '.$commande->id.' enregistrée – SETUP-Gabon')
            ->greeting('Bonjour '.$notifiable->prenom.',')
            ->line('Votre commande n° '.$commande->id.' a bien été enregistrée le '.$commande->date_c->translatedFormat('d/m/Y à H\hi').'.');

        foreach ($commande->lignesCommandes as $ligne) {
            $prix = $ligne->prix_final_unitaire ?? $ligne->prix_unitaire_com;
            $message->line('• '.($ligne->produit?->designation ?? 'Produit').' × '.$ligne->quantite_com.' : '.number_format($prix * $ligne->quantite_com, 0, ',', ' ').' FCFA');
        }

        return $message
            ->line('**Total : '.number_format($commande->montant_c, 0, ',', ' ').' FCFA** (livraison gratuite à Libreville).')
            ->line('Livraison : '.$commande->adresse_livraison.'. Paiement à la réception : '.$commande->libelleMethodePaiement().'.')
            ->line('La boutique vous recontacte pour confirmer la disponibilité. La livraison intervient sous '.config('boutique.livraison.delai_heures').' h après cette confirmation.')
            ->action('Suivre ma commande', route('orders.show', $commande->id))
            ->line('Vous disposez de '.config('boutique.retractation_jours').' jours après réception pour changer d\'avis. Conditions de vente acceptées le '.$commande->conditions_acceptees_at?->translatedFormat('d/m/Y').' : '.route('legal.cgv'))
            ->salutation('L\'équipe SETUP-Gabon · '.config('boutique.telephone'));
    }
}
