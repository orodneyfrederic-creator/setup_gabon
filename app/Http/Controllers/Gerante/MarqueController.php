<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Marque;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MarqueController extends Controller
{
    public function index(Request $request): View
    {
        $marques = Marque::query()
            ->withCount('produits')
            ->when($request->filled('search'), fn ($q) => $q->where('nom', 'like', '%'.$request->search.'%'))
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        return view('gerante.marques.index', ['marques' => $marques]);
    }

    public function create(): View
    {
        return view('gerante.marques.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:marques,nom',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('marques', 'public');
        }

        Marque::create($validated);

        return redirect()->route('gerante.marques.index')->with('success', 'Marque créée avec succès.');
    }

    public function edit(Marque $marque): View
    {
        return view('gerante.marques.edit', compact('marque'));
    }

    public function update(Request $request, Marque $marque): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:marques,nom,'.$marque->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        if ($request->hasFile('logo')) {
            if ($marque->logo) {
                Storage::disk('public')->delete($marque->logo);
            }
            $validated['logo'] = $request->file('logo')->store('marques', 'public');
        }

        $marque->update($validated);

        return redirect()->route('gerante.marques.index')->with('success', 'Marque modifiée avec succès.');
    }

    public function destroy(Marque $marque): RedirectResponse
    {
        if ($marque->produits()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette marque car elle contient des produits.');
        }

        if ($marque->logo) {
            Storage::disk('public')->delete($marque->logo);
        }

        $marque->delete();

        return redirect()->route('gerante.marques.index')->with('success', 'Marque supprimée avec succès.');
    }
}
