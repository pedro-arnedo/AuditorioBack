<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenido extends Model
{
    protected $table = 'contenidos';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'tema_id',
        'tipo',
        'titulo',
        'descripcion',
        'contenido_data',
        'configuracion',
        'orden',
        'puntos_valor',
        'es_obligatorio',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'contenido_data' => 'array',
        'configuracion' => 'array'
    ];

    public function tema()
    {
        return $this->belongsTo(Tema::class, 'tema_id', 'id');
    }

    public function recursos()
    {
        return $this->belongsToMany(
            RecursoMultimedia::class,
            'contenido_recursos',
            'contenido_id',
            'recurso_id'
        )
        ->withPivot(['orden', 'es_principal'])
        ->orderBy('orden', 'asc');
    }

    public function evaluacion()
    {
        return $this->hasOne(Evaluacion::class, 'contenido_id', 'id');
    }
}
