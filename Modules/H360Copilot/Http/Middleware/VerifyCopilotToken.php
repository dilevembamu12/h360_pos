<?php

namespace Modules\H360Copilot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyCopilotToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Récupérer le token depuis l'en-tête de la requête
        $token = $request->header('X-H360-Copilot-Token');

        // Récupérer le token attendu depuis le fichier .env
        $expectedToken = env('H360_COPILOT_TOKEN');

        // Vérifier si le token est manquant ou invalide
        if (!$token || $token !== $expectedToken) {
            return response()->json(['error' => 'Non autorisé. Token invalide ou manquant.'], 401);
        }

        return $next($request);
    }
}
