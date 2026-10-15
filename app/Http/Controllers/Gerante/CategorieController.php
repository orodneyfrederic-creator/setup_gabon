<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategorieController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('produits')
            ->when($request->filled('search'), fn ($q) => $q->where('nom', 'like', '%'.$request->search.'%'))
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        return view('gerante.categories.index', ['categories' => $categories]);
    }

    public function create()
    {
        return view('gerante.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories,nom',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()->route('gerante.categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Category $category)
    {
        return view('gerante.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories,nom,'.$category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()->route('gerante.categories.index')->with('success', 'Catégorie modifiée avec succès.');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        // Dissocier les produits associés avant suppression
        $category->produits()->update(['id_cat' => null]);

        $category->delete();

        return redirect()->route('gerante.categories.index')->with('success', 'Catégorie supprimée avec succès.');
    }
}
