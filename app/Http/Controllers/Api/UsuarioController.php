<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    /**
     * GET /usuarios
     * Superadmin -> puede ver todos
     * Admin -> solo usuarios de su institución
     */
    public function index(Request $request)
    {
        $auth = $request->get('usuario_auth');

        if ($auth->rol->slug === 'superadmin') {
            $usuarios = Usuario::with('rol', 'institucion')->get();
        } elseif ($auth->rol->slug === 'admin') {
            $usuarios = Usuario::with('rol', 'institucion')
                ->where('institucion_id', $auth->institucion_id)
                ->get();
        } else {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => null, 'message' => 'No autorizado']],
                'meta' => null,
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => ['usuarios' => $usuarios],
            'errors' => null,
            'meta' => null,
        ], 200);
    }

    /**
     * POST /usuarios
     * Crear usuario con password temporal automático
     */
    public function store(Request $request)
    {
        $auth = $request->get('usuario_auth');

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'rol_id' => 'required|exists:roles,id',
            'institucion_id' => 'nullable|exists:instituciones,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => collect($validator->errors()->toArray())
                    ->map(fn ($m, $f) => ['field' => $f, 'message' => $m[0]])
                    ->values(),
                'meta' => null,
            ], 422);
        }

        $data = $validator->validated();
        $rol = Rol::find($data['rol_id']);

        // REGLAS DE NEGOCIO — Creación según rol
        if ($auth->rol->slug === 'admin') {

            if (! in_array($rol->slug, ['teacher', 'student'])) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'errors' => [
                        ['field' => 'rol_id', 'message' => 'No autorizado para crear este tipo de usuario'],
                    ],
                    'meta' => null,
                ], 403);
            }

            // Admin solo crea en su propia institución
            $data['institucion_id'] = $auth->institucion_id;
        }

        // SUPERADMIN puede crear lo que quiera
        if ($auth->rol->slug === 'superadmin') {
            if (empty($data['institucion_id'])) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'errors' => [
                        ['field' => 'institucion_id', 'message' => 'La institución es obligatoria'],
                    ],
                    'meta' => null,
                ], 422);
            }
        }

        // Generar password temporal automático
        $tempPass = Str::random(10);

        $usuario = Usuario::create([
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'password' => Hash::make($tempPass),
            'rol_id' => $data['rol_id'],
            'institucion_id' => $data['institucion_id'],
            'estado' => 'activo',
            'foto_perfil' => null,
            'motivo_suspension' => null,
            'debe_cambiar_password' => 1,
            'email_verified_at' => null,
            'remember_token' => null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'usuario' => $usuario,
                'password_temporal' => $tempPass,
            ],
            'errors' => null,
            'meta' => null,
        ], 201);
    }

    /**
     * GET /usuarios/{id}
     */
    public function show($id, Request $request)
    {
        $auth = $request->get('usuario_auth');
        $usuario = Usuario::with('rol', 'institucion')->find($id);

        if (! $usuario) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => 'id', 'message' => 'Usuario no encontrado']],
                'meta' => null,
            ], 404);
        }

        if ($auth->rol->slug === 'admin' && $usuario->institucion_id !== $auth->institucion_id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => null, 'message' => 'No autorizado']],
                'meta' => null,
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => ['usuario' => $usuario],
            'errors' => null,
            'meta' => null,
        ], 200);
    }

    /**
     * PUT /usuarios/{id}
     */
    public function update($id, Request $request)
    {
        $auth = $request->get('usuario_auth');
        $usuario = Usuario::find($id);

        if (! $usuario) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => 'id', 'message' => 'Usuario no encontrado']],
                'meta' => null,
            ], 404);
        }

        // Admin solo actualiza su institución
        if ($auth->rol->slug === 'admin' && $usuario->institucion_id !== $auth->institucion_id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => null, 'message' => 'No autorizado']],
                'meta' => null,
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:usuarios,email,'.$usuario->id,
            'rol_id' => 'sometimes|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => collect($validator->errors()->toArray())
                    ->map(fn ($m, $f) => ['field' => $f, 'message' => $m[0]])
                    ->values(),
                'meta' => null,
            ], 422);
        }

        $usuario->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => ['usuario' => $usuario->fresh()],
            'errors' => null,
            'meta' => null,
        ], 200);
    }

    /**
     * PATCH /usuarios/{id}/estado
     */
    public function cambiarEstado($id, Request $request)
    {
        $auth = $request->get('usuario_auth');
        $usuario = Usuario::find($id);

        if (! $usuario) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => 'id', 'message' => 'Usuario no encontrado']],
                'meta' => null,
            ], 404);
        }

        if ($auth->rol->slug === 'admin' && $usuario->institucion_id !== $auth->institucion_id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => null, 'message' => 'No autorizado']],
                'meta' => null,
            ], 403);
        }

        $estado = $request->input('estado');

        if (! in_array($estado, ['activo', 'inactivo', 'suspendido'])) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => 'estado', 'message' => 'Estado inválido']],
                'meta' => null,
            ], 422);
        }

        $usuario->estado = $estado;
        $usuario->save();

        return response()->json([
            'success' => true,
            'data' => ['usuario' => $usuario],
            'errors' => null,
            'meta' => null,
        ], 200);
    }

    /**
     * POST /usuarios/{id}/restablecer-password
     */
    public function restablecerPassword($id, Request $request)
    {
        $auth = $request->get('usuario_auth');
        $usuario = Usuario::find($id);

        if (! $usuario) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => 'id', 'message' => 'Usuario no encontrado']],
                'meta' => null,
            ], 404);
        }

        if ($auth->rol->slug === 'admin' && $usuario->institucion_id !== $auth->institucion_id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [['field' => null, 'message' => 'No autorizado']],
                'meta' => null,
            ], 403);
        }

        $tempPass = Str::random(10);

        $usuario->password = Hash::make($tempPass);
        $usuario->debe_cambiar_password = 1;
        $usuario->save();

        return response()->json([
            'success' => true,
            'data' => [
                'usuario' => $usuario,
                'password_temporal' => $tempPass,
            ],
            'errors' => null,
            'meta' => null,
        ], 200);
    }
}
