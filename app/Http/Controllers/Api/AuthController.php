<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\IntentoLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Buscar usuario
        $usuario = Usuario::where('email', $request->email)->first();

        // Registrar intento de login
        IntentoLogin::create([
            'email' => $request->email,
            'exitoso' => $usuario && Hash::check($request->password, $usuario->password),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'mensaje' => $usuario ? 'Usuario encontrado' : 'Usuario no encontrado'
        ]);

        // Validar existencia
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => 'email', 'message' => 'Credenciales incorrectas']
                ],
                'meta' => null
            ], 401);
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => 'password', 'message' => 'Credenciales incorrectas']
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

        // Generar token manual (sin Sanctum)
        $token = Str::random(60);

        // Guardar token en BD
        $usuario->remember_token = $token;
        $usuario->fecha_ultimo_acceso = now();
        $usuario->save();

        return response()->json([
            'success' => true,
            'data' => [
                'usuario' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'email' => $usuario->email,
                    'rol' => $usuario->rol->nombre ?? null,
                    'token' => $token
                ]
            ],
            'errors' => null,
            'meta' => null
        ], 200);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => null, 'message' => 'Token no enviado']
                ],
                'meta' => null
            ], 400);
        }

        // Buscar usuario con ese token
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

        // Invalidar token
        $usuario->remember_token = null;
        $usuario->save();

        return response()->json([
            'success' => true,
            'data' => ['message' => 'Sesión cerrada correctamente'],
            'errors' => null,
            'meta' => null
        ], 200);
    }
}
