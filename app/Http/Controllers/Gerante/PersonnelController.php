<?php

namespace App\Http\Controllers\Gerante;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Comptes du personnel : la gérante crée les comptes vendeurs, les désactive au départ d'un employé
 * et réinitialise un mot de passe oublié. La boutique garde toujours au moins une gérante active.
 */
class PersonnelController extends Controller
{
    public function index(): View
    {
        $membres = Staff::orderByDesc('actif')->orderBy('role')->orderBy('email')->get();

        return view('gerante.personnel.index', compact('membres'));
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:staff,email'],
            'role' => ['required', Rule::in(array_keys(Staff::ROLES))],
            'mot_de_passe' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'email.unique' => 'Un compte du personnel utilise déjà cette adresse.',
        ]);

        Staff::create([
            'nom' => $donnees['nom'],
            'email' => strtolower($donnees['email']),
            'role' => $donnees['role'],
            'passwd' => $donnees['mot_de_passe'],
            'actif' => true,
        ]);

        return back()->with('success', 'Compte créé. Communiquez le mot de passe en main propre et demandez de le changer à la première connexion.');
    }

    public function update(Request $request, Staff $membre): RedirectResponse
    {
        $donnees = $request->validate([
            'role' => ['required', Rule::in(array_keys(Staff::ROLES))],
            'actif' => ['required', 'boolean'],
            'mot_de_passe' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $resteGerante = $donnees['role'] === Staff::ROLE_GERANTE && (bool) $donnees['actif'];

        if (! $resteGerante && $membre->estGerante() && $this->estDerniereGeranteActive($membre)) {
            return back()->with('error', 'La boutique doit garder au moins une gérante active : créez ou réactivez une autre gérante d\'abord.');
        }

        if ($membre->is(Auth::guard('staff')->user()) && ! $resteGerante) {
            return back()->with('error', 'Vous ne pouvez pas retirer votre propre accès gérante.');
        }

        $membre->fill(['role' => $donnees['role'], 'actif' => (bool) $donnees['actif']]);

        if (filled($donnees['mot_de_passe'] ?? null)) {
            $membre->passwd = $donnees['mot_de_passe'];
        }

        $membre->save();

        return back()->with('success', 'Compte de '.($membre->nom ?: $membre->email).' mis à jour.');
    }

    private function estDerniereGeranteActive(Staff $membre): bool
    {
        return ! Staff::where('role', Staff::ROLE_GERANTE)
            ->where('actif', true)
            ->whereKeyNot($membre->getKey())
            ->exists();
    }
}
