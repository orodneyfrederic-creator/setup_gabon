<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Afficher la page profil du client (Mon Compte).
     */
    public function profile()
    {
        $user = Auth::user() ?? Auth::guard('staff')->user();

        return view('account.profile', compact('user'));
    }

    /**
     * Mettre à jour les informations du profil client.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'tel' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'e-mail est obligatoire.',
            'email.unique' => 'Cet e-mail est déjà utilisé.',
            'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if ($request->filled('new_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->tel = $request->tel;
        $user->save();

        return back()->with('success', 'Vos informations personnelles ont été mises à jour avec succès.');
    }

    /**
     * Afficher l'historique des commandes du client.
     */
    public function orders()
    {
        $user = Auth::user();

        $commandes = Commande::where('id_ut', $user?->id)
            ->with(['lignesCommandes.produit'])
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('commandes'));
    }

    /**
     * Afficher le détail d'une commande spécifique.
     */
    public function showOrder($id)
    {
        $user = Auth::user();

        $commande = Commande::where('id_ut', $user?->id)
            ->with(['lignesCommandes.produit', 'paiement'])
            ->findOrFail($id);

        return view('account.order_detail', compact('commande'));
    }
}
