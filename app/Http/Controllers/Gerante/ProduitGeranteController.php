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
        ], [
            'image.max' => 'L\'image est trop lourde. Elle ne doit pas dépasser 10 Mo.',
            'image.image' => 'Le fichier téléchargé doit être une image valide.',
            'video.max' => 'La vidéo est trop lourde. Elle ne doit pas dépasser 200 Mo.',
            'video.mimes' => 'La vidéo doit être au format MP4, MOV, AVI ou WebM.',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('produits', 'public');
            $validated['image'] = $path;
        } else {
            $validated['image'] = 'produits/default.jpg';
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
            $validated['video'] = $this->convertVideoToBaseline($videoPath);
        }

        $validated = $this->normaliserReglesNegociation($validated, $request);

        Produit::create($validated);

        return redirect()->route('gerante.produits.index')->with('success', 'Produit ajouté avec succès.');
    }

    public function edit(Produit $produit)
    {
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
        ], [
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

        $produit->update($validated);

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
        $produit->delete();

        return redirect()->route('gerante.produits.index')->with('success', 'Produit supprimé avec succès.');
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
