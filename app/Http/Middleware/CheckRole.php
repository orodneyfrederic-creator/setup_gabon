<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Réserve une route au personnel de la boutique ayant l'un des rôles indiqués.
     * Seuls les comptes du personnel (table staff) sont acceptés : un compte client n'y accède jamais.
     * Un compte désactivé est déconnecté immédiatement, même si sa session était encore ouverte.
     *
     * Exemple : ->middleware('role:gerante,vendeur')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $membre = Auth::guard('staff')->user();

        if ($membre && ! $membre->estActif()) {
            Auth::guard('staff')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Ce compte a été désactivé. Contactez la gérante.']);
        }

        if (! $membre || ! in_array($membre->role, $roles, true)) {
            abort(403, 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}
