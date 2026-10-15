<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        // On récupère les Catégories pour la section "Catégories d'abord"
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

        $produits = $query->latest()->paginate(12)->withQueryString();

        return view('home', compact('produits', 'categories'));
    }

    public function show($id)
    {
        $produit = Produit::with(['category', 'marque'])->findOrFail($id);
        $categories = Category::all(); // On les passe au cas où le layout en a besoin

        return view('produits.show', compact('produit', 'categories'));
    }

    /**
     * Stream la vidéo d'un produit directement via PHP.
     * Contourne les problèmes de junction Windows / symlinks Apache.
     */
    public function streamVideo(int $id)
    {
        $produit = Produit::findOrFail($id);

        if (! $produit->video) {
            abort(404, 'Aucune vidéo associée à ce produit.');
        }

        $path = storage_path('app/public/'.$produit->video);

        if (! file_exists($path)) {
            abort(404, 'Fichier vidéo introuvable.');
        }

        $mimeMap = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/mp4',
            'ogg' => 'video/ogg',
        ];
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = $mimeMap[$ext] ?? 'video/mp4';

        $response = response()->file($path, [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
        ]);

        $response->prepare(request());

        return $response;
    }

    public function categories()
    {
        $categories = Category::with('produits')->get();

        return view('categories.index', compact('categories'));
    }
}
