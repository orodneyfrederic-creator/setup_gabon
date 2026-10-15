<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur connecté possède le rôle requis.
     *
     * @param  string  $role  Le rôle attendu (ex: 'gerante', 'admin', 'client')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user('staff') ?? $request->user();

        if (! $user || $user->role !== $role) {
            abort(403, 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}
