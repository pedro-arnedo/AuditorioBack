<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionModulo extends Model
{
    protected $table = 'inscripciones_modulos';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'modulo_id',
        'fecha_inscripcion',
        'fecha_inicio',
        'fecha_completado',
        'estado',
        'progreso_general',
        'puntos_totales',
        'created_at',
        'updated_at'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }
}
