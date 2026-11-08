<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Chaque membre du personnel change lui-même son nom affiché et son mot de passe.
 */
class CompteStaffController extends Controller
{
    public function edit(): View
    {
        return view('gerante.compte.edit', ['membre' => Auth::guard('staff')->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $membre = Auth::guard('staff')->user();

        $donnees = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'mot_de_passe_actuel' => ['nullable', 'required_with:nouveau_mot_de_passe', 'string'],
            'nouveau_mot_de_passe' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'mot_de_passe_actuel.required_with' => 'Saisissez votre mot de passe actuel pour en choisir un nouveau.',
        ]);

        if (filled($donnees['nouveau_mot_de_passe'] ?? null)) {
            if (! Hash::check($donnees['mot_de_passe_actuel'], $membre->passwd)) {
                return back()->withErrors(['mot_de_passe_actuel' => 'Le mot de passe actuel est incorrect.']);
            }

            $membre->passwd = $donnees['nouveau_mot_de_passe'];
        }

        $membre->nom = $donnees['nom'];
        $membre->save();

        return back()->with('success', 'Votre compte a été mis à jour.');
    }
}
