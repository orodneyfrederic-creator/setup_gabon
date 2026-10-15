<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * Affiche la page À propos de SETUP-Gabon.
     */
    public function about()
    {
        return view('about');
    }
}
