<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware
{
    /**
     * Cette méthode gère une requête entrante. Elle tente d'authentifier l'utilisateur à l'aide d'un token JWT.
     * Si le token est invalide, a expiré ou est absent, une réponse appropriée est renvoyée.
     *
     * @param  \Illuminate\Http\Request  $request  La requête entrante
     * @param  \Closure  $next  Le prochain middleware dans la pile
     * @return mixed  La réponse à la requête
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Tente d'authentifier l'utilisateur à l'aide du token JWT
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            // Gère les exceptions spécifiques liées au token JWT
            // @phpstan-ignore-next-line
            if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException) {
                // Le token est invalide
                return response()->json(['status' => 'Token est invalide']);
            // @phpstan-ignore-next-line
            } else if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException) {
                // Le token a expiré
                return response()->json(['status' => 'Token a expiré']);
            } else {
                // Le token est absent
                return response()->json(['status' => 'Authorization Token non trouvé']);
            }

        }
        // Si aucune exception n'a été levée, passe à la prochaine middleware
        return $next($request);
    }

}
