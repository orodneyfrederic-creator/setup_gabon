<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\View\View;

/**
 * Suivi d'une commande sans compte par son lien secret.
 * Le lien donne accès à la commande sans mot de passe : il n'est communiqué qu'au client
 * (page de confirmation et message WhatsApp).
 */
class SuiviCommandeController extends Controller
{
    public function show(string $jeton): View
    {
        $commande = $this->commandeParJeton($jeton);

        return view('account.order_detail', [
            'commande' => $commande,
            'vueInvitee' => true,
        ]);
    }

    private function commandeParJeton(string $jeton): Commande
    {
        return Commande::where('jeton_suivi', $jeton)
            ->with('lignesCommandes.produit')
            ->firstOrFail();
    }
}
