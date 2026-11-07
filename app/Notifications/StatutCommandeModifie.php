<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Informe le client de l'avancement de sa commande (confirmée, livrée, annulée).
 */
class StatutCommandeModifie extends Notification
{
    use Queueable;

    /** @var list<string> */
    public const STATUTS_NOTIFIES = ['confirmee', 'livree', 'annulee'];

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
        $commande = $this->commande;
        $delai = config('boutique.livraison.delai_heures');

        [$sujet, $texte] = match ($commande->statut) {
            'confirmee' => [
                'Commande n° '.$commande->id.' confirmée',
                'Bonne nouvelle : la boutique a confirmé votre commande. Elle vous sera livrée sous '.$delai.' h à l\'adresse indiquée, ou vous pouvez la retirer en boutique.',
            ],
            'livree' => [
                'Commande n° '.$commande->id.' livrée',
                'Votre commande a été livrée. Votre garantie boutique et votre délai de '.config('boutique.retractation_jours').' jours pour changer d\'avis commencent aujourd\'hui. Merci pour votre confiance !',
            ],
            'annulee' => [
                'Commande n° '.$commande->id.' annulée',
                'Votre commande a été annulée. Si vous aviez déjà payé, la boutique vous rembourse par le même moyen de paiement. Pour toute question, répondez-nous sur WhatsApp.',
            ],
            default => [
                'Commande n° '.$commande->id.' mise à jour',
                'Le statut de votre commande a été mis à jour.',
            ],
        };

        return (new MailMessage)
            ->subject($sujet.' – SETUP-Gabon')
            ->greeting('Bonjour '.$notifiable->prenom.',')
            ->line($texte)
            ->action('Voir ma commande', route('orders.show', $commande->id))
            ->salutation('L\'équipe SETUP-Gabon · '.config('boutique.telephone'));
    }
}
