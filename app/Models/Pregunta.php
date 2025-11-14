<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $table = 'preguntas';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'evaluacion_id',
        'enunciado',
        'tipo',
        'opciones',
        'respuesta_correcta',
        'puntos',
        'explicacion',
        'orden',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'opciones' => 'array',
        'respuesta_correcta' => 'array'
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }
}
