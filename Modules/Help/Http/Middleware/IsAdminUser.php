<?php

namespace Modules\Help\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdminUser
{
    /**
     * Gère une requête entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // **CORRECTION : Utilise le système de permission standard de l'application**
        // Cela vérifie si l'utilisateur a la permission 'superadmin',
        // ce qui est la méthode standard et plus fiable.
        if (auth()->user()->can('superadmin')) {
            return $next($request);
        }

        // Si l'utilisateur n'a pas la permission, on retourne une erreur 403.
        abort(403, 'Unauthorized action.');
    }
}