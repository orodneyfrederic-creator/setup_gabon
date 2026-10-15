<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Produit;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // On récupère les Ctégories pour le menu latéral
        $categories = Category::all();

        // Requête de recherches et filtrage des produits
        $query = Produit::with(['category', 'marque']);

        if ($request->filled('search')) {
            $query->where('designation', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('category')) {
            $query->where('id_cat', $request->category);
        }

        $produits = $query->latest()->paginate(12);

        return view('home', compact('produits', 'categories'));

    }
}
