<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use App\Services\ImagePartageService;
use App\Services\NegociationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    public function __construct(
        protected NegociationService $negociationService
    ) {}

    /**
     * Tris proposés au client : clé d'URL => libellé.
     *
     * @var array<string, string>
     */
    public const TRIS = [
        'recent' => 'Nouveautés',
        'prix_croissant' => 'Prix croissant',
        'prix_decroissant' => 'Prix décroissant',
        'nom' => 'Nom (A → Z)',
    ];

    public function index(Request $request)
    {
        // Lecture tolérante des filtres : une valeur invalide dans l'URL est ignorée plutôt que de bloquer la page
        $entierPositif = fn (string $cle) => filter_var($request->query($cle), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) ?: null;
        $filtres = [
            'marque' => $entierPositif('marque'),
            'prix_min' => filter_var($request->query('prix_min'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false ? null : (int) $request->query('prix_min'),
            'prix_max' => filter_var($request->query('prix_max'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false ? null : (int) $request->query('prix_max'),
            'tri' => array_key_exists((string) $request->query('tri'), self::TRIS) ? $request->query('tri') : 'recent',
        ];

        // On récupère les Catégories pour la section "Catégories d'abord"
        $categories = Category::all();

        // Marques réellement présentes au catalogue, pour le filtre
        $marques = Marque::whereHas('produits')->orderBy('nom')->get(['id', 'nom']);

        // Requête de recherches et filtrage des produits
        $query = Produit::with(['category', 'marque']);

        if ($request->filled('search')) {
            // Regroupé entre parenthèses pour que le filtre catégorie s'applique aux deux conditions
            $query->where(function ($sousRequete) use ($request) {
                $sousRequete->where('designation', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('id_cat', $request->category);
        }

        if (filled($filtres['marque'] ?? null)) {
            $query->where('id_m', $filtres['marque']);
        }

        // Fourchette de prix : les bornes inversées par erreur sont remises dans l'ordre
        $prixMin = $filtres['prix_min'] ?? null;
        $prixMax = $filtres['prix_max'] ?? null;
        if ($prixMin !== null && $prixMax !== null && $prixMin > $prixMax) {
            [$prixMin, $prixMax] = [$prixMax, $prixMin];
        }
        if ($prixMin !== null) {
            $query->where('prix_unitaire', '>=', $prixMin);
        }
        if ($prixMax !== null) {
            $query->where('prix_unitaire', '<=', $prixMax);
        }

        if ($request->boolean('en_stock')) {
            $query->where('quantite', '>', 0);
        }

        match ($filtres['tri'] ?? 'recent') {
            'prix_croissant' => $query->orderBy('prix_unitaire')->orderBy('id'),
            'prix_decroissant' => $query->orderByDesc('prix_unitaire')->orderBy('id'),
            'nom' => $query->orderBy('designation'),
            default => $query->latest()->orderByDesc('id'),
        };

        $produits = $query->paginate(12)->withQueryString();
        $tris = self::TRIS;

        return view('home', compact('produits', 'categories', 'marques', 'tris', 'prixMin', 'prixMax'));
    }

    public function show($id)
    {
        $produit = Produit::with(['category', 'marque', 'images'])->findOrFail($id);
        $categories = Category::all(); // On les passe au cas où le layout en a besoin
        $estNegociable = $this->negociationService->isNegociable($produit);

        $avisPublies = $produit->avis()->publies()->with('user')->latest()->get();
        $client = Auth::guard('web')->user();
        $monAvis = $client ? $produit->avis()->where('id_ut', $client->id)->first() : null;
        $peutDonnerAvis = $client !== null && ($monAvis?->estPublie() ?? true) && $client->commandeLivreeContenant($produit) !== null;
        $produitsSimilaires = $produit->produitsSimilaires();

        return view('produits.show', compact(
            'produit', 'categories', 'estNegociable', 'avisPublies', 'monAvis', 'peutDonnerAvis', 'produitsSimilaires'
        ));
    }

    /**
     * Image d'aperçu pour le partage du lien produit (WhatsApp, Facebook) : 1200 × 630, allégée.
     */
    public function imagePartage(int $id, ImagePartageService $imagePartageService)
    {
        $produit = Produit::findOrFail($id);
        $vignette = $imagePartageService->cheminVignette($produit);

        if ($vignette) {
            return response()->file(Storage::disk('public')->path($vignette), [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=604800',
            ]);
        }

        // Sans vignette (pas d'image ou GD absent) : image d'origine, ou visuel générique de la boutique
        return $produit->image
            ? redirect(asset('storage/'.$produit->image))
            : redirect(asset('img/partage-setup.jpg'));
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
