<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $table = 'evaluaciones';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'contenido_id',
        'titulo',
        'descripcion',
        'tipo',
        'duracion_minutos',
        'intentos_permitidos',
        'puntuacion_minima',
        'fecha_disponible_desde',
        'fecha_disponible_hasta',
        'mostrar_respuestas',
        'configuracion',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'configuracion' => 'array'
    ];

    public function contenido()
    {
        return $this->belongsTo(Contenido::class, 'contenido_id');
    }

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class, 'evaluacion_id');
    }
}
