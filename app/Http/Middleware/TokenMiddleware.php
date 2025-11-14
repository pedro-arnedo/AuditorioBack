<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Usuario;

class TokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Leer token desde Authorization: Bearer XXXXX
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => null, 'message' => 'Token no proporcionado']
                ],
                'meta' => null
            ], 401);
        }

        // Buscar usuario por token
        $usuario = Usuario::where('remember_token', $token)->first();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => null, 'message' => 'Token inválido']
                ],
                'meta' => null
            ], 401);
        }

        // Validar estado del usuario
        if ($usuario->estado === 'inactivo') {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => null, 'message' => 'Usuario inactivo']
                ],
                'meta' => null
            ], 403);
        }

        if ($usuario->estado === 'suspendido') {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => null, 'message' => 'Usuario suspendido']
                ],
                'meta' => null
            ], 403);
        }

        // Guardar usuario autenticado en request
        $request->merge(['usuario_auth' => $usuario]);

        return $next($request);
    }
}
