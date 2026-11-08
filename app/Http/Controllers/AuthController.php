<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // On affiche le formulaire de connexion
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Traiter la connexion

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt Staff authentication first
        if (Auth::guard('staff')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->remember)) {
            $staff = Auth::guard('staff')->user();

            if (! $staff->estActif()) {
                Auth::guard('staff')->logout();

                return back()->withErrors(['email' => 'Ce compte a été désactivé. Contactez la gérante.'])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->route($staff->routeAccueil())->with('success', 'Bienvenue, '.($staff->nom ?: $staff->libelleRole()).' !');
        }

        // Attempt Web User authentication
        if (Auth::guard('web')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'))->with('success', 'Bienvenue !');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis sont incorrects.',
        ])->onlyInput('email');
    }

    // Afficher le formulaire d'inscription
    public function showRegisterForm(Request $request)
    {
        return view('auth.register');
    }

    // Traiter l'inscription
    public function register(Request $request)
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'tel' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nom.required' => 'Le champ nom est obligatoire.',
            'prenom.required' => 'Le champ prénom est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.unique' => 'Cet e-mail est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.']);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'tel' => $request->tel,
            'role' => 'client', // Rôle par défaut
            'password' => $request->password,
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Votre compte à été créé avec succès, félicitations !');
    }

    // Déconnexion
    public function logout(Request $request)
    {
        if (Auth::guard('staff')->check()) {
            Auth::guard('staff')->logout();
        }
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
