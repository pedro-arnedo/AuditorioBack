<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadoEvaluacion extends Model
{
    protected $table = 'resultados_evaluaciones';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'evaluacion_id',
        'usuario_id',
        'puntuacion',
        'intentos_usados',
        'aprobado',
        'fecha_realizacion',
        'detalle',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'detalle' => 'array'
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
