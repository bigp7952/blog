<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SimpleApiResponse
{
    /**
     * Middleware pour standardiser les réponses API
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Forcer les réponses JSON pour les routes API
        if ($request->is('api/*')) {
            $request->headers->set('Accept', 'application/json');
        }

        $response = $next($request);

        // Si c'est une réponse d'erreur d'authentification, la formater
        if ($response->getStatusCode() === 401) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
                'error' => 'Token manquant ou invalide'
            ], 401);
        }

        return $response;
    }
}
