<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RegleMotDePasse;
use Illuminate\View\View;

/**
 * Réinitialisation du mot de passe des comptes clients, par lien envoyé à l'adresse e-mail du compte.
 */
class MotDePasseOublieController extends Controller
{
    public function demande(): View
    {
        return view('auth.mot-de-passe-oublie');
    }

    /**
     * Envoie le lien de réinitialisation. La réponse est identique que l'adresse existe ou non,
     * pour ne pas révéler quels e-mails ont un compte.
     */
    public function envoyerLien(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']], [
            'email.required' => 'Indiquez l\'adresse e-mail de votre compte.',
            'email.email' => 'Cette adresse e-mail n\'est pas valide.',
        ]);

        $statut = Password::broker('users')->sendResetLink($request->only('email'));

        if ($statut === Password::RESET_THROTTLED) {
            return back()->withInput()->withErrors(['email' => 'Un lien vient déjà d\'être envoyé. Patientez une minute avant de recommencer.']);
        }

        return back()->with('success', 'Si un compte existe avec cette adresse, un lien de réinitialisation vient d\'y être envoyé. Il est valable 60 minutes.');
    }

    public function formulaire(Request $request, string $token): View
    {
        return view('auth.nouveau-mot-de-passe', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function enregistrer(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', RegleMotDePasse::min(8)],
        ], [
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $statut = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $utilisateur, string $motDePasse) {
                // Le cast « hashed » du modèle chiffre le mot de passe à l'enregistrement
                $utilisateur->forceFill([
                    'password' => $motDePasse,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($utilisateur));
            }
        );

        if ($statut === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Votre mot de passe a été modifié. Vous pouvez vous connecter.');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Ce lien de réinitialisation est invalide ou a expiré. Demandez-en un nouveau.',
        ]);
    }
}
