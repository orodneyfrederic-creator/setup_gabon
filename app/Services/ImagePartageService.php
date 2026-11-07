<?php

namespace App\Services;

use App\Models\Produit;
use Illuminate\Support\Facades\Storage;

/**
 * Prépare l'image d'aperçu affichée quand un lien produit est partagé (WhatsApp, Facebook).
 *
 * WhatsApp n'affiche pas d'aperçu au-delà d'environ 300 Ko et Facebook recommande 1200 × 630 :
 * la photo du produit est donc réduite et centrée sur un fond clair, puis mise en cache.
 */
class ImagePartageService
{
    public const LARGEUR = 1200;

    public const HAUTEUR = 630;

    private const QUALITE_JPEG = 80;

    /**
     * Chemin (disque « public ») de la vignette de partage, générée si besoin.
     * Retourne null si le produit n'a pas d'image ou si l'extension GD est indisponible.
     */
    public function cheminVignette(Produit $produit): ?string
    {
        if (! $produit->image || ! Storage::disk('public')->exists($produit->image)) {
            return null;
        }

        // Le nom dépend de l'image source : une nouvelle photo produit une nouvelle vignette
        $cheminVignette = 'partage/produit-'.$produit->id.'-'.substr(md5($produit->image), 0, 10).'.jpg';

        if (Storage::disk('public')->exists($cheminVignette)) {
            return $cheminVignette;
        }

        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $source = @imagecreatefromstring(Storage::disk('public')->get($produit->image));
        if ($source === false) {
            return null;
        }

        $vignette = imagecreatetruecolor(self::LARGEUR, self::HAUTEUR);
        imagefill($vignette, 0, 0, imagecolorallocate($vignette, 246, 247, 250));

        $largeurSource = imagesx($source);
        $hauteurSource = imagesy($source);
        $marge = 30;
        $echelle = min((self::LARGEUR - 2 * $marge) / $largeurSource, (self::HAUTEUR - 2 * $marge) / $hauteurSource);
        $largeurCible = (int) round($largeurSource * $echelle);
        $hauteurCible = (int) round($hauteurSource * $echelle);

        imagecopyresampled(
            $vignette, $source,
            (int) ((self::LARGEUR - $largeurCible) / 2), (int) ((self::HAUTEUR - $hauteurCible) / 2),
            0, 0,
            $largeurCible, $hauteurCible,
            $largeurSource, $hauteurSource
        );

        ob_start();
        imagejpeg($vignette, null, self::QUALITE_JPEG);
        $contenu = ob_get_clean();

        imagedestroy($source);
        imagedestroy($vignette);

        Storage::disk('public')->put($cheminVignette, $contenu);

        return $cheminVignette;
    }
}
