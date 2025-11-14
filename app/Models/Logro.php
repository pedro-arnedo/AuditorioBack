<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logro extends Model
{
    protected $table = 'logros';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'categoria',
        'condicion',
        'puntos_recompensa',
        'rareza',
        'activo',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'condicion' => 'array'
    ];
}
