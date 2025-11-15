<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institucion;
use Illuminate\Support\Facades\Validator;

class InstitucionController extends Controller
{
    /**
     * GET /api/v1/instituciones/validar-codigo?codigo=XXXX
     * Valida que el código de institución sea único.
     */
    public function validarCodigo(Request $request)
    {
        $codigo = $request->query('codigo');

        if (!$codigo) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => 'codigo', 'message' => 'El código es obligatorio']
                ],
                'meta' => null,
            ], 422);
        }

        $existe = Institucion::where('codigo', $codigo)->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'codigo' => $codigo,
                'disponible' => !$existe,
            ],
            'errors' => null,
            'meta' => null,
        ], 200);
    }

    /**
     * POST /api/v1/instituciones
     * Crear institución (HU-001).
     */
    public function store(Request $request)
    {
        $usuario = $request->get('usuario_auth');

        // Solo superadmin
        if (!$usuario || !$usuario->rol || $usuario->rol->slug !== 'superadmin') {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => null, 'message' => 'No autorizado']
                ],
                'meta' => null,
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre'         => 'required|string|max:255',
            'codigo'         => 'required|string|max:50|unique:instituciones,codigo',
            'direccion'      => 'nullable|string|max:255',
            'ciudad'         => 'nullable|string|max:100',
            'pais'           => 'nullable|string|max:100',
            'telefono'       => 'nullable|string|max:50',
            'email_contacto' => 'nullable|email|max:255',
            'logo'           => 'nullable|string|max:255',
            'configuracion'  => 'nullable|array',
            'estado'         => 'nullable|in:activa,inactiva',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => collect($validator->errors()->toArray())->map(function ($msg, $field) {
                    return ['field' => $field, 'message' => $msg[0]];
                })->values(),
                'meta' => null,
            ], 422);
        }

        $data = $validator->validated();

        // País por defecto
        if (empty($data['pais'])) {
            $data['pais'] = 'Colombia';
        }

        // Estado por defecto
        if (empty($data['estado'])) {
            $data['estado'] = 'activa';
        }

        // Si viene configuracion como array, Eloquent lo castea a JSON
        $institucion = Institucion::create([
            'nombre'         => $data['nombre'],
            'codigo'         => $data['codigo'],
            'direccion'      => $data['direccion'] ?? null,
            'ciudad'         => $data['ciudad'] ?? null,
            'pais'           => $data['pais'],
            'telefono'       => $data['telefono'] ?? null,
            'email_contacto' => $data['email_contacto'] ?? null,
            'logo'           => $data['logo'] ?? null,
            'configuracion'  => $data['configuracion'] ?? null,
            'estado'         => $data['estado'],
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'institucion' => $institucion,
            ],
            'errors' => null,
            'meta' => null,
        ], 201);
    }

    /**
     * GET /api/v1/instituciones
     * Obtener todas las instituciones.
     */
    public function index(Request $request)
    {
        $usuario = $request->get('usuario_auth');

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => null, 'message' => 'No autorizado']
                ],
                'meta' => null,
            ], 401);
        }

        $instituciones = Institucion::orderBy('id', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => ['instituciones' => $instituciones],
            'errors' => null,
            'meta' => null,
        ], 200);
    }

    /**
     * GET /api/v1/instituciones/{id}
     * Obtener datos de institución.
     */
    public function show($id, Request $request)
    {
        $usuario = $request->get('usuario_auth');

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => null, 'message' => 'No autorizado']
                ],
                'meta' => null,
            ], 401);
        }

        $institucion = Institucion::find($id);

        if (!$institucion) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => 'id', 'message' => 'Institución no encontrada']
                ],
                'meta' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'institucion' => $institucion,
            ],
            'errors' => null,
            'meta' => null,
        ], 200);
    }

    /**
     * PUT /api/v1/instituciones/{id}
     * Actualizar institución (sin permitir cambiar el código).
     */
    public function update($id, Request $request)
    {
        $usuario = $request->get('usuario_auth');

        if (!$usuario || !$usuario->rol || $usuario->rol->slug !== 'superadmin') {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => null, 'message' => 'No autorizado']
                ],
                'meta' => null,
            ], 403);
        }

        $institucion = Institucion::find($id);

        if (!$institucion) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => 'id', 'message' => 'Institución no encontrada']
                ],
                'meta' => null,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre'         => 'sometimes|required|string|max:255',
            // 'codigo' NO SE PUEDE MODIFICAR
            'direccion'      => 'nullable|string|max:255',
            'ciudad'         => 'nullable|string|max:100',
            'pais'           => 'nullable|string|max:100',
            'telefono'       => 'nullable|string|max:50',
            'email_contacto' => 'nullable|email|max:255',
            'logo'           => 'nullable|string|max:255',
            'configuracion'  => 'nullable|array',
            'estado'         => 'nullable|in:activa,inactiva',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => collect($validator->errors()->toArray())->map(function ($msg, $field) {
                    return ['field' => $field, 'message' => $msg[0]];
                })->values(),
                'meta' => null,
            ], 422);
        }

        $data = $validator->validated();

        // NUNCA tocar el código, aunque lo manden desde el frontend
        unset($data['codigo']);

        if (array_key_exists('pais', $data) && empty($data['pais'])) {
            $data['pais'] = 'Colombia';
        }

        $institucion->update($data);

        return response()->json([
            'success' => true,
            'data' => [
                'institucion' => $institucion->fresh(),
            ],
            'errors' => null,
            'meta' => null,
        ], 200);
    }
}
