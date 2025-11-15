<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;

class RolController extends Controller
{
    /**
     * GET /api/v1/roles
     * Listar todos los roles
     */
    public function index()
    {
        $roles = Rol::select('id', 'nombre', 'slug', 'descripcion')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'roles' => $roles,
            ],
            'errors' => null,
            'meta' => null,
        ], 200);
    }

    /**
     * GET /api/v1/roles/{id}
     * Mostrar un rol específico
     */
    public function show($id)
    {
        $rol = Rol::select('id', 'nombre', 'slug', 'descripcion', 'permisos')
            ->find($id);

        if (! $rol) {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => [
                    ['field' => 'id', 'message' => 'Rol no encontrado'],
                ],
                'meta' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'rol' => $rol,
            ],
            'errors' => null,
            'meta' => null,
        ], 200);
    }
}
