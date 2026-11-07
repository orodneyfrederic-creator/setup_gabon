<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Marque;
use App\Models\Produit;
use App\Services\NegociationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitGeranteController extends Controller
{
    /** Nombre maximal de photos en plus de la photo principale. */
    private const PHOTOS_SUPPLEMENTAIRES_MAX = 8;

    public function index(Request $request)
    {
        $query = Produit::with(['category', 'marque']);

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where('designation', 'like', "%{$search}%")
                ->orWhere('reference', 'like', "%{$search}%");
        }

        $produits = $query->latest()->paginate(15);

        return view('gerante.produits.index', compact('produits'));
    }

    public function create()
    {
        $categories = Category::all();
        $marques = Marque::all();

        return view('gerante.produits.create', compact('categories', 'marques'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|unique:produits',
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_unitaire' => 'required|numeric|min:0',
            'prix_achat' => 'nullable|integer|min:0',
            'quantite' => 'required|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
            'id_cat' => 'required|exists:categories,id',
            'id_m' => 'required|exists:marques,id',
            'negociable' => 'nullable|in:inherit,0,1',
            'remise_max_pct' => 'nullable|integer|min:0|max:50',
            'nb_tours_max' => 'nullable|integer|min:1|max:10',
            'decision_manuelle' => 'nullable|boolean',
            'image' => 'nullable|image|max:10240',
            'video' => 'nullable|mimes:mp4,mov,avi,webm|max:204800',
            'photos' => 'nullable|array|max:'.self::PHOTOS_SUPPLEMENTAIRES_MAX,
            'photos.*' => 'image|max:10240',
            'caracteristiques' => 'nullable|array|max:30',
            'caracteristiques.*.libelle' => 'nullable|string|max:60',
            'caracteristiques.*.valeur' => 'nullable|string|max:255',
        ], [
            'photos.max' => 'Vous pouvez ajouter au plus '.self::PHOTOS_SUPPLEMENTAIRES_MAX.' photos supplémentaires.',
            'photos.*.image' => 'Chaque photo doit être une image valide (JPG, PNG ou WEBP).',
            'photos.*.max' => 'Chaque photo doit peser 10 Mo au maximum.',
            'image.max' => 'L\'image est trop lourde. Elle ne doit pas dépasser 10 Mo.',
            'image.image' => 'Le fichier téléchargé doit être une image valide.',
            'video.max' => 'La vidéo est trop lourde. Elle ne doit pas dépasser 200 Mo.',
            'video.mimes' => 'La vidéo doit être au format MP4, MOV, AVI ou WebM.',
        ]);

        // Sans photo, le champ reste vide : les vues affichent alors un visuel neutre
        $validated['image'] = $request->hasFile('image')
            ? $request->file('image')->store('produits', 'public')
            : null;

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
            $validated['video'] = $this->convertVideoToBaseline($videoPath);
        }

        $validated = $this->normaliserReglesNegociation($validated, $request);
        $validated['caracteristiques'] = $this->normaliserCaracteristiques($request);
        unset($validated['photos']);

        $produit = Produit::create($validated);
        $this->ajouterPhotos($produit, $request);

        return redirect()->route('gerante.produits.index')->with('success', 'Produit ajouté avec succès.');
    }

    public function edit(Produit $produit)
    {
        $produit->load('images');
        $categories = Category::all();
        $marques = Marque::all();

        return view('gerante.produits.edit', compact('produit', 'categories', 'marques'));
    }

    public function update(Request $request, Produit $produit)
    {
        $validated = $request->validate([
            'reference' => 'required|string|unique:produits,reference,'.$produit->id,
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_unitaire' => 'required|numeric|min:0',
            'prix_achat' => 'nullable|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
            'id_cat' => 'required|exists:categories,id',
            'id_m' => 'required|exists:marques,id',
            'negociable' => 'nullable|in:inherit,0,1',
            'remise_max_pct' => 'nullable|integer|min:0|max:50',
            'nb_tours_max' => 'nullable|integer|min:1|max:10',
            'decision_manuelle' => 'nullable|boolean',
            'image' => 'nullable|image|max:10240',
            'video' => 'nullable|mimes:mp4,mov,avi,webm|max:204800',
            'photos' => 'nullable|array|max:'.self::PHOTOS_SUPPLEMENTAIRES_MAX,
            'photos.*' => 'image|max:10240',
            'caracteristiques' => 'nullable|array|max:30',
            'caracteristiques.*.libelle' => 'nullable|string|max:60',
            'caracteristiques.*.valeur' => 'nullable|string|max:255',
        ], [
            'photos.max' => 'Vous pouvez ajouter au plus '.self::PHOTOS_SUPPLEMENTAIRES_MAX.' photos supplémentaires.',
            'photos.*.image' => 'Chaque photo doit être une image valide (JPG, PNG ou WEBP).',
            'photos.*.max' => 'Chaque photo doit peser 10 Mo au maximum.',
            'image.max' => 'L\'image est trop lourde. Elle ne doit pas dépasser 10 Mo.',
            'image.image' => 'Le fichier téléchargé doit être une image valide.',
            'video.max' => 'La vidéo est trop lourde. Elle ne doit pas dépasser 200 Mo.',
            'video.mimes' => 'La vidéo doit être au format MP4, MOV, AVI ou WebM.',
        ]);

        if ($request->hasFile('image')) {
            if ($produit->image && $produit->image !== 'produits/default.jpg') {
                Storage::disk('public')->delete($produit->image);
            }
            $path = $request->file('image')->store('produits', 'public');
            $validated['image'] = $path;
        }

        if ($request->hasFile('video')) {
            if ($produit->video) {
                Storage::disk('public')->delete($produit->video);
            }
            $videoPath = $request->file('video')->store('videos', 'public');
            $validated['video'] = $this->convertVideoToBaseline($videoPath);
        }

        $validated = $this->normaliserReglesNegociation($validated, $request);
        $validated['caracteristiques'] = $this->normaliserCaracteristiques($request);
        unset($validated['photos']);

        $produit->update($validated);
        $this->supprimerPhotos($produit, $request->input('supprimer_photos', []));
        $this->ajouterPhotos($produit, $request);

        return redirect()->route('gerante.produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Produit $produit)
    {
        if ($produit->image && $produit->image !== 'produits/default.jpg') {
            Storage::disk('public')->delete($produit->image);
        }
        if ($produit->video) {
            Storage::disk('public')->delete($produit->video);
        }
        Storage::disk('public')->delete($produit->images()->pluck('chemin')->all());
        $produit->delete();

        return redirect()->route('gerante.produits.index')->with('success', 'Produit supprimé avec succès.');
    }

    /**
     * Enregistre les photos supplémentaires envoyées, à la suite de celles existantes, dans la limite autorisée.
     */
    private function ajouterPhotos(Produit $produit, Request $request): void
    {
        $photos = $request->file('photos', []);
        if (empty($photos)) {
            return;
        }

        $positionSuivante = (int) $produit->images()->max('position') + 1;
        $placesRestantes = self::PHOTOS_SUPPLEMENTAIRES_MAX - $produit->images()->count();

        foreach (array_slice($photos, 0, max(0, $placesRestantes)) as $photo) {
            $produit->images()->create([
                'chemin' => $photo->store('produits', 'public'),
                'position' => $positionSuivante++,
            ]);
        }
    }

    /**
     * Supprime les photos supplémentaires cochées (uniquement celles de ce produit).
     *
     * @param  array<int, mixed>  $identifiants
     */
    private function supprimerPhotos(Produit $produit, array $identifiants): void
    {
        $photos = $produit->images()->whereIn('id', array_map('intval', $identifiants))->get();

        foreach ($photos as $photo) {
            Storage::disk('public')->delete($photo->chemin);
            $photo->delete();
        }
    }

    /**
     * Fiche technique : supprime les lignes incomplètes et les espaces superflus.
     *
     * @return list<array{libelle: string, valeur: string}>|null
     */
    private function normaliserCaracteristiques(Request $request): ?array
    {
        $lignes = collect($request->input('caracteristiques', []))
            ->map(fn ($ligne) => [
                'libelle' => trim((string) ($ligne['libelle'] ?? '')),
                'valeur' => trim((string) ($ligne['valeur'] ?? '')),
            ])
            ->filter(fn ($ligne) => $ligne['libelle'] !== '' && $ligne['valeur'] !== '')
            ->values()
            ->all();

        return $lignes === [] ? null : $lignes;
    }

    /**
     * Convertit les champs de négociation du formulaire en valeurs de colonne.
     * "inherit" (ou vide) => null : le produit hérite de la règle de sa catégorie.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normaliserReglesNegociation(array $validated, Request $request): array
    {
        $validated['negociable'] = match ($request->input('negociable')) {
            '1' => true,
            '0' => false,
            default => null,
        };
        $validated['nb_tours_max'] = $validated['nb_tours_max'] ?? NegociationService::DEFAULT_NB_TOURS_MAX;
        $validated['decision_manuelle'] = $request->boolean('decision_manuelle');

        return $validated;
    }

    /**
     * Re-encode a stored video to H264 Baseline profile + faststart using ffmpeg.
     * This ensures compatibility with Chrome/Edge on Intel Iris Xe (hardware acceleration).
     * Falls back to the original path if ffmpeg is unavailable or fails.
     *
     * @param  string  $storagePath  Relative path inside storage/app/public/ (e.g. "videos/foo.mp4")
     * @return string Same relative path (file is overwritten in place)
     */
    private function convertVideoToBaseline(string $storagePath): string
    {
        $fullPath = storage_path('app/public/'.$storagePath);
        $tmpPath = $fullPath.'.baseline_tmp.mp4';

        $cmd = sprintf(
            'ffmpeg -y -i %s -c:v libx264 -profile:v baseline -level 3.0 -pix_fmt yuv420p -refs 1 -bf 0 -preset fast -c:a aac -b:a 128k -movflags +faststart %s 2>&1',
            escapeshellarg($fullPath),
            escapeshellarg($tmpPath)
        );

        exec($cmd, $output, $code);

        if ($code === 0 && file_exists($tmpPath) && filesize($tmpPath) > 0) {
            rename($tmpPath, $fullPath);
        } elseif (file_exists($tmpPath)) {
            @unlink($tmpPath);
        }

        return $storagePath;
    }
}
