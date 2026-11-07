<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Produit;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Affiche la page À propos de SETUP-Gabon.
     */
    public function about(): View
    {
        return view('about');
    }

    /**
     * Conditions générales de vente.
     */
    public function cgv(): View
    {
        return view('legal.cgv', ['boutique' => config('boutique')]);
    }

    /**
     * Mentions légales : identification de l'éditeur du site (loi n°025/2021, art. 23 et 31).
     */
    public function mentionsLegales(): View
    {
        return view('legal.mentions', ['boutique' => config('boutique')]);
    }

    /**
     * Politique de confidentialité (loi n°001/2011 modifiée par la loi n°025/2023).
     */
    public function confidentialite(): View
    {
        return view('legal.confidentialite', ['boutique' => config('boutique')]);
    }

    /**
     * Plan du site pour les moteurs de recherche.
     */
    public function sitemap(): Response
    {
        $produits = Produit::query()->select(['id', 'updated_at'])->latest('updated_at')->get();
        $categories = Category::query()->select(['id', 'updated_at'])->get();

        return response()
            ->view('sitemap', compact('produits', 'categories'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Retours, rétractation et garanties, en version pratique.
     */
    public function retoursGaranties(): View
    {
        return view('legal.retours', ['boutique' => config('boutique')]);
    }
}
